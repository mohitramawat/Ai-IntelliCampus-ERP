<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    protected $fillable = [
        'student_unit_installment_id',
        'amount_paid',
        'payment_date',
        'payment_method',   // used by FeeController mock payments
        'payment_mode',     // original field from migration
        'transaction_reference',
        'status',
        'is_active',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function installment()
    {
        return $this->belongsTo(StudentUnitInstallment::class, 'student_unit_installment_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
