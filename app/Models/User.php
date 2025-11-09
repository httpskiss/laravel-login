<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'gender',
        'dob',
        'employee_id',
        'department',
        'profile_photo_path',
        'user_status',
        'hire_date',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'hire_date' => 'date',
    ];

    protected $appends = ['profile_photo_url', 'role'];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        // Fallback to UI Avatars with department-based colors
        $colors = [
            'STCS' => 'rgba(255, 159, 64, 0.6)',  // Orange
            'SOE' => 'rgba(153, 102, 255, 0.6)',   // Violet
            'SCJE' => 'rgba(255, 99, 132, 0.6)', // Pink
            'SNHS' => 'rgba(75, 192, 192, 0.6)',  // Green
            'SME' =>  'rgba(255, 206, 86, 0.6)',   // Yellow
            'SAS' => 'rgba(54, 162, 235, 0.6)', //Blue
            'STED' => 'rgba(199, 199, 199, 0.6)', //Gray
            'default' => '7C3AED' // Purple
        ];

        
        $bgColor = $colors[$this->department] ?? $colors['default'];
        
        return "https://ui-avatars.com/api/?" . http_build_query([
            'name' => $this->first_name . '+' . $this->last_name,
            'background' => $bgColor,
            'color' => 'FFFFFF',
            'size' => '256',
            'rounded' => 'true',
            'bold' => 'true',
            'format' => 'png'
        ]);


    }

    

    public function getRoleAttribute()
    {
        return $this->roles->first()?->name ?? 'Employee';
    }

    // Relationships
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
    
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    
  // Add this to the User model
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->today();
    }
    
     public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveBalances()
    {
        return [
            'vacation' => 15, // This should come from database in real implementation
            'sick' => 10,
            'emergency' => 5,
            'special' => 30
        ];
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot('status')
            ->withTimestamps();
    }

    
    
}