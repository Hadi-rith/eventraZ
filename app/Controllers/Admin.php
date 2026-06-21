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
            header('Location: ' . base_url('/'));
            exit();
        }
    }

    /**
     * Validates that a string is a real calendar date in Y-m-d format.
     * This method was being called from createProgram()/createSubProgram()/
     * updateProgram() but never actually existed anywhere in the codebase —
     * that's the "Call to undefined method ... isValidDate()" error.
     */
    private function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $parsed = \DateTime::createFromFormat($format, $date);

        // createFromFormat() will silently "correct" invalid dates like
        // 2026-02-30 into 2026-03-02, so re-format and compare exactly
        // to catch those instead of accepting them.
        return $parsed !== false && $parsed->format($format) === $date;
    }

    public function dashboard()
    {
        return view('admin_dashboard');
    }

    /**
     * Converts uncaught admin errors into JSON so frontend fetch calls do not
     * fail while parsing an HTML error page.
     */
    private function serverErrorResponse(\Throwable $e, string $context)
    {
        log_message('error', "[Admin::{$context}] " . $e->getMessage() . "\n" . $e->getTraceAsString());

        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Ralat pelayan dalaman. Sila semak log pelayan.',
        ]);
    }

    public function createProgram()
    {
        try {
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
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'createProgram');
        }
    }

    public function createSubProgram()
    {
        try {
            $parentCode  = strtoupper(trim((string) $this->request->getPost('parent_code')));
            $programCode = strtoupper(trim((string) $this->request->getPost('program_code')));
            $programName = trim((string) $this->request->getPost('program_name'));
            $startDate   = trim((string) $this->request->getPost('start_date'));
            $endDate     = trim((string) $this->request->getPost('end_date'));

            if ($parentCode === '' || $programCode === '' || $programName === '' || $startDate === '' || $endDate === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Program induk, kod program, nama program, tarikh mula dan tarikh tamat diperlukan.',
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

            $parent = $programModel->where('program_code', $parentCode)->first();
            if (!$parent) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program induk tidak ditemui.',
                ]);
            }

            if ($programCode === $parentCode) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Program tidak boleh menjadi induk kepada dirinya sendiri.',
                ]);
            }

            if ($programModel->where('program_code', $programCode)->first()) {
                return $this->response->setStatusCode(409)->setJSON([
                    'success' => false,
                    'message' => 'Kod program ini telah wujud.',
                ]);
            }

            $status = $programModel->calculateStatus($startDate, $endDate);

            $saved = $programModel->insert([
                'program_code' => $programCode,
                'parent_id'    => (int) $parent['id'],
                'program_name' => $programName,
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'status'       => $status,
            ]);

            if (!$saved) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan sub program ke pangkalan data.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sub program berjaya didaftarkan.',
                'program' => [
                    'id'        => $programCode,
                    'nama'      => $programName,
                    'mula'      => $startDate,
                    'tamat'     => $endDate,
                    'status'    => $status,
                    'parent_id' => (int) $parent['id'],
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'createSubProgram');
        }
    }

    public function updateProgram(string $currentCode)
    {
        try {
            $currentCode = strtoupper(trim($currentCode));
            $programCode = strtoupper(trim((string) $this->request->getPost('program_code')));
            $programName = trim((string) $this->request->getPost('program_name'));
            $startDate   = trim((string) $this->request->getPost('start_date'));
            $endDate     = trim((string) $this->request->getPost('end_date'));

            // Only touch parent_id if the client explicitly sent this field.
            // getPost() returns null when the key is absent from the request body,
            // which lets older/unchanged frontend calls (that don't send parent_code)
            // keep editing a program's name/dates without accidentally wiping its parent.
            $parentCodeRaw = $this->request->getPost('parent_code');
            $parentCodeProvided = ($parentCodeRaw !== null);
            $parentCode = $parentCodeProvided ? strtoupper(trim((string) $parentCodeRaw)) : null;

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

            $updateData = [
                'program_code' => $programCode,
                'program_name' => $programName,
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'status'       => $programModel->calculateStatus($startDate, $endDate),
            ];

            if ($parentCodeProvided) {
                if ($parentCode === '') {
                    // Explicitly cleared -> this becomes (or stays) a main program.
                    $updateData['parent_id'] = null;
                } else {
                    if ($parentCode === $programCode) {
                        return $this->response->setStatusCode(422)->setJSON([
                            'success' => false,
                            'message' => 'Program tidak boleh menjadi induk kepada dirinya sendiri.',
                        ]);
                    }

                    $newParent = $programModel->where('program_code', $parentCode)->first();
                    if (!$newParent) {
                        return $this->response->setStatusCode(404)->setJSON([
                            'success' => false,
                            'message' => 'Program induk tidak ditemui.',
                        ]);
                    }

                    // Guard against a simple one-level cycle: the chosen parent
                    // cannot itself be a child of the program being edited.
                    if ((int) ($newParent['parent_id'] ?? 0) === (int) $program['id']) {
                        return $this->response->setStatusCode(422)->setJSON([
                            'success' => false,
                            'message' => 'Program induk yang dipilih adalah sub program kepada program ini.',
                        ]);
                    }

                    $updateData['parent_id'] = (int) $newParent['id'];
                }
            }

            $oldProgramName = $program['program_name'];
            $updated = $programModel->update($program['id'], $updateData);

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
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'updateProgram');
        }
    }

    public function deleteProgram(string $programCode)
    {
        try {
            $programCode  = strtoupper(trim($programCode));
            $programModel = new ProgramModel();
            $program      = $programModel->where('program_code', $programCode)->first();

            if (!$program) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program tidak ditemui.',
                ]);
            }

            if ($this->programHasRegistrations($program)) {
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
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'deleteProgram');
        }
    }

    private function programHasRegistrations($program): bool
    {
        $programNames = [];

        if (is_array($program)) {
            $programNames[] = (string) ($program['program_name'] ?? '');

            $programId = (int) ($program['id'] ?? 0);
            if ($programId > 0) {
                $programModel = new ProgramModel();
                $children = $programModel
                    ->select('program_name')
                    ->where('parent_id', $programId)
                    ->findAll();

                foreach ($children as $child) {
                    $programNames[] = (string) ($child['program_name'] ?? '');
                }
            }
        } else {
            $programNames[] = (string) $program;
        }

        $programNames = array_values(array_unique(array_filter(array_map('trim', $programNames))));
        if ($programNames === []) {
            return false;
        }

        foreach ($programNames as $programName) {
            if (
                (new DaftarSekolahModel())->where('program_name', $programName)->countAllResults() > 0 ||
                (new DaftarLuarModel())->where('program_name', $programName)->countAllResults() > 0 ||
                (new DaftarAwamModel())->where('program_name', $programName)->countAllResults() > 0
            ) {
                return true;
            }
        }

        return false;
    }

    private function syncRegistrationProgramName(string $oldProgramName, string $newProgramName): void
    {
        $oldProgramName = trim($oldProgramName);
        $newProgramName = trim($newProgramName);

        if ($oldProgramName === '' || $newProgramName === '' || $oldProgramName === $newProgramName) {
            return;
        }

        (new DaftarSekolahModel())
            ->where('program_name', $oldProgramName)
            ->set(['program_name' => $newProgramName])
            ->update();

        (new DaftarLuarModel())
            ->where('program_name', $oldProgramName)
            ->set(['program_name' => $newProgramName])
            ->update();

        (new DaftarAwamModel())
            ->where('program_name', $oldProgramName)
            ->set(['program_name' => $newProgramName])
            ->update();
    }

    public function getAdminData()
    {
        try {
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
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getAdminData');
        }
    }

    public function getProgramList()
    {
        try {
            $programModel = new ProgramModel();
            $programModel->refreshProgramStatuses();

            $programs = $programModel
                ->orderBy('parent_id', 'ASC')
                ->orderBy('start_date', 'ASC')
                ->orderBy('program_name', 'ASC')
                ->findAll();

            $idToCode = [];
            foreach ($programs as $prog) {
                $idToCode[(int) $prog['id']] = $prog['program_code'];
            }

            $list = [];
            foreach ($programs as $prog) {
                $parentId = ($prog['parent_id'] !== null && $prog['parent_id'] !== '' && (int)$prog['parent_id'] !== 0) ? (int) $prog['parent_id'] : null;
                $parentKod = $parentId ? ($idToCode[$parentId] ?? null) : null;

                $list[] = [
                    'db_id'      => (int) $prog['id'],
                    'id'         => $prog['program_code'],
                    'kod'        => $prog['program_code'],
                    'nama'       => $prog['program_name'],
                    'mula'       => $prog['start_date'],
                    'tamat'      => $prog['end_date'],
                    'status'     => $prog['status'],
                    'start_date' => $prog['start_date'],
                    'end_date'   => $prog['end_date'],
                    'parent_id'  => $parentId,
                    'parent_kod' => $parentKod,
                ];
            }

            return $this->response
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setJSON($list);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getProgramList');
        }
    }

    public function getSubPrograms(string $parentCode)
    {
        try {
            $programModel = new ProgramModel();
            $parent = $programModel
                ->where('program_code', strtoupper(trim($parentCode)))
                ->first();

            if (!$parent) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Program induk tidak ditemui.',
                ]);
            }

            $subs = $programModel
                ->where('parent_id', (int) $parent['id'])
                ->orderBy('start_date', 'ASC')
                ->orderBy('program_name', 'ASC')
                ->findAll();

            return $this->response
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setJSON([
                    'success' => true,
                    'programs' => $subs,
                ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getSubPrograms');
        }
    }

    public function getAccounts()
    {
        try {
            $schoolModel = new SchoolAccountModel();
            $publicModel = new PublicAccountModel();

            return $this->response
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setJSON([
                    'success' => true,
                    'school'  => $schoolModel->orderBy('school_name', 'ASC')->findAll(),
                    'public'  => $publicModel->orderBy('name', 'ASC')->findAll(),
                ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getAccounts');
        }
    }

    public function createAccount(string $type)
    {
        try {
            $type = strtolower($type);

            if ($type === 'school') {
                return $this->createSchoolAccount();
            }

            if ($type === 'public') {
                return $this->createPublicAccount();
            }

            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Jenis akaun tidak sah.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'createAccount');
        }
    }

    public function updateAccount(string $type, int $id)
    {
        try {
            $type = strtolower($type);

            if ($type === 'school') {
                return $this->updateSchoolAccount($id);
            }

            if ($type === 'public') {
                return $this->updatePublicAccount($id);
            }

            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Jenis akaun tidak sah.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'updateAccount');
        }
    }

    public function deleteAccount(string $type, int $id)
    {
        try {
            $model = $this->getAccountModel($type);

            if (!$model) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Jenis akaun tidak sah.',
                ]);
            }

            if (!$model->find($id)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Akaun tidak ditemui.',
                ]);
            }

            if (!$model->delete($id)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal memadam akaun.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Akaun berjaya dipadam.',
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'deleteAccount');
        }
    }

    public function getRegistrationStudents(int $registrationId)
    {
        try {
            $sekolahModel = new DaftarSekolahModel();
            $registration = $sekolahModel->find($registrationId);

            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Rekod pendaftaran sekolah tidak ditemui.',
                ]);
            }

            $muridModel = new DaftarMuridModel();
            $students = $muridModel
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
                'success'  => true,
                'school'   => $registration['nama_sekolah'],
                'program'  => $registration['program_name'],
                'students' => $list,
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e, 'getRegistrationStudents');
        }
    }

    private function createSchoolAccount()
    {
        $schoolCode = strtoupper(trim((string) $this->request->getPost('school_code')));
        $schoolName = trim((string) $this->request->getPost('school_name'));
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($schoolCode === '' || $schoolName === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Kod sekolah, nama sekolah, emel dan kata laluan diperlukan.',
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $model = new SchoolAccountModel();
        if (
            $model
                ->groupStart()
                    ->where('school_code', $schoolCode)
                    ->orWhere('email', $email)
                ->groupEnd()
                ->first()
        ) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Kod sekolah atau emel telah digunakan.',
            ]);
        }

        if (!$model->insert([
            'school_code' => $schoolCode,
            'school_name' => $schoolName,
            'email'       => $email,
            'password'    => $password,
        ])) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mencipta akaun sekolah.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun sekolah berjaya dicipta.',
        ]);
    }

    private function createPublicAccount()
    {
        $name = trim((string) $this->request->getPost('name'));
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($name === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Nama, emel dan kata laluan diperlukan.',
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $model = new PublicAccountModel();
        if ($model->where('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Emel ini telah digunakan.',
            ]);
        }

        if (!$model->insert([
            'name'       => $name,
            'email'      => $email,
            'password'   => $password,
            'created_at' => date('Y-m-d H:i:s'),
        ])) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mencipta akaun awam.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun awam berjaya dicipta.',
        ]);
    }

    private function updateSchoolAccount(int $id)
    {
        $model = new SchoolAccountModel();
        $account = $model->find($id);

        if (!$account) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Akaun sekolah tidak ditemui.',
            ]);
        }

        $schoolCode = strtoupper(trim((string) $this->request->getPost('school_code')));
        $schoolName = trim((string) $this->request->getPost('school_name'));
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($schoolCode === '' || $schoolName === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Kod dan nama sekolah diperlukan.',
            ]);
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $duplicate = $model
            ->groupStart()
                ->where('school_code', $schoolCode)
                ->orWhere('email', $email)
            ->groupEnd()
            ->first();

        if ($duplicate && (int) $duplicate['id'] !== $id) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Kod sekolah atau emel telah digunakan.',
            ]);
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
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengemaskini akaun sekolah.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun sekolah berjaya dikemaskini.',
        ]);
    }

    private function updatePublicAccount(int $id)
    {
        $model = new PublicAccountModel();
        $account = $model->find($id);

        if (!$account) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Akaun awam tidak ditemui.',
            ]);
        }

        $name = trim((string) $this->request->getPost('name'));
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($name === '' || $email === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Nama dan emel diperlukan.',
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $duplicate = $model->where('email', $email)->first();
        if ($duplicate && (int) $duplicate['id'] !== $id) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Emel ini telah digunakan.',
            ]);
        }

        $data = [
            'name'  => $name,
            'email' => $email,
        ];

        if ($password !== '') {
            $data['password'] = $password;
        }

        if (!$model->update($id, $data)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengemaskini akaun awam.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun awam berjaya dikemaskini.',
        ]);
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

    private function formatData($data, $jenis): array
    {
        $result = [];

        foreach ($data as $row) {
            $obj = [
                'timestamp' => date('d/m/Y H:i', strtotime($row['timestamp'])),
                'program'   => $row['program_name'],
            ];

            if ($jenis === 'TRG') {
                $obj['id'] = $row['id'];
                $obj['namaSekolah'] = $row['nama_sekolah'];
                $obj['kodSekolah'] = $row['kod_sekolah'];
                $obj['namaGuru'] = $row['nama_guru'];
                $obj['telGuru'] = $row['tel_guru'];
                $obj['bilMurid'] = $row['bil_murid'];
            } elseif ($jenis === 'LUAR') {
                $obj['namaSekolah'] = $row['nama_sekolah'];
                $obj['kodSekolah'] = $row['kod_sekolah'];
                $obj['tel'] = $row['tel'];
                $obj['email'] = $row['email'];
            } elseif ($jenis === 'AWAM') {
                $obj['nama'] = $row['nama'];
                $obj['ic'] = $row['ic'];
                $obj['tel'] = $row['tel'];
                $obj['email'] = $row['email'];
            }

            $result[] = $obj;
        }

        return $result;
    }
}
