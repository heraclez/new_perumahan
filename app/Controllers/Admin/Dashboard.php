<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\LeadModel;
use App\Models\PropertyModel;
use App\Models\VisitorModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $propertyModel = new PropertyModel();
        $leadModel     = new LeadModel();
        $adminModel    = new AdminModel();

        // Statistics
        $totalProperties     = $propertyModel->countAllResults();
        $availableProperties = $propertyModel->where('status', 'Tersedia')->countAllResults();
        $bookingProperties   = $propertyModel->where('status', 'Booking')->countAllResults();
        $soldProperties      = $propertyModel->where('status', 'Terjual')->countAllResults();
        $totalLeads          = $leadModel->countAllResults();
        $isDeveloper         = session()->get('admin_role') === 'Developer';
        $totalAdmins         = $isDeveloper 
            ? $adminModel->countAllResults() 
            : $adminModel->where('role !=', 'Developer')->countAllResults();

        // Recent Data
        $recentLeads      = $leadModel->getLeadsWithProperty();
        $recentProperties = $propertyModel->getPropertiesWithThumbnail([], 5);

        $data = [
            'title'               => 'Dashboard Administrasi',
            'settings'            => $this->settings,
            'totalProperties'     => $totalProperties,
            'availableProperties' => $availableProperties,
            'bookingProperties'   => $bookingProperties,
            'soldProperties'      => $soldProperties,
            'totalLeads'          => $totalLeads,
            'totalAdmins'         => $totalAdmins,
            'recentLeads'         => array_slice($recentLeads, 0, 5),
            'recentProperties'    => $recentProperties,
        ];

        return view('admin/dashboard/index', $data);
    }
}
