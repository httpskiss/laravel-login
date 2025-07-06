<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'employee_id',
        'department',
        'email',
        'password',
        'profile_photo_path',
        'user_status',
        'hire_date',
        'phone',
        'address',
        'gender',
        'dob'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'hire_date' => 'date',
        'dob' => 'date',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }
}