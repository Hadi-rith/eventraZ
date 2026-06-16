<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarMuridModel extends Model
{
    protected $table = 'daftar_murid';
    protected $primaryKey = 'id';
    protected $allowedFields = ['registration_id', 'nama_murid', 'ic_murid'];
    protected $useTimestamps = false;
}