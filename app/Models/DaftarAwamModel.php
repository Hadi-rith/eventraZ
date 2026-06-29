<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarAwamModel extends Model
{
    protected $table      = 'daftar_awam';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'program_id', 'program_name',
        'nama', 'ic', 'tel', 'email',
        'bil_ahli', 'status_hadir', 'created_at',
    ];
    protected $useTimestamps = false;
}