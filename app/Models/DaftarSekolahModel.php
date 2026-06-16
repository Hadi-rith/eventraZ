<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarSekolahModel extends Model
{
    protected $table = 'daftar_sekolah';
    protected $primaryKey = 'id';
    protected $allowedFields = ['timestamp', 'program_name', 'nama_sekolah', 'kod_sekolah', 'nama_guru', 'ic_guru', 'tel_guru', 'email', 'bil_murid', 'status'];
    protected $useTimestamps = false;
}