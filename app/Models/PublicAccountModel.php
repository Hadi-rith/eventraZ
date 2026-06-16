<?php

namespace App\Models;

use CodeIgniter\Model;

class PublicAccountModel extends Model
{
    protected $table = 'public_accounts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'password', 'created_at'];
    protected $useTimestamps = false;
}
