<?php

namespace App\Models;

class TeachingSession extends BaseModel
{
    protected $dates = ['end_time', 'start_time'];
    protected $attributesMapping = [
        'end_date' => 'end_time',
        'name' => 'session_name',
        'start_date' => 'start_time'
    ];
    protected $fillable = [
        'end_date', 'name',
        'start_date', 'user_id'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'teaching_session_books', 'session_id', 'book_id');
    }

    public function getEndTimeAttribute($value)
    {
        return $this->formatDateTime($value);
    }

    public function getStartTimeAttribute($value)
    {
        return $this->formatDateTime($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
