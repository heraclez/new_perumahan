<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admins';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'username',
        'password',
        'role',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[admins.email,id,{id}]',
        'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[admins.username,id,{id}]',
        'role'     => 'required|in_list[Developer,Superadmin,Admin]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email ini sudah digunakan oleh admin lain.',
        ],
        'username' => [
            'is_unique' => 'Username ini sudah digunakan oleh admin lain.',
        ],
    ];

    protected $skipValidation = false;
}
