<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarFamilyModel extends Model
{
    protected $table      = 'daftar_family';
    protected $primaryKey = 'id';
    protected $allowedFields = ['registration_id', 'nama_ahli', 'ic_ahli'];
    protected $useTimestamps  = false;
}