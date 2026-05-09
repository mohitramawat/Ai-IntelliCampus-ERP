<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseUnitFee extends Model
{
    protected $fillable = [
        'fee_structure_id',
        'unit_number',
        'unit_name',
        'unit_fee',
        'is_active',
    ];

    protected $casts = [
        'unit_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }
}
