<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteplanModel extends Model
{
    protected $table            = 'siteplans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'image',
        'description',
        'is_active',
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
        'title' => 'required|min_length[3]|max_length[255]',
    ];

    /**
     * Get all active siteplans for frontend navigation and display
     * 
     * @return array
     */
    public function getActiveSiteplans(): array
    {
        return $this->where('is_active', 1)
                    ->where('deleted_at', null)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Get single active siteplan (by ID or default to first active)
     */
    public function getActiveSiteplan(?int $id = null): ?array
    {
        $builder = $this->where('is_active', 1)
                        ->where('deleted_at', null);

        if ($id !== null && $id > 0) {
            $builder->where('id', $id);
            $result = $builder->first();
            if ($result) {
                return $result;
            }
        }

        return $this->where('is_active', 1)
                    ->where('deleted_at', null)
                    ->orderBy('id', 'ASC')
                    ->first();
    }

    /**
     * Toggle active status of a siteplan
     */
    public function toggleActive(int $id): bool
    {
        $siteplan = $this->find($id);
        if (!$siteplan) {
            return false;
        }

        $newStatus = empty($siteplan['is_active']) ? 1 : 0;
        return (bool) $this->update($id, ['is_active' => $newStatus]);
    }
}
