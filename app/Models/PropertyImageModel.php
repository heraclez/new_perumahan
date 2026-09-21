<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyImageModel extends Model
{
    protected $table            = 'property_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'image_name',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getImagesByPropertyId(int $propertyId): array
    {
        return $this->where('property_id', $propertyId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }
}
