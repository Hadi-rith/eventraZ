<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'program_id', 'event_title', 'event_description', 
        'poster_image', 'start_date', 'end_date', 
        'location', 'status', 'is_featured'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getEventsByStatus($status = null)
    {
        $this->select('events.*, programs.program_name, programs.pic_nama, programs.pic_tel')
             ->join('programs', 'programs.id = events.program_id', 'left');
        
        if ($status) {
            $this->where('events.status', $status);
        }
        
        return $this->orderBy('start_date', 'ASC')->findAll();
    }

    public function getFeaturedEvents()
    {
        return $this->select('events.*, programs.program_name, programs.pic_nama, programs.pic_tel')
                    ->join('programs', 'programs.id = events.program_id', 'left')
                    ->where('is_featured', 1)
                    ->where('status !=', 'past')
                    ->orderBy('start_date', 'ASC')
                    ->findAll();
    }

    public function updateEventStatus($eventId)
    {
        $event = $this->find($eventId);
        if (!$event) return false;

        $today = date('Y-m-d');
        $start = $event['start_date'];
        $end = $event['end_date'];

        if ($end < $today) {
            $status = 'past';
        } elseif ($start <= $today && $end >= $today) {
            $status = 'ongoing';
        } else {
            $status = 'upcoming';
        }

        return $this->update($eventId, ['status' => $status]);
    }

    public function refreshAllEventStatuses()
    {
        $events = $this->findAll();
        foreach ($events as $event) {
            $this->updateEventStatus($event['id']);
        }
    }
}