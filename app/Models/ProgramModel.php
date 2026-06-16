<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['program_code', 'program_name', 'start_date', 'end_date', 'status'];
    protected $useTimestamps = false;

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
            $startDate = $program['start_date'] ?? null;
            $endDate   = $program['end_date'] ?? null;

            $status = $this->calculateStatus($startDate, $endDate);

            if (($program['status'] ?? '') !== $status) {
                $this->update($program[$this->primaryKey], ['status' => $status]);
            }
        }
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
}
