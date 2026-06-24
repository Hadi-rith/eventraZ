<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminAccountModel extends Model
{
    protected $table      = 'admin_accounts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'name', 'email', 'password', 'is_active',
    ];
    protected $useTimestamps = false;

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)
                    ->where('is_active', 1)
                    ->first();
    }

    /**
     * Stats used by Super Admin dashboard — total events and registrations
     * per admin, joined from programs + registration tables.
     */
    public function getAllAdminStats(): array
    {
        $db      = \Config\Database::connect();
        $today   = date('Y-m-d');
        $admins  = $this->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        foreach ($admins as &$admin) {
            $adminId = (int) $admin['id'];

            // Programs owned by this admin
            $programs = $db->table('programs')
                           ->where('admin_id', $adminId)
                           ->get()
                           ->getResultArray();

            $programIds   = array_column($programs, 'id');
            $programNames = array_column($programs, 'program_name');

            $admin['total_programs'] = count($programs);

            $active    = 0;
            $completed = 0;
            foreach ($programs as $p) {
                if (($p['end_date'] ?? '') < $today) {
                    $completed++;
                } else {
                    $active++;
                }
            }
            $admin['active_programs']    = $active;
            $admin['completed_programs'] = $completed;

            // Total registrations across all tables
            $totalRegs = 0;
            if (!empty($programIds)) {
                $totalRegs += (int) $db->table('daftar_sekolah')->whereIn('program_id', $programIds)->countAllResults();
                $totalRegs += (int) $db->table('daftar_awam')->whereIn('program_id', $programIds)->countAllResults();
                $totalRegs += (int) $db->table('daftar_luar')->whereIn('program_id', $programIds)->countAllResults();
            }
            $admin['total_registrations'] = $totalRegs;

            unset($admin['password']); // never expose password
        }

        return $admins;
    }
}