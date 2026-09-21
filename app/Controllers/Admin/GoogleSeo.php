<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VisitorModel;

class GoogleSeo extends BaseController
{
    protected VisitorModel $visitorModel;

    public function __construct()
    {
        $this->visitorModel = new VisitorModel();
    }

    /**
     * Display Google & SEO Suite (Visitor Analytics + Google Integration)
     */
    public function index(): string
    {
        // Visitor & SEO Traffic Analytics
        $visitorSummary = $this->visitorModel->getSummaryCounts();
        $dailyStats     = $this->visitorModel->getDailyStats(14);
        $sourceStats    = $this->visitorModel->getSourceBreakdown();
        $topPages       = $this->visitorModel->getTopPages(5);
        $deviceStats    = $this->visitorModel->getDeviceBreakdown();

        $data = [
            'title'          => 'Google & SEO Suite',
            'settings'       => $this->settings,
            'visitorSummary' => $visitorSummary,
            'dailyStats'     => $dailyStats,
            'sourceStats'    => $sourceStats,
            'topPages'       => $topPages,
            'deviceStats'    => $deviceStats,
        ];

        return view('admin/google_seo/index', $data);
    }

    /**
     * Update Google Analytics, Search Console, and SEO settings
     */
    public function update()
    {
        $rules = [
            'google_analytics_id'        => 'permit_empty|max_length[50]',
            'google_search_console_code' => 'permit_empty|max_length[150]',
            'meta_keywords'              => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'google_analytics_id'        => trim((string) $this->request->getPost('google_analytics_id')),
            'google_search_console_code' => trim((string) $this->request->getPost('google_search_console_code')),
            'meta_keywords'              => trim((string) $this->request->getPost('meta_keywords')),
        ];

        $this->settingModel->updateSettingsBatch($payload);
        $this->settingModel->clearCache();

        return redirect()->to(site_url('admin/google-seo'))->with('success', 'Konfigurasi Google & Metadata SEO berhasil disimpan dan cache telah diperbarui.');
    }
}
