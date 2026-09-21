<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MediaModel;

class Media extends BaseController
{
    protected MediaModel $mediaModel;

    public function __construct()
    {
        $this->mediaModel = new MediaModel();
    }

    public function manager(): string
    {
        $data = [
            'title'    => 'File Manager',
            'settings' => $this->settings,
        ];

        return view('admin/media/elfinder', $data);
    }

    public function popup(): string
    {
        $data = [
            'title'     => 'Pilih Media',
            'targetId'  => $this->request->getGet('target') ?: '',
            'previewId' => $this->request->getGet('preview') ?: '',
        ];

        return view('admin/media/popup', $data);
    }

    public function connector()
    {
        require_once APPPATH . 'ThirdParty/elFinder/autoload.php';

        $uploadRoot = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadRoot)) {
            mkdir($uploadRoot, 0777, true);
        }

        foreach (['properties', 'articles', 'media', 'siteplan', 'settings', 'brochures', '.trash'] as $sub) {
            $dir = $uploadRoot . $sub;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }

        $accessCallback = function ($attr, $path, $data, $volume, $isDir, $relpath) {
            $basename = basename($path);
            if ($basename[0] === '.' && strlen($relpath) !== 1) {
                return !($attr == 'read' || $attr == 'write');
            }
            $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
            if (in_array($ext, ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar', 'exe', 'sh', 'bat', 'cmd', 'htaccess'], true)) {
                return !($attr == 'read' || $attr == 'write');
            }
            return null;
        };

        $opts = [
            'locale' => 'id_ID.UTF-8',
            'bind'   => [
                'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre rm.pre' => [
                    'Plugin.Sanitizer.cmdPreprocess'
                ],
                'upload.presave' => [
                    'Plugin.AutoRotate.onUpLoadPreSave',
                    'Plugin.AutoResize.onUpLoadPreSave'
                ]
            ],
            'plugin' => [
                'Sanitizer' => [
                    'enable'  => true,
                    'targets' => ['\\', '/', ':', '*', '?', '"', '<', '>', '|'],
                    'replace' => '_'
                ],
                'AutoRotate' => [
                    'enable' => true
                ],
                'AutoResize' => [
                    'enable'       => true,
                    'maxWidth'     => 1920,
                    'maxHeight'    => 1920,
                    'quality'      => 85,
                    'preserveExif' => false
                ]
            ],
            'roots'  => [
                [
                    'driver'        => 'LocalFileSystem',
                    'path'          => $uploadRoot,
                    'URL'           => base_url('uploads/'),
                    'alias'         => 'Semua Berkas (Uploads)',
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
                    'uploadDeny'    => ['text/x-php', 'application/x-php', 'text/php', 'application/php', 'application/x-httpd-php'],
                    'uploadAllow'   => ['all'],
                    'uploadOrder'   => ['deny', 'allow'],
                    'accessControl' => $accessCallback,
                    'trashHash'     => 't1_Lw',
                    'attributes'    => [
                        [
                            'pattern' => '/\.trash$/',
                            'hidden'  => true,
                        ],
                    ],
                ],
                [
                    'id'            => '1',
                    'driver'        => 'Trash',
                    'path'          => $uploadRoot . '.trash' . DIRECTORY_SEPARATOR,
                    'tmbURL'        => base_url('uploads/.trash/.tmb/'),
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
                    'uploadDeny'    => ['all'],
                    'uploadAllow'   => ['all'],
                    'uploadOrder'   => ['deny', 'allow'],
                    'accessControl' => $accessCallback,
                ]
            ]
        ];

        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();
        exit;
    }

    public function index(): string
    {
        $items = $this->mediaModel->getAll(100);

        $data = [
            'title' => 'Media Manager (Klasik)',
            'items' => $items,
        ];

        return view('admin/media/index', $data);
    }

    public function upload()
    {
        $file = $this->request->getFile('media_file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $file ? $file->getErrorString() : 'Tidak ada berkas yang diunggah.',
            ]);
        }

        // Validate MIME
        $mime = $file->getMimeType();
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif',
            'video/mp4', 'video/webm', 'application/pdf',
        ];

        if (!in_array($mime, $allowedMimes, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format berkas tidak diizinkan. Gunakan JPG, PNG, WEBP, SVG, MP4, atau PDF.',
            ]);
        }

        // Validate Size (Max 25MB)
        if ($file->getSizeByUnit('mb') > 25) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ukuran berkas melebihi batas maksimal 25MB.',
            ]);
        }

        $targetDir = FCPATH . 'uploads/media';
        $optResult = \App\Libraries\ImageOptimizer::convertToWebp($file, $targetDir);

        $siteName = (new \App\Models\SettingModel())->get('company_name') ?: 'Grand Harmoni Residence';
        $caption = \App\Libraries\ImageOptimizer::generateAltText(
            $this->request->getPost('caption'),
            $file->getClientName(),
            $siteName
        );

        $id = $this->mediaModel->insert([
            'filename'      => $optResult['filename'],
            'original_name' => $file->getClientName(),
            'file_path'     => 'uploads/media/' . $optResult['filename'],
            'file_type'     => $optResult['file_type'],
            'file_size'     => $optResult['file_size'],
            'caption'       => $caption,
        ]);

        $fileUrl = base_url('uploads/media/' . $optResult['filename']);

        $message = 'Berkas berhasil diunggah.';
        if (!empty($optResult['is_webp']) && !empty($optResult['saved_percent'])) {
            $message .= " Dioptimasi ke WebP (Hemat {$optResult['saved_percent']}%).";
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'       => true,
                'id'            => $id,
                'filename'      => $optResult['filename'],
                'url'           => $fileUrl,
                'type'          => $optResult['file_type'],
                'caption'       => $caption,
                'saved_percent' => $optResult['saved_percent'] ?? 0,
                'message'       => $message,
            ]);
        }

        return redirect()->to(site_url('admin/media'))->with('success', $message);
    }

    public function delete(int $id)
    {
        $item = $this->mediaModel->find($id);
        if (!$item) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Media tidak ditemukan.']);
            }
            return redirect()->to(site_url('admin/media'))->with('error', 'Media tidak ditemukan.');
        }

        $filePath = FCPATH . $item['file_path'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        $this->mediaModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Media berhasil dihapus.']);
        }

        return redirect()->to(site_url('admin/media'))->with('success', 'Media berhasil dihapus.');
    }

    /**
     * AJAX endpoint for Modal Picker in Section Editors
     */
    public function listJson()
    {
        $items = $this->mediaModel->getAll(80);
        foreach ($items as &$item) {
            $item['url'] = base_url($item['file_path']);
            $item['formatted_size'] = number_format($item['file_size'] / 1024, 1) . ' KB';
            $item['is_image'] = str_starts_with($item['file_type'], 'image/');
            $item['is_video'] = str_starts_with($item['file_type'], 'video/');
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $items,
        ]);
    }
}
