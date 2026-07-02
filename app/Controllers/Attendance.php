<?php

namespace App\Controllers;

use App\Models\AttendanceSessionModel;
use App\Models\AttendanceRecordModel;
use App\Models\DaftarSekolahModel;
use App\Models\DaftarAwamModel;

class Attendance extends BaseController
{
    private function currentParticipant(): ?array
    {
        if (!$this->session->get('logged_in')) return null;

        $role = $this->session->get('role');
        if ($role === 'school') {
            return [
                'key' => (string)$this->session->get('school_code'),
                'type' => 'school',
                'name' => (string)$this->session->get('school_name')
            ];
        }
        if ($role === 'public') {
            return [
                'key' => (string)$this->session->get('public_id'),
                'type' => 'public',
                'name' => (string)$this->session->get('name')
            ];
        }
        return null;
    }

    public function processScan()
    {
        $participant = $this->currentParticipant();
        if (!$participant) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false, 
                'status' => 'error', 
                'message' => 'Sila log masuk.'
            ]);
        }

        $qrText = trim((string) $this->request->getPost('qr_text'));
        if ($qrText === '') {
            return $this->response->setJSON([
                'success' => false, 
                'status' => 'error', 
                'message' => 'Kod QR kosong.'
            ]);
        }

        // Token extraction logic
        $token = $qrText;
        if (str_contains($qrText, '/attendance/checkin/')) {
            $parts = explode('/attendance/checkin/', $qrText);
            $token = trim(end($parts), '/');
        }
        $token = explode('?', $token)[0];

        $result = $this->attemptCheckin($token, $participant, 'qr');
        
        return $this->response->setJSON($result);
    }

    public function checkin($token = null)
    {
        if (!$token) {
            return view('attendance_result', [
                'result' => 'invalid',
                'message' => 'Kod QR tidak sah.',
                'session' => null
            ]);
        }

        $sessionModel = new AttendanceSessionModel();
        $recordModel = new AttendanceRecordModel();
        
        $session = $sessionModel->findByToken($token);
        
        if (!$session) {
            return view('attendance_result', [
                'result' => 'invalid',
                'message' => 'Kod QR tidak sah.',
                'session' => null
            ]);
        }

        if (!$sessionModel->isActive($session)) {
            return view('attendance_result', [
                'result' => 'expired',
                'message' => 'Sesi kehadiran ini telah tamat tempoh.',
                'session' => $session
            ]);
        }

        // Get the current user from session
        $participant = $this->currentParticipant();
        
        if (!$participant) {
            return view('attendance_result', [
                'result' => 'invalid',
                'message' => 'Sila log masuk terlebih dahulu.',
                'session' => $session
            ]);
        }

        // FIX: Check if user is registered for this program
        if (!$this->isUserRegisteredForProgram($session['event_id'], $participant)) {
            return view('attendance_result', [
                'result' => 'not_registered',
                'message' => 'Anda tidak berdaftar untuk program ini.',
                'session' => $session
            ]);
        }

        // Check if already attended
        if ($recordModel->hasAttended($session['id'], $participant['key'], $participant['type'])) {
            return view('attendance_result', [
                'result' => 'duplicate',
                'session' => $session
            ]);
        }

        // Record attendance
        $result = $recordModel->recordAttendance(
            (int)$session['id'],
            $participant['key'],
            $participant['type'],
            $participant['name'],
            'link',
            $this->request->getIPAddress() ?? '127.0.0.1'
        );

        if ($result['status'] === 'success') {
            return view('attendance_result', [
                'result' => 'success',
                'session' => $session
            ]);
        }

        return view('attendance_result', [
            'result' => 'invalid',
            'message' => $result['message'] ?? 'Gagal merekod kehadiran.',
            'session' => $session
        ]);
    }

    private function attemptCheckin(string $token, array $participant, string $method): array
    {
        $sessionModel = new AttendanceSessionModel();
        $recordModel  = new AttendanceRecordModel();

        $session = $sessionModel->findByToken($token);
        
        if (!$session) {
            return [
                'success' => false, 
                'status' => 'invalid', 
                'message' => 'Kod QR tidak sah.'
            ];
        }

        if (!$sessionModel->isActive($session)) {
            return [
                'success' => false, 
                'status' => 'expired', 
                'message' => 'Sesi telah tamat.'
            ];
        }

        // FIX: Check if user is registered for this program
        if (!$this->isUserRegisteredForProgram($session['event_id'], $participant)) {
            return [
                'success' => false,
                'status' => 'not_registered',
                'message' => 'Anda tidak berdaftar untuk program ini.'
            ];
        }

        if ($recordModel->hasAttended($session['id'], $participant['key'], $participant['type'])) {
            return [
                'success' => false, 
                'status' => 'duplicate', 
                'message' => 'Kehadiran telah direkodkan.'
            ];
        }

        $recordResult = $recordModel->recordAttendance(
            (int)$session['id'],
            $participant['key'],
            $participant['type'],
            $participant['name'],
            $method,
            $this->request->getIPAddress() ?? '127.0.0.1'
        );

        if ($recordResult['status'] === 'success') {
            return [
                'success' => true,
                'status' => 'success',
                'message' => 'Attendance recorded successfully',
                'session' => [
                    'id' => $session['id'],
                    'session_name' => $session['session_name'],
                    'event_id' => $session['event_id']
                ]
            ];
        }

        return [
            'success' => false,
            'status' => 'error',
            'message' => $recordResult['message'] ?? 'Gagal merekod kehadiran.'
        ];
    }

    /**
     * Check if the user is registered for the given program
     */
    private function isUserRegisteredForProgram(int $programId, array $participant): bool
    {
        $db = \Config\Database::connect();
        
        if ($participant['type'] === 'school') {
            // School users: check daftar_sekolah by school code
            $count = $db->table('daftar_sekolah')
                ->where('program_id', $programId)
                ->where('kod_sekolah', $participant['key'])
                ->countAllResults();
            return $count > 0;
        } elseif ($participant['type'] === 'public') {
            // Public users: check daftar_awam by email
            $email = $this->session->get('email');
            
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