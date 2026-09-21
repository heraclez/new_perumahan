<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['setting_key', 'setting_value'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const CACHE_KEY = 'site_settings_cache';

    /**
     * Get all settings as an associative key-value array with caching.
     */
    public function getAllSettings(): array
    {
        $settings = cache(self::CACHE_KEY);
        if ($settings !== null && is_array($settings)) {
            return $settings;
        }

        try {
            $records = $this->findAll();
            $settings = [];
            foreach ($records as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            cache()->save(self::CACHE_KEY, $settings, 3600);
            return $settings;
        } catch (\Throwable $e) {
            log_message('notice', 'Using fallback settings: ' . $e->getMessage());
            return [
                'company_name'      => 'Grand Harmoni Residence',
                'company_tagline'   => 'Modern Architecture, Spring Physics & Sustainable Living',
                'company_phone'     => '021-5558989',
                'company_whatsapp'  => '6281234567890',
                'company_email'     => 'hello@harmoni.estate',
                'company_address'   => 'Grand Boulevard No. 1, Jakarta Barat',
                'primary_color'     => '#0f172a',
                'secondary_color'   => '#0284c7',
            ];
        }
    }

    /**
     * Alias for getAllSettings()
     */
    public function getSettings(): array
    {
        return $this->getAllSettings();
    }

    /**
     * Get a single setting by key.
     */
    public function getSetting(string $key, $default = null)
    {
        $all = $this->getAllSettings();
        return $all[$key] ?? $default;
    }

    /**
     * Update multiple settings using batch updates and invalidate cache.
     * $data is key-value array: ['company_name' => 'ABC', 'primary_color' => '#123456', ...]
     */
    public function updateSettingsBatch(array $data): bool
    {
        $batch = [];
        foreach ($data as $key => $value) {
            // Check if key exists
            $existing = $this->where('setting_key', $key)->first();
            if ($existing) {
                $batch[] = [
                    'id'            => $existing['id'],
                    'setting_key'   => $key,
                    'setting_value' => (string) $value,
                ];
            } else {
                $this->insert([
                    'setting_key'   => $key,
                    'setting_value' => (string) $value,
                ]);
            }
        }

        if (!empty($batch)) {
            $this->updateBatch($batch, 'id');
        }

        // Invalidate cache
        cache()->delete(self::CACHE_KEY);
        @unlink(WRITEPATH . 'cache/' . self::CACHE_KEY);

        return true;
    }

    /**
     * Clear settings cache manually.
     */
    public function clearCache(): void
    {
        cache()->delete(self::CACHE_KEY);
        @unlink(WRITEPATH . 'cache/' . self::CACHE_KEY);
    }
}
