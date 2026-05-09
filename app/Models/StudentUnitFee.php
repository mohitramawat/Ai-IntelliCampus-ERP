<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentUnitFee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'unit_number',
        'unit_name',
        'unit_fee',
        'total_paid',
        'status',
        'is_active',
    ];

    protected $casts = [
        'unit_fee' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function installments()
    {
        return $this->hasMany(StudentUnitInstallment::class);
    }
}
