<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table            = 'properties';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'harga',
        'deskripsi',
        'spesifikasi_kamar',
        'luas_tanah',
        'luas_bangunan',
        'status',
        'brosur_pdf',
        'is_promo',
        'promo_title',
        'promo_desc',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title'             => 'required|min_length[3]|max_length[255]',
        'slug'              => 'required|alpha_dash|min_length[3]|max_length[255]|is_unique[properties.slug,id,{id}]',
        'harga'             => 'required|numeric|greater_than_equal_to[0]',
        'luas_tanah'        => 'required|integer|greater_than_equal_to[0]',
        'luas_bangunan'     => 'required|integer|greater_than_equal_to[0]',
        'status'            => 'required|in_list[Tersedia,Booking,Terjual]',
    ];

    /**
     * Compute dynamic property status and badges from mapped siteplan pins
     */
    public function computePropertyStatus(array $property): array
    {
        $total     = (int) ($property['total_kavling'] ?? 0);
        $available = (int) ($property['available_kavling'] ?? 0);
        $booking   = (int) ($property['booking_kavling'] ?? 0);
        $sold      = (int) ($property['sold_kavling'] ?? 0);

        if ($total === 0) {
            // Belum ada kavling terplot di siteplan
            $property['computed_status'] = 'Tersedia';
            $property['status_label']    = 'Tersedia (Ready)';
            $property['status_badge']    = 'bg-success';
            $property['stock_text']      = 'Unit Ready Order';
        } elseif ($available > 0) {
            // Masih ada unit kavling tersedia
            $property['computed_status'] = 'Tersedia';
            $property['status_label']    = "Tersedia (Sisa {$available} Unit)";
            $property['status_badge']    = 'bg-success';
            $property['stock_text']      = "Sisa {$available} dari {$total} Unit";
        } elseif ($booking > 0) {
            // Semua unit sedang dibooking
            $property['computed_status'] = 'Booking';
            $property['status_label']    = "Full Booking ({$booking} Unit)";
            $property['status_badge']    = 'bg-warning text-dark';
            $property['stock_text']      = "Full Booking ({$booking} Unit)";
        } else {
            // Semua unit sudah terjual
            $property['computed_status'] = 'Terjual';
            $property['status_label']    = 'Sold Out (Habis)';
            $property['status_badge']    = 'bg-danger';
            $property['stock_text']      = 'Sold Out (0 Tersedia)';
        }

        return $property;
    }

    /**
     * Get properties list with primary thumbnail image and dynamic stock counts.
     */
    public function getPropertiesWithThumbnail(array $filter = [], int $limit = 0, int $offset = 0): array
    {
        $builder = $this->builder();
        $builder->select('properties.*, 
            (SELECT image_name FROM property_images WHERE property_images.property_id = properties.id ORDER BY id ASC LIMIT 1) as primary_image,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id) as total_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Tersedia") as available_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Booking") as booking_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Terjual") as sold_kavling
        ');
        $builder->where('properties.deleted_at', null);

        if (!empty($filter['status'])) {
            if (strcasecmp($filter['status'], 'Promo') === 0) {
                $builder->where('properties.is_promo', 1);
            } elseif (strcasecmp($filter['status'], 'Tersedia') === 0) {
                $builder->where('((SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Tersedia") > 0 OR (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id) = 0)');
            } elseif (strcasecmp($filter['status'], 'Booking') === 0) {
                $builder->where('((SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Tersedia") = 0 AND (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Booking") > 0)');
            } elseif (strcasecmp($filter['status'], 'Terjual') === 0) {
                $builder->where('((SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Tersedia") = 0 AND (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Booking") = 0 AND (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Terjual") > 0)');
            }
        }

        if (isset($filter['is_promo']) && $filter['is_promo'] !== '') {
            $builder->where('properties.is_promo', (int) $filter['is_promo']);
        }

        if (!empty($filter['keyword'])) {
            $builder->groupStart()
                    ->like('properties.title', $filter['keyword'])
                    ->orLike('properties.deskripsi', $filter['keyword'])
                    ->orLike('properties.spesifikasi_kamar', $filter['keyword'])
                    ->groupEnd();
        }

        if (!empty($filter['min_price'])) {
            $builder->where('properties.harga >=', (float) $filter['min_price']);
        }

        if (!empty($filter['max_price'])) {
            $builder->where('properties.harga <=', (float) $filter['max_price']);
        }

        $builder->orderBy('properties.created_at', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        $results = $builder->get()->getResultArray();
        return array_map([$this, 'computePropertyStatus'], $results);
    }

    /**
     * Get single property detail with gallery images and stock by slug.
     */
    public function getPropertyBySlug(string $slug): ?array
    {
        $builder = $this->builder();
        $builder->select('properties.*,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id) as total_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Tersedia") as available_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Booking") as booking_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Terjual") as sold_kavling
        ');
        $builder->where('properties.slug', $slug);
        $builder->where('properties.deleted_at', null);

        $property = $builder->get()->getRowArray();
        if (!$property) {
            return null;
        }

        $property = $this->computePropertyStatus($property);

        $imageModel = new PropertyImageModel();
        $property['images'] = $imageModel->getImagesByPropertyId((int) $property['id']);
        
        return $property;
    }

    /**
     * Get single property by ID with gallery images and stock.
     */
    public function getPropertyWithImages(int $id): ?array
    {
        $builder = $this->builder();
        $builder->select('properties.*,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id) as total_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Tersedia") as available_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Booking") as booking_kavling,
            (SELECT COUNT(*) FROM siteplan_pins WHERE siteplan_pins.property_id = properties.id AND siteplan_pins.status = "Terjual") as sold_kavling
        ');
        $builder->where('properties.id', $id);
        $builder->where('properties.deleted_at', null);

        $property = $builder->get()->getRowArray();
        if (!$property) {
            return null;
        }

        $property = $this->computePropertyStatus($property);

        $imageModel = new PropertyImageModel();
        $property['images'] = $imageModel->getImagesByPropertyId($id);

        return $property;
    }
}
