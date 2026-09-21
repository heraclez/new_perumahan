<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Admins extends BaseController
{
    protected AdminModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    /**
     * List all admins
     */
    public function index(): string
    {
        $admins = $this->adminModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title'    => 'Manajemen Akun Admin (RBAC)',
            'settings' => $this->settings,
            'admins'   => $admins,
        ];

        return view('admin/admins/index', $data);
    }

    /**
     * Create admin form
     */
    public function create(): string
    {
        $data = [
            'title'    => 'Tambah Akun Admin Baru',
            'settings' => $this->settings,
        ];

        return view('admin/admins/create', $data);
    }

    /**
     * Store new admin
     */
    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[admins.email]',
            'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[admins.username]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[Developer,Superadmin,Admin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $passwordHash = password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT);

        $this->adminModel->insert([
            'name'     => trim((string) $this->request->getPost('name')),
            'email'    => trim((string) $this->request->getPost('email')),
            'username' => trim((string) $this->request->getPost('username')),
            'password' => $passwordHash,
            'role'     => $this->request->getPost('role'),
        ]);

        return redirect()->to(site_url('admin/admins'))->with('success', 'Akun admin berhasil dibuat.');
    }

    /**
     * Edit admin form
     */
    public function edit(int $id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Akun admin tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Akun: ' . $admin['name'],
            'settings' => $this->settings,
            'admin'    => $admin,
        ];

        return view('admin/admins/edit', $data);
    }

    /**
     * Update admin
     */
    public function update(int $id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Akun admin tidak ditemukan.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => "required|valid_email|is_unique[admins.email,id,{$id}]",
            'username' => "required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[admins.username,id,{$id}]",
            'password' => 'permit_empty|min_length[6]',
            'role'     => 'required|in_list[Developer,Superadmin,Admin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name'     => trim((string) $this->request->getPost('name')),
            'email'    => trim((string) $this->request->getPost('email')),
            'username' => trim((string) $this->request->getPost('username')),
            'role'     => $this->request->getPost('role'),
        ];

        // Only update password if filled
        $password = (string) $this->request->getPost('password');
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->adminModel->update($id, $updateData);

        // If updating current logged in user, refresh session name & email
        if ((int) session()->get('admin_id') === $id) {
            session()->set([
                'admin_name'     => $updateData['name'],
                'admin_username' => $updateData['username'],
                'admin_email'    => $updateData['email'],
                'admin_role'     => $updateData['role'],
            ]);
        }

        return redirect()->to(site_url('admin/admins'))->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Delete admin
     */
    public function delete(int $id)
    {
        $currentLoggedInId = (int) session()->get('admin_id');
        
        // Superadmin tidak bisa menghapus akunnya sendiri yang sedang login
        if ($currentLoggedInId === $id) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login!');
        }

        $admin = $this->adminModel->find($id);
        if (!$admin) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Akun tidak ditemukan.');
        }

        $this->adminModel->delete($id);

        return redirect()->to(site_url('admin/admins'))->with('success', 'Akun admin berhasil dihapus.');
    }
}
