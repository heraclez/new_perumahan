<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyModel;
use App\Models\SiteplanModel;
use App\Models\SiteplanPinModel;
use CodeIgniter\HTTP\ResponseInterface;

class Siteplan extends BaseController
{
    protected SiteplanModel $siteplanModel;
    protected SiteplanPinModel $pinModel;
    protected PropertyModel $propertyModel;

    public function __construct()
    {
        $this->siteplanModel = new SiteplanModel();
        $this->pinModel      = new SiteplanPinModel();
        $this->propertyModel = new PropertyModel();
    }

    /**
     * List all siteplans
     */
    public function index(): string
    {
        $siteplans = $this->siteplanModel->where('deleted_at', null)->orderBy('id', 'DESC')->findAll();

        // Attach pin count to each siteplan
        foreach ($siteplans as &$sp) {
            $sp['total_pins'] = $this->pinModel->where('siteplan_id', (int) $sp['id'])->countAllResults();
        }

        $data = [
            'title'     => 'Master Siteplan & Denah Interaktif',
            'settings'  => $this->settings,
            'siteplans' => $siteplans,
        ];

        return view('admin/siteplan/index', $data);
    }

    /**
     * Upload and create new siteplan
     */
    public function store()
    {
        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty',
            'image'       => 'uploaded[image]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,10240]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageFile = $this->request->getFile('image');
        $targetDir = FCPATH . 'uploads/siteplan';
        $opt = \App\Libraries\ImageOptimizer::convertToWebp($imageFile, $targetDir, 'masterplan-siteplan', 2400, 85);
        $imageName = $opt['filename'];

        $siteplanId = $this->siteplanModel->insert([
            'title'       => trim((string) $this->request->getPost('title')),
            'image'       => $imageName,
            'description' => trim((string) $this->request->getPost('description')),
            'is_active'   => 1,
        ]);

        if (!$siteplanId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data siteplan.');
        }

        return redirect()->to(site_url('admin/siteplan/builder/' . $siteplanId))->with('success', 'Gambar siteplan berhasil diunggah! Silakan mulai klik pada denah untuk menaruh pin kavling.');
    }

    /**
     * Interactive Visual Pin Plotter Builder
     */
    public function builder(int $id)
    {
        $siteplan = $this->siteplanModel->find($id);
        if (!$siteplan) {
            return redirect()->to(site_url('admin/siteplan'))->with('error', 'Data siteplan tidak ditemukan.');
        }

        $pins       = $this->pinModel->getPinsWithProperty($id);
        $properties = $this->propertyModel->where('deleted_at', null)->orderBy('title', 'ASC')->findAll();

        $data = [
            'title'      => 'Visual Pin Plotter: ' . $siteplan['title'],
            'settings'   => $this->settings,
            'siteplan'   => $siteplan,
            'pins'       => $pins,
            'properties' => $properties,
        ];

        return view('admin/siteplan/builder', $data);
    }

    /**
     * Save all pins via AJAX from Interactive Builder
     */
    public function savePins(int $id)
    {
        $siteplan = $this->siteplanModel->find($id);
        if (!$siteplan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data siteplan tidak ditemukan.',
                'csrf'    => csrf_hash(),
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $rawJson = $this->request->getPost('pins');
        $pins    = is_string($rawJson) ? json_decode($rawJson, true) : (is_array($rawJson) ? $rawJson : []);

        if (!is_array($pins)) {
            $pins = [];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Remove old pins for this siteplan
        $this->pinModel->where('siteplan_id', $id)->delete();

        // Insert new updated pins
        foreach ($pins as $pin) {
            $kavlingNumber = trim($pin['kavling_number'] ?? '');
            $propertyId    = (int) ($pin['property_id'] ?? 0);
            $status        = in_array($pin['status'] ?? '', ['Tersedia', 'Booking', 'Terjual']) ? $pin['status'] : 'Tersedia';
            $posX          = (float) ($pin['pos_x'] ?? 0);
            $posY          = (float) ($pin['pos_y'] ?? 0);
            $notes         = trim($pin['notes'] ?? '');

            if (!empty($kavlingNumber) && $propertyId > 0 && $posX >= 0 && $posY >= 0) {
                $this->pinModel->insert([
                    'siteplan_id'    => $id,
                    'property_id'    => $propertyId,
                    'kavling_number' => $kavlingNumber,
                    'status'         => $status,
                    'pos_x'          => round($posX, 2),
                    'pos_y'          => round($posY, 2),
                    'notes'          => $notes,
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan pin kavling ke database.',
                'csrf'    => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Semua posisi pin kavling (' . count($pins) . ' unit) berhasil disimpan dengan sukses!',
            'total_pins' => count($pins),
            'csrf'       => csrf_hash(),
        ]);
    }

    /**
     * Toggle active status of siteplan
     */
    public function toggleStatus(int $id)
    {
        $siteplan = $this->siteplanModel->find($id);
        if (!$siteplan) {
            return redirect()->to(site_url('admin/siteplan'))->with('error', 'Data siteplan tidak ditemukan.');
        }

        $newStatus = empty($siteplan['is_active']) ? 1 : 0;
        $this->siteplanModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan (tampil di menu website)' : 'dinonaktifkan / disembunyikan (Draft)';
        return redirect()->to(site_url('admin/siteplan'))->with('success', 'Siteplan "' . esc($siteplan['title']) . '" berhasil ' . $statusText . '.');
    }

    /**
     * Activate siteplan
     */
    public function activate(int $id)
    {
        $this->siteplanModel->update($id, ['is_active' => 1]);
        return redirect()->to(site_url('admin/siteplan'))->with('success', 'Siteplan berhasil diaktifkan untuk tampilan website publik.');
    }

    /**
     * Deactivate siteplan (Draft)
     */
    public function deactivate(int $id)
    {
        $this->siteplanModel->update($id, ['is_active' => 0]);
        return redirect()->to(site_url('admin/siteplan'))->with('success', 'Siteplan berhasil dinonaktifkan (disembunyikan dari website publik).');
    }

    /**
     * Soft delete siteplan
     */
    public function delete(int $id)
    {
        $this->siteplanModel->delete($id);
        return redirect()->to(site_url('admin/siteplan'))->with('success', 'Siteplan berhasil dihapus.');
    }
}
