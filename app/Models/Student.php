<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batch_id',
        'roll_number',
        'enrollment_number',
        'admission_date',
        'status',
        'is_active',
        'category',
        'father_name',
        'mother_name',
        'contact_number',
        'address',
        'gender',
        'date_of_birth',
        'current_unit',
        'academic_status',
        'profile_picture',
        'face_descriptor',
    ];

    protected $casts = [
        'admission_date'  => 'date',
        'date_of_birth'   => 'date',
        'is_active'       => 'boolean',
        'current_unit'    => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function unitFees()
    {
        return $this->hasMany(StudentUnitFee::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    /**
     * The most recent AI attendance risk prediction for this student.
     */
    public function latestRiskPrediction()
    {
        return $this->hasOne(AttendanceRiskPrediction::class)->latestOfMany();
    }

    /**
     * All AI attendance risk predictions for this student.
     */
    public function riskPredictions()
    {
        return $this->hasMany(AttendanceRiskPrediction::class);
    }
}

