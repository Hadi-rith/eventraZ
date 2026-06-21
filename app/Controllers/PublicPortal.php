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
        $required     = ['mainProgramId', 'subKategori', 'namaPenuh', 'noIC', 'telAwam', 'email'];

        foreach ($required as $field) {
            if (trim((string) ($formData[$field] ?? '')) === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Sila lengkapkan semua medan wajib.',
                ]);
            }
        }

        if (!in_array($formData['subKategori'], ['Sekolah Luar', 'Orang Awam'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Kategori pendaftaran tidak sah.',
            ]);
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
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