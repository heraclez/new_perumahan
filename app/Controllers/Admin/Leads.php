<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeadModel;

class Leads extends BaseController
{
    protected LeadModel $leadModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
    }

    public function index(): string
    {
        $leads = $this->leadModel->getLeadsWithProperty();

        $data = [
            'title'    => 'Daftar Prospek Masuk (Leads)',
            'settings' => $this->settings,
            'leads'    => $leads,
        ];

        return view('admin/leads/index', $data);
    }

    public function delete(int $id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to(site_url('admin/leads'))->with('error', 'Data prospek tidak ditemukan.');
        }

        $this->leadModel->delete($id);

        return redirect()->to(site_url('admin/leads'))->with('success', 'Data prospek berhasil dihapus.');
    }
}
