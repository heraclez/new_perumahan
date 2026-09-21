<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table            = 'media_library';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'filename',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'caption',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getAll(int $limit = 60, int $offset = 0): array
    {
        return $this->orderBy('id', 'DESC')->findAll($limit, $offset);
    }
}
