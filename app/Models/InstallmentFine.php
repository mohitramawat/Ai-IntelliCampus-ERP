<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentFine extends Model
{
    protected $fillable = [
        'student_unit_installment_id',
        'fine_rule_id',
        'fine_amount',
        'applied_on',
        'is_paid',
    ];

    protected $casts = [
        'fine_amount' => 'decimal:2',
        'applied_on' => 'date',
        'is_paid' => 'boolean',
    ];

    public function installment()
    {
        return $this->belongsTo(StudentUnitInstallment::class, 'student_unit_installment_id');
    }

    public function rule()
    {
        return $this->belongsTo(FineRule::class, 'fine_rule_id');
    }
}
