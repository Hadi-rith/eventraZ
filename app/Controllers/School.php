<?php

namespace App\Controllers;

use App\Models\DaftarSekolahModel;
use App\Models\DaftarMuridModel;
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

    public function simpanPendaftaran()
    {
        $formData     = $this->request->getPost();
        $programModel = new ProgramModel();
        $required     = ['mainProgramId', 'namaSekolah', 'kodSekolah', 'namaGuru', 'icGuru', 'telGuru', 'email', 'bilMurid'];

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

        $bil = (int) $formData['bilMurid'];
        if ($bil < 1 || $bil > 10) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Bilangan murid mesti antara 1 hingga 10.',
            ]);
        }

        for ($i = 0; $i < $bil; $i++) {
            if (trim((string) ($formData["namaMurid_{$i}"] ?? '')) === '' || trim((string) ($formData["icMurid_{$i}"] ?? '')) === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Sila lengkapkan semua maklumat murid.',
                ]);
            }
        }

        // Use the selected sub program when present; otherwise register under the main program.
        $programId   = !empty($formData['subProgramId']) ? $formData['subProgramId'] : $formData['mainProgramId'];
        $program     = $programModel->find($programId);
        if (!$program) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Program yang dipilih tidak sah.',
            ]);
        }
        $programName = $program['program_name'];

        $sekolahModel = new DaftarSekolahModel();

        $data = [
            'timestamp'    => date('Y-m-d H:i:s'),
            'program_name' => $programName,
            'nama_sekolah' => $formData['namaSekolah'],
            'kod_sekolah'  => $formData['kodSekolah'],
            'nama_guru'    => $formData['namaGuru'],
            'ic_guru'      => $formData['icGuru'],
            'tel_guru'     => $formData['telGuru'],
            'email'        => $formData['email'],
            'bil_murid'    => $bil,
            'status'       => 'Baru',
        ];

        $registrationId = $sekolahModel->insert($data);

        if ($registrationId) {
            $muridModel = new DaftarMuridModel();

            for ($i = 0; $i < $bil; $i++) {
                $muridModel->insert([
                    'registration_id' => $registrationId,
                    'nama_murid'      => $formData["namaMurid_{$i}"],
                    'ic_murid'        => $formData["icMurid_{$i}"],
                ]);
            }

            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
    }

    // Registration forms should start with main programs; sub programs are loaded after selection.
    public function getProgramList()
    {
        $programModel = new ProgramModel();
        $programs     = $programModel
            ->where('status', 'AKTIF')
            ->where('parent_id IS NULL', null, false)
            ->findAll();

        $list = [];
        foreach ($programs as $prog) {
            $list[] = ['id' => $prog['id'], 'nama' => $prog['program_name']];
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON($list);
    }

    // Keep the child list scoped to the chosen main program.
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
            $list[] = ['id' => $prog['id'], 'nama' => $prog['program_name']];
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON($list);
    }
}