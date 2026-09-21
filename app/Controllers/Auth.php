<?php

namespace App\Controllers;

use App\Models\AdminModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function login()
    {
        $session = session();
        if ($session->get('is_admin_logged_in')) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $data = [
            'title'    => 'Login Administrator - ' . ($this->settings['company_name'] ?? 'Grand Harmoni'),
            'settings' => $this->settings,
        ];

        return view('admin/auth/login', $data);
    }

    public function processLogin()
    {
        $session = session();

        $rules = [
            'username' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Username atau Email wajib diisi.']
            ],
            'password' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Password wajib diisi.']
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $loginInput = trim((string) $this->request->getPost('username'));
        $password   = (string) $this->request->getPost('password');

        $adminModel = new AdminModel();
        
        // Find admin by username OR email
        $admin = $adminModel->where('username', $loginInput)
                            ->orWhere('email', $loginInput)
                            ->first();

        if (!$admin || !password_verify($password, $admin['password'])) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi Username/Email dan Password salah.');
        }

        // Set session
        $sessionData = [
            'admin_id'          => (int) $admin['id'],
            'admin_name'        => $admin['name'],
            'admin_username'    => $admin['username'],
            'admin_email'       => $admin['email'],
            'admin_role'        => $admin['role'],
            'is_admin_logged_in'=> true,
        ];
        $session->set($sessionData);

        return redirect()->to(site_url('admin/dashboard'))->with('success', 'Selamat datang kembali, ' . esc($admin['name']) . '!');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(site_url('admin/login'))->with('success', 'Anda telah berhasil logout.');
    }
}
