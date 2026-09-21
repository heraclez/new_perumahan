<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SectionModel;

class Sections extends BaseController
{
    protected SectionModel $sectionModel;

    public function __construct()
    {
        $this->sectionModel = new SectionModel();
    }

    public function index(): string
    {
        $sections = $this->sectionModel->getSections();

        $data = [
            'title'    => 'Section Manager Beranda',
            'sections' => $sections,
        ];

        return view('admin/sections/index', $data);
    }

    public function reorder()
    {
        $orderedIds = $this->request->getPost('order');
        if (!is_array($orderedIds)) {
            $json = $this->request->getJSON(true);
            $orderedIds = $json['order'] ?? [];
        }

        if (!empty($orderedIds)) {
            $this->sectionModel->reorder($orderedIds);
            return $this->response->setJSON(['success' => true, 'message' => 'Urutan section berhasil diperbarui.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Data urutan tidak valid.']);
    }

    public function toggle(int $id)
    {
        $newState = $this->sectionModel->toggleActive($id);
        return $this->response->setJSON([
            'success'   => true,
            'is_active' => $newState,
            'message'   => 'Status section berhasil diubah.',
        ]);
    }

    public function edit(string $key): string
    {
        $section = $this->sectionModel->getByKey($key);
        if (!$section) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Section '{$key}' tidak ditemukan.");
        }

        $data = [
            'title'   => 'Edit Section: ' . esc($section['title']),
            'section' => $section,
            'content' => $section['content'] ?? [],
        ];

        return view('admin/sections/edit', $data);
    }

    public function update(string $key)
    {
        $section = $this->sectionModel->getByKey($key);
        if (!$section) {
            return redirect()->to(site_url('admin/sections'))->with('error', 'Section tidak ditemukan.');
        }

        $title = trim((string) $this->request->getPost('title'));
        $subtitle = trim((string) $this->request->getPost('subtitle'));
        $isActive = $this->request->getPost('is_active') ? 1 : 0;
        $layoutVariant = trim((string) $this->request->getPost('layout_variant')) ?: $section['layout_variant'];

        $content = $section['content'] ?? [];

        // Handle Image File Upload if provided
        $uploadedFile = $this->request->getFile('uploaded_image');
        $uploadedUrl = null;
        if ($uploadedFile && $uploadedFile->isValid() && !$uploadedFile->hasMoved()) {
            $targetDir = FCPATH . 'uploads/media';
            $opt = \App\Libraries\ImageOptimizer::convertToWebp($uploadedFile, $targetDir, 'section-' . $key);
            $uploadedUrl = base_url('uploads/media/' . $opt['filename']);
        }

        // Section-specific field extraction
        switch ($key) {
            case 'hero':
                $content['badge_text']         = trim((string) $this->request->getPost('badge_text'));
                $content['btn1_text']          = trim((string) $this->request->getPost('btn1_text'));
                $content['btn1_link']          = trim((string) $this->request->getPost('btn1_link'));
                $content['btn2_text']          = trim((string) $this->request->getPost('btn2_text'));
                $content['btn2_link']          = trim((string) $this->request->getPost('btn2_link'));
                $content['stat1_value']        = trim((string) $this->request->getPost('stat1_value'));
                $content['stat1_label']        = trim((string) $this->request->getPost('stat1_label'));
                $content['stat2_value']        = trim((string) $this->request->getPost('stat2_value'));
                $content['stat2_label']        = trim((string) $this->request->getPost('stat2_label'));
                $content['stat3_value']        = trim((string) $this->request->getPost('stat3_value'));
                $content['stat3_label']        = trim((string) $this->request->getPost('stat3_label'));
                $content['media_type']         = $this->request->getPost('media_type') ?: 'image';
                $content['focal_x']            = $this->request->getPost('focal_x') ?: 'center';
                $content['focal_y']            = $this->request->getPost('focal_y') ?: 'center';
                $content['aspect_ratio']       = $this->request->getPost('aspect_ratio') ?: '4/3';
                $content['price_badge_prefix'] = trim((string) $this->request->getPost('price_badge_prefix'));
                $content['price_badge_text']   = trim((string) $this->request->getPost('price_badge_text'));
                $content['video_source']       = $this->request->getPost('video_source') ?: 'youtube';
                $content['video_url']          = trim((string) $this->request->getPost('video_url'));
                $content['video_poster']       = trim((string) $this->request->getPost('video_poster'));

                if ($uploadedUrl) {
                    $content['image_url'] = $uploadedUrl;
                } elseif ($this->request->getPost('image_url')) {
                    $content['image_url'] = trim((string) $this->request->getPost('image_url'));
                }
                break;

            case 'properties':
                $content['limit']       = max(1, (int) $this->request->getPost('limit'));
                $content['btn_text']    = trim((string) $this->request->getPost('btn_text'));
                $content['btn_link']    = trim((string) $this->request->getPost('btn_link'));
                $content['show_filter'] = $this->request->getPost('show_filter') ? 1 : 0;
                break;

            case 'about':
                $content['description'] = trim((string) $this->request->getPost('description'));
                $content['highlight_1'] = trim((string) $this->request->getPost('highlight_1'));
                $content['highlight_2'] = trim((string) $this->request->getPost('highlight_2'));
                $content['highlight_3'] = trim((string) $this->request->getPost('highlight_3'));
                $content['experience']  = trim((string) $this->request->getPost('experience'));
                $content['exp_label']   = trim((string) $this->request->getPost('exp_label'));
                $content['focal_x']     = $this->request->getPost('focal_x') ?: 'center';
                $content['focal_y']     = $this->request->getPost('focal_y') ?: 'center';

                if ($uploadedUrl) {
                    $content['image_url'] = $uploadedUrl;
                } elseif ($this->request->getPost('image_url')) {
                    $content['image_url'] = trim((string) $this->request->getPost('image_url'));
                }
                break;

            case 'features':
                $content['intro'] = trim((string) $this->request->getPost('intro'));
                $rawItems = $this->request->getPost('items');
                if (is_array($rawItems)) {
                    $cleanItems = [];
                    foreach ($rawItems as $item) {
                        if (!empty($item['title'])) {
                            $cleanItems[] = [
                                'icon'  => trim((string) ($item['icon'] ?? 'bi-check-circle')),
                                'title' => trim((string) $item['title']),
                                'desc'  => trim((string) ($item['desc'] ?? '')),
                            ];
                        }
                    }
                    if (!empty($cleanItems)) {
                        $content['items'] = $cleanItems;
                    }
                }
                break;

            case 'gallery':
                $rawGallery = $this->request->getPost('gallery_items');
                if (is_array($rawGallery)) {
                    $cleanGallery = [];
                    foreach ($rawGallery as $gItem) {
                        if (!empty($gItem['image'])) {
                            $cleanGallery[] = [
                                'image'   => trim((string) $gItem['image']),
                                'caption' => trim((string) ($gItem['caption'] ?? '')),
                                'tag'     => trim((string) ($gItem['tag'] ?? 'Umum')),
                            ];
                        }
                    }
                    if (!empty($cleanGallery)) {
                        $content['items'] = $cleanGallery;
                    }
                }
                break;

            case 'siteplan':
                $content['description'] = trim((string) $this->request->getPost('description'));
                $content['btn_text']    = trim((string) $this->request->getPost('btn_text'));
                $content['btn_link']    = trim((string) $this->request->getPost('btn_link'));
                break;

            case 'promo':
                $content['badge']    = trim((string) $this->request->getPost('badge'));
                $content['desc']     = trim((string) $this->request->getPost('desc'));
                $content['btn_text'] = trim((string) $this->request->getPost('btn_text'));
                $content['focal_x']  = $this->request->getPost('focal_x') ?: 'center';
                $content['focal_y']  = $this->request->getPost('focal_y') ?: 'center';

                if ($uploadedUrl) {
                    $content['image_url'] = $uploadedUrl;
                } elseif ($this->request->getPost('image_url')) {
                    $content['image_url'] = trim((string) $this->request->getPost('image_url'));
                }
                break;

            case 'cta':
                $content['desc']      = trim((string) $this->request->getPost('desc'));
                $content['btn1_text'] = trim((string) $this->request->getPost('btn1_text'));
                $content['btn1_type'] = $this->request->getPost('btn1_type') ?: 'whatsapp';
                $content['btn2_text'] = trim((string) $this->request->getPost('btn2_text'));
                $content['btn2_link'] = trim((string) $this->request->getPost('btn2_link'));
                $content['bg_type']   = $this->request->getPost('bg_type') ?: 'gradient';
                $content['bg_video']  = trim((string) $this->request->getPost('bg_video'));

                if ($uploadedUrl) {
                    $content['bg_image'] = $uploadedUrl;
                } elseif ($this->request->getPost('bg_image')) {
                    $content['bg_image'] = trim((string) $this->request->getPost('bg_image'));
                }
                break;
        }

        // Extract Common Section Styling (Typography, Colors, Shadow, Photo Overlay & Background)
        $content['styling'] = [
            'bg_type'            => $this->request->getPost('sec_bg_type') ?: 'default',
            'bg_color'           => trim((string) $this->request->getPost('sec_bg_color')),
            'gradient_from'      => trim((string) $this->request->getPost('sec_gradient_from')),
            'gradient_to'        => trim((string) $this->request->getPost('sec_gradient_to')),
            'gradient_angle'     => trim((string) $this->request->getPost('sec_gradient_angle') ?: '135deg'),
            'text_mode'          => $this->request->getPost('sec_text_mode') ?: 'auto',
            'custom_title_color' => trim((string) $this->request->getPost('sec_custom_title_color')),
            'custom_text_color'  => trim((string) $this->request->getPost('sec_custom_text_color')),
            'font_family'        => $this->request->getPost('sec_font_family') ?: 'default',
            'title_font_weight'  => $this->request->getPost('sec_title_font_weight') ?: 'default',
            'title_transform'    => $this->request->getPost('sec_title_transform') ?: 'default',
            'text_shadow'        => $this->request->getPost('sec_text_shadow') ?: 'none',
            'overlay_opacity'    => $this->request->getPost('sec_overlay_opacity') ?: 'default',
            'border_radius'      => $this->request->getPost('sec_border_radius') ?: 'default',
        ];

        $this->sectionModel->saveSectionData($key, [
            'title'          => $title,
            'subtitle'       => $subtitle,
            'is_active'      => $isActive,
            'layout_variant' => $layoutVariant,
            'content'        => $content,
        ]);

        return redirect()->to(site_url('admin/sections'))->with('success', "Perubahan section '{$title}' berhasil disimpan.");
    }
}
