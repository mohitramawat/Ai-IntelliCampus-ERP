<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LectureSession extends Model
{
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'batch_id',
        'lecture_date',
        'period_number',
        'start_time',
        'end_time',
        'status',
        'teacher_gps_lat',
        'teacher_gps_long',
        'gps_radius_meters',
        'attendance_window_minutes',
        'is_ultrasonic',
        'ultrasonic_token',
    ];

    protected $casts = [
        'lecture_date' => 'date',
        'start_time'   => 'datetime',
        'end_time'     => 'datetime',
    ];

    // ── Direct Relationships (no assignment layer) ───────────────

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
