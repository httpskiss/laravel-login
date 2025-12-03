<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'as_of_date',
        'vacation_leave',
        'sick_leave',
        'vacation_service_credits',
        'proportional_vacation_pay',
        'special_leave_privileges',
        'maternity_leave',
        'paternity_leave_count',
        'paternity_leave_days',
        'forced_leave_taken',
        'monetized_this_year',
        'last_computed_at',
        'computation_notes',
    ];

    protected $casts = [
        'as_of_date' => 'date',
        'last_computed_at' => 'datetime',
        'vacation_leave' => 'decimal:4',
        'sick_leave' => 'decimal:4',
        'vacation_service_credits' => 'decimal:4',
        'proportional_vacation_pay' => 'decimal:4',
        'special_leave_privileges' => 'decimal:2',
        'maternity_leave' => 'decimal:2',
        'paternity_leave_days' => 'decimal:2',
        'forced_leave_taken' => 'decimal:2',
        'monetized_this_year' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}