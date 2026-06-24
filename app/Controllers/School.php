<?php

namespace App\Controllers;

use App\Models\DaftarSekolahModel;
use App\Models\DaftarMuridModel;
use App\Models\DaftarGuruModel;
use App\Models\ProgramModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class School extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (!$this->session->get('logged_in') || $this->session->get('role') !== 'school') {
            redirect()->to('/')->send();
            exit();
        }
    }

    public function portal()
    {
        return view('school_portal');
    }

    public function events()
    {
        return view('school_events');
    }

    // ------------------------------------------------------------------
    // Registration submission
    // ------------------------------------------------------------------

    public function simpanPendaftaran()
    {
        $formData     = $this->request->getPost();
        $programModel = new ProgramModel();

        // ---- Required base fields ----
        $required = ['programId', 'namaSekolah', 'kodSekolah', 'emailSekolah', 'telSekolah', 'bilMurid'];
        foreach ($required as $field) {
            if (trim((string) ($formData[$field] ?? '')) === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Sila lengkapkan semua medan wajib.',
                ]);
            }
        }

        if (!filter_var($formData['emailSekolah'], FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        // ---- Validate Guru Pengiring (at least 1 required) ----
        $guruList = [];
        $guruIdx  = 0;
        while (isset($formData["namaGuru_{$guruIdx}"])) {
            $namaGuru = trim((string) ($formData["namaGuru_{$guruIdx}"] ?? ''));
            $icGuru   = trim((string) ($formData["icGuru_{$guruIdx}"]   ?? ''));

            if ($namaGuru === '' || $icGuru === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => "Sila lengkapkan maklumat Guru Pengiring ke-" . ($guruIdx + 1) . ".",
                ]);
            }

            $guruList[] = ['nama_guru' => $namaGuru, 'ic_guru' => $icGuru];
            $guruIdx++;
        }

        if (empty($guruList)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Sekurang-kurangnya satu Guru Pengiring diperlukan.',
            ]);
        }

        // ---- Student count validation (N guru = max N×10 students) ----
        $bilMurid  = (int) $formData['bilMurid'];
        $maxMurid  = count($guruList) * 10;

        if ($bilMurid < 1) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Bilangan murid mesti sekurang-kurangnya 1.',
            ]);
        }

        if ($bilMurid > $maxMurid) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => "Bilangan murid melebihi had. {count($guruList)} Guru Pengiring membenarkan maksimum {$maxMurid} murid.",
            ]);
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

        // ---- Capacity check ----
        if (!$programModel->hasCapacity($programId, $bilMurid)) {
            $remaining = $programModel->getRemainingCapacity($programId);
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => "Pendaftaran telah ditutup. Kapasiti program telah penuh.",
                'remaining' => $remaining,
            ]);
        }

        // ---- Save school registration ----
        $sekolahModel = new DaftarSekolahModel();
        $registrationId = $sekolahModel->insert([
            'program_id'    => $programId,
            'program_name'  => $program['program_name'],
            'nama_sekolah'  => $formData['namaSekolah'],
            'kod_sekolah'   => $formData['kodSekolah'],
            'email_sekolah' => $formData['emailSekolah'],
            'tel_sekolah'   => $formData['telSekolah'],
            'bil_murid'     => $bilMurid,
            'status'        => 'Baru',
        ]);

        if (!$registrationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data pendaftaran.',
            ]);
        }

        // ---- Save Guru Pengiring ----
        $guruModel = new DaftarGuruModel();
        foreach ($guruList as $guru) {
            $guruModel->insert(array_merge($guru, ['registration_id' => $registrationId]));
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pendaftaran berjaya disimpan.',
        ]);
    }

    // ------------------------------------------------------------------
    // My registrations
    // ------------------------------------------------------------------

    public function myRegistrations()
    {
        $schoolCode   = $this->session->get('school_code');
        $sekolahModel = new DaftarSekolahModel();
        $guruModel    = new DaftarGuruModel();
        $programModel = new ProgramModel();

        $registrations = $sekolahModel->where('kod_sekolah', $schoolCode)
                                      ->orderBy('created_at', 'DESC')
                                      ->findAll();

        foreach ($registrations as &$reg) {
            // Attach guru list
            $reg['guru'] = $guruModel->where('registration_id', $reg['id'])->findAll();

            // Attach program info
            $program = $programModel->find($reg['program_id']);
            $reg['start_date']  = $program['start_date']  ?? null;
            $reg['end_date']    = $program['end_date']    ?? null;
            $reg['prog_status'] = $program['status']      ?? null;
            $reg['pic_nama']    = $program['pic_nama']    ?? '-';
            $reg['pic_tel']     = $program['pic_tel']     ?? '-';
            $reg['location']    = $program['location']    ?? '-';
            $reg['organizer']   = $program['organizer']   ?? '-';
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
    // Events list for the events page
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
            log_message('error', '[School::getEvents] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuatkan acara.',
            ]);
        }
    }
}