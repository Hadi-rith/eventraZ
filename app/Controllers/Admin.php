<?php

namespace App\Controllers;

use App\Models\DaftarSekolahModel;
use App\Models\DaftarLuarModel;
use App\Models\DaftarAwamModel;
use App\Models\DaftarMuridModel;
use App\Models\DaftarGuruModel;
use App\Models\ProgramModel;
use App\Models\PublicAccountModel;
use App\Models\SchoolAccountModel;
use App\Models\AdminAccountModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Admin extends BaseController
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

    // ------------------------------------------------------------------
    // Helpers — role / admin_id from session
    // ------------------------------------------------------------------

    /** Returns the DB admin_id, or null for Super Admin. */
    private function getAdminId(): ?int
    {
        $id = $this->session->get('admin_id');
        return ($id !== null && $id !== '') ? (int) $id : null;
    }

    private function isSuperAdmin(): bool
    {
        return $this->session->get('role') === 'super_admin';
    }

    /**
     * Abort with 403 if the caller is not Super Admin.
     * Returns the JSON response or null if access is allowed.
     */
    private function requireSuperAdmin()
    {
        if (!$this->isSuperAdmin()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Super Admin dibenarkan.',
            ]);
        }
        return null;
    }

    /**
     * Verify that a program belongs to the current admin.
     * Super Admin can access any program.
     */
    private function canAccessProgram(array $program): bool
    {
        if ($this->isSuperAdmin()) return true;
        return (int) ($program['admin_id'] ?? -1) === $this->getAdminId();
    }

    private function serverErrorResponse(\Throwable $e, string $context)
    {
        log_message('error', "[Admin::{$context}] " . $e->getMessage() . "\n" . $e->getTraceAsString());
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Ralat pelayan dalaman. Sila semak log pelayan.',
        ]);
    }

    private function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $parsed = \DateTime::createFromFormat($format, $date);
        return $parsed !== false && $parsed->format($format) === $date;
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    public function dashboard()
    {
        return view('admin_dashboard');
    }

    /**
     * GET /admin/dashboard-stats
     * Returns role-appropriate statistics as JSON.
     */
    public function getDashboardStats()
    {
        try {
            $programModel = new ProgramModel();

            if ($this->isSuperAdmin()) {
                $stats = $programModel->getSuperAdminStats();

                // Also include per-admin breakdown
                $adminModel = new AdminAccountModel();
                $stats['admins'] = $adminModel->getAllAdminStats();

                $stats['role'] = 'super_admin';
            } else {
                $stats = $programModel->getAdminStats($this->getAdminId());
                $stats['role'] = 'admin';
            }

            return $this->response->setJSON(['success' => true, 'stats' => $stats]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getDashboardStats');
        }
    }

    // ------------------------------------------------------------------
    // Program CRUD
    // ------------------------------------------------------------------

    public function getProgramList()
    {
        try {
            $programModel = new ProgramModel();
            $programs     = $programModel->getProgramsForAdmin($this->getAdminId());

            $idToCode = [];
            foreach ($programs as $prog) {
                $idToCode[(int) $prog['id']] = $prog['program_code'];
            }

            $list = [];
            foreach ($programs as $prog) {
                $parentId  = ($prog['parent_id'] !== null && (int) $prog['parent_id'] !== 0)
                             ? (int) $prog['parent_id'] : null;
                $parentKod = $parentId ? ($idToCode[$parentId] ?? null) : null;

                $used      = $programModel->getUsedCapacity((int) $prog['id']);
                $limit     = (int) ($prog['registration_limit'] ?? 0);
                $remaining = $limit > 0 ? max(0, $limit - $used) : null;

                $list[] = [
                    'db_id'              => (int) $prog['id'],
                    'id'                 => $prog['program_code'],
                    'kod'                => $prog['program_code'],
                    'nama'               => $prog['program_name'],
                    'description'        => $prog['description']  ?? '',
                    'mula'               => $prog['start_date'],
                    'tamat'              => $prog['end_date'],
                    'event_time'         => $prog['event_time']   ?? '',
                    'location'           => $prog['location']     ?? '',
                    'organizer'          => $prog['organizer']    ?? '',
                    'status'             => $prog['status'],
                    'start_date'         => $prog['start_date'],
                    'end_date'           => $prog['end_date'],
                    'parent_id'          => $parentId,
                    'parent_kod'         => $parentKod,
                    'pic_nama'           => $prog['pic_nama']    ?? '',
                    'pic_tel'            => $prog['pic_tel']     ?? '',
                    'poster_image'       => $prog['poster_image'] ?? '',
                    'is_featured'        => (int) ($prog['is_featured'] ?? 0),
                    'registration_limit' => $limit,
                    'used_capacity'      => $used,
                    'remaining_capacity' => $remaining,
                    'admin_id'           => $prog['admin_id'],
                ];
            }

            return $this->response
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setJSON($list);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getProgramList');
        }
    }

    public function createProgram()
    {
        try {
            $programCode = strtoupper(trim((string) $this->request->getPost('program_code')));
            $programName = trim((string) $this->request->getPost('program_name'));
            $description = trim((string) $this->request->getPost('description'));
            $startDate   = trim((string) $this->request->getPost('start_date'));
            $endDate     = trim((string) $this->request->getPost('end_date'));
            $eventTime   = trim((string) $this->request->getPost('event_time'));
            $location    = trim((string) $this->request->getPost('location'));
            $organizer   = trim((string) $this->request->getPost('organizer'));
            $picNama     = trim((string) $this->request->getPost('pic_nama'));
            $picTel      = trim((string) $this->request->getPost('pic_tel'));
            $regLimit    = (int) $this->request->getPost('registration_limit');

            if ($programCode === '' || $programName === '' || $startDate === '' || $endDate === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Kod program, nama program, tarikh mula dan tarikh tamat diperlukan.',
                ]);
            }

            if (!preg_match('/^[A-Z0-9_-]{2,30}$/', $programCode)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Kod program hanya boleh mengandungi huruf, nombor, tanda - atau _.',
                ]);
            }

            if (!$this->isValidDate($startDate) || !$this->isValidDate($endDate)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Format tarikh tidak sah.',
                ]);
            }

            if ($endDate < $startDate) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Tarikh tamat mesti sama atau selepas tarikh mula.',
                ]);
            }

            if ($regLimit < 0) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Had pendaftaran tidak boleh negatif.',
                ]);
            }

            $programModel = new ProgramModel();
            $status       = $programModel->calculateStatus($startDate, $endDate);

            if ($programModel->where('program_code', $programCode)->first()) {
                return $this->response->setStatusCode(409)->setJSON([
                    'success' => false,
                    'message' => 'Kod program ini telah wujud.',
                ]);
            }

            // Handle poster upload
            $posterPath = null;
            $posterFile = $this->request->getFile('poster_image');
            if ($posterFile && $posterFile->isValid() && !$posterFile->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/posters/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $newName    = time() . '_' . bin2hex(random_bytes(10)) . '.' . $posterFile->getExtension();
                $posterFile->move($uploadPath, $newName);
                $posterPath = 'uploads/posters/' . $newName;
            }

            $saved = $programModel->insert([
                'program_code'       => $programCode,
                'program_name'       => $programName,
                'description'        => $description,
                'start_date'         => $startDate,
                'end_date'           => $endDate,
                'event_time'         => $eventTime,
                'location'           => $location,
                'organizer'          => $organizer,
                'status'             => $status,
                'pic_nama'           => $picNama,
                'pic_tel'            => $picTel,
                'registration_limit' => $regLimit,
                'admin_id'           => $this->getAdminId(),
                'poster_image'       => $posterPath,
            ]);

            if (!$saved) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan program ke pangkalan data.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program berjaya didaftarkan.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'createProgram');
        }
    }

    public function createSubProgram()
    {
        try {
            $parentCode  = strtoupper(trim((string) $this->request->getPost('parent_code')));
            $programCode = strtoupper(trim((string) $this->request->getPost('program_code')));
            $programName = trim((string) $this->request->getPost('program_name'));
            $description = trim((string) $this->request->getPost('description'));
            $startDate   = trim((string) $this->request->getPost('start_date'));
            $endDate     = trim((string) $this->request->getPost('end_date'));
            $eventTime   = trim((string) $this->request->getPost('event_time'));
            $location    = trim((string) $this->request->getPost('location'));
            $organizer   = trim((string) $this->request->getPost('organizer'));
            $picNama     = trim((string) $this->request->getPost('pic_nama'));
            $picTel      = trim((string) $this->request->getPost('pic_tel'));
            $regLimit    = (int) $this->request->getPost('registration_limit');

            if ($parentCode === '' || $programCode === '' || $programName === '' || $startDate === '' || $endDate === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Program induk, kod program, nama program, tarikh mula dan tarikh tamat diperlukan.',
                ]);
            }

            if (!preg_match('/^[A-Z0-9_-]{2,30}$/', $programCode)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Kod program hanya boleh mengandungi huruf, nombor, tanda - atau _.',
                ]);
            }

            if (!$this->isValidDate($startDate) || !$this->isValidDate($endDate)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Format tarikh tidak sah.',
                ]);
            }

            if ($endDate < $startDate) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Tarikh tamat mesti sama atau selepas tarikh mula.',
                ]);
            }

            $programModel = new ProgramModel();
            $parent       = $programModel->where('program_code', $parentCode)->first();

            if (!$parent) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program induk tidak ditemui.',
                ]);
            }

            // Admin can only create sub-programs under their own programs
            if (!$this->canAccessProgram($parent)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak mempunyai akses kepada program induk ini.',
                ]);
            }

            if ($programCode === $parentCode) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Program tidak boleh menjadi induk kepada dirinya sendiri.',
                ]);
            }

            if ($programModel->where('program_code', $programCode)->first()) {
                return $this->response->setStatusCode(409)->setJSON([
                    'success' => false,
                    'message' => 'Kod program ini telah wujud.',
                ]);
            }

            $status     = $programModel->calculateStatus($startDate, $endDate);
            $posterPath = null;
            $posterFile = $this->request->getFile('poster_image');
            if ($posterFile && $posterFile->isValid() && !$posterFile->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/posters/';
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
                $newName    = time() . '_' . bin2hex(random_bytes(10)) . '.' . $posterFile->getExtension();
                $posterFile->move($uploadPath, $newName);
                $posterPath = 'uploads/posters/' . $newName;
            }

            $saved = $programModel->insert([
                'program_code'       => $programCode,
                'parent_id'          => (int) $parent['id'],
                'program_name'       => $programName,
                'description'        => $description,
                'start_date'         => $startDate,
                'end_date'           => $endDate,
                'event_time'         => $eventTime,
                'location'           => $location,
                'organizer'          => $organizer,
                'status'             => $status,
                'pic_nama'           => $picNama,
                'pic_tel'            => $picTel,
                'registration_limit' => $regLimit,
                'admin_id'           => $this->getAdminId(),
                'poster_image'       => $posterPath,
            ]);

            if (!$saved) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan sub program.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sub program berjaya didaftarkan.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'createSubProgram');
        }
    }

    public function updateProgram(string $currentCode)
    {
        try {
            $currentCode = strtoupper(trim($currentCode));
            $programModel = new ProgramModel();
            $program      = $programModel->where('program_code', $currentCode)->first();

            if (!$program) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program tidak ditemui.',
                ]);
            }

            if (!$this->canAccessProgram($program)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak mempunyai akses untuk mengemaskini program ini.',
                ]);
            }

            $programCode = strtoupper(trim((string) $this->request->getPost('program_code')));
            $programName = trim((string) $this->request->getPost('program_name'));
            $description = trim((string) $this->request->getPost('description'));
            $startDate   = trim((string) $this->request->getPost('start_date'));
            $endDate     = trim((string) $this->request->getPost('end_date'));
            $eventTime   = trim((string) $this->request->getPost('event_time'));
            $location    = trim((string) $this->request->getPost('location'));
            $organizer   = trim((string) $this->request->getPost('organizer'));
            $picNama     = trim((string) $this->request->getPost('pic_nama'));
            $picTel      = trim((string) $this->request->getPost('pic_tel'));
            $regLimit    = (int) $this->request->getPost('registration_limit');
            $isFeatured  = $this->request->getPost('is_featured') ? 1 : 0;

            if ($programCode === '' || $programName === '' || $startDate === '' || $endDate === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Kod program, nama program, tarikh mula dan tarikh tamat diperlukan.',
                ]);
            }

            if (!$this->isValidDate($startDate) || !$this->isValidDate($endDate)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Format tarikh tidak sah.',
                ]);
            }

            if ($endDate < $startDate) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Tarikh tamat mesti sama atau selepas tarikh mula.',
                ]);
            }

            $duplicate = $programModel->where('program_code', $programCode)->first();
            if ($duplicate && (int) $duplicate['id'] !== (int) $program['id']) {
                return $this->response->setStatusCode(409)->setJSON([
                    'success' => false,
                    'message' => 'Kod program ini telah wujud.',
                ]);
            }

            // Handle poster upload
            $posterPath = $program['poster_image'] ?? null;
            $posterFile = $this->request->getFile('poster_image');
            if ($posterFile && $posterFile->isValid() && !$posterFile->hasMoved()) {
                if ($posterPath && file_exists(FCPATH . $posterPath)) {
                    unlink(FCPATH . $posterPath);
                }
                $uploadPath = FCPATH . 'uploads/posters/';
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
                $newName    = time() . '_' . bin2hex(random_bytes(10)) . '.' . $posterFile->getExtension();
                $posterFile->move($uploadPath, $newName);
                $posterPath = 'uploads/posters/' . $newName;
            }

            // parent_id handling
            $parentCodeRaw      = $this->request->getPost('parent_code');
            $parentCodeProvided = ($parentCodeRaw !== null);
            $parentCode         = $parentCodeProvided ? strtoupper(trim((string) $parentCodeRaw)) : null;

            $updateData = [
                'program_code'       => $programCode,
                'program_name'       => $programName,
                'description'        => $description,
                'start_date'         => $startDate,
                'end_date'           => $endDate,
                'event_time'         => $eventTime,
                'location'           => $location,
                'organizer'          => $organizer,
                'status'             => $programModel->calculateStatus($startDate, $endDate),
                'pic_nama'           => $picNama,
                'pic_tel'            => $picTel,
                'registration_limit' => $regLimit,
                'is_featured'        => $isFeatured,
                'poster_image'       => $posterPath,
            ];

            if ($parentCodeProvided) {
                if ($parentCode === '') {
                    $updateData['parent_id'] = null;
                } else {
                    if ($parentCode === $programCode) {
                        return $this->response->setStatusCode(422)->setJSON([
                            'success' => false,
                            'message' => 'Program tidak boleh menjadi induk kepada dirinya sendiri.',
                        ]);
                    }
                    $newParent = $programModel->where('program_code', $parentCode)->first();
                    if (!$newParent) {
                        return $this->response->setStatusCode(404)->setJSON([
                            'success' => false,
                            'message' => 'Program induk tidak ditemui.',
                        ]);
                    }
                    if ((int) ($newParent['parent_id'] ?? 0) === (int) $program['id']) {
                        return $this->response->setStatusCode(422)->setJSON([
                            'success' => false,
                            'message' => 'Program induk yang dipilih adalah sub program kepada program ini.',
                        ]);
                    }
                    $updateData['parent_id'] = (int) $newParent['id'];
                }
            }

            $oldProgramName = $program['program_name'];
            $programModel->update($program['id'], $updateData);

            if ($oldProgramName !== $programName) {
                $this->syncRegistrationProgramName($oldProgramName, $programName);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program berjaya dikemaskini.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'updateProgram');
        }
    }

    public function deleteProgram(string $programCode)
    {
        try {
            $programCode  = strtoupper(trim($programCode));
            $programModel = new ProgramModel();
            $program      = $programModel->where('program_code', $programCode)->first();

            if (!$program) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program tidak ditemui.',
                ]);
            }

            if (!$this->canAccessProgram($program)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak mempunyai akses untuk memadam program ini.',
                ]);
            }

            if ($this->programHasRegistrations($program)) {
                return $this->response->setStatusCode(409)->setJSON([
                    'success' => false,
                    'message' => 'Program ini sudah mempunyai rekod pendaftaran dan tidak boleh dipadam.',
                ]);
            }

            $programModel->delete($program['id']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program berjaya dipadam.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'deleteProgram');
        }
    }

    // ------------------------------------------------------------------
    // Registration data
    // ------------------------------------------------------------------

    public function getAdminData()
    {
        try {
            $sekolahModel = new DaftarSekolahModel();
            $luarModel    = new DaftarLuarModel();
            $awamModel    = new DaftarAwamModel();
            $guruModel    = new DaftarGuruModel();

            if ($this->isSuperAdmin()) {
                $sekolahRows = $sekolahModel->orderBy('created_at', 'DESC')->findAll();
                $luarRows    = $luarModel->orderBy('created_at', 'DESC')->findAll();
                $awamRows    = $awamModel->orderBy('created_at', 'DESC')->findAll();
            } else {
                // Scope to programs owned by this admin
                $programModel = new ProgramModel();
                $programs     = $programModel->where('admin_id', $this->getAdminId())->findAll();
                $programIds   = array_column($programs, 'id');

                if (empty($programIds)) {
                    return $this->response->setJSON([
                        'success'     => true,
                        'sekolahTRG'  => [],
                        'sekolahLuar' => [],
                        'orangAwam'   => [],
                    ]);
                }

                $sekolahRows = $sekolahModel->whereIn('program_id', $programIds)->orderBy('created_at', 'DESC')->findAll();
                $luarRows    = $luarModel->whereIn('program_id', $programIds)->orderBy('created_at', 'DESC')->findAll();
                $awamRows    = $awamModel->whereIn('program_id', $programIds)->orderBy('created_at', 'DESC')->findAll();
            }

            // Attach guru list to each school registration
            foreach ($sekolahRows as &$row) {
                $row['guru'] = $guruModel->where('registration_id', $row['id'])->findAll();
            }

            return $this->response->setJSON([
                'success'     => true,
                'sekolahTRG'  => $this->formatSekolahData($sekolahRows),
                'sekolahLuar' => $this->formatLuarData($luarRows),
                'orangAwam'   => $this->formatAwamData($awamRows),
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getAdminData');
        }
    }

    public function getRegistrationStudents(int $registrationId)
    {
        try {
            $sekolahModel = new DaftarSekolahModel();
            $registration = $sekolahModel->find($registrationId);

            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Rekod pendaftaran sekolah tidak ditemui.',
                ]);
            }

            // Access check
            $programModel = new ProgramModel();
            $program      = $programModel->find($registration['program_id']);
            if ($program && !$this->canAccessProgram($program)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak.',
                ]);
            }

            $muridModel = new DaftarMuridModel();
            $guruModel  = new DaftarGuruModel();

            $students = $muridModel->where('registration_id', $registrationId)->orderBy('id', 'ASC')->findAll();
            $gurus    = $guruModel->where('registration_id', $registrationId)->orderBy('id', 'ASC')->findAll();

            return $this->response->setJSON([
                'success'  => true,
                'school'   => $registration['nama_sekolah'],
                'program'  => $registration['program_name'],
                'students' => array_map(fn($s) => ['nama' => $s['nama_murid'], 'ic' => $s['ic_murid']], $students),
                'gurus'    => array_map(fn($g) => ['nama' => $g['nama_guru'],  'ic' => $g['ic_guru']],  $gurus),
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getRegistrationStudents');
        }
    }

    // ------------------------------------------------------------------
    // Account management — Super Admin only (for admin accounts)
    // Regular admin CRUD (school/public) is accessible to all admins
    // ------------------------------------------------------------------

    public function getAccounts()
    {
        try {
            $schoolModel = new SchoolAccountModel();
            $publicModel = new PublicAccountModel();

            $data = [
                'success' => true,
                'school'  => $schoolModel->orderBy('school_name', 'ASC')->findAll(),
                'public'  => $publicModel->orderBy('name', 'ASC')->findAll(),
            ];

            // Super Admin also gets the list of admin accounts
            if ($this->isSuperAdmin()) {
                $adminModel = new AdminAccountModel();
                $admins = $adminModel->orderBy('name', 'ASC')->findAll();
                // Strip passwords from output
                foreach ($admins as &$a) unset($a['password']);
                $data['admins'] = $admins;
            }

            return $this->response
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setJSON($data);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getAccounts');
        }
    }

    public function createAccount(string $type)
    {
        try {
            $type = strtolower($type);

            if ($type === 'admin') {
                if ($denied = $this->requireSuperAdmin()) return $denied;
                return $this->createAdminAccount();
            }

            if ($type === 'school') return $this->createSchoolAccount();
            if ($type === 'public') return $this->createPublicAccount();

            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Jenis akaun tidak sah.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'createAccount');
        }
    }

    public function updateAccount(string $type, int $id)
    {
        try {
            $type = strtolower($type);

            if ($type === 'admin') {
                if ($denied = $this->requireSuperAdmin()) return $denied;
                return $this->updateAdminAccount($id);
            }

            if ($type === 'school') return $this->updateSchoolAccount($id);
            if ($type === 'public') return $this->updatePublicAccount($id);

            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Jenis akaun tidak sah.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'updateAccount');
        }
    }

    public function deleteAccount(string $type, int $id)
    {
        try {
            $type = strtolower($type);

            if ($type === 'admin') {
                if ($denied = $this->requireSuperAdmin()) return $denied;
                $model = new AdminAccountModel();
            } elseif ($type === 'school') {
                $model = new SchoolAccountModel();
            } elseif ($type === 'public') {
                $model = new PublicAccountModel();
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Jenis akaun tidak sah.',
                ]);
            }

            if (!$model->find($id)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Akaun tidak ditemui.',
                ]);
            }

            $model->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Akaun berjaya dipadam.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'deleteAccount');
        }
    }

    // ------------------------------------------------------------------
    // Events (alias — same as getProgramList for the events tab)
    // ------------------------------------------------------------------

    public function getEvents()
    {
        try {
            $programModel = new ProgramModel();
            $programs     = $programModel->getProgramsForAdmin($this->getAdminId());

            return $this->response->setJSON([
                'success'  => true,
                'programs' => $programs,
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getEvents');
        }
    }

    public function updateEvent($programId)
    {
        try {
            $programModel = new ProgramModel();
            $program      = $programModel->find($programId);

            if (!$program) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program tidak ditemui.',
                ]);
            }

            if (!$this->canAccessProgram($program)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak.',
                ]);
            }

            $programName = trim($this->request->getPost('program_name'));
            $startDate   = $this->request->getPost('start_date');
            $endDate     = $this->request->getPost('end_date');
            $location    = trim($this->request->getPost('location') ?? '');
            $picNama     = trim($this->request->getPost('pic_nama') ?? '');
            $picTel      = trim($this->request->getPost('pic_tel')  ?? '');
            $isFeatured  = $this->request->getPost('is_featured') ? 1 : 0;
            $regLimit    = (int) $this->request->getPost('registration_limit');

            if (!$programName || !$startDate || !$endDate) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Nama program, tarikh mula dan tarikh tamat diperlukan.',
                ]);
            }

            $posterPath = $program['poster_image'] ?? null;
            $posterFile = $this->request->getFile('poster_image');
            if ($posterFile && $posterFile->isValid() && !$posterFile->hasMoved()) {
                if ($posterPath && file_exists(FCPATH . $posterPath)) unlink(FCPATH . $posterPath);
                $uploadPath = FCPATH . 'uploads/posters/';
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
                $newName    = time() . '_' . bin2hex(random_bytes(10)) . '.' . $posterFile->getExtension();
                $posterFile->move($uploadPath, $newName);
                $posterPath = 'uploads/posters/' . $newName;
            }

            $programModel->update($programId, [
                'program_name'       => $programName,
                'start_date'         => $startDate,
                'end_date'           => $endDate,
                'location'           => $location,
                'pic_nama'           => $picNama,
                'pic_tel'            => $picTel,
                'is_featured'        => $isFeatured,
                'registration_limit' => $regLimit,
                'status'             => $programModel->calculateStatus($startDate, $endDate),
                'poster_image'       => $posterPath,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program berjaya dikemaskini.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'updateEvent');
        }
    }

    // ------------------------------------------------------------------
    // Capacity endpoint (used by registration forms)
    // ------------------------------------------------------------------

    public function getProgramCapacity(int $programId)
    {
        try {
            $programModel = new ProgramModel();
            $program      = $programModel->find($programId);

            if (!$program) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program tidak ditemui.',
                ]);
            }

            $limit     = (int) ($program['registration_limit'] ?? 0);
            $used      = $programModel->getUsedCapacity($programId);
            $remaining = $limit > 0 ? max(0, $limit - $used) : null;

            return $this->response->setJSON([
                'success'            => true,
                'program_id'         => $programId,
                'registration_limit' => $limit,
                'used_capacity'      => $used,
                'remaining_capacity' => $remaining,
                'is_full'            => $limit > 0 && $remaining <= 0,
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getProgramCapacity');
        }
    }

    // ------------------------------------------------------------------
    // Sub-programs
    // ------------------------------------------------------------------

    public function getSubPrograms(string $parentCode)
    {
        try {
            $programModel = new ProgramModel();
            $parent = $programModel->where('program_code', strtoupper(trim($parentCode)))->first();

            if (!$parent) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program induk tidak ditemui.',
                ]);
            }

            $subs = $programModel
                ->where('parent_id', (int) $parent['id'])
                ->orderBy('start_date', 'ASC')
                ->orderBy('program_name', 'ASC')
                ->findAll();

            return $this->response
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setJSON(['success' => true, 'programs' => $subs]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getSubPrograms');
        }
    }

    // ------------------------------------------------------------------
    // Private helpers — account CRUD
    // ------------------------------------------------------------------

    private function createAdminAccount()
    {
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $name     = trim((string) $this->request->getPost('name'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($username === '' || $name === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Username, nama, emel dan kata laluan diperlukan.',
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $model = new AdminAccountModel();
        if ($model->where('username', $username)->orWhere('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Username atau emel telah digunakan.',
            ]);
        }

        $model->insert([
            'username'  => $username,
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
            'is_active' => 1,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun admin berjaya dicipta.',
        ]);
    }

    private function updateAdminAccount(int $id)
    {
        $model   = new AdminAccountModel();
        $account = $model->find($id);

        if (!$account) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Akaun admin tidak ditemui.',
            ]);
        }

        $username = strtolower(trim((string) $this->request->getPost('username')));
        $name     = trim((string) $this->request->getPost('name'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));
        $isActive = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : $account['is_active'];

        if ($username === '' || $name === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Username dan nama diperlukan.',
            ]);
        }

        $duplicate = $model->groupStart()
            ->where('username', $username)
            ->orWhere('email', $email)
            ->groupEnd()
            ->first();

        if ($duplicate && (int) $duplicate['id'] !== $id) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Username atau emel telah digunakan.',
            ]);
        }

        $data = [
            'username'  => $username,
            'name'      => $name,
            'email'     => $email,
            'is_active' => $isActive,
        ];

        if ($password !== '') {
            $data['password'] = $password;
        }

        $model->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun admin berjaya dikemaskini.',
        ]);
    }

    private function createSchoolAccount()
    {
        $schoolCode = strtoupper(trim((string) $this->request->getPost('school_code')));
        $schoolName = trim((string) $this->request->getPost('school_name'));
        $email      = strtolower(trim((string) $this->request->getPost('email')));
        $password   = trim((string) $this->request->getPost('password'));

        if ($schoolCode === '' || $schoolName === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Kod sekolah, nama sekolah, emel dan kata laluan diperlukan.',
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $model = new SchoolAccountModel();
        if ($model->groupStart()->where('school_code', $schoolCode)->orWhere('email', $email)->groupEnd()->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Kod sekolah atau emel telah digunakan.',
            ]);
        }

        $model->insert([
            'school_code' => $schoolCode,
            'school_name' => $schoolName,
            'email'       => $email,
            'password'    => $password,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun sekolah berjaya dicipta.',
        ]);
    }

    private function updateSchoolAccount(int $id)
    {
        $model   = new SchoolAccountModel();
        $account = $model->find($id);

        if (!$account) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Akaun sekolah tidak ditemui.',
            ]);
        }

        $schoolCode = strtoupper(trim((string) $this->request->getPost('school_code')));
        $schoolName = trim((string) $this->request->getPost('school_name'));
        $email      = strtolower(trim((string) $this->request->getPost('email')));
        $password   = trim((string) $this->request->getPost('password'));

        if ($schoolCode === '' || $schoolName === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Kod dan nama sekolah diperlukan.',
            ]);
        }

        $duplicate = $model->groupStart()->where('school_code', $schoolCode)->orWhere('email', $email)->groupEnd()->first();
        if ($duplicate && (int) $duplicate['id'] !== $id) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Kod sekolah atau emel telah digunakan.',
            ]);
        }

        $data = ['school_code' => $schoolCode, 'school_name' => $schoolName, 'email' => $email];
        if ($password !== '') $data['password'] = $password;

        $model->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun sekolah berjaya dikemaskini.',
        ]);
    }

    private function createPublicAccount()
    {
        $name     = trim((string) $this->request->getPost('name'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($name === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Nama, emel dan kata laluan diperlukan.',
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $model = new PublicAccountModel();
        if ($model->where('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Emel ini telah digunakan.',
            ]);
        }

        $model->insert([
            'name'       => $name,
            'email'      => $email,
            'password'   => $password,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun awam berjaya dicipta.',
        ]);
    }

    private function updatePublicAccount(int $id)
    {
        $model   = new PublicAccountModel();
        $account = $model->find($id);

        if (!$account) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Akaun awam tidak ditemui.',
            ]);
        }

        $name     = trim((string) $this->request->getPost('name'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($name === '' || $email === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Nama dan emel diperlukan.',
            ]);
        }

        $duplicate = $model->where('email', $email)->first();
        if ($duplicate && (int) $duplicate['id'] !== $id) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Emel ini telah digunakan.',
            ]);
        }

        $data = ['name' => $name, 'email' => $email];
        if ($password !== '') $data['password'] = $password;

        $model->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun awam berjaya dikemaskini.',
        ]);
    }

    // ------------------------------------------------------------------
    // Private helpers — registrations
    // ------------------------------------------------------------------

    private function programHasRegistrations($program): bool
    {
        $programNames = [];
        $programIds   = [];

        if (is_array($program)) {
            $programNames[] = (string) ($program['program_name'] ?? '');
            $programIds[]   = (int)   ($program['id'] ?? 0);

            $children = (new ProgramModel())->select('program_name, id')->where('parent_id', (int) $program['id'])->findAll();
            foreach ($children as $child) {
                $programNames[] = (string) ($child['program_name'] ?? '');
                $programIds[]   = (int)   ($child['id'] ?? 0);
            }
        }

        $programIds = array_filter($programIds);

        if (!empty($programIds)) {
            if ((new DaftarSekolahModel())->whereIn('program_id', $programIds)->countAllResults() > 0) return true;
            if ((new DaftarAwamModel())->whereIn('program_id', $programIds)->countAllResults()    > 0) return true;
            if ((new DaftarLuarModel())->whereIn('program_id', $programIds)->countAllResults()    > 0) return true;
        }

        return false;
    }

    private function syncRegistrationProgramName(string $oldName, string $newName): void
    {
        if ($oldName === '' || $newName === '' || $oldName === $newName) return;

        (new DaftarSekolahModel())->where('program_name', $oldName)->set(['program_name' => $newName])->update();
        (new DaftarLuarModel())->where('program_name', $oldName)->set(['program_name'    => $newName])->update();
        (new DaftarAwamModel())->where('program_name', $oldName)->set(['program_name'    => $newName])->update();
    }

    private function formatSekolahData(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id'          => $row['id'],
                'timestamp'   => date('d/m/Y H:i', strtotime($row['created_at'])),
                'program'     => $row['program_name'],
                'namaSekolah' => $row['nama_sekolah'],
                'kodSekolah'  => $row['kod_sekolah'],
                'email'       => $row['email_sekolah'],
                'tel'         => $row['tel_sekolah'],
                'bilMurid'    => $row['bil_murid'],
                'guru'        => $row['guru'] ?? [],
                'status'      => $row['status'],
            ];
        }
        return $result;
    }

    private function formatLuarData(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id'          => $row['id'],
                'timestamp'   => date('d/m/Y H:i', strtotime($row['created_at'])),
                'program'     => $row['program_name'],
                'namaSekolah' => $row['nama_sekolah'],
                'kodSekolah'  => $row['kod_sekolah'],
                'tel'         => $row['tel'],
                'email'       => $row['email'],
            ];
        }
        return $result;
    }

    private function formatAwamData(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id'        => $row['id'],
                'timestamp' => date('d/m/Y H:i', strtotime($row['created_at'])),
                'program'   => $row['program_name'],
                'nama'      => $row['nama'],
                'ic'        => $row['ic'],
                'tel'       => $row['tel'],
                'email'     => $row['email'],
                'bilAhli'   => $row['bil_ahli'],
            ];
        }
        return $result;
    }
}