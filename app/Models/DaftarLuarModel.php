<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarLuarModel extends Model
{
    protected $table = 'daftar_luar';
    protected $primaryKey = 'id';
    protected $allowedFields = ['timestamp', 'program_name', 'nama_sekolah', 'kod_sekolah', 'tel', 'email', 'kategori', 'status'];
    protected $useTimestamps = false;
}