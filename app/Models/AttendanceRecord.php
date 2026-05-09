<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'lecture_session_id',
        'student_id',
        'status',
        'marked_at',
        'student_gps_lat',
        'student_gps_long',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(LectureSession::class, 'lecture_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
