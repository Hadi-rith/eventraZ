<?php

namespace App\Controllers;

use App\Models\DaftarLuarModel;
use App\Models\DaftarAwamModel;
use App\Models\ProgramModel;

class PublicPortal extends BaseController
{
    public function index()
    {
        return view('public_portal');
    }

    public function simpanPendaftaran()
    {
        $formData     = $this->request->getPost();
        $programModel = new ProgramModel();

        // Use sub_program_id if selected, otherwise fall back to main program
        $programId   = !empty($formData['subProgramId']) ? $formData['subProgramId'] : $formData['mainProgramId'];
        $program     = $programModel->find($programId);
        $programName = $program ? $program['program_name'] : $programId;

        if ($formData['subKategori'] === 'Sekolah Luar') {
            $model = new DaftarLuarModel();
            $data  = [
                'timestamp'    => date('Y-m-d H:i:s'),
                'program_name' => $programName,
                'nama_sekolah' => $formData['namaPenuh'],
                'kod_sekolah'  => $formData['noIC'],
                'tel'          => $formData['telAwam'],
                'email'        => $formData['email'],
                'kategori'     => 'Sekolah Luar',
            ];
        } else {
            $model = new DaftarAwamModel();
            $data  = [
                'timestamp'    => date('Y-m-d H:i:s'),
                'program_name' => $programName,
                'nama'         => $formData['namaPenuh'],
                'ic'           => $formData['noIC'],
                'tel'          => $formData['telAwam'],
                'email'        => $formData['email'],
                'kategori'     => 'Orang Awam',
            ];
        }

        if ($model->insert($data)) {
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
        $subs         = $programModel
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