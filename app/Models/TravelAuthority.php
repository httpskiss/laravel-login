<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAuthority extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'travel_authority_no',
        'designation',
        'destination',
        'inclusive_date_of_travel',
        'purpose',
        'transportation',
        'estimated_expenses',
        'source_of_funds',
        'travel_type',
        'duration_type',
        'start_date',
        'end_date',
        'other_funds_specification',
        'status',
        'remarks'
    ];

    protected $casts = [
        'inclusive_date_of_travel' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Travel Type Constants
    const TYPE_OFFICIAL_TIME = 'official_time';
    const TYPE_OFFICIAL_BUSINESS = 'official_business';
    const TYPE_PERSONAL_ABROAD = 'personal_abroad';
    const TYPE_OFFICIAL_TRAVEL = 'official_travel';

    // Duration Type Constants
    const DURATION_SINGLE_DAY = 'single_day';
    const DURATION_MULTIPLE_DAYS = 'multiple_days';

    // Transportation Constants
    const TRANSPORT_UNIVERSITY_VEHICLE = 'university_vehicle';
    const TRANSPORT_PUBLIC_CONVEYANCE = 'public_conveyance';
    const TRANSPORT_PRIVATE_VEHICLE = 'private_vehicle';

    // Source of Funds Constants
    const FUNDS_MOOE = 'mooe';
    const FUNDS_PERSONAL = 'personal';
    const FUNDS_OTHER = 'other';

    public static function getTravelTypes()
    {
        return [
            self::TYPE_OFFICIAL_TIME => 'Official Time',
            self::TYPE_OFFICIAL_BUSINESS => 'Official Business',
            self::TYPE_PERSONAL_ABROAD => 'Personal Travel Abroad',
            self::TYPE_OFFICIAL_TRAVEL => 'Official Travel',
        ];
    }

    public static function getDurationTypes()
    {
        return [
            self::DURATION_SINGLE_DAY => 'Single Day',
            self::DURATION_MULTIPLE_DAYS => 'Multiple Days',
        ];
    }

    public static function getTransportationTypes()
    {
        return [
            self::TRANSPORT_UNIVERSITY_VEHICLE => 'University Vehicle',
            self::TRANSPORT_PUBLIC_CONVEYANCE => 'Public Conveyance',
            self::TRANSPORT_PRIVATE_VEHICLE => 'Private Vehicle',
        ];
    }

    public static function getFundSources()
    {
        return [
            self::FUNDS_MOOE => 'MOOE (Maintenance and Other Operating Expenses)',
            self::FUNDS_PERSONAL => 'Personal Funds',
            self::FUNDS_OTHER => 'Other Sources',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(TravelAuthorityApproval::class);
    }

    /**
     * Check if travel requires president approval
     */
    public function requiresPresidentApproval(): bool
    {
        return $this->travel_type === self::TYPE_PERSONAL_ABROAD;
    }

    /**
     * Check if travel uses official funds
     */
    public function usesOfficialFunds(): bool
    {
        return in_array($this->travel_type, [
            self::TYPE_OFFICIAL_TIME,
            self::TYPE_OFFICIAL_BUSINESS,
            self::TYPE_OFFICIAL_TRAVEL
        ]);
    }

     public function canBeApprovedBy(User $user)
    {
        $userRole = $user->getRoleNames()->first();
        
        // Define which roles can approve which steps
        $approvalRoles = [
            'recommending_approval' => ['Department Head', 'Super Admin', 'HR Manager'],
            'allotment_available' => ['Chief Administrative Officer-Finance', 'Finance Officer', 'Super Admin'],
            'funds_available' => ['Accountant', 'Finance Officer', 'Super Admin'],
            'final_approval' => ['University President', 'Super Admin']
        ];

        foreach ($approvalRoles as $step => $roles) {
            $approval = $this->approvals()->where('approval_type', $step)->first();
            
            // If approval step exists and is pending, and user has the right role
            if ($approval && $approval->status === 'pending' && in_array($userRole, $roles)) {
                return $step;
            }
        }

        return null;
    }

     public function getNextApprovalStep()
    {
        $steps = ['recommending_approval', 'allotment_available', 'funds_available', 'final_approval'];
        
        foreach ($steps as $step) {
            $approval = $this->approvals()->where('approval_type', $step)->first();
            if ($approval && $approval->status === 'pending') {
                return $step;
            }
        }
        
        return null;
    }
}