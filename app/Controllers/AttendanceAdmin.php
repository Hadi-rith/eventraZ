<?php

namespace App\Controllers;

use App\Models\AttendanceSessionModel;
use App\Models\AttendanceRecordModel;
use App\Models\ProgramModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Admin / Super Admin attendance session management.
 * Auth pattern mirrors App\Controllers\Admin exactly.
 */
class AttendanceAdmin extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $role = $this->session->get('role');
        if (!$this->session->get('logged_in') || !in_array($role, ['admin', 'super_admin'], true)) {
            header('Location: ' . base_url('/'));
            exit();
        }
    }

    private function getAdminId(): ?int
    {
        $id = $this->session->get('admin_id');
        return ($id !== null && $id !== '') ? (int) $id : null;
    }

    private function isSuperAdmin(): bool
    {
        return $this->session->get('role') === 'super_admin';
    }

    private function getProgramScopeAdminId(): ?int
    {
        return $this->isSuperAdmin() ? null : $this->getAdminId();
    }

    private function canAccessSession(array $session): bool
    {
        if ($this->isSuperAdmin()) return true;
        return $session['created_by'] !== null && (int) $session['created_by'] === $this->getAdminId();
    }

    /**
     * Fragment injected into the "ATTENDANCE" tab of admin_dashboard.php via AJAX.
     * See integration_snippets/admin_dashboard_tab.php for the tab markup + JS that calls this.
     */
    public function index()
    {
        $sessionModel = new AttendanceSessionModel();
        $recordModel  = new AttendanceRecordModel();
        $programModel = new ProgramModel();

        $sessions = $sessionModel->orderBy('created_at', 'DESC')->findAll();

        $scopeAdminId = $this->getProgramScopeAdminId();
        if ($scopeAdminId !== null) {
            $sessions = array_values(array_filter($sessions, fn ($s) => (int) $s['created_by'] === $scopeAdminId));
        }

        $programsById = [];
        foreach ($programModel->getProgramsForAdmin($scopeAdminId) as $p) {
            $programsById[$p['id']] = $p['program_name'];
        }

        $rows = [];
        foreach ($sessions as $s) {
            $rows[] = [
                'id'            => $s['id'],
                'session_name'  => $s['session_name'],
                'program_name'  => $programsById[$s['event_id']] ?? '-',
                'session_date'  => $s['session_date'],
                'start_time'    => $s['start_time'],
                'end_time'      => $s['end_time'],
                'status'        => $s['status'],
                'is_active'     => $sessionModel->isActive($s),
                'checkin_count' => $recordModel->countForSession($s['id']),
                'token'         => $s['token'],
                'checkin_url'   => base_url('attendance/checkin/' . $s['token']),
            ];
        }

        // Program dropdown options for the "create session" form.
        // Only main programs (parent_id IS NULL) are offered at the top level;
        // if a main program has sub-programs, its `subs` array is attached so
        // the UI can reveal a second "pick sub program" dropdown — same
        // main→sub pattern used on the school/awam registration forms.
        // Past (ended) programs are excluded, unless a main program's own
        // dates have passed but it still has an upcoming sub-program to offer.
        $today          = date('Y-m-d');
        $scopedPrograms = $programModel->getProgramsForAdmin($scopeAdminId);
        $isUpcoming     = fn ($p) => empty($p['end_date']) || $p['end_date'] >= $today;

        $subsByParent = [];
        foreach ($scopedPrograms as $p) {
            if (!empty($p['parent_id'])) {
                $subsByParent[(int) $p['parent_id']][] = $p;
            }
        }

        $programOptions = [];
        foreach ($scopedPrograms as $p) {
            if (!empty($p['parent_id'])) {
                continue; // sub-programs are attached to their parent below, not listed at top level
            }

            $upcomingSubs = array_values(array_filter($subsByParent[(int) $p['id']] ?? [], $isUpcoming));

            if (!$isUpcoming($p) && empty($upcomingSubs)) {
                continue; // this program (and any of its subs) is entirely in the past
            }

            $programOptions[] = [
                'id'         => $p['id'],
                'name'       => $p['program_name'],
                'start_date' => $p['start_date'],
                'end_date'   => $p['end_date'],
                'subs'       => array_map(fn ($s) => [
                    'id'         => $s['id'],
                    'name'       => $s['program_name'],
                    'start_date' => $s['start_date'],
                    'end_date'   => $s['end_date'],
                ], $upcomingSubs),
            ];
        }

        return $this->response->setJSON([
            'success'  => true,
            'sessions' => $rows,
            'programs' => $programOptions,
        ]);
    }

    public function create()
    {
        $rules = [
            'event_id'     => 'required|integer',
            'session_name' => 'required|min_length[3]|max_length[150]',
            'session_date' => 'required|valid_date[Y-m-d]',
            'start_time'   => 'required|regex_match[/^([01]\d|2[0-3]):[0-5]\d$/]',
            'end_time'     => 'required|regex_match[/^([01]\d|2[0-3]):[0-5]\d$/]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        $date = $this->request->getPost('session_date');
        $startTime = $date . ' ' . $this->request->getPost('start_time');
        $endTime   = $date . ' ' . $this->request->getPost('end_time');

        if (strtotime($endTime) <= strtotime($startTime)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Masa tamat mesti selepas masa mula.',
            ]);
        }

        $programModel = new ProgramModel();
        $program = $programModel->find((int) $this->request->getPost('event_id'));
        if (!$program) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Program yang dipilih tidak sah.',
            ]);
        }
        // Non-super-admins can only create sessions for their own programs
        if (!$this->isSuperAdmin() && (int) ($program['admin_id'] ?? -1) !== $this->getAdminId()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Akses ditolak untuk program ini.',
            ]);
        }
        // Program already ended — no new attendance sessions for past events
        if (!empty($program['end_date']) && $program['end_date'] < date('Y-m-d')) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Program ini telah tamat. Sesi kehadiran hanya boleh dicipta untuk program semasa atau akan datang.',
            ]);
        }
        // Tarikh sesi mesti berada dalam tempoh program yang dipilih —
        // tarikh tidak lagi ditaip bebas oleh admin, jadi ini adalah semakan pertahanan tambahan.
        if (!empty($program['start_date']) && $date < $program['start_date']) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Tarikh sesi mesti dalam tempoh program yang dipilih.',
            ]);
        }
        if (!empty($program['end_date']) && $date > $program['end_date']) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Tarikh sesi mesti dalam tempoh program yang dipilih.',
            ]);
        }

        $sessionModel = new AttendanceSessionModel();
        $token = $sessionModel->generateToken();

        $id = $sessionModel->insert([
            'event_id'     => $program['id'],
            'session_name' => $this->request->getPost('session_name'),
            'token'        => $token,
            'session_date' => $date,
            'start_time'   => $startTime, // already validated Y-m-d H:i, 24hr
            'end_time'     => $endTime,
            'status'       => 'active',
            'created_by'   => $this->getAdminId(), // null for super admin
        ]);

        return $this->response->setJSON([
            'success'     => true,
            'id'          => $id,
            'token'       => $token,
            'checkin_url' => base_url('attendance/checkin/' . $token),
        ]);
    }

    public function regenerate($id)
    {
        $sessionModel = new AttendanceSessionModel();
        $session = $sessionModel->find($id);
        if (!$session) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Sesi tidak ditemui.']);
        }
        if (!$this->canAccessSession($session)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $newToken = $sessionModel->generateToken();
        $sessionModel->update($id, ['token' => $newToken]);

        return $this->response->setJSON([
            'success'     => true,
            'token'       => $newToken,
            'checkin_url' => base_url('attendance/checkin/' . $newToken),
        ]);
    }

    public function toggleStatus($id)
    {
        $sessionModel = new AttendanceSessionModel();
        $session = $sessionModel->find($id);
        if (!$session) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Sesi tidak ditemui.']);
        }
        if (!$this->canAccessSession($session)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $newStatus = $session['status'] === 'active' ? 'disabled' : 'active';
        $sessionModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
    }

    public function records($id)
    {
        $sessionModel = new AttendanceSessionModel();
        $recordModel  = new AttendanceRecordModel();

        $session = $sessionModel->find($id);
        if (!$session || !$this->canAccessSession($session)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $records = $recordModel->where('session_id', $id)->orderBy('attendance_time', 'DESC')->findAll();

        return $this->response->setJSON([
            'success' => true,
            'session' => $session,
            'records' => $records,
        ]);
    }
}