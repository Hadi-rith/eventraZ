<?php

namespace App\Controllers;

use App\Models\DaftarAwamModel;
use App\Models\DaftarFamilyModel;
use App\Models\ProgramModel;
use App\Validation\ValidationRules;

class PublicPortal extends BaseController
{
    // ------------------------------------------------------------------
    // Auth guard helper — returns a 403 JSON response or null if OK
    // ------------------------------------------------------------------
    private function requirePublicAuth()
    {
        if (!$this->session->get('logged_in') || $this->session->get('role') !== 'public') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sila log masuk untuk meneruskan.',
            ]);
        }
        return null;
    }

    // ------------------------------------------------------------------
    // Pages
    // ------------------------------------------------------------------

    public function index()
    {
        return redirect()->to(base_url('awam/events'));
    }

    public function portal()
    {
        return view('public_portal');
    }

    public function events()
    {
        return view('public_events');
    }

    // ------------------------------------------------------------------
    // Registration submission
    // ------------------------------------------------------------------

    public function simpanPendaftaran()
    {
        // ── Auth guard ────────────────────────────────────────────────
        if ($err = $this->requirePublicAuth()) return $err;

        // ── Server-side validation (base fields) ──────────────────────
        if (!$this->validate(ValidationRules::PUBLIC_REGISTRATION)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        $formData     = $this->request->getPost();
        $programModel = new ProgramModel();
        $bilAhli      = (int) $formData['bilAhli'];

        // ── Validate family member fields ─────────────────────────────
        for ($i = 0; $i < $bilAhli; $i++) {
            $namaAhli = trim((string) ($formData["namaAhli_{$i}"] ?? ''));
            $icAhli   = trim((string) ($formData["icAhli_{$i}"]   ?? ''));

            if ($namaAhli === '' || $icAhli === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Sila lengkapkan semua maklumat ahli keluarga.',
                ]);
            }

            // IC: 12 digits (dashes optional) — basic sanitise
            $icClean = preg_replace('/\D/', '', $icAhli);
            if (strlen($icClean) !== 12) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => "No. IC ahli ke-" . ($i + 1) . " tidak sah.",
                ]);
            }

            if (strlen($namaAhli) > 100) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => "Nama ahli ke-" . ($i + 1) . " terlalu panjang.",
                ]);
            }
        }

        // ── Validate program exists and is active ─────────────────────
        $programId = (int) $formData['programId'];
        $program   = $programModel->find($programId);

        if (!$program || $program['status'] !== 'AKTIF') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Program yang dipilih tidak sah atau tidak aktif.',
            ]);
        }

        // ── Capacity check — registrant (1) + family members ─────────
        $slotsNeeded = 1 + $bilAhli;
        if (!$programModel->hasCapacity($programId, $slotsNeeded)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success'   => false,
                'message'   => 'Pendaftaran telah ditutup. Kapasiti program telah penuh.',
                'remaining' => $programModel->getRemainingCapacity($programId),
            ]);
        }

        // ── Ownership: email must match session ───────────────────────
        $sessionEmail = $this->session->get('email');
        if (strtolower(trim($formData['email'])) !== strtolower($sessionEmail)) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Emel tidak sepadan dengan akaun semasa.',
            ]);
        }

        // ── Save registration ─────────────────────────────────────────
        $awamModel      = new DaftarAwamModel();
        $registrationId = $awamModel->insert([
            'program_id'   => $programId,
            'program_name' => $program['program_name'],
            'nama'         => $formData['namaPenuh'],
            'ic'           => preg_replace('/\D/', '', $formData['noIC']),
            'tel'          => $formData['telAwam'],
            'email'        => $sessionEmail, // always use session email
            'bil_ahli'     => $bilAhli,
            'status_hadir' => 'Belum Hadir',
        ]);

        if (!$registrationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data.',
            ]);
        }

        // ── Save family members ───────────────────────────────────────
        if ($bilAhli > 0) {
            $familyModel = new DaftarFamilyModel();
            for ($i = 0; $i < $bilAhli; $i++) {
                $familyModel->insert([
                    'registration_id' => $registrationId,
                    'nama_ahli'       => trim($formData["namaAhli_{$i}"]),
                    'ic_ahli'         => preg_replace('/\D/', '', $formData["icAhli_{$i}"]),
                ]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    // ------------------------------------------------------------------
    // My registrations — scoped to the logged-in user's email ONLY
    // ------------------------------------------------------------------

    public function myRegistrations()
    {
        // ── Auth guard ────────────────────────────────────────────────
        if ($err = $this->requirePublicAuth()) return $err;

        // Always pull the email from the session — never trust the request
        $email        = $this->session->get('email');
        $awamModel    = new DaftarAwamModel();
        $familyModel  = new DaftarFamilyModel();
        $programModel = new ProgramModel();

        $registrations = $awamModel->where('email', $email)->orderBy('created_at', 'DESC')->findAll();

        foreach ($registrations as &$reg) {
            $program = $programModel->find($reg['program_id']);
            $reg['start_date']  = $program['start_date']  ?? null;
            $reg['end_date']    = $program['end_date']    ?? null;
            $reg['prog_status'] = $program['status']      ?? null;
            $reg['pic_nama']    = $program['pic_nama']    ?? '-';
            $reg['pic_tel']     = $program['pic_tel']     ?? '-';
            $reg['location']    = $program['location']    ?? '-';
            $reg['organizer']   = $program['organizer']   ?? '-';
            $reg['ahli']        = $familyModel->where('registration_id', $reg['id'])->findAll();
        }

        return $this->response->setJSON(['success' => true, 'data' => $registrations]);
    }

    // ------------------------------------------------------------------
    // Program lists for registration form
    // ------------------------------------------------------------------

    public function getProgramList()
    {
        $programModel = new ProgramModel();
        $programs = $programModel
            ->where('status', 'AKTIF')
            ->where('parent_id IS NULL', null, false)
            ->findAll();

        $list = [];
        foreach ($programs as $prog) {
            $limit     = (int) ($prog['registration_limit'] ?? 0);
            $used      = $programModel->getUsedCapacity((int) $prog['id']);
            $remaining = $limit > 0 ? max(0, $limit - $used) : null;

            $list[] = [
                'id'                 => $prog['id'],
                'nama'               => $prog['program_name'],
                'description'        => $prog['description']  ?? '',
                'start_date'         => $prog['start_date'],
                'end_date'           => $prog['end_date'],
                'event_time'         => $prog['event_time']   ?? '',
                'location'           => $prog['location']     ?? '',
                'organizer'          => $prog['organizer']    ?? '',
                'pic_nama'           => $prog['pic_nama']     ?? '-',
                'pic_tel'            => $prog['pic_tel']      ?? '-',
                'poster_image'       => $prog['poster_image'] ?? '',
                'registration_limit' => $limit,
                'used_capacity'      => $used,
                'remaining_capacity' => $remaining,
                'is_full'            => $limit > 0 && $remaining <= 0,
            ];
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON($list);
    }

    public function getSubPrograms($parentId = null)
    {
        if (!$parentId || !is_numeric($parentId)) {
            return $this->response->setJSON([]);
        }

        $programModel = new ProgramModel();
        $subs = $programModel->where('parent_id', (int) $parentId)->where('status', 'AKTIF')->findAll();

        $list = [];
        foreach ($subs as $prog) {
            $limit     = (int) ($prog['registration_limit'] ?? 0);
            $used      = $programModel->getUsedCapacity((int) $prog['id']);
            $remaining = $limit > 0 ? max(0, $limit - $used) : null;

            $list[] = [
                'id'                 => $prog['id'],
                'nama'               => $prog['program_name'],
                'description'        => $prog['description']  ?? '',
                'start_date'         => $prog['start_date'],
                'end_date'           => $prog['end_date'],
                'event_time'         => $prog['event_time']   ?? '',
                'location'           => $prog['location']     ?? '',
                'organizer'          => $prog['organizer']    ?? '',
                'pic_nama'           => $prog['pic_nama']     ?? '-',
                'pic_tel'            => $prog['pic_tel']      ?? '-',
                'poster_image'       => $prog['poster_image'] ?? '',
                'registration_limit' => $limit,
                'used_capacity'      => $used,
                'remaining_capacity' => $remaining,
                'is_full'            => $limit > 0 && $remaining <= 0,
            ];
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON($list);
    }

    public function getProgramDetails($programId = null)
    {
        if (!$programId || !is_numeric($programId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Program ID tidak sah.',
            ]);
        }

        $programModel = new ProgramModel();
        $program      = $programModel->find((int) $programId);

        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Program tidak ditemui.',
            ]);
        }

        $limit     = (int) ($program['registration_limit'] ?? 0);
        $used      = $programModel->getUsedCapacity((int) $program['id']);
        $remaining = $limit > 0 ? max(0, $limit - $used) : null;

        return $this->response->setJSON([
            'success' => true,
            'program' => [
                'id'                 => $program['id'],
                'name'               => $program['program_name'],
                'description'        => $program['description']  ?? '',
                'start_date'         => $program['start_date'],
                'end_date'           => $program['end_date'],
                'event_time'         => $program['event_time']   ?? '',
                'location'           => $program['location']     ?? '',
                'organizer'          => $program['organizer']    ?? '',
                'pic_nama'           => $program['pic_nama']     ?? '-',
                'pic_tel'            => $program['pic_tel']      ?? '-',
                'poster_image'       => $program['poster_image'] ?? '',
                'status'             => $program['status'],
                'registration_limit' => $limit,
                'used_capacity'      => $used,
                'remaining_capacity' => $remaining,
                'is_full'            => $limit > 0 && $remaining <= 0,
            ],
        ]);
    }

    // ------------------------------------------------------------------
    // Events list
    // ------------------------------------------------------------------

    public function getEvents()
    {
        try {
            $programModel = new ProgramModel();
            $programModel->refreshProgramStatuses();

            $allPrograms = $programModel->orderBy('start_date', 'DESC')->findAll();
            $today    = date('Y-m-d');
            $upcoming = [];
            $ongoing  = [];
            $past     = [];
            $featured = [];

            foreach ($allPrograms as $prog) {
                if (!$prog['start_date'] || !$prog['end_date']) continue;
                if (!$programModel->isVisibleOnPublicViews($prog)) continue;

                $limit     = (int) ($prog['registration_limit'] ?? 0);
                $used      = $programModel->getUsedCapacity((int) $prog['id']);
                $remaining = $limit > 0 ? max(0, $limit - $used) : null;

                $prog['registration_limit'] = $limit;
                $prog['used_capacity']      = $used;
                $prog['remaining_capacity'] = $remaining;
                $prog['is_full']            = $limit > 0 && $remaining <= 0;

                if ($prog['end_date'] < $today) {
                    $prog['event_status'] = 'past';
                    $past[] = $prog;
                } elseif ($prog['start_date'] <= $today && $prog['end_date'] >= $today) {
                    $prog['event_status'] = 'ongoing';
                    $ongoing[] = $prog;
                    if ($prog['is_featured']) $featured[] = $prog;
                } else {
                    $prog['event_status'] = 'upcoming';
                    $upcoming[] = $prog;
                    if ($prog['is_featured']) $featured[] = $prog;
                }
            }

            return $this->response->setJSON([
                'success'  => true,
                'upcoming' => $upcoming,
                'ongoing'  => $ongoing,
                'past'     => $past,
                'featured' => $featured,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PublicPortal::getEvents] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuatkan acara.',
            ]);
        }
    }
}