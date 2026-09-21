<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\SectionModel;
use App\Models\SiteplanModel;

class Home extends BaseController
{
    public function index(): string
    {
        $propertyModel = new PropertyModel();
        $sectionModel  = new SectionModel();
        $siteplanModel = new SiteplanModel();

        // 1. Fetch Active Homepage Sections in Sorted Order
        $sections = [];
        try {
            $sections = $sectionModel->getSections(true);
        } catch (\Throwable $e) {
            log_message('notice', 'Using fallback sections: ' . $e->getMessage());
        }

        // 2. Fetch Featured Properties with thumbnails and status
        $propertyLimit = 6;
        foreach ($sections as $s) {
            if ($s['section_key'] === 'properties' && !empty($s['content']['limit'])) {
                $propertyLimit = (int) $s['content']['limit'];
            }
        }

        $featuredProperties = [];
        try {
            $featuredProperties = $propertyModel->getPropertiesWithThumbnail([], $propertyLimit);
        } catch (\Throwable $e) {
            log_message('notice', 'Using fallback property data: ' . $e->getMessage());
            $featuredProperties = [
                [
                    'id'          => 1,
                    'title'       => 'The Lumina Sanctuary',
                    'slug'        => 'the-lumina-sanctuary',
                    'cluster_name'=> 'Nordic Forest',
                    'harga'       => 1850000000,
                    'luas_tanah'  => 120,
                    'luas_bangunan'=> 145,
                    'spesifikasi_kamar' => '4 KT / 3 KM',
                    'primary_image'  => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
                    'status'      => 'Tersedia',
                    'status_label'=> 'Tersedia',
                    'status_badge'=> 'bg-success',
                ],
                [
                    'id'          => 2,
                    'title'       => 'The Horizon Villa',
                    'slug'        => 'the-horizon-villa',
                    'cluster_name'=> 'Zenith Garden',
                    'harga'       => 2450000000,
                    'luas_tanah'  => 160,
                    'luas_bangunan'=> 190,
                    'spesifikasi_kamar' => '5 KT / 4 KM',
                    'primary_image'  => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
                    'status'      => 'Tersedia',
                    'status_label'=> 'Tersedia',
                    'status_badge'=> 'bg-success',
                ],
            ];
        }

        // 3. Fetch Master Siteplan for siteplan section
        $activeSiteplans = [];
        try {
            $activeSiteplans = $siteplanModel->getActiveSiteplans();
        } catch (\Throwable $e) {
            log_message('notice', 'Siteplans fetch notice: ' . $e->getMessage());
        }

        // Extract SEO Meta
        $heroTitle = null;
        $heroDesc = null;
        foreach ($sections as $sec) {
            if ($sec['section_key'] === 'hero') {
                $heroTitle = $sec['title'] ?? null;
                $heroDesc = $sec['subtitle'] ?? null;
                break;
            }
        }

        $data = [
            'title'              => $this->settings['company_name'] ?? 'Grand Harmoni Residence',
            'meta_description'   => $heroDesc ?? ($this->settings['company_tagline'] ?? 'Hunian Mewah, Asri, dan Strategis untuk Keluarga Idaman'),
            'settings'           => $this->settings,
            'sections'           => $sections,
            'featuredProperties' => $featuredProperties,
            'activeSiteplans'    => $activeSiteplans,
        ];

        return view('frontend/home', $data);
    }
}
