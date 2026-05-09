<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }
    /**
     * Get the dashboard route name based on the user's role.
     */
    public function getDashboardRouteAttribute(): string
    {
        if ($this->hasRole('admin')) {
            return 'admin.dashboard';
        }

        if ($this->hasRole('hod')) {
            return 'hod.dashboard';
        }

        if ($this->hasRole('accounts')) {
            return 'accounts.dashboard';
        }

        if ($this->hasRole('writer')) {
            return 'writer.dashboard';
        }

        if ($this->hasRole('teacher')) {
            return 'teacher.dashboard';
        }

        if ($this->hasRole('student')) {
            return 'student.dashboard';
        }

        return 'login';
    }
}
