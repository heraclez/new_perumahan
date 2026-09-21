<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteplanPinModel extends Model
{
    protected $table            = 'siteplan_pins';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siteplan_id',
        'property_id',
        'kavling_number',
        'status',
        'pos_x',
        'pos_y',
        'notes',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'siteplan_id'    => 'required|integer',
        'property_id'    => 'required|integer',
        'kavling_number' => 'required|min_length[1]|max_length[50]',
        'status'         => 'required|in_list[Tersedia,Booking,Terjual]',
        'pos_x'          => 'required|numeric',
        'pos_y'          => 'required|numeric',
    ];

    /**
     * Get all pins for a siteplan with joined property details
     */
    public function getPinsWithProperty(int $siteplanId): array
    {
        return $this->select('siteplan_pins.*, properties.title as property_title, properties.slug as property_slug, properties.harga as property_harga, properties.luas_tanah, properties.luas_bangunan, properties.spesifikasi_kamar, properties.is_promo, properties.promo_title, properties.promo_desc, (SELECT image_name FROM property_images WHERE property_images.property_id = properties.id ORDER BY id ASC LIMIT 1) as primary_image')
                    ->join('properties', 'properties.id = siteplan_pins.property_id', 'left')
                    ->where('siteplan_pins.siteplan_id', $siteplanId)
                    ->orderBy('siteplan_pins.id', 'ASC')
                    ->findAll();
    }

    /**
     * Get all mapped kavling pins for a specific property
     */
    public function getPinsByPropertyId(int $propertyId): array
    {
        return $this->select('siteplan_pins.*, siteplans.title as siteplan_title, siteplans.is_active as siteplan_active')
                    ->join('siteplans', 'siteplans.id = siteplan_pins.siteplan_id', 'left')
                    ->where('siteplan_pins.property_id', $propertyId)
                    ->where('siteplans.deleted_at', null)
                    ->orderBy('siteplan_pins.kavling_number', 'ASC')
                    ->findAll();
    }
}
