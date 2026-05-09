<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'employee_code',
        'staff_type',
        'joining_date',
        'phone_number',
        'address',
        'gender',
        'date_of_birth',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date'  => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
