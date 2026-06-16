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
        $formData = $this->request->getPost();
        $programModel = new ProgramModel();

        $program = $programModel->where('program_code', $formData['programId'])->first();
        $programName = $program ? $program['program_name'] : $formData['programId'];

        if ($formData['subKategori'] === 'Sekolah Luar') {
            $model = new DaftarLuarModel();
            $data = [
                'timestamp'    => date('Y-m-d H:i:s'),
                'program_name' => $programName,
                'nama_sekolah' => $formData['namaPenuh'],
                'kod_sekolah'  => $formData['noIC'],
                'tel'          => $formData['telAwam'],
                'email'        => $formData['email'],
                'kategori'     => 'Sekolah Luar'
            ];
        } else {
            $model = new DaftarAwamModel();
            $data = [
                'timestamp'    => date('Y-m-d H:i:s'),
                'program_name' => $programName,
                'nama'         => $formData['namaPenuh'],
                'ic'           => $formData['noIC'],
                'tel'          => $formData['telAwam'],
                'email'        => $formData['email'],
                'kategori'     => 'Orang Awam'
            ];
        }

        if ($model->insert($data)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
    }

    public function getProgramList()
    {
        $programModel = new ProgramModel();
        $programs = $programModel->getActivePrograms();

        $list = [];
        foreach ($programs as $prog) {
            $list[] = ['id' => $prog['program_code'], 'nama' => $prog['program_name']];
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON($list);
    }
}
