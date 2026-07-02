<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceSessionModel extends Model
{
    protected $table            = 'attendance_sessions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'event_id', 'session_name', 'token', 'session_date',
        'start_time', 'end_time', 'status', 'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate a cryptographically secure random token.
     */
    public function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(24)); // 48-char hex token
            $exists = $this->where('token', $token)->first();
        } while ($exists);

        return $token;
    }

    public function findByToken(string $token): ?array
    {
        return $this->where('token', $token)->first();
    }

    public function isExpired(array $session): bool
    {
        return strtotime($session['end_time']) < time();
    }

    public function isActive(array $session): bool
    {
        return $session['status'] === 'active' && ! $this->isExpired($session);
    }

    /**
     * Get the program/event ID for a session
     */
    public function getProgramId(int $sessionId): ?int
    {
        $session = $this->find($sessionId);
        return $session ? (int) $session['event_id'] : null;
    }
}