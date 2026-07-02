<?php

namespace App\Models;
use CodeIgniter\Model;

class AttendanceRecordModel extends Model
{
    protected $table = 'attendance_records';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'session_id', 'user_key', 'user_type', 'display_name', 
        'attendance_time', 'method', 'ip_address', 'created_at'
    ];

    public function recordAttendance($sessionId, $userKey, $userType, $displayName, $method, $ip): array
    {
        try {
            $this->insert([
                'session_id'      => $sessionId,
                'user_key'        => $userKey,
                'user_type'       => $userType,
                'display_name'    => $displayName,
                'attendance_time' => date('Y-m-d H:i:s'),
                'method'          => $method,
                'ip_address'      => $ip,
                'created_at'      => date('Y-m-d H:i:s')
            ]);
            return ['status' => 'success', 'message' => 'Attendance recorded successfully'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function hasAttended($sessionId, $userKey, $userType): bool
    {
        return $this->where(['session_id' => $sessionId, 'user_key' => $userKey, 'user_type' => $userType])->countAllResults() > 0;
    }

    public function countForSession(int $sessionId): int
    {
        return (int) $this->where('session_id', $sessionId)->countAllResults();
    }

    /**
     * Check if a user is registered for a specific program/event
     * Returns true if the user has a registration for the given program_id
     */
    public function isUserRegisteredForProgram(int $programId, string $userKey, string $userType): bool
    {
        $db = \Config\Database::connect();
        
        if ($userType === 'school') {
            // School users: check daftar_sekolah table by school code
            $count = $db->table('daftar_sekolah')
                ->where('program_id', $programId)
                ->where('kod_sekolah', $userKey)
                ->countAllResults();
            return $count > 0;
        } elseif ($userType === 'public') {
            // Public users: check daftar_awam table by email (stored in session)
            // userKey for public is the public_id, but we need to check by email
            // Since we store email in session, we need to look up by email
            $session = service('session');
            $email = $session->get('email');
            
            if (!$email) {
                return false;
            }
            
            $count = $db->table('daftar_awam')
                ->where('program_id', $programId)
                ->where('email', $email)
                ->countAllResults();
            return $count > 0;
        }
        
        return false;
    }
}