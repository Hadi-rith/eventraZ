<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarSekolahModel extends Model
{
    protected $table      = 'daftar_sekolah';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'program_id', 'program_name',
        'nama_sekolah', 'kod_sekolah', 'email_sekolah', 'tel_sekolah',
        'bil_murid', 'status',
    ];
    protected $useTimestamps = false;
}