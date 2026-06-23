<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarAwamModel extends Model
{
    protected $table      = 'daftar_awam';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'timestamp', 'program_name', 'nama', 'ic', 'tel', 'email',
        'kategori', 'bil_tiket', 'bil_ahli', 'status_hadir',
    ];
    protected $useTimestamps = false;
}