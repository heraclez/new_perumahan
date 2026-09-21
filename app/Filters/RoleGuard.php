<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleGuard implements FilterInterface
{
    /**
     * Check if the authenticated user has the required role.
     *
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('is_admin_logged_in')) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

        // Refresh user role from DB if logged in to ensure immediate sync
        $adminId = $session->get('admin_id');
        if ($adminId) {
            $adminModel = new \App\Models\AdminModel();
            $admin = $adminModel->find($adminId);
            if ($admin && !empty($admin['role'])) {
                $userRole = $admin['role'];
                $session->set('admin_role', $userRole);
            } else {
                $userRole = $session->get('admin_role');
            }
        } else {
            $userRole = $session->get('admin_role');
        }

        if (!empty($arguments)) {
            // If the route strictly requires Developer, only Developer can access
            if (in_array('Developer', $arguments, true)) {
                if ($userRole !== 'Developer') {
                    return redirect()->to(site_url('admin/dashboard'))->with('error', 'Akses ditolak. Fitur ini khusus untuk hak akses Developer.');
                }
                return;
            }

            // Developer has full access to all admin/superadmin routes
            if ($userRole === 'Developer') {
                return;
            }

            // Standard check for other roles
            if (!in_array($userRole, $arguments, true)) {
                return redirect()->to(site_url('admin/dashboard'))->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman tersebut.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
