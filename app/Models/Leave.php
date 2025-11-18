<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department',
        'filing_date',
        'position',
        'salary',
        'type',
        'leave_location',
        'abroad_specify',
        'sick_type',
        'hospital_illness',
        'outpatient_illness',
        'special_women_illness',
        'study_purpose',
        'other_purpose_specify',
        'emergency_details',
        'other_leave_details',
        'start_date',
        'end_date',
        'days',
        'commutation',
        'reason',
        'signature_data',
        'credit_as_of_date',
        'vacation_earned',
        'vacation_less',
        'vacation_balance',
        'sick_earned',
        'sick_less',
        'sick_balance',
        'recommendation',
        'disapproval_reason',
        'approved_for',
        'with_pay_days',
        'without_pay_days',
        'others_specify',
        'disapproved_reason',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        // Add new fields
        'medical_certificate_path',
        'travel_itinerary_path',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'handover_person_id',
        'handover_notes',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'credit_as_of_date' => 'date',
        'salary' => 'decimal:2',
        'days' => 'decimal:1',
        'vacation_earned' => 'decimal:1',
        'vacation_less' => 'decimal:1',
        'vacation_balance' => 'decimal:1',
        'sick_earned' => 'decimal:1',
        'sick_less' => 'decimal:1',
        'sick_balance' => 'decimal:1',
        'approved_at' => 'datetime',
    ];

    // Leave type constants
    const TYPE_VACATION = 'vacation';
    const TYPE_MANDATORY = 'mandatory';
    const TYPE_SICK = 'sick';
    const TYPE_MATERNITY = 'maternity';
    const TYPE_PATERNITY = 'paternity';
    const TYPE_SPECIAL_PRIVILEGE = 'special_privilege';
    const TYPE_SOLO_PARENT = 'solo_parent';
    const TYPE_STUDY = 'study';
    const TYPE_VAWC = 'vawc';
    const TYPE_REHABILITATION = 'rehabilitation';
    const TYPE_SPECIAL_WOMEN = 'special_women';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_ADOPTION = 'adoption';
    const TYPE_MONETIZATION = 'monetization';
    const TYPE_TERMINAL = 'terminal';
    const TYPE_OTHER = 'other';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Commutation constants
    const COMMUTATION_REQUESTED = 'requested';
    const COMMUTATION_NOT_REQUESTED = 'not_requested';

    // Recommendation constants
    const RECOMMENDATION_APPROVE = 'approve';
    const RECOMMENDATION_DISAPPROVE = 'disapprove';

    // Approved for constants
    const APPROVED_WITH_PAY = 'with_pay';
    const APPROVED_WITHOUT_PAY = 'without_pay';
    const APPROVED_OTHERS = 'others';

    /**
     * Get available leave types
     */
    public static function getLeaveTypes(): array
    {
        return [
            self::TYPE_VACATION => 'Vacation Leave',
            self::TYPE_MANDATORY => 'Mandatory/Forced Leave',
            self::TYPE_SICK => 'Sick Leave',
            self::TYPE_MATERNITY => 'Maternity Leave',
            self::TYPE_PATERNITY => 'Paternity Leave',
            self::TYPE_SPECIAL_PRIVILEGE => 'Special Privilege Leave',
            self::TYPE_SOLO_PARENT => 'Solo Parent Leave',
            self::TYPE_STUDY => 'Study Leave',
            self::TYPE_VAWC => '10-Day VAWC Leave',
            self::TYPE_REHABILITATION => 'Rehabilitation Privilege Leave',
            self::TYPE_SPECIAL_WOMEN => 'Special Leave Benefits for Women',
            self::TYPE_EMERGENCY => 'Special Emergency (Calamity) Leave',
            self::TYPE_ADOPTION => 'Adoption Leave',
            self::TYPE_MONETIZATION => 'Monetization of Leave Credits',
            self::TYPE_TERMINAL => 'Terminal Leave',
            self::TYPE_OTHER => 'Other Leave Types',
        ];
    }

    /**
     * Get leave type requirements
     */
    public static function getLeaveRequirements(string $type): array
    {
        $requirements = [
            self::TYPE_VACATION => [
                'title' => 'Vacation Leave Requirements',
                'color' => 'blue',
                'items' => [
                    'Minimum 3 working days advance notice required',
                    'Maximum consecutive leave: 15 working days',
                    'Blackout periods may apply during peak seasons',
                    'Coordinate with your team before submission'
                ]
            ],
            self::TYPE_SICK => [
                'title' => 'Sick Leave Requirements',
                'color' => 'green',
                'items' => [
                    'Medical certificate required for leaves exceeding 3 days',
                    'Notification should be sent as soon as possible',
                    'Follow-up documents may be requested by HR',
                    'Contact your supervisor immediately for emergencies'
                ]
            ],
            self::TYPE_MATERNITY => [
                'title' => 'Maternity Leave Requirements',
                'color' => 'pink',
                'items' => [
                    '105 days maternity leave as per R.A. No. 11210',
                    'Submit certificate of pregnancy from physician',
                    'Additional 15 days for solo mothers',
                    '30 days for miscarriage or ectopic pregnancy'
                ]
            ],
        ];

        return $requirements[$type] ?? [
            'title' => 'Leave Requirements',
            'color' => 'blue',
            'items' => ['Please ensure all required documents are submitted', 'Follow agency-specific guidelines for this leave type']
        ];
    }

    /**
     * Relationship with User (Applicant)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Approver
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship with Handover Person - FIXED
     */
    public function handoverPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handover_person_id');
    }

    /**
     * Check if leave can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Calculate total days
     */
    public function calculateDays(): float
    {
        if ($this->start_date && $this->end_date) {
            $start = $this->start_date;
            $end = $this->end_date;
            return $end->diffInDays($start) + 1;
        }
        return 0;
    }

    /**
     * Get formatted status with color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray'
        };
    }

    /**
     * Scope for pending leaves
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved leaves
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected leaves
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}