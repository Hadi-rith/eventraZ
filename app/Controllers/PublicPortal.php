<?php

namespace App\Controllers;

use App\Models\DaftarAwamModel;
use App\Models\DaftarFamilyModel;
use App\Models\ProgramModel;
use App\Models\EventModel;

class PublicPortal extends BaseController
{
    public function index()
    {
        return view('public_portal');
    }

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
        $required     = ['mainProgramId', 'namaPenuh', 'noIC', 'telAwam', 'email', 'bilAhli'];

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

        $bil = (int) $formData['bilAhli'];
        if ($bil < 0 || $bil > 10) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Bilangan ahli keluarga mesti antara 1 hingga 10.',
            ]);
        }

        for ($i = 0; $i < $bil; $i++) {
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

        $programId   = !empty($formData['subProgramId']) ? $formData['subProgramId'] : $formData['mainProgramId'];
        $program     = $programModel->find($programId);
        if (!$program) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Program yang dipilih tidak sah.',
            ]);
        }
        $programName = $program['program_name'];

        $awamModel = new DaftarAwamModel();
        $data = [
            'timestamp'    => date('Y-m-d H:i:s'),
            'program_name' => $programName,
            'nama'         => $formData['namaPenuh'],
            'ic'           => $formData['noIC'],
            'tel'          => $formData['telAwam'],
            'email'        => $formData['email'],
            'kategori'     => 'Orang Awam',
            'bil_ahli'     => $bil,
        ];

        $registrationId = $awamModel->insert($data);

        if ($registrationId) {
            $familyModel = new DaftarFamilyModel();
            for ($i = 0; $i < $bil; $i++) {
                $familyModel->insert([
                    'registration_id' => $registrationId,
                    'nama_ahli'       => $formData["namaAhli_{$i}"],
                    'ic_ahli'         => $formData["icAhli_{$i}"],
                ]);
            }

            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
    }

    public function myRegistrations()
    {
        if (!$this->session->get('logged_in') || $this->session->get('role') !== 'public') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Tidak dibenarkan.']);
        }

        $email        = $this->session->get('email');
        $awamModel    = new DaftarAwamModel();
        $familyModel  = new DaftarFamilyModel();
        $programModel = new ProgramModel();

        $registrations = $awamModel->where('email', $email)->orderBy('timestamp', 'DESC')->findAll();

        foreach ($registrations as &$reg) {
            $program = $programModel->where('program_name', $reg['program_name'])->first();
            $reg['start_date']  = $program['start_date'] ?? null;
            $reg['end_date']    = $program['end_date']   ?? null;
            $reg['prog_status'] = $program['status']     ?? null;
            $reg['pic_nama']    = $program['pic_nama']   ?? '-';
            $reg['pic_tel']     = $program['pic_tel']    ?? '-';

            // Pull from daftar_family — completely separate from daftar_murid
            $reg['ahli'] = $familyModel->where('registration_id', $reg['id'])->findAll();
        }

        return $this->response->setJSON(['success' => true, 'data' => $registrations]);
    }

    public function getProgramList()
    {
        $programModel = new ProgramModel();
        $programs = $programModel
            ->where('status', 'AKTIF')
            ->where('parent_id IS NULL', null, false)
            ->findAll();

        $list = [];
        foreach ($programs as $prog) {
            $list[] = [
                'id' => $prog['id'], 
                'nama' => $prog['program_name'],
                'pic_nama' => $prog['pic_nama'] ?? '-',
                'pic_tel' => $prog['pic_tel'] ?? '-'
            ];
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON($list);
    }

    public function getSubPrograms($parentId = null)
    {
        if (!$parentId) {
            return $this->response->setJSON([]);
        }

        $programModel = new ProgramModel();
        $subs = $programModel
            ->where('parent_id', $parentId)
            ->where('status', 'AKTIF')
            ->findAll();

        $list = [];
        foreach ($subs as $prog) {
            $list[] = [
                'id' => $prog['id'], 
                'nama' => $prog['program_name'],
                'pic_nama' => $prog['pic_nama'] ?? '-',
                'pic_tel' => $prog['pic_tel'] ?? '-'
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
                'message' => 'Program ID diperlukan'
            ]);
        }

        $programModel = new ProgramModel();
        $program = $programModel->find($programId);
        
        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false, 
                'message' => 'Program tidak ditemui'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'program' => [
                'id' => $program['id'],
                'name' => $program['program_name'],
                'pic_nama' => $program['pic_nama'] ?? '-',
                'pic_tel' => $program['pic_tel'] ?? '-',
                'start_date' => $program['start_date'],
                'end_date' => $program['end_date'],
                'status' => $program['status']
            ]
        ]);
    }

    /**
 * Display the public events page
 */
public function events()
{
    return view('public_events');
}

/**
 * Get all programs as events for public display
 */
public function getEvents()
{
    try {
        $programModel = new \App\Models\ProgramModel();
        $programModel->refreshProgramStatuses();
        
        $allPrograms = $programModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $upcoming = [];
        $ongoing = [];
        $past = [];
        $featured = [];
        
        $today = date('Y-m-d');
        
        foreach ($allPrograms as $prog) {
            // Skip programs without dates
            if (!$prog['start_date'] || !$prog['end_date']) continue;
            
            // Determine event status
            if ($prog['end_date'] < $today) {
                $prog['event_status'] = 'past';
                $past[] = $prog;
            } elseif ($prog['start_date'] <= $today && $prog['end_date'] >= $today) {
                $prog['event_status'] = 'ongoing';
                $ongoing[] = $prog;
                if ($prog['is_featured']) {
                    $featured[] = $prog;
                }
            } else {
                $prog['event_status'] = 'upcoming';
                $upcoming[] = $prog;
                if ($prog['is_featured']) {
                    $featured[] = $prog;
                }
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'upcoming' => $upcoming,
            'ongoing' => $ongoing,
            'past' => $past,
            'featured' => $featured
        ]);
    } catch (\Throwable $e) {
        log_message('error', '[PublicPortal::getEvents] ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal memuatkan acara.'
        ]);
    }
}
}