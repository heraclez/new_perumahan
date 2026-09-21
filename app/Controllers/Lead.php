<?php

namespace App\Controllers;

use App\Models\LeadModel;
use CodeIgniter\HTTP\ResponseInterface;

class Lead extends BaseController
{
    public function store(): ResponseInterface
    {
        // Require AJAX or POST
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Metode HTTP tidak diizinkan.'
            ]);
        }

        $rules = [
            'nama_prospek' => [
                'rules'  => 'required|min_length[2]|max_length[150]',
                'errors' => [
                    'required'   => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama minimal 2 karakter.'
                ]
            ],
            'no_wa' => [
                'rules'  => 'required|min_length[8]|max_length[30]',
                'errors' => [
                    'required'   => 'Nomor WhatsApp wajib diisi.',
                    'min_length' => 'Nomor WhatsApp tidak valid.'
                ]
            ],
            'property_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
                'csrf'    => csrf_hash(),
            ]);
        }

        $namaProspek = trim((string) $this->request->getPost('nama_prospek'));
        $noWa        = trim((string) $this->request->getPost('no_wa'));
        $propertyId  = $this->request->getPost('property_id') ? (int) $this->request->getPost('property_id') : null;

        // Clean WhatsApp number (replace non-numeric, format 08... to 628...)
        $cleanWa = preg_replace('/[^0-9]/', '', $noWa);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        }

        $leadModel = new LeadModel();
        $inserted = $leadModel->insert([
            'property_id'  => $propertyId,
            'nama_prospek' => $namaProspek,
            'no_wa'        => $cleanWa,
        ]);

        if (!$inserted) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan data prospek.',
                'csrf'    => csrf_hash(),
            ]);
        }

        // Build download URL
        $downloadUrl = $propertyId 
            ? site_url('brochure/download/' . $propertyId) 
            : site_url('brochure/download-global');

        return $this->response->setJSON([
            'success'      => true,
            'message'      => 'Terima kasih, data Anda berhasil diverifikasi. Brosur sedang diunduh...',
            'download_url' => $downloadUrl,
            'csrf'         => csrf_hash(),
        ]);
    }
}
