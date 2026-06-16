<?php

namespace App\Controllers;

use App\Models\DaftarSekolahModel;
use App\Models\DaftarLuarModel;
use App\Models\DaftarAwamModel;
use App\Models\DaftarMuridModel;
use App\Models\ProgramModel;
use App\Models\PublicAccountModel;
use App\Models\SchoolAccountModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Admin extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (!$this->session->get('logged_in') || $this->session->get('role') !== 'admin') {
            redirect()->to('/')->send();
            exit();
        }
    }

    public function dashboard()
    {
        return view('admin_dashboard');
    }

    public function createProgram()
    {
        $programCode = strtoupper(trim((string) $this->request->getPost('program_code')));
        $programName = trim((string) $this->request->getPost('program_name'));
        $startDate   = trim((string) $this->request->getPost('start_date'));
        $endDate     = trim((string) $this->request->getPost('end_date'));

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

        $programModel = new ProgramModel();
        $status       = $programModel->calculateStatus($startDate, $endDate);

        if ($programModel->where('program_code', $programCode)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Kod program ini telah wujud.',
            ]);
        }

        $saved = $programModel->insert([
            'program_code' => $programCode,
            'program_name' => $programName,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'status'       => $status,
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
            'program' => [
                'id'     => $programCode,
                'nama'   => $programName,
                'mula'   => $startDate,
                'tamat'  => $endDate,
                'status' => $status,
            ],
        ]);
    }

    public function updateProgram(string $currentCode)
    {
        $currentCode = strtoupper(trim($currentCode));
        $programCode = strtoupper(trim((string) $this->request->getPost('program_code')));
        $programName = trim((string) $this->request->getPost('program_name'));
        $startDate   = trim((string) $this->request->getPost('start_date'));
        $endDate     = trim((string) $this->request->getPost('end_date'));

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

        $programModel = new ProgramModel();
        $program      = $programModel->where('program_code', $currentCode)->first();

        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Program tidak ditemui.',
            ]);
        }

        $duplicate = $programModel->where('program_code', $programCode)->first();
        if ($duplicate && (int) $duplicate['id'] !== (int) $program['id']) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Kod program ini telah wujud.',
            ]);
        }

        $status  = $programModel->calculateStatus($startDate, $endDate);
        $oldProgramName = $program['program_name'];
        $updated = $programModel->update($program['id'], [
            'program_code' => $programCode,
            'program_name' => $programName,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'status'       => $status,
        ]);

        if (!$updated) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengemaskini program.',
            ]);
        }

        if ($oldProgramName !== $programName) {
            $this->syncRegistrationProgramName($oldProgramName, $programName);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Program berjaya dikemaskini.',
        ]);
    }

    public function deleteProgram(string $programCode)
    {
        $programCode  = strtoupper(trim($programCode));
        $programModel = new ProgramModel();
        $program      = $programModel->where('program_code', $programCode)->first();

        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Program tidak ditemui.',
            ]);
        }

        if ($this->programHasRegistrations($program['program_name'])) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Program ini sudah mempunyai rekod pendaftaran dan tidak boleh dipadam.',
            ]);
        }

        if (!$programModel->delete($program['id'])) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memadam program.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Program berjaya dipadam.',
        ]);
    }

    public function getAdminData()
    {
        $sekolahModel = new DaftarSekolahModel();
        $luarModel    = new DaftarLuarModel();
        $awamModel    = new DaftarAwamModel();

        $sekolahTRG  = $sekolahModel->findAll();
        $sekolahLuar = $luarModel->findAll();
        $orangAwam   = $awamModel->findAll();

        return $this->response->setJSON([
            'success'     => true,
            'sekolahTRG'  => $this->formatData($sekolahTRG,  'TRG'),
            'sekolahLuar' => $this->formatData($sekolahLuar, 'LUAR'),
            'orangAwam'   => $this->formatData($orangAwam,   'AWAM'),
        ]);
    }

    public function getProgramList()
    {
        $programModel = new ProgramModel();
        $programModel->refreshProgramStatuses();
        $programs = $programModel
            ->orderBy('start_date', 'ASC')
            ->orderBy('program_name', 'ASC')
            ->findAll();

        $list = [];
        foreach ($programs as $prog) {
            $list[] = [
                'id'         => $prog['program_code'],
                'kod'        => $prog['program_code'],
                'nama'       => $prog['program_name'],
                'mula'       => $prog['start_date'],
                'tamat'      => $prog['end_date'],
                'status'     => $prog['status'],
                'start_date' => $prog['start_date'],
                'end_date'   => $prog['end_date'],
            ];
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON($list);
    }

    public function getAccounts()
    {
        $schoolModel = new SchoolAccountModel();
        $publicModel = new PublicAccountModel();

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON([
                'success' => true,
                'school'  => $schoolModel->orderBy('school_name', 'ASC')->findAll(),
                'public'  => $publicModel->orderBy('name', 'ASC')->findAll(),
            ]);
    }

    public function updateAccount(string $type, int $id)
    {
        $type = strtolower($type);

        if ($type === 'school') {
            return $this->updateSchoolAccount($id);
        }

        if ($type === 'public') {
            return $this->updatePublicAccount($id);
        }

        return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Jenis akaun tidak sah.']);
    }

    public function deleteAccount(string $type, int $id)
    {
        $model = $this->getAccountModel($type);

        if (!$model) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Jenis akaun tidak sah.']);
        }

        if (!$model->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Akaun tidak ditemui.']);
        }

        if (!$model->delete($id)) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal memadam akaun.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Akaun berjaya dipadam.']);
    }

    public function getRegistrationStudents(int $registrationId)
    {
        $sekolahModel = new DaftarSekolahModel();
        $registration = $sekolahModel->find($registrationId);

        if (!$registration) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Rekod pendaftaran sekolah tidak ditemui.',
            ]);
        }

        $muridModel = new DaftarMuridModel();
        $students   = $muridModel
            ->where('registration_id', $registrationId)
            ->orderBy('id', 'ASC')
            ->findAll();

        $list = [];
        foreach ($students as $student) {
            $list[] = [
                'nama' => $student['nama_murid'],
                'ic'   => $student['ic_murid'],
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'school'  => $registration['nama_sekolah'],
            'program' => $registration['program_name'],
            'students' => $list,
        ]);
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTime::createFromFormat('Y-m-d', $date);

        return $parsed && $parsed->format('Y-m-d') === $date;
    }

    private function updateSchoolAccount(int $id)
    {
        $model = new SchoolAccountModel();
        $account = $model->find($id);

        if (!$account) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Akaun sekolah tidak ditemui.']);
        }

        $schoolCode = strtoupper(trim((string) $this->request->getPost('school_code')));
        $schoolName = trim((string) $this->request->getPost('school_name'));
        $email      = strtolower(trim((string) $this->request->getPost('email')));
        $password   = trim((string) $this->request->getPost('password'));

        if ($schoolCode === '' || $schoolName === '') {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Kod dan nama sekolah diperlukan.']);
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Format emel tidak sah.']);
        }

        $duplicate = $model->where('school_code', $schoolCode)->orWhere('email', $email)->first();
        if ($duplicate && (int) $duplicate['id'] !== $id) {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'message' => 'Kod sekolah atau emel telah digunakan.']);
        }

        $data = [
            'school_code' => $schoolCode,
            'school_name' => $schoolName,
            'email'       => $email,
        ];

        if ($password !== '') {
            $data['password'] = $password;
        }

        if (!$model->update($id, $data)) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal mengemaskini akaun sekolah.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Akaun sekolah berjaya dikemaskini.']);
    }

    private function updatePublicAccount(int $id)
    {
        $model = new PublicAccountModel();
        $account = $model->find($id);

        if (!$account) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Akaun awam tidak ditemui.']);
        }

        $name     = trim((string) $this->request->getPost('name'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($name === '' || $email === '') {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Nama dan emel diperlukan.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Format emel tidak sah.']);
        }

        $duplicate = $model->where('email', $email)->first();
        if ($duplicate && (int) $duplicate['id'] !== $id) {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'message' => 'Emel ini telah digunakan.']);
        }

        $data = ['name' => $name, 'email' => $email];

        if ($password !== '') {
            $data['password'] = $password;
        }

        if (!$model->update($id, $data)) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal mengemaskini akaun awam.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Akaun awam berjaya dikemaskini.']);
    }

    private function getAccountModel(string $type): ?object
    {
        if (strtolower($type) === 'school') {
            return new SchoolAccountModel();
        }

        if (strtolower($type) === 'public') {
            return new PublicAccountModel();
        }

        return null;
    }

    private function programHasRegistrations(string $programName): bool
    {
        $sekolahModel = new DaftarSekolahModel();
        $luarModel    = new DaftarLuarModel();
        $awamModel    = new DaftarAwamModel();

        return $sekolahModel->where('program_name', $programName)->countAllResults() > 0
            || $luarModel->where('program_name', $programName)->countAllResults() > 0
            || $awamModel->where('program_name', $programName)->countAllResults() > 0;
    }

    private function syncRegistrationProgramName(string $oldName, string $newName): void
    {
        $sekolahModel = new DaftarSekolahModel();
        $luarModel    = new DaftarLuarModel();
        $awamModel    = new DaftarAwamModel();

        $sekolahModel->where('program_name', $oldName)->set(['program_name' => $newName])->update();
        $luarModel->where('program_name', $oldName)->set(['program_name' => $newName])->update();
        $awamModel->where('program_name', $oldName)->set(['program_name' => $newName])->update();
    }

    private function formatData($data, $jenis)
    {
        $result = [];
        foreach ($data as $row) {
            $obj              = [];
            $obj['timestamp'] = date('d/m/Y H:i', strtotime($row['timestamp']));
            $obj['program']   = $row['program_name'];

            if ($jenis === 'TRG') {
                $obj['id']          = $row['id'];
                $obj['namaSekolah'] = $row['nama_sekolah'];
                $obj['kodSekolah']  = $row['kod_sekolah'];
                $obj['namaGuru']    = $row['nama_guru'];
                $obj['telGuru']     = $row['tel_guru'];
                $obj['bilMurid']    = $row['bil_murid'];
            } elseif ($jenis === 'LUAR') {
                $obj['namaSekolah'] = $row['nama_sekolah'];
                $obj['kodSekolah']  = $row['kod_sekolah'];
                $obj['tel']         = $row['tel'];
                $obj['email']       = $row['email'];
            } elseif ($jenis === 'AWAM') {
                $obj['nama']  = $row['nama'];
                $obj['ic']    = $row['ic'];
                $obj['tel']   = $row['tel'];
                $obj['email'] = $row['email'];
            }

            $result[] = $obj;
        }
        return $result;
    }
}
