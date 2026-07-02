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
        try {
            $db       = \Config\Database::connect();
            $programs = $db->query("SELECT id, start_date, end_date, status FROM `programs`")->getResultArray();

            foreach ($programs as $program) {
                $newStatus = $this->calculateStatus(
                    $program['start_date'] ?? null,
                    $program['end_date']   ?? null
                );
                if (($program['status'] ?? '') !== $newStatus) {
                    $db->query(
                        "UPDATE `programs` SET status = ? WHERE id = ?",
                        [$newStatus, (int) $program['id']]
                    );
                }
            }
        } catch (\Throwable $e) {
            log_message('error', '[ProgramModel::refreshProgramStatuses] ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------------
    // Scoped queries
    // ------------------------------------------------------------------

    public function getProgramsForAdmin(?int $adminId): array
    {
        $this->refreshProgramStatuses();

        $db  = \Config\Database::connect();
        $sql = "SELECT * FROM `programs`";
        if ($adminId !== null) {
            $sql .= " WHERE admin_id = ?";
        }
        $sql .= " ORDER BY parent_id ASC, start_date ASC, program_name ASC";

        if ($adminId !== null) {
            return $db->query($sql, [$adminId])->getResultArray();
        }
        return $db->query($sql)->getResultArray();
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

    public function getUsedCapacity(int $programId): int
    {
        $db = \Config\Database::connect();

        $school = (int) $db->table('daftar_sekolah')
                           ->selectSum('bil_murid')
                           ->where('program_id', $programId)
                           ->get()->getRow()->bil_murid;

        $awamRow = $db->table('daftar_awam')
                      ->select('COUNT(*) as cnt, COALESCE(SUM(bil_ahli),0) as ahli_sum')
                      ->where('program_id', $programId)
                      ->get()->getRowArray();
        $awam = (int) ($awamRow['cnt'] ?? 0) + (int) ($awamRow['ahli_sum'] ?? 0);

        return $school + $awam;
    }

    public function getRemainingCapacity(int $programId): int
    {
        $program = $this->find($programId);
        if (!$program) return 0;
        $limit = (int) ($program['registration_limit'] ?? 0);
        if ($limit <= 0) return PHP_INT_MAX;
        return max(0, $limit - $this->getUsedCapacity($programId));
    }

    public function hasCapacity(int $programId, int $slotsNeeded): bool
    {
        return $this->getRemainingCapacity($programId) >= $slotsNeeded;
    }

    // ------------------------------------------------------------------
    // Public / school events visibility
    // ------------------------------------------------------------------

    public function getPublicVisibilityCutoff(): string
    {
        return date('Y-m-d', strtotime('-7 days'));
    }

    public function isVisibleOnPublicViews(array $program): bool
    {
        $endDate = $program['end_date'] ?? null;
        if (!$endDate) {
            return false;
        }
        return $endDate >= $this->getPublicVisibilityCutoff();
    }

    // ------------------------------------------------------------------
    // Per-program event statistics with attendance
    // ------------------------------------------------------------------

    public function getProgramEventStats(int $programId): ?array
    {
        $program = $this->find($programId);
        if (!$program) {
            return null;
        }

        $db = \Config\Database::connect();

        $sekolahRegs = $db->table('daftar_sekolah')
            ->where('program_id', $programId)
            ->get()->getResultArray();

        $awamRegs = $db->table('daftar_awam')
            ->where('program_id', $programId)
            ->get()->getResultArray();

        $sekolahCount = count($sekolahRegs);
        $totalMurid   = (int) array_sum(array_map(fn ($r) => (int) $r['bil_murid'], $sekolahRegs));

        $regIds = array_map(fn ($r) => (int) $r['id'], $sekolahRegs);
        $guruCount        = 0;
        $muridListedCount = 0;
        if (!empty($regIds)) {
            $guruCount = (int) $db->table('daftar_guru')
                ->whereIn('registration_id', $regIds)
                ->countAllResults();
            $muridListedCount = (int) $db->table('daftar_murid')
                ->whereIn('registration_id', $regIds)
                ->countAllResults();
        }

        $awamRegCount      = count($awamRegs);
        $awamFamilyMembers = (int) array_sum(array_map(fn ($r) => (int) $r['bil_ahli'], $awamRegs));
        $awamParticipants  = $awamRegCount + $awamFamilyMembers;

        $hadirCount      = 0;
        $belumHadirCount = 0;
        foreach ($awamRegs as $r) {
            $st = $r['status_hadir'] ?? 'Belum Hadir';
            if (strcasecmp($st, 'Hadir') === 0) {
                $hadirCount++;
            } else {
                $belumHadirCount++;
            }
        }

        // Get actual attendance from attendance_records, scoped to THIS program's
        // attendance sessions (join on event_id) rather than counted globally.

        // Public (awam): attendance_records.user_key stores the public_accounts.id
        // of the logged-in participant — daftar_awam has no public_id column, only
        // email — so we match via public_accounts.email.
        $attendedPublicAccountIds = array_column(
            $db->table('attendance_records ar')
                ->select('ar.user_key')
                ->join('attendance_sessions ats', 'ats.id = ar.session_id')
                ->where('ats.event_id', $programId)
                ->where('ar.user_type', 'public')
                ->get()->getResultArray(),
            'user_key'
        );

        $attendedEmails = [];
        if (!empty($attendedPublicAccountIds)) {
            $attendedEmails = array_column(
                $db->table('public_accounts')
                    ->select('email')
                    ->whereIn('id', $attendedPublicAccountIds)
                    ->get()->getResultArray(),
                'email'
            );
            $attendedEmails = array_flip($attendedEmails); // for fast isset() lookups
        }

        $actualAttended = 0;
        foreach ($awamRegs as $r) {
            if (isset($attendedEmails[$r['email']])) {
                $actualAttended++;
            }
        }

        // School: attendance_records.user_key stores school_code directly, which
        // IS the natural key shared with daftar_sekolah.kod_sekolah, so this one
        // can compare directly — but still needs the empty-array guard and the
        // event_id scoping.
        $schoolCodes = array_column($sekolahRegs, 'kod_sekolah');
        $schoolAttendance = 0;
        if (!empty($schoolCodes)) {
            $attendedSchoolCodes = array_column(
                $db->table('attendance_records ar')
                    ->select('ar.user_key')
                    ->join('attendance_sessions ats', 'ats.id = ar.session_id')
                    ->where('ats.event_id', $programId)
                    ->where('ar.user_type', 'school')
                    ->get()->getResultArray(),
                'user_key'
            );
            $schoolAttendance = count(array_intersect($schoolCodes, $attendedSchoolCodes));
        }

        $schoolStatusCounts = [];
        foreach ($sekolahRegs as $r) {
            $st = $r['status'] ?? 'Baru';
            $schoolStatusCounts[$st] = ($schoolStatusCounts[$st] ?? 0) + 1;
        }

        $limit     = (int) ($program['registration_limit'] ?? 0);
        $used      = $this->getUsedCapacity($programId);
        $remaining = $limit > 0 ? max(0, $limit - $used) : null;
        $fillPct   = $limit > 0 ? round(($used / $limit) * 100, 1) : null;

        $today       = date('Y-m-d');
        $eventStatus = 'upcoming';
        if (($program['end_date'] ?? '') < $today) {
            $eventStatus = 'past';
        } elseif (($program['start_date'] ?? '') <= $today) {
            $eventStatus = 'ongoing';
        }

        $totalRegistered = $sekolahCount + $awamRegCount;
        $totalAttended = $schoolAttendance + $actualAttended;

        return [
            'program_id'            => (int) $program['id'],
            'program_code'          => $program['program_code'],
            'program_name'          => $program['program_name'],
            'start_date'            => $program['start_date'],
            'end_date'              => $program['end_date'],
            'location'              => $program['location'] ?? '',
            'organizer'             => $program['organizer'] ?? '',
            'status'                => $program['status'] ?? '',
            'event_status'          => $eventStatus,
            'registration_limit'    => $limit,
            'used_capacity'         => $used,
            'remaining_capacity'    => $remaining,
            'fill_percent'          => $fillPct,
            'sekolah_registrations' => $sekolahCount,
            'total_murid'           => $totalMurid,
            'guru_pengiring'        => $guruCount,
            'murid_listed'          => $muridListedCount,
            'awam_registrations'    => $awamRegCount,
            'awam_family_members'   => $awamFamilyMembers,
            'awam_participants'     => $awamParticipants,
            'total_participants'    => $totalMurid + $awamParticipants,
            'awam_hadir'            => $hadirCount,
            'awam_belum_hadir'      => $belumHadirCount,
            'school_status_breakdown' => $schoolStatusCounts,
            'parent_id'             => $program['parent_id'],
            'total_registered'      => $totalRegistered,
            'total_attended'        => $totalAttended,
            'school_attended'       => $schoolAttendance,
            'awam_attended'         => $actualAttended,
            'attendance_rate'       => $totalRegistered > 0 ? round(($totalAttended / $totalRegistered) * 100, 1) : 0,
        ];
    }

    public function getAllProgramEventStats(?int $adminId = null): array
    {
        $programs = $this->getProgramsForAdmin($adminId);
        $stats    = [];

        foreach ($programs as $program) {
            $row = $this->getProgramEventStats((int) $program['id']);
            if ($row !== null) {
                $stats[] = $row;
            }
        }

        return $stats;
    }

    // ------------------------------------------------------------------
    // Dashboard stats
    // ------------------------------------------------------------------

    public function getSuperAdminStats(): array
    {
        $today = date('Y-m-d');
        $db    = \Config\Database::connect();
        $all   = $this->findAll();

        $active = $completed = 0;
        foreach ($all as $p) {
            if (($p['end_date'] ?? '') < $today) $completed++;
            else $active++;
        }

        $sekolahRegs = (int) $db->table('daftar_sekolah')->countAllResults();
        $awamRegs    = (int) $db->table('daftar_awam')->countAllResults();

        return [
            'total_programs'        => count($all),
            'active_programs'       => $active,
            'completed_programs'    => $completed,
            'total_registrations'   => $sekolahRegs + $awamRegs,
            'sekolah_registrations' => $sekolahRegs,
            'awam_registrations'    => $awamRegs,
        ];
    }

    public function getAdminStats(int $adminId): array
    {
        $today    = date('Y-m-d');
        $db       = \Config\Database::connect();
        $programs = $this->where('admin_id', $adminId)->findAll();

        $active = $completed = 0;
        foreach ($programs as $p) {
            if (($p['end_date'] ?? '') < $today) $completed++;
            else $active++;
        }

        $programIds  = array_column($programs, 'id');
        $sekolahRegs = $awamRegs = 0;
        if (!empty($programIds)) {
            $sekolahRegs = (int) $db->table('daftar_sekolah')->whereIn('program_id', $programIds)->countAllResults();
            $awamRegs    = (int) $db->table('daftar_awam')->whereIn('program_id', $programIds)->countAllResults();
        }

        return [
            'total_programs'        => count($programs),
            'active_programs'       => $active,
            'completed_programs'    => $completed,
            'total_registrations'   => $sekolahRegs + $awamRegs,
            'sekolah_registrations' => $sekolahRegs,
            'awam_registrations'    => $awamRegs,
        ];
    }
}