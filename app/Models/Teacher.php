<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'employee_code',
        'qualification',
        'experience_years',
        'joining_date',
        'phone_number',
        'address',
        'gender',
        'date_of_birth',
        'profile_photo',
        'status',
    ];

    protected $casts = [
        'date_of_birth'    => 'date',
        'joining_date'     => 'date',
        'experience_years' => 'integer',
    ];

    /*
     |─────────────────────────────────────────────────────────────────
     | HOD ARCHITECTURE
     |─────────────────────────────────────────────────────────────────
     | HOD is NOT a separate table. A HOD is a Teacher whose User
     | has the Spatie role 'hod' assigned.
     |
     | Structural assumption:
     |   Teacher with role 'hod' must have a non-null department_id.
     |   This is enforced structurally via the NOT NULL constraint on
     |   teachers.department_id (set at migration level).
     |
     | Role assignment happens at controller/service layer — not here.
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
