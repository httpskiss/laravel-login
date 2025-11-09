<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'department', 'filing_date', 'position', 'salary',
        'type', 'leave_location', 'abroad_specify', 'sick_type', 
        'hospital_illness', 'outpatient_illness', 'special_women_illness',
        'study_purpose', 'other_purpose_specify', 'emergency_details',
        'other_leave_details', 'days', 'commutation', 'start_date', 'end_date',
        'reason', 'signature_data', 'credit_as_of_date', 'vacation_earned',
        'vacation_less', 'vacation_balance', 'sick_earned', 'sick_less',
        'sick_balance', 'recommendation', 'disapproval_reason', 'approved_for',
        'with_pay_days', 'without_pay_days', 'others_specify', 'disapproved_reason',
        'status', 'admin_notes', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'filing_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'credit_as_of_date' => 'date',
        'salary' => 'decimal:2',
        'days' => 'decimal:1',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods for leave type details
    public function getLeaveTypeDetails()
    {
        $types = [
            'vacation' => ['Vacation Leave', '(Sec. 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)'],
            'mandatory' => ['Mandatory/Forced Leave', '(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)'],
            'sick' => ['Sick Leave', '(Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)'],
            'maternity' => ['Maternity Leave', '(R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)'],
            'paternity' => ['Paternity Leave', '(R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)'],
            'special_privilege' => ['Special Privilege Leave', '(Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)'],
            'solo_parent' => ['Solo Parent Leave', '(RA No. 8972 / CSC MC No. 8, s. 2004)'],
            'study' => ['Study Leave', '(Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)'],
            'vawc' => ['10-Day VAWC Leave', '(RA No. 9262 / CSC MC No. 15, s. 2005)'],
            'rehabilitation' => ['Rehabilitation Privilege', '(Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)'],
            'special_women' => ['Special Leave Benefits for Women', '(RA No. 9710 / CSC MC No. 25, s. 2010)'],
            'emergency' => ['Special Emergency (Calamity) Leave', '(CSC MC No. 2, s. 2012, as amended)'],
            'adoption' => ['Adoption Leave', '(R.A. No. 8552)'],
            'monetization' => ['Monetization of Leave Credits', ''],
            'terminal' => ['Terminal Leave', ''],
            'other' => ['Other Leave', '']
        ];

        return $types[$this->type] ?? ['Unknown', ''];
    }
}