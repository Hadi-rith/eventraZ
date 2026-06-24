<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarGuruModel extends Model
{
    protected $table      = 'daftar_guru';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'registration_id', 'nama_guru', 'ic_guru',
    ];
    protected $useTimestamps = false;
}