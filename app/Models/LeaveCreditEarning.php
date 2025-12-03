<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveCreditEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'earning_date',
        'credit_type',
        'days_earned',
        'rate_per_day',
        'description',
        'computation_details',
    ];

    protected $casts = [
        'earning_date' => 'date',
        'days_earned' => 'decimal:4',
        'rate_per_day' => 'decimal:4',
        'computation_details' => 'array',
    ];

    const TYPE_VACATION = 'vacation_leave';
    const TYPE_SICK = 'sick_leave';
    const TYPE_VACATION_SERVICE = 'vacation_service';
    const TYPE_MATERNITY = 'maternity';
    const TYPE_PATERNITY = 'paternity';
    const TYPE_SPECIAL_PRIVILEGE = 'special_privilege';
    const TYPE_FORCED_LEAVE = 'forced_leave';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getCreditTypes(): array
    {
        return [
            self::TYPE_VACATION => 'Vacation Leave',
            self::TYPE_SICK => 'Sick Leave',
            self::TYPE_VACATION_SERVICE => 'Vacation Service Credits',
            self::TYPE_MATERNITY => 'Maternity Leave',
            self::TYPE_PATERNITY => 'Paternity Leave',
            self::TYPE_SPECIAL_PRIVILEGE => 'Special Leave Privilege',
            self::TYPE_FORCED_LEAVE => 'Forced Leave',
        ];
    }
}