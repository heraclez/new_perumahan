<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    public function index(): string
    {
        $settings = $this->settingModel->getAllSettings();

        $data = [
            'title'    => 'Pengaturan Website & CMS Global',
            'settings' => $settings,
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $rules = [
            'company_name'        => 'required|min_length[3]|max_length[150]',
            'company_tagline'     => 'permit_empty|max_length[255]',
            'company_address'     => 'permit_empty',
            'company_phone'       => 'permit_empty|max_length[50]',
            'company_whatsapp'    => 'required|min_length[8]|max_length[30]',
            'company_email'       => 'permit_empty|valid_email',
            'primary_color'       => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'secondary_color'     => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'site_theme_preset'   => 'permit_empty|in_list[modern-residential,soft-brutalist,modern-luxury,tropical-modern,urban-contemporary,editorial-architecture,industrialist,brutalist]',
            'accent_color'        => 'permit_empty|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'font_family'         => 'permit_empty|max_length[50]',
            'heading_font'        => 'permit_empty|max_length[50]',
            'body_font'           => 'permit_empty|max_length[50]',
            'gradient_from'       => 'permit_empty|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'gradient_to'         => 'permit_empty|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'gradient_angle'      => 'permit_empty|max_length[20]',
            'border_radius'       => 'permit_empty|max_length[20]',
            'promo_modal_active'  => 'permit_empty|in_list[0,1]',
            'promo_modal_title'   => 'permit_empty|max_length[200]',
            'promo_modal_desc'    => 'permit_empty',
            'promo_modal_btn_text'=> 'permit_empty|max_length[100]',
            'va_name'              => 'permit_empty|max_length[100]',
            'va_phone'             => 'permit_empty|max_length[30]',
            'n8n_webhook_url'      => 'permit_empty|valid_url',
            'google_analytics_id'        => 'permit_empty|max_length[50]',
            'google_search_console_code' => 'permit_empty|max_length[150]',
            'meta_keywords'              => 'permit_empty|max_length[255]',
            'global_brochure_pdf'  => 'permit_empty|ext_in[global_brochure_pdf,pdf]|max_size[global_brochure_pdf,10240]',
            'promo_image_file'     => 'permit_empty|is_image[promo_image_file]|mime_in[promo_image_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[promo_image_file,3072]',
            'company_logo'         => 'permit_empty|is_image[company_logo]|mime_in[company_logo,image/jpg,image/jpeg,image/png,image/webp,image/svg+xml]|max_size[company_logo,3072]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // WhatsApp number cleanup
        $wa = preg_replace('/[^0-9]/', '', (string) $this->request->getPost('company_whatsapp'));
        if (str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }

        $vaPhone = preg_replace('/[^0-9]/', '', (string) $this->request->getPost('va_phone'));
        if (str_starts_with($vaPhone, '0')) {
            $vaPhone = '62' . substr($vaPhone, 1);
        }

        $updateData = [
            'company_name'         => trim((string) $this->request->getPost('company_name')),
            'company_tagline'      => trim((string) $this->request->getPost('company_tagline')),
            'company_address'      => trim((string) $this->request->getPost('company_address')),
            'company_phone'        => trim((string) $this->request->getPost('company_phone')),
            'company_whatsapp'     => $wa,
            'company_email'        => trim((string) $this->request->getPost('company_email')),
            'company_about'        => trim((string) $this->request->getPost('company_about')),
            'primary_color'        => trim((string) $this->request->getPost('primary_color')),
            'secondary_color'      => trim((string) $this->request->getPost('secondary_color')),
            'accent_color'         => trim((string) $this->request->getPost('accent_color')),
            'font_family'          => trim((string) $this->request->getPost('font_family') ?: 'default'),
            'heading_font'         => trim((string) $this->request->getPost('heading_font') ?: 'default'),
            'body_font'            => trim((string) $this->request->getPost('body_font') ?: 'default'),
            'gradient_from'        => trim((string) $this->request->getPost('gradient_from') ?: $this->request->getPost('primary_color')),
            'gradient_to'          => trim((string) $this->request->getPost('gradient_to') ?: $this->request->getPost('secondary_color')),
            'gradient_angle'       => trim((string) $this->request->getPost('gradient_angle') ?: '135deg'),
            'border_radius'        => trim((string) $this->request->getPost('border_radius') ?: 'default'),
            'site_theme_preset'    => $this->request->getPost('site_theme_preset') ?? 'modern-residential',
            'promo_modal_active'   => $this->request->getPost('promo_modal_active') ? '1' : '0',
            'promo_modal_title'    => trim((string) $this->request->getPost('promo_modal_title')),
            'promo_modal_desc'     => trim((string) $this->request->getPost('promo_modal_desc')),
            'promo_modal_btn_text' => trim((string) $this->request->getPost('promo_modal_btn_text')),
            'va_name'              => trim((string) $this->request->getPost('va_name')),
            'va_phone'             => $vaPhone,
            'chat_provider'        => 'n8n',
            'n8n_webhook_url'      => trim((string) $this->request->getPost('n8n_webhook_url')),
            'google_analytics_id'        => trim((string) $this->request->getPost('google_analytics_id')),
            'google_search_console_code' => trim((string) $this->request->getPost('google_search_console_code')),
            'meta_keywords'              => trim((string) $this->request->getPost('meta_keywords')),
        ];

        // Helper to normalize file manager URLs to local relative path or clean filename
        $normalizeMedia = function (string $rawUrl, string $defaultFolder = '') {
            $rawUrl = trim($rawUrl);
            if (empty($rawUrl)) return '';
            $baseUrl = rtrim(base_url(), '/') . '/';
            if (str_starts_with($rawUrl, $baseUrl)) {
                $rawUrl = substr($rawUrl, strlen($baseUrl));
            }
            $rawUrl = ltrim($rawUrl, '/');
            if (!empty($defaultFolder) && str_starts_with($rawUrl, 'uploads/' . $defaultFolder . '/')) {
                $rawUrl = substr($rawUrl, strlen('uploads/' . $defaultFolder . '/'));
            }
            return $rawUrl;
        };

        // Company Logo upload, file manager selection, or removal
        $logoFile = $this->request->getFile('company_logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $logoFileName = $logoFile->getRandomName();
            $logoFile->move(FCPATH . 'uploads/settings', $logoFileName);
            $updateData['company_logo'] = $logoFileName;
        } elseif ($this->request->getPost('delete_company_logo') === '1') {
            $updateData['company_logo'] = '';
        } else {
            $selectedLogo = (string) $this->request->getPost('company_logo_url');
            if ($selectedLogo !== '') {
                $updateData['company_logo'] = $normalizeMedia($selectedLogo, 'settings');
            }
        }

        // Promo image file upload, file manager URL, or removal
        $promoFile = $this->request->getFile('promo_image_file');
        if ($promoFile && $promoFile->isValid() && !$promoFile->hasMoved()) {
            $targetDir = FCPATH . 'uploads/settings';
            $opt = \App\Libraries\ImageOptimizer::convertToWebp($promoFile, $targetDir, 'promo-modal');
            $updateData['promo_modal_image'] = base_url('uploads/settings/' . $opt['filename']);
        } elseif ($this->request->getPost('delete_promo_image') === '1') {
            $updateData['promo_modal_image'] = '';
        } else {
            $promoImgUrl = trim((string) $this->request->getPost('promo_modal_image_url'));
            if ($promoImgUrl !== '') {
                $updateData['promo_modal_image'] = $promoImgUrl;
            }
        }

        // Global Brochure PDF upload, file manager selection, or removal
        $brochureFile = $this->request->getFile('global_brochure_pdf');
        if ($brochureFile && $brochureFile->isValid() && !$brochureFile->hasMoved()) {
            $brochureFileName = $brochureFile->getRandomName();
            $brochureFile->move(FCPATH . 'uploads/brochures', $brochureFileName);
            $updateData['global_brochure_pdf'] = $brochureFileName;
        } elseif ($this->request->getPost('delete_global_brochure_pdf') === '1') {
            $updateData['global_brochure_pdf'] = '';
        } else {
            $selectedBrochure = (string) $this->request->getPost('global_brochure_pdf_url');
            if ($selectedBrochure !== '') {
                $updateData['global_brochure_pdf'] = $normalizeMedia($selectedBrochure, 'brochures');
            }
        }

        // Batch update and clear cache
        $this->settingModel->updateSettingsBatch($updateData);

        return redirect()->to(site_url('admin/settings'))->with('success', 'Pengaturan website berhasil disimpan dan cache telah diperbarui.');
    }
}
