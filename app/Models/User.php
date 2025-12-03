<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'is_pwd',
        'email',
        'password',
        'phone',
        'address',
        'gender',
        'gender_other',
        'sex',
        'civil_status',
        'dob',
        'employee_id',
        'department',
        'program',
        'highest_educational_attainment',
        'profile_photo_path',
        'user_status',
        'hire_date',
        'position',
        'designation',
        'employee_type',
        'employment_type',
        'employee_category',
        'work_schedule',
        'provider',
        'provider_id',
        'settings',
        // ADD THESE NEW FIELDS:
        'employee_classification',
        'employment_status',
        'work_hours_per_week',
        'work_hours_per_day',
        'is_teacher',
        'vacation_service_credits',
        'marital_status',
        'delivery_count',
        'last_delivery_date',
        'last_leave_computation_date',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'hire_date' => 'date',
        'settings' => 'array',
        'work_hours_per_week' => 'decimal:2',
        'work_hours_per_day' => 'decimal:2',
        'vacation_service_credits' => 'decimal:3',
        'is_teacher' => 'boolean',
        'is_pwd' => 'boolean',
        'last_delivery_date' => 'date',
        'last_leave_computation_date' => 'date',
    ];

    protected $appends = ['profile_photo_url', 'role', 'full_name'];

    // ========== CSC CLASSIFICATION CONSTANTS ==========
    const CLASSIFICATION_REGULAR = 'regular';
    const CLASSIFICATION_TEACHER = 'teacher';
    const CLASSIFICATION_PART_TIME = 'part_time';
    const CLASSIFICATION_CONTRACTUAL = 'contractual';
    const CLASSIFICATION_LOCAL_ELECTIVE = 'local_elective';
    const CLASSIFICATION_JUDICIAL = 'judicial';
    const CLASSIFICATION_EXECUTIVE = 'executive';
    const CLASSIFICATION_FACULTY = 'faculty';

    const EMPLOYMENT_PERMANENT = 'permanent';
    const EMPLOYMENT_TEMPORARY = 'temporary';
    const EMPLOYMENT_CASUAL = 'casual';
    const EMPLOYMENT_COTERMINOUS = 'coterminous';

    const MARITAL_SINGLE = 'single';
    const MARITAL_MARRIED = 'married';
    const MARITAL_WIDOWED = 'widowed';
    const MARITAL_SEPARATED = 'separated';

    // ========== RELATIONSHIPS ==========
    
    // Existing relationships...
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
    
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->today();
    }
    
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function handoverLeaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'handover_person_id');
    }

    public function approvedLeaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }

    // NEW RELATIONSHIPS FOR CSC
    public function leaveBalances(): HasOne
    {
        return $this->hasOne(LeaveBalance::class)->latestOfMany('as_of_date');
    }

    public function allLeaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveCreditEarnings(): HasMany
    {
        return $this->hasMany(LeaveCreditEarning::class);
    }

    // ========== COMPUTED ATTRIBUTES ==========
    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? " {$this->middle_name} " : ' ';
        return "{$this->first_name}{$middle}{$this->last_name}";
    }

    public function getProfilePhotoUrlAttribute()
    {
        // Your existing profile photo logic...
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        $colors = [
            'STCS' => 'rgba(255, 159, 64, 0.6)',
            'SOE' => 'rgba(153, 102, 255, 0.6)',
            'SCJE' => 'rgba(255, 99, 132, 0.6)',
            'SNHS' => 'rgba(75, 192, 192, 0.6)',
            'SME' =>  'rgba(255, 206, 86, 0.6)',
            'SAS' => 'rgba(54, 162, 235, 0.6)',
            'STED' => 'rgba(199, 199, 199, 0.6)',
            'default' => '7C3AED'
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

    // ========== CSC-SPECIFIC METHODS ==========
    
    /**
     * Get employee classification options
     */
    public static function getClassificationOptions(): array
    {
        return [
            self::CLASSIFICATION_REGULAR => 'Regular Employee',
            self::CLASSIFICATION_TEACHER => 'Teacher',
            self::CLASSIFICATION_PART_TIME => 'Part-time Employee',
            self::CLASSIFICATION_CONTRACTUAL => 'Contractual',
            self::CLASSIFICATION_LOCAL_ELECTIVE => 'Local Elective Official',
            self::CLASSIFICATION_JUDICIAL => 'Judicial Official',
            self::CLASSIFICATION_EXECUTIVE => 'Executive Official',
            self::CLASSIFICATION_FACULTY => 'Faculty Member',
        ];
    }

    /**
     * Get employment status options
     */
    public static function getEmploymentStatusOptions(): array
    {
        return [
            self::EMPLOYMENT_PERMANENT => 'Permanent',
            self::EMPLOYMENT_TEMPORARY => 'Temporary',
            self::EMPLOYMENT_CASUAL => 'Casual',
            self::EMPLOYMENT_COTERMINOUS => 'Coterminous',
        ];
    }

    /**
     * Get marital status options
     */
    public static function getMaritalStatusOptions(): array
    {
        return [
            self::MARITAL_SINGLE => 'Single',
            self::MARITAL_MARRIED => 'Married',
            self::MARITAL_WIDOWED => 'Widowed',
            self::MARITAL_SEPARATED => 'Separated',
        ];
    }

    /**
     * Check if employee is eligible for maternity leave
     */
    public function isEligibleForMaternityLeave(): bool
    {
        return $this->gender === 'Female' && 
               $this->marital_status === self::MARITAL_MARRIED;
    }

    /**
     * Check if employee is eligible for paternity leave
     */
    public function isEligibleForPaternityLeave(): bool
    {
        return $this->gender === 'Male' && 
               $this->marital_status === self::MARITAL_MARRIED &&
               $this->delivery_count < 4;
    }

    /**
     * Check if employee follows CSC Omnibus Rules
     */
    public function followsCscRules(): bool
    {
        return in_array($this->employee_classification, [
            self::CLASSIFICATION_REGULAR,
            self::CLASSIFICATION_TEACHER,
            self::CLASSIFICATION_PART_TIME,
            self::CLASSIFICATION_CONTRACTUAL,
        ]);
    }

    /**
     * Check if employee is a teacher (for PVP computation)
     */
    public function isTeacher(): bool
    {
        return $this->is_teacher || $this->employee_classification === self::CLASSIFICATION_TEACHER;
    }

    /**
     * Get work hours per day (for part-time computation)
     */
    public function getWorkHoursPerDay(): float
    {
        return $this->work_hours_per_day ?? 8.0;
    }

    /**
     * Get service years for maternity leave computation
     */
    public function getServiceYearsForMaternity(): float
    {
        if (!$this->hire_date) return 0;
        
        $years = $this->hire_date->diffInDays(now()) / 365.25;
        return round($years, 2);
    }

    /**
     * Calculate actual service days for leave earning
     */
    public function getActualServiceDays($fromDate, $toDate): int
    {
        // Implementation depends on your attendance system
        // This is a simplified version
        return $toDate->diffInDaysFiltered(function($date) {
            return !$date->isWeekend();
        }, $fromDate);
    }

    /**
     * Get current leave balance based on CSC rules
     * @deprecated Use leaveBalances relationship instead
     */
    public function getCscLeaveBalances(): array
    {
        $balance = $this->leaveBalances;
        
        if (!$balance) {
            return [
                'vacation_leave' => 0,
                'sick_leave' => 0,
                'vacation_service_credits' => $this->vacation_service_credits ?? 0,
                'special_leave_privileges' => 3.00,
                'maternity_leave' => 60.00,
                'paternity_leave_days' => 7.00,
                'paternity_leave_count' => $this->delivery_count ?? 0,
            ];
        }

        return [
            'vacation_leave' => $balance->vacation_leave,
            'sick_leave' => $balance->sick_leave,
            'vacation_service_credits' => $balance->vacation_service_credits,
            'proportional_vacation_pay' => $balance->proportional_vacation_pay,
            'special_leave_privileges' => $balance->special_leave_privileges,
            'maternity_leave' => $balance->maternity_leave,
            'paternity_leave_days' => $balance->paternity_leave_days,
            'paternity_leave_count' => $balance->paternity_leave_count,
            'forced_leave_taken' => $balance->forced_leave_taken,
            'monetized_this_year' => $balance->monetized_this_year,
        ];
    }

    /**
     * Get the CSC leave basis for this employee
     */
    public function getLeaveBasis(): string
    {
        if ($this->isTeacher()) {
            return 'teacher_pvp';
        }

        if ($this->employee_classification === self::CLASSIFICATION_PART_TIME) {
            return 'part_time_proportional';
        }

        if (in_array($this->employee_classification, [
            self::CLASSIFICATION_JUDICIAL,
            self::CLASSIFICATION_EXECUTIVE,
            self::CLASSIFICATION_FACULTY
        ])) {
            return 'special_law';
        }

        return 'standard_vl_sl';
    }

    /**
     * Get effective work percentage for part-time employees
     */
    public function getWorkPercentage(): float
    {
        if ($this->employee_classification !== self::CLASSIFICATION_PART_TIME) {
            return 1.0;
        }

        return ($this->work_hours_per_week ?? 20) / 40;
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot('status')
            ->withTimestamps();
    }
}