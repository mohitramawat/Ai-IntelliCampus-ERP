<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'code',
        'duration_years',
        'unit_type',
        'total_units',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function fineRules()
    {
        return $this->hasMany(FineRule::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
