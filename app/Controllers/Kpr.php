<?php

namespace App\Controllers;

use App\Models\PropertyModel;

class Kpr extends BaseController
{
    public function index(): string
    {
        $propertyModel = new PropertyModel();
        $properties = $propertyModel->getPropertiesWithThumbnail(['status' => 'Tersedia']);

        $data = [
            'title'      => 'Kalkulator Simulasi KPR Rumah - ' . ($this->settings['company_name'] ?? 'Grand Harmoni'),
            'settings'   => $this->settings,
            'properties' => $properties,
        ];

        return view('frontend/kpr_calculator', $data);
    }
}
