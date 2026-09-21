<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Property extends BaseController
{
    protected PropertyModel $propertyModel;

    public function __construct()
    {
        $this->propertyModel = new PropertyModel();
    }

    /**
     * Property Catalog listing with filters
     */
    public function index(): string
    {
        $keyword  = $this->request->getGet('keyword');
        $status   = $this->request->getGet('status');
        $promo    = $this->request->getGet('promo');
        $minPrice = $this->request->getGet('min_price');
        $maxPrice = $this->request->getGet('max_price');

        if ($promo === '1' && empty($status)) {
            $status = 'Promo';
        }

        $filter = [
            'keyword'   => $keyword,
            'status'    => $status,
            'promo'     => $promo,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
        ];

        $properties = $this->propertyModel->getPropertiesWithThumbnail($filter);

        $data = [
            'title'      => 'Katalog Properti & Tipe Rumah - ' . ($this->settings['company_name'] ?? 'Grand Harmoni Residence'),
            'settings'   => $this->settings,
            'properties' => $properties,
            'filters'    => $filter,
        ];

        return view('frontend/properties/index', $data);
    }

    /**
     * Property Detail by slug with SEO & Open Graph support
     */
    public function detail(string $slug): string
    {
        $property = $this->propertyModel->getPropertyBySlug($slug);

        if (!$property) {
            throw PageNotFoundException::forPageNotFound("Tipe properti dengan slug '{$slug}' tidak ditemukan.");
        }

        // Get thumbnail for Open Graph & WhatsApp share
        $ogImage = base_url('assets/images/default-house.jpg');
        if (!empty($property['images']) && isset($property['images'][0]['image_name'])) {
            $firstImg = $property['images'][0]['image_name'];
            $ogImage = str_starts_with($firstImg, 'http') ? $firstImg : base_url('uploads/properties/' . $firstImg);
        }

        // Related properties
        $relatedProperties = $this->propertyModel->getPropertiesWithThumbnail(['status' => 'Tersedia'], 3);

        // Fetch all mapped kavling pins in siteplan for this property
        $pinModel   = new \App\Models\SiteplanPinModel();
        $mappedPins = $pinModel->getPinsByPropertyId((int) $property['id']);

        $data = [
            'title'             => $property['title'] . ' - ' . ($this->settings['company_name'] ?? 'Grand Harmoni'),
            'meta_description'  => character_limiter(strip_tags($property['deskripsi'] ?? ''), 160),
            'og_title'          => $property['title'] . ' | Harga Rp ' . number_format($property['harga'], 0, ',', '.'),
            'og_description'    => 'Spesifikasi: ' . $property['spesifikasi_kamar'] . ', LT ' . $property['luas_tanah'] . 'm2 / LB ' . $property['luas_bangunan'] . 'm2. Klik untuk simulasi KPR & brosur lengkap.',
            'og_image'          => $ogImage,
            'og_url'            => current_url(),
            'settings'          => $this->settings,
            'property'          => $property,
            'mappedPins'        => $mappedPins,
            'relatedProperties' => $relatedProperties,
        ];

        return view('frontend/properties/detail', $data);
    }
}
