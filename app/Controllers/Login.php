<?php

namespace App\Controllers;

use App\Models\SchoolAccountModel;
use App\Models\PublicAccountModel;
use App\Models\AdminAccountModel;
use App\Validation\ValidationRules;

class Login extends BaseController
{
    public function index()
    {
        if ($this->session->get('logged_in')) {
            return $this->redirectByRole();
        }

        return view('login');
    }

    public function proses()
    {
        // ── Server-side validation ────────────────────────────────────
        if (!$this->validate(ValidationRules::LOGIN)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = trim((string) $this->request->getPost('password'));
        $role     = $this->request->getPost('role') ?: 'school';

        if ($role === 'admin') {
            return $this->processAdminLogin($username, $password);
        }

        if ($role === 'awam') {
            return $this->processPublicLogin($username, $password);
        }

        return $this->processSchoolLogin($username, $password);
    }

    // ------------------------------------------------------------------
    // Admin login — Super Admin via .env, regular Admin via DB
    // ------------------------------------------------------------------

    private function processAdminLogin(string $username, string $password)
    {
        // Super Admin — hardcoded credentials from .env
        $superUser = env('ADMIN_USER', 'adminpskt');
        $superPass = env('ADMIN_PASS', 'pskt2026');

        if ($username === $superUser && $password === $superPass) {
            $this->session->set([
                'logged_in'  => true,
                'role'       => 'super_admin',
                'admin_id'   => null,
                'username'   => $username,
                'admin_name' => 'Super Admin',
            ]);
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => 'admin/dashboard',
            ]);
        }

        // Regular Admin — DB lookup
        $model = new AdminAccountModel();
        $admin = $model->findByUsername($username);

        if ($admin && $admin['password'] === $password) {
            $this->session->set([
                'logged_in'  => true,
                'role'       => 'admin',
                'admin_id'   => (int) $admin['id'],
                'username'   => $admin['username'],
                'admin_name' => $admin['name'],
            ]);
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => 'admin/dashboard',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'ID Admin atau Kata Laluan Salah!',
        ]);
    }

    // ------------------------------------------------------------------
    // Public (Awam) login
    // ------------------------------------------------------------------

    private function processPublicLogin(string $email, string $password)
    {
        // Validate email format for awam login
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Format emel tidak sah.',
            ]);
        }

        $model = new PublicAccountModel();
        $user  = $model->where('email', $email)->first();

        if ($user && $user['password'] === $password) {
            $this->session->set([
                'logged_in' => true,
                'role'      => 'public',
                'public_id' => $user['id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
            ]);
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => 'awam/portal',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Emel atau Kata Laluan Salah!',
        ]);
    }

    // ------------------------------------------------------------------
    // School login
    // ------------------------------------------------------------------

    private function processSchoolLogin(string $username, string $password)
    {
        $model  = new SchoolAccountModel();
        $school = $model->where('school_code', $username)
                        ->orWhere('email', $username)
                        ->first();

        if ($school && $school['password'] === $password) {
            $this->session->set([
                'logged_in'   => true,
                'role'        => 'school',
                'school_code' => $school['school_code'],
                'school_name' => $school['school_name'],
            ]);
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => 'school/portal',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'ID (Kod Sekolah / Emel) atau Kata Laluan Salah!',
        ]);
    }

    // ------------------------------------------------------------------
    // Redirect helper
    // ------------------------------------------------------------------

    private function redirectByRole()
    {
        $role = $this->session->get('role');

        if ($role === 'super_admin' || $role === 'admin') {
            return redirect()->to('/admin/dashboard');
        }

        if ($role === 'school') {
            return redirect()->to('/school/portal');
        }

        return redirect()->to('/awam/portal');
    }

    // ------------------------------------------------------------------
    // Self-registration
    // ------------------------------------------------------------------

    public function signupSchool()
    {
        // ── Server-side validation ────────────────────────────────────
        if (!$this->validate(ValidationRules::SIGNUP_SCHOOL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        $schoolCode = strtoupper(trim((string) $this->request->getPost('school_code')));
        $schoolName = trim((string) $this->request->getPost('school_name'));
        $email      = strtolower(trim((string) $this->request->getPost('email')));
        $password   = trim((string) $this->request->getPost('password'));

        $model = new SchoolAccountModel();
        if ($model->where('school_code', $schoolCode)->orWhere('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Kod sekolah atau emel telah didaftarkan.',
            ]);
        }

        $model->insert([
            'school_code' => $schoolCode,
            'school_name' => $schoolName,
            'email'       => $email,
            'password'    => $password,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun sekolah berjaya didaftarkan.',
        ]);
    }

    public function signupAwam()
    {
        // ── Server-side validation ────────────────────────────────────
        if (!$this->validate(ValidationRules::SIGNUP_AWAM)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        $name     = trim((string) $this->request->getPost('name'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        $model = new PublicAccountModel();
        if ($model->where('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Emel ini telah didaftarkan.',
            ]);
        }

        $model->insert([
            'name'       => $name,
            'email'      => $email,
            'password'   => $password,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Akaun awam berjaya didaftarkan.',
        ]);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}