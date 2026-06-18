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

        // Use sub_program_id if selected, otherwise fall back to main program
        $programId   = !empty($formData['subProgramId']) ? $formData['subProgramId'] : $formData['mainProgramId'];
        $program     = $programModel->find($programId);
        $programName = $program ? $program['program_name'] : $programId;

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
            'bil_murid'    => $formData['bilMurid'],
            'status'       => 'Baru',
        ];

        $registrationId = $sekolahModel->insert($data);

        if ($registrationId) {
            $muridModel = new DaftarMuridModel();
            $bil        = (int) $formData['bilMurid'];

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

    // Returns only MAIN programs (parent_id IS NULL)
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

    // Returns SUB programs for a given main program id
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