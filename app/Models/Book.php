<?php

namespace App\Models;

class Book extends BaseModel
{
    protected $dates = ['published_at'];
    protected $dateFormat = 'Y-m-d';

    public function getPublishedAtAttribute($value)
    {
        return $this->formatDateTime($value);
    }

    public function teachingSessions() {
        return $this->belongsToMany(TeachingSession::class, 'teaching_session_books', 'book_id', 'session_id');
    }
}
