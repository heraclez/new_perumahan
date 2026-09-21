<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\SiteplanModel;
use App\Models\SiteplanPinModel;

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
     * Public Interactive Siteplan Page
     *
     * @param int|null $id Specific siteplan ID to display
     */
    public function index(?int $id = null): string
    {
        $activeSiteplans = $this->siteplanModel->getActiveSiteplans();
        $siteplan        = $this->siteplanModel->getActiveSiteplan($id);

        $pins   = [];
        $counts = [
            'total'    => 0,
            'tersedia' => 0,
            'booking'  => 0,
            'terjual'  => 0,
        ];

        if ($siteplan) {
            $pins            = $this->pinModel->getPinsWithProperty((int) $siteplan['id']);
            $counts['total'] = count($pins);
            foreach ($pins as $p) {
                $st = strtolower($p['status']);
                if (isset($counts[$st])) {
                    $counts[$st]++;
                }
            }
        }

        $properties = $this->propertyModel->where('deleted_at', null)->orderBy('title', 'ASC')->findAll();

        $pageTitle = ($siteplan ? esc($siteplan['title']) . ' - ' : '') . 'Master Siteplan & Denah Kavling Interaktif - ' . ($this->settings['company_name'] ?? 'Grand Harmoni');

        $data = [
            'title'            => $pageTitle,
            'meta_description' => 'Eksplorasi denah dan posisi kavling rumah secara interaktif di ' . ($this->settings['company_name'] ?? 'Grand Harmoni') . '. Cek status ketersediaan unit real-time.',
            'settings'         => $this->settings,
            'siteplan'         => $siteplan,
            'activeSiteplans'  => $activeSiteplans,
            'pins'             => $pins,
            'counts'           => $counts,
            'properties'       => $properties,
        ];

        return view('frontend/siteplan/index', $data);
    }
}
