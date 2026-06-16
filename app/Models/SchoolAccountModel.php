<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolAccountModel extends Model
{
    protected $table = 'school_accounts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['school_code', 'school_name', 'email', 'password'];
    protected $useTimestamps = false;
}
