<?php

namespace App\Models;

use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table            = 'homepage_sections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'section_key',
        'title',
        'subtitle',
        'is_active',
        'sort_order',
        'layout_variant',
        'content_json',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const CACHE_KEY = 'homepage_sections_cache';

    /**
     * Get all sections ordered by sort_order
     */
    public function getSections(bool $activeOnly = false): array
    {
        $builder = $this->orderBy('sort_order', 'ASC');
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        $rows = $builder->findAll();

        foreach ($rows as &$row) {
            if (!empty($row['content_json'])) {
                $decoded = json_decode($row['content_json'], true);
                $row['content'] = is_array($decoded) ? $decoded : [];
            } else {
                $row['content'] = [];
            }
        }

        return $rows;
    }

    /**
     * Get single section by key with decoded content
     */
    public function getByKey(string $key): ?array
    {
        $row = $this->where('section_key', $key)->first();
        if ($row) {
            if (!empty($row['content_json'])) {
                $decoded = json_decode($row['content_json'], true);
                $row['content'] = is_array($decoded) ? $decoded : [];
            } else {
                $row['content'] = [];
            }
        }
        return $row;
    }

    /**
     * Save/update section data
     */
    public function saveSectionData(string $key, array $data): bool
    {
        $existing = $this->where('section_key', $key)->first();
        if (!$existing) {
            return false;
        }

        $payload = [];
        if (isset($data['title'])) {
            $payload['title'] = $data['title'];
        }
        if (isset($data['subtitle'])) {
            $payload['subtitle'] = $data['subtitle'];
        }
        if (isset($data['is_active'])) {
            $payload['is_active'] = (int) $data['is_active'];
        }
        if (isset($data['layout_variant'])) {
            $payload['layout_variant'] = $data['layout_variant'];
        }
        if (isset($data['sort_order'])) {
            $payload['sort_order'] = (int) $data['sort_order'];
        }
        if (isset($data['content'])) {
            $payload['content_json'] = json_encode($data['content']);
        } elseif (isset($data['content_json'])) {
            $payload['content_json'] = is_string($data['content_json']) ? $data['content_json'] : json_encode($data['content_json']);
        }

        $result = $this->update($existing['id'], $payload);
        cache()->delete(self::CACHE_KEY);
        return $result;
    }

    /**
     * Reorder sections batch
     */
    public function reorder(array $orderedIds): bool
    {
        $batch = [];
        foreach ($orderedIds as $index => $id) {
            $batch[] = [
                'id'         => (int) $id,
                'sort_order' => $index + 1,
            ];
        }

        if (!empty($batch)) {
            $this->updateBatch($batch, 'id');
            cache()->delete(self::CACHE_KEY);
        }

        return true;
    }

    /**
     * Toggle active state
     */
    public function toggleActive(int $id): bool
    {
        $item = $this->find($id);
        if (!$item) {
            return false;
        }
        $newState = $item['is_active'] == 1 ? 0 : 1;
        $this->update($id, ['is_active' => $newState]);
        cache()->delete(self::CACHE_KEY);
        return (bool) $newState;
    }
}
