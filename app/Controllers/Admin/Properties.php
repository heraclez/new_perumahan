<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyImageModel;
use App\Models\PropertyModel;
use CodeIgniter\HTTP\ResponseInterface;

class Properties extends BaseController
{
    protected PropertyModel $propertyModel;
    protected PropertyImageModel $imageModel;

    public function __construct()
    {
        $this->propertyModel = new PropertyModel();
        $this->imageModel    = new PropertyImageModel();
    }

    /**
     * Property list table
     */
    public function index(): string
    {
        $status  = $this->request->getGet('status');
        $keyword = $this->request->getGet('keyword');

        $filter = [];
        if (!empty($status)) {
            $filter['status'] = $status;
        }
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        $properties = $this->propertyModel->getPropertiesWithThumbnail($filter);

        $data = [
            'title'      => 'Katalog Properti Perumahan',
            'settings'   => $this->settings,
            'properties' => $properties,
            'filters'    => $filter,
        ];

        return view('admin/properties/index', $data);
    }

    /**
     * Create property form
     */
    public function create(): string
    {
        $data = [
            'title'    => 'Tambah Tipe Properti Baru',
            'settings' => $this->settings,
        ];

        return view('admin/properties/create', $data);
    }

    /**
     * Store new property
     */
    public function store()
    {
        $rules = [
            'title'             => 'required|min_length[3]|max_length[255]',
            'slug'              => 'permit_empty|alpha_dash|min_length[3]|max_length[255]|is_unique[properties.slug]',
            'harga'             => 'required|numeric|greater_than_equal_to[0]',
            'deskripsi'         => 'permit_empty',
            'spesifikasi_kamar' => 'permit_empty|max_length[100]',
            'luas_tanah'        => 'required|integer|greater_than_equal_to[0]',
            'luas_bangunan'     => 'required|integer|greater_than_equal_to[0]',
            'status'            => 'required|in_list[Tersedia,Booking,Terjual]',
            'is_promo'          => 'permit_empty|in_list[0,1]',
            'promo_title'       => 'permit_empty|max_length[255]',
            'promo_desc'        => 'permit_empty',
            'brosur_pdf'        => 'permit_empty|ext_in[brosur_pdf,pdf]|max_size[brosur_pdf,5120]',
            'images.*'          => 'permit_empty|is_image[images]|mime_in[images,image/jpg,image/jpeg,image/png,image/webp]|max_size[images,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $title = trim((string) $this->request->getPost('title'));
        $slug  = trim((string) $this->request->getPost('slug'));
        if (empty($slug)) {
            $slug = url_title($title, '-', true);
            // Ensure unique slug
            $existing = $this->propertyModel->where('slug', $slug)->first();
            if ($existing) {
                $slug .= '-' . time();
            }
        }

        // Handle Brochure PDF
        $brosurPdfName = null;
        $brosurFile = $this->request->getFile('brosur_pdf');
        if ($brosurFile && $brosurFile->isValid() && !$brosurFile->hasMoved()) {
            $brosurPdfName = $brosurFile->getRandomName();
            $brosurFile->move(FCPATH . 'uploads/brochures', $brosurPdfName);
        }

        $propertyId = $this->propertyModel->insert([
            'title'             => $title,
            'slug'              => $slug,
            'harga'             => (float) $this->request->getPost('harga'),
            'deskripsi'         => $this->request->getPost('deskripsi'),
            'spesifikasi_kamar' => $this->request->getPost('spesifikasi_kamar'),
            'luas_tanah'        => (int) $this->request->getPost('luas_tanah'),
            'luas_bangunan'     => (int) $this->request->getPost('luas_bangunan'),
            'status'            => $this->request->getPost('status'),
            'is_promo'          => $this->request->getPost('is_promo') ? 1 : 0,
            'promo_title'       => trim((string) $this->request->getPost('promo_title')),
            'promo_desc'        => trim((string) $this->request->getPost('promo_desc')),
            'brosur_pdf'        => $brosurPdfName,
        ]);

        if (!$propertyId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data properti.');
        }

        // Handle Multiple Images
        $images = $this->request->getFiles();
        if (isset($images['images'])) {
            $targetDir = FCPATH . 'uploads/properties';
            foreach ($images['images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $opt = \App\Libraries\ImageOptimizer::convertToWebp($img, $targetDir, $slug . '-unit');
                    $this->imageModel->insert([
                        'property_id' => $propertyId,
                        'image_name'  => $opt['filename'],
                    ]);
                }
            }
        }

        return redirect()->to(site_url('admin/properties'))->with('success', 'Tipe properti berhasil ditambahkan!');
    }

    /**
     * Edit property form
     */
    public function edit(int $id)
    {
        $property = $this->propertyModel->getPropertyWithImages($id);
        if (!$property) {
            return redirect()->to(site_url('admin/properties'))->with('error', 'Data properti tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Properti: ' . $property['title'],
            'settings' => $this->settings,
            'property' => $property,
        ];

        return view('admin/properties/edit', $data);
    }

    /**
     * Update property
     */
    public function update(int $id)
    {
        $property = $this->propertyModel->find($id);
        if (!$property) {
            return redirect()->to(site_url('admin/properties'))->with('error', 'Data properti tidak ditemukan.');
        }

        $rules = [
            'title'             => 'required|min_length[3]|max_length[255]',
            'slug'              => "required|alpha_dash|min_length[3]|max_length[255]|is_unique[properties.slug,id,{$id}]",
            'harga'             => 'required|numeric|greater_than_equal_to[0]',
            'deskripsi'         => 'permit_empty',
            'spesifikasi_kamar' => 'permit_empty|max_length[100]',
            'luas_tanah'        => 'required|integer|greater_than_equal_to[0]',
            'luas_bangunan'     => 'required|integer|greater_than_equal_to[0]',
            'status'            => 'required|in_list[Tersedia,Booking,Terjual]',
            'is_promo'          => 'permit_empty|in_list[0,1]',
            'promo_title'       => 'permit_empty|max_length[255]',
            'promo_desc'        => 'permit_empty',
            'brosur_pdf'        => 'permit_empty|ext_in[brosur_pdf,pdf]|max_size[brosur_pdf,5120]',
            'images.*'          => 'permit_empty|is_image[images]|mime_in[images,image/jpg,image/jpeg,image/png,image/webp]|max_size[images,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $brosurPdfName = $property['brosur_pdf'];
        $brosurFile    = $this->request->getFile('brosur_pdf');
        if ($brosurFile && $brosurFile->isValid() && !$brosurFile->hasMoved()) {
            // Remove old brochure if exists
            if (!empty($brosurPdfName) && is_file(FCPATH . 'uploads/brochures/' . $brosurPdfName)) {
                @unlink(FCPATH . 'uploads/brochures/' . $brosurPdfName);
            }
            $brosurPdfName = $brosurFile->getRandomName();
            $brosurFile->move(FCPATH . 'uploads/brochures', $brosurPdfName);
        }

        $this->propertyModel->update($id, [
            'title'             => trim((string) $this->request->getPost('title')),
            'slug'              => trim((string) $this->request->getPost('slug')),
            'harga'             => (float) $this->request->getPost('harga'),
            'deskripsi'         => $this->request->getPost('deskripsi'),
            'spesifikasi_kamar' => $this->request->getPost('spesifikasi_kamar'),
            'luas_tanah'        => (int) $this->request->getPost('luas_tanah'),
            'luas_bangunan'     => (int) $this->request->getPost('luas_bangunan'),
            'status'            => $this->request->getPost('status'),
            'is_promo'          => $this->request->getPost('is_promo') ? 1 : 0,
            'promo_title'       => trim((string) $this->request->getPost('promo_title')),
            'promo_desc'        => trim((string) $this->request->getPost('promo_desc')),
            'brosur_pdf'        => $brosurPdfName,
        ]);

        // Upload additional gallery images
        $images = $this->request->getFiles();
        if (isset($images['images'])) {
            $targetDir = FCPATH . 'uploads/properties';
            foreach ($images['images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $opt = \App\Libraries\ImageOptimizer::convertToWebp($img, $targetDir, $slug . '-unit');
                    $this->imageModel->insert([
                        'property_id' => $id,
                        'image_name'  => $opt['filename'],
                    ]);
                }
            }
        }

        return redirect()->to(site_url('admin/properties'))->with('success', 'Data properti berhasil diperbarui!');
    }

    /**
     * Soft delete property
     */
    public function delete(int $id)
    {
        $property = $this->propertyModel->find($id);
        if (!$property) {
            return redirect()->to(site_url('admin/properties'))->with('error', 'Data tidak ditemukan.');
        }

        $this->propertyModel->delete($id);

        return redirect()->to(site_url('admin/properties'))->with('success', 'Properti berhasil dihapus (Soft Delete).');
    }

    /**
     * Delete individual image from gallery (AJAX or form POST)
     */
    public function deleteImage(int $imageId)
    {
        $image = $this->imageModel->find($imageId);
        if (!$image) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Gambar tidak ditemukan.'
            ]);
        }

        // Delete physical file if local
        if (!str_starts_with($image['image_name'], 'http')) {
            $filePath = FCPATH . 'uploads/properties/' . $image['image_name'];
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $this->imageModel->delete($imageId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto galeri berhasil dihapus.',
                'csrf'    => csrf_hash(),
            ]);
        }

        return redirect()->back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
