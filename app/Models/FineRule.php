<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FineRule extends Model
{
    protected $fillable = [
        'course_id',
        'unit_number',
        'fine_amount',
        'effective_from_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'fine_amount' => 'decimal:2',
        'effective_from_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function fines()
    {
        return $this->hasMany(InstallmentFine::class, 'fine_rule_id');
    }
}
