<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table            = 'leads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'nama_prospek',
        'no_wa',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'nama_prospek' => 'required|min_length[2]|max_length[150]',
        'no_wa'        => 'required|min_length[8]|max_length[30]',
    ];

    protected $validationMessages = [
        'nama_prospek' => [
            'required' => 'Nama lengkap wajib diisi.',
        ],
        'no_wa' => [
            'required' => 'Nomor WhatsApp aktif wajib diisi.',
        ],
    ];

    /**
     * Fetch all leads joined with property title.
     */
    public function getLeadsWithProperty(): array
    {
        return $this->select('leads.*, properties.title as property_title, properties.slug as property_slug')
                    ->join('properties', 'properties.id = leads.property_id', 'left')
                    ->orderBy('leads.created_at', 'DESC')
                    ->findAll();
    }
}
