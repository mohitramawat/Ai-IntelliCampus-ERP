<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'total_fee',
        'effective_from_year',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'total_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function unitFees()
    {
        return $this->hasMany(CourseUnitFee::class);
    }
}
