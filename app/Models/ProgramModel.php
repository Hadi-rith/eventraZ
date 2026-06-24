<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table      = 'programs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'program_code', 'parent_id', 'program_name', 'description',
        'start_date', 'end_date', 'event_time', 'location', 'organizer',
        'status', 'is_featured', 'pic_nama', 'pic_tel', 'poster_image',
        'registration_limit', 'admin_id',
    ];
    protected $useTimestamps = false;

    // ------------------------------------------------------------------
    // Status helpers
    // ------------------------------------------------------------------

    public function calculateStatus(?string $startDate, ?string $endDate): string
    {
        $today = date('Y-m-d');

        if ($endDate && $endDate < $today) {
            return 'TIDAK AKTIF';
        }

        return 'AKTIF';
    }

    public function refreshProgramStatuses(): void
    {
        $programs = $this->findAll();

        foreach ($programs as $program) {
            $status = $this->calculateStatus(
                $program['start_date'] ?? null,
                $program['end_date']   ?? null
            );

            if (($program['status'] ?? '') !== $status) {
                $this->update($program[$this->primaryKey], ['status' => $status]);
            }
        }
    }

    // ------------------------------------------------------------------
    // Scoped queries — admin_id = NULL means Super Admin owns it
    // ------------------------------------------------------------------

    /**
     * Get all programs visible to the given admin.
     * Super Admin (adminId = null) sees everything.
     * Regular Admin sees only their own programs.
     */
    public function getProgramsForAdmin(?int $adminId): array
    {
        $this->refreshProgramStatuses();

        if ($adminId === null) {
            // Super Admin — all programs
            return $this->orderBy('parent_id', 'ASC')
                        ->orderBy('start_date', 'ASC')
                        ->orderBy('program_name', 'ASC')
                        ->findAll();
        }

        return $this->where('admin_id', $adminId)
                    ->orderBy('parent_id', 'ASC')
                    ->orderBy('start_date', 'ASC')
                    ->orderBy('program_name', 'ASC')
                    ->findAll();
    }

    public function getActivePrograms(): array
    {
        $this->refreshProgramStatuses();

        return $this->groupStart()
                ->where('status', 'AKTIF')
                ->orWhere('status', null)
                ->orWhere('status', '')
            ->groupEnd()
            ->orderBy('start_date', 'ASC')
            ->orderBy('program_name', 'ASC')
            ->findAll();
    }

    public function getFeaturedPrograms(): array
    {
        $this->refreshProgramStatuses();

        return $this->where('is_featured', 1)
                    ->where('status', 'AKTIF')
                    ->orderBy('start_date', 'ASC')
                    ->findAll();
    }

    // ------------------------------------------------------------------
    // Capacity helpers
    // ------------------------------------------------------------------

    /**
     * Calculate how many capacity slots are already consumed for a program.
     * - School: each registration consumes bil_murid slots
     * - Awam:   each registration consumes 1 (registrant) + bil_ahli slots
     * - Luar:   each registration consumes 1 slot
     */
    public function getUsedCapacity(int $programId): int
    {
        $db = \Config\Database::connect();

        // School — sum of bil_murid
        $school = (int) $db->table('daftar_sekolah')
                           ->selectSum('bil_murid')
                           ->where('program_id', $programId)
                           ->get()
                           ->getRow()
                           ->bil_murid;

        // Awam — count registrants + sum of bil_ahli
        $awamRow  = $db->table('daftar_awam')
                       ->select('COUNT(*) as cnt, COALESCE(SUM(bil_ahli),0) as ahli_sum')
                       ->where('program_id', $programId)
                       ->get()
                       ->getRowArray();
        $awam = (int) ($awamRow['cnt'] ?? 0) + (int) ($awamRow['ahli_sum'] ?? 0);

        // Luar — each registration = 1 slot
        $luar = (int) $db->table('daftar_luar')
                         ->where('program_id', $programId)
                         ->countAllResults();

        return $school + $awam + $luar;
    }

    public function getRemainingCapacity(int $programId): int
    {
        $program = $this->find($programId);
        if (!$program) return 0;

        $limit = (int) ($program['registration_limit'] ?? 0);
        if ($limit <= 0) return PHP_INT_MAX; // 0 means unlimited

        $used = $this->getUsedCapacity($programId);
        return max(0, $limit - $used);
    }

    public function hasCapacity(int $programId, int $slotsNeeded): bool
    {
        $remaining = $this->getRemainingCapacity($programId);
        return $remaining >= $slotsNeeded;
    }

    // ------------------------------------------------------------------
    // Dashboard stats for Super Admin
    // ------------------------------------------------------------------

    public function getSuperAdminStats(): array
    {
        $today    = date('Y-m-d');
        $db       = \Config\Database::connect();
        $all      = $this->findAll();

        $active    = 0;
        $completed = 0;
        foreach ($all as $p) {
            if (($p['end_date'] ?? '') < $today) {
                $completed++;
            } else {
                $active++;
            }
        }

        $totalRegs = (int) $db->table('daftar_sekolah')->countAllResults()
                  + (int) $db->table('daftar_awam')->countAllResults()
                  + (int) $db->table('daftar_luar')->countAllResults();

        return [
            'total_programs'      => count($all),
            'active_programs'     => $active,
            'completed_programs'  => $completed,
            'total_registrations' => $totalRegs,
        ];
    }

    /**
     * Dashboard stats for a single Admin.
     */
    public function getAdminStats(int $adminId): array
    {
        $today    = date('Y-m-d');
        $db       = \Config\Database::connect();
        $programs = $this->where('admin_id', $adminId)->findAll();

        $active    = 0;
        $completed = 0;
        foreach ($programs as $p) {
            if (($p['end_date'] ?? '') < $today) {
                $completed++;
            } else {
                $active++;
            }
        }

        $programIds = array_column($programs, 'id');
        $totalRegs  = 0;
        if (!empty($programIds)) {
            $totalRegs += (int) $db->table('daftar_sekolah')->whereIn('program_id', $programIds)->countAllResults();
            $totalRegs += (int) $db->table('daftar_awam')->whereIn('program_id', $programIds)->countAllResults();
            $totalRegs += (int) $db->table('daftar_luar')->whereIn('program_id', $programIds)->countAllResults();
        }

        return [
            'total_programs'      => count($programs),
            'active_programs'     => $active,
            'completed_programs'  => $completed,
            'total_registrations' => $totalRegs,
        ];
    }
}