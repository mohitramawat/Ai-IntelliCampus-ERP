<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentUnitInstallment extends Model
{
    protected $fillable = [
        'student_unit_fee_id',
        'installment_number',
        'installment_amount',
        'paid_amount',
        'due_date',
        'status',
        'is_active',
    ];

    protected $casts = [
        'installment_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function studentUnitFee()
    {
        return $this->belongsTo(StudentUnitFee::class);
    }

    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class);
    }

    public function fines()
    {
        return $this->hasMany(InstallmentFine::class, 'student_unit_installment_id');
    }
}
