<?php

namespace App\Controllers;

use App\Models\DaftarAwamModel;
use App\Models\DaftarFamilyModel;
use App\Models\ProgramModel;

class PublicPortal extends BaseController
{
    public function index()
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
        if (!$this->session->get('logged_in') || $this->session->get('role') !== 'public') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sila log masuk untuk mendaftar.',
            ]);
        }

        $formData     = $this->request->getPost();
        $programModel = new ProgramModel();

        $required = ['programId', 'namaPenuh', 'noIC', 'telAwam', 'email', 'bilAhli'];
        foreach ($required as $field) {
            if (trim((string) ($formData[$field] ?? '')) === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Sila lengkapkan semua medan wajib.',
                ]);
            }
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $bilAhli = (int) $formData['bilAhli'];
        if ($bilAhli < 0 || $bilAhli > 20) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Bilangan ahli keluarga mesti antara 0 hingga 20.',
            ]);
        }

        // Validate family member fields
        for ($i = 0; $i < $bilAhli; $i++) {
            if (
                trim((string) ($formData["namaAhli_{$i}"] ?? '')) === '' ||
                trim((string) ($formData["icAhli_{$i}"]   ?? '')) === ''
            ) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Sila lengkapkan semua maklumat ahli keluarga.',
                ]);
            }
        }

        // ---- Validate program ----
        $programId = (int) $formData['programId'];
        $program   = $programModel->find($programId);

        if (!$program) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Program yang dipilih tidak sah.',
            ]);
        }

        // ---- Capacity check — registrant (1) + family members ----
        $slotsNeeded = 1 + $bilAhli;
        if (!$programModel->hasCapacity($programId, $slotsNeeded)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success'   => false,
                'message'   => 'Pendaftaran telah ditutup. Kapasiti program telah penuh.',
                'remaining' => $programModel->getRemainingCapacity($programId),
            ]);
        }

        // ---- Save registration ----
        $awamModel = new DaftarAwamModel();
        $registrationId = $awamModel->insert([
            'program_id'   => $programId,
            'program_name' => $program['program_name'],
            'nama'         => $formData['namaPenuh'],
            'ic'           => $formData['noIC'],
            'tel'          => $formData['telAwam'],
            'email'        => $formData['email'],
            'bil_ahli'     => $bilAhli,
            'status_hadir' => 'Belum Hadir',
        ]);

        if (!$registrationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data.',
            ]);
        }

        // ---- Save family members ----
        if ($bilAhli > 0) {
            $familyModel = new DaftarFamilyModel();
            for ($i = 0; $i < $bilAhli; $i++) {
                $familyModel->insert([
                    'registration_id' => $registrationId,
                    'nama_ahli'       => $formData["namaAhli_{$i}"],
                    'ic_ahli'         => $formData["icAhli_{$i}"],
                ]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    // ------------------------------------------------------------------
    // My registrations
    // ------------------------------------------------------------------

    public function myRegistrations()
    {
        if (!$this->session->get('logged_in') || $this->session->get('role') !== 'public') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Tidak dibenarkan.',
            ]);
        }

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
        if (!$parentId) return $this->response->setJSON([]);

        $programModel = new ProgramModel();
        $subs = $programModel->where('parent_id', $parentId)->where('status', 'AKTIF')->findAll();

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
        if (!$programId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Program ID diperlukan.',
            ]);
        }

        $programModel = new ProgramModel();
        $program      = $programModel->find($programId);

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
            $today       = date('Y-m-d');
            $upcoming    = [];
            $ongoing     = [];
            $past        = [];
            $featured    = [];

            foreach ($allPrograms as $prog) {
                if (!$prog['start_date'] || !$prog['end_date']) continue;

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