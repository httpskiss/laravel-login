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
        'status',
        'remarks'
    ];

    protected $casts = [
        'inclusive_date_of_travel' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(TravelAuthorityApproval::class);
    }

    public function recommendingApproval()
    {
        return $this->hasOne(TravelAuthorityApproval::class)->where('approval_type', 'recommending_approval');
    }

    public function allotmentApproval()
    {
        return $this->hasOne(TravelAuthorityApproval::class)->where('approval_type', 'allotment_available');
    }

    public function fundsApproval()
    {
        return $this->hasOne(TravelAuthorityApproval::class)->where('approval_type', 'funds_available');
    }

    public function finalApproval()
    {
        return $this->hasOne(TravelAuthorityApproval::class)->where('approval_type', 'final_approval');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Methods
    public function generateTravelAuthorityNo()
    {
        $year = now()->format('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        $this->travel_authority_no = "TA-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        $this->save();
    }

    public function canBeApprovedBy($user)
    {
        $userRoles = $user->getRoleNames()->toArray();
        
        // Define which roles can approve which steps
        $approvalRules = [
            'recommending_approval' => ['Department Head', 'VP for Research, Innovation and Extension Services'],
            'allotment_available' => ['Chief Administrative Officer-Finance'],
            'funds_available' => ['Accountant'],
            'final_approval' => ['University President']
        ];

        foreach ($approvalRules as $type => $roles) {
            if (array_intersect($userRoles, $roles)) {
                $approvalExists = $this->approvals()->where('approval_type', $type)->exists();
                if (!$approvalExists) {
                    return $type;
                }
            }
        }

        return false;
    }
}