<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Brochure extends BaseController
{
    /**
     * Download property brochure with fallback to global brochure.
     */
    public function download(int $propertyId)
    {
        $propertyModel = new PropertyModel();
        $property = $propertyModel->getPropertyWithImages($propertyId);

        $filePath = null;
        $fileName = 'Brosur_Properti.pdf';

        // 1. Check if property has specific brochure
        if ($property && !empty($property['brosur_pdf'])) {
            $specificFile = FCPATH . 'uploads/brochures/' . $property['brosur_pdf'];
            if (is_file($specificFile)) {
                $filePath = $specificFile;
                $fileName = 'Brosur_' . url_title($property['title'], '_', true) . '.pdf';
            }
        }

        // 2. Fallback to global brochure from settings
        if (!$filePath) {
            $globalPdf = $this->settings['global_brochure_pdf'] ?? '';
            if (!empty($globalPdf)) {
                if (is_file(FCPATH . 'uploads/brochures/' . $globalPdf)) {
                    $filePath = FCPATH . 'uploads/brochures/' . $globalPdf;
                    $fileName = 'Brosur_Katalog_Perumahan_Lengkap.pdf';
                } elseif (is_file(FCPATH . ltrim($globalPdf, '/'))) {
                    $filePath = FCPATH . ltrim($globalPdf, '/');
                    $fileName = 'Brosur_Katalog_Perumahan_Lengkap.pdf';
                }
            }
        }

        // 3. Fallback to any available PDF in brochures directory
        if (!$filePath) {
            $existingPdfs = glob(FCPATH . 'uploads/brochures/*.pdf');
            if (!empty($existingPdfs) && is_file($existingPdfs[0])) {
                $filePath = $existingPdfs[0];
                $fileName = $property 
                    ? 'Brosur_' . url_title($property['title'], '_', true) . '.pdf'
                    : 'Brosur_Katalog_Perumahan.pdf';
            }
        }

        // If file exists, force direct browser download
        if ($filePath && is_file($filePath)) {
            return $this->response->download($filePath, null)->setFileName($fileName);
        }

        // If no PDF file exists on server, display the Digital E-Brochure Sheet (ready to print/save as PDF)
        return view('frontend/brochure_fallback', [
            'title'    => 'E-Brosur Digital: ' . ($property['title'] ?? 'Katalog Perumahan'),
            'property' => $property,
            'settings' => $this->settings,
        ]);
    }

    /**
     * Download global catalog brochure
     */
    public function downloadGlobal()
    {
        $filePath = null;

        $globalPdf = $this->settings['global_brochure_pdf'] ?? '';
        if (!empty($globalPdf)) {
            $globalFile = FCPATH . 'uploads/brochures/' . $globalPdf;
            if (is_file($globalFile)) {
                $filePath = $globalFile;
            }
        }

        if (!$filePath) {
            $existingPdfs = glob(FCPATH . 'uploads/brochures/*.pdf');
            if (!empty($existingPdfs) && is_file($existingPdfs[0])) {
                $filePath = $existingPdfs[0];
            }
        }

        if ($filePath && is_file($filePath)) {
            return $this->response->download($filePath, null)->setFileName('Brosur_Katalog_Perumahan_Lengkap.pdf');
        }

        return view('frontend/brochure_fallback', [
            'title'    => 'Katalog Brosur Digital',
            'property' => null,
            'settings' => $this->settings,
        ]);
    }
}
