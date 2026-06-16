<?php

namespace App\Controllers;

use App\Models\SchoolAccountModel;
use App\Models\PublicAccountModel;

class Login extends BaseController
{
    public function index()
    {
        // Redirect already-logged-in users away from login page
        if ($this->session->get('logged_in')) {
            $role = $this->session->get('role');
            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard?tab=daftar');
            }

            return redirect()->to($role === 'school' ? '/school/portal' : '/public');
        }

        return view('login');
    }

    // Named 'proses' to match the fetch() call in Login.php view: baseUrl + '/login/proses'
    public function proses()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $role     = $this->request->getPost('role') ?: 'school';

        if ($role === 'admin') {
            // Load admin credentials from .env  (set ADMIN_USER and ADMIN_PASS there)
            $adminUser = env('ADMIN_USER', 'adminpskt');
            $adminPass = env('ADMIN_PASS', 'pskt2026');

            if ($username === $adminUser && $password === $adminPass) {
                $this->session->set([
                    'logged_in' => true,
                    'role'      => 'admin',
                    'username'  => $username,
                ]);
                return $this->response->setJSON(['success' => true, 'redirect' => 'admin/dashboard?tab=daftar']);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'ID Admin atau Kata Laluan Salah!']);
        }

        if ($role === 'awam') {
            $model = new PublicAccountModel();
            $user  = $model->where('email', $username)->first();

            if ($user && $user['password'] === $password) {
                $this->session->set([
                    'logged_in' => true,
                    'role'      => 'public',
                    'public_id' => $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                ]);
                return $this->response->setJSON(['success' => true, 'redirect' => 'public']);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Emel atau Kata Laluan Salah!']);
        }

        $model  = new SchoolAccountModel();
        $school = $model->where('school_code', $username)->orWhere('email', $username)->first();

        if ($school && $school['password'] === $password) {
            $this->session->set([
                'logged_in'   => true,
                'role'        => 'school',
                'school_code' => $school['school_code'],
                'school_name' => $school['school_name'],
            ]);
            return $this->response->setJSON(['success' => true, 'redirect' => 'school/portal']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'ID (Kod Sekolah / Emel) atau Kata Laluan Salah!']);
    }

    public function signupSchool()
    {
        $schoolCode = strtoupper(trim((string) $this->request->getPost('school_code')));
        $schoolName = trim((string) $this->request->getPost('school_name'));
        $email      = strtolower(trim((string) $this->request->getPost('email')));
        $password   = trim((string) $this->request->getPost('password'));

        if ($schoolCode === '' || $schoolName === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Sila lengkapkan semua maklumat sekolah.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Format emel tidak sah.']);
        }

        $model = new SchoolAccountModel();
        if ($model->where('school_code', $schoolCode)->orWhere('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'message' => 'Kod sekolah atau emel telah didaftarkan.']);
        }

        $model->insert([
            'school_code' => $schoolCode,
            'school_name' => $schoolName,
            'email'       => $email,
            'password'    => $password,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Akaun sekolah berjaya didaftarkan.']);
    }

    public function signupAwam()
    {
        $name     = trim((string) $this->request->getPost('name'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = trim((string) $this->request->getPost('password'));

        if ($name === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Sila lengkapkan semua maklumat awam.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Format emel tidak sah.']);
        }

        $model = new PublicAccountModel();
        if ($model->where('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'message' => 'Emel ini telah didaftarkan.']);
        }

        $model->insert([
            'name'       => $name,
            'email'      => $email,
            'password'   => $password,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Akaun awam berjaya didaftarkan.']);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}
