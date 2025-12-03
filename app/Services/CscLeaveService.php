<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\User;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CscLeaveService
{
    /**
     * Compute leave days based on CSC Omnibus Rules
     */
    public function computeLeaveDays(Leave $leave): array
    {
        $user = $leave->user;
        $data = [
            'equivalent_days_csc' => (float) $leave->days,
            'computation_method' => 'standard_8_hour_day',
            'computation_notes' => '',
        ];

        // For part-time employees or those with non-standard work hours
        if ($leave->csc_employee_type === 'part_time' || $user->work_hours_per_day != 8) {
            $data['computation_method'] = 'proportional_based_on_work_hours';
            $workHoursPerDay = $user->work_hours_per_day ?? 8.0;
            $data['equivalent_days_csc'] = round($leave->days * ($workHoursPerDay / 8), 4);
            $data['computation_notes'] = "Based on work hours: {$workHoursPerDay} hours/day (Proportional to 8-hour day)";
        }

        // For half-day leaves with time tracking
        if ($leave->duration_type === 'half_day' && $leave->total_hours) {
            $workHoursPerDay = $user->work_hours_per_day ?? 8.0;
            $data['equivalent_days_csc'] = round($leave->total_hours / $workHoursPerDay, 4);
            $data['computation_method'] = 'exact_hours_to_days';
            $data['computation_notes'] = "{$leave->total_hours} hours ÷ {$workHoursPerDay} hours/day = " . $data['equivalent_days_csc'] . " days";
        }

        // For multiple days with time tracking (if applicable)
        if ($leave->duration_type === 'multiple_days' && $leave->total_hours) {
            $workHoursPerDay = $user->work_hours_per_day ?? 8.0;
            $data['equivalent_days_csc'] = round($leave->total_hours / $workHoursPerDay, 4);
            $data['computation_method'] = 'total_hours_to_days';
            $data['computation_notes'] = "Total {$leave->total_hours} hours ÷ {$workHoursPerDay} hours/day";
        }

        // For teachers using service credits
        if ($leave->is_vacation_service && $leave->service_credits_used > 0) {
            $data['computation_method'] = 'vacation_service_credits';
            $data['computation_notes'] .= " | Using {$leave->service_credits_used} vacation service credits";
        }

        // For LWOP computation
        if ($leave->is_lwop) {
            $lwopData = $this->computeLwopDeduction($leave);
            $data = array_merge($data, $lwopData);
        }

        return $data;
    }

    /**
     * Compute LWOP deduction rates based on CSC rules
     * Rule: 1 day LWOP = 0.25 day VL deduction, max 1 day deduction per 4 days LWOP
     */
    public function computeLwopDeduction(Leave $leave): array
    {
        $lwopDays = (float) $leave->days;
        
        // CSC deduction rates for LWOP
        $deductionRates = [
            1 => 0.25,    // 1 day LWOP = 0.25 day VL deduction
            2 => 0.50,    // 2 days LWOP = 0.50 day VL deduction
            3 => 0.75,    // 3 days LWOP = 0.75 day VL deduction
            4 => 1.00,    // 4 days LWOP = 1.00 day VL deduction
        ];

        // For more than 4 days, it's 1 day deduction per 4 days
        if ($lwopDays > 4) {
            $fullCycles = floor($lwopDays / 4);
            $remainingDays = fmod($lwopDays, 4);
            $rate = $deductionRates[min($remainingDays, 4)] ?? 0;
            $charged = $fullCycles + $rate;
        } else {
            $rate = $deductionRates[min($lwopDays, 4)] ?? 1.00;
            $charged = $lwopDays * $rate;
        }

        return [
            'lwop_deduction_rate' => $rate,
            'lwop_days_charged' => round($charged, 4),
            'computation_notes' => isset($data['computation_notes']) ? 
                $data['computation_notes'] . " | LWOP: {$lwopDays} days × {$rate} rate = {$charged} days charged to VL" :
                "LWOP: {$lwopDays} days × {$rate} rate = {$charged} days charged to VL",
        ];
    }

    /**
     * Check leave eligibility based on CSC rules
     */
    public function checkEligibility(User $user, string $leaveType): array
    {
        $eligible = true;
        $message = '';
        $requirements = [];

        // Get current leave balance
        $balance = $user->leaveBalances;

        switch ($leaveType) {
            case Leave::TYPE_MATERNITY:
                // Only female employees
                if ($user->gender !== 'Female') {
                    $eligible = false;
                    $message = 'Maternity leave is only for female employees';
                }
                // Must be married (for legitimate pregnancy)
                elseif ($user->marital_status !== 'married') {
                    $requirements[] = 'Marriage certificate required';
                }
                // Check if within service period
                elseif ($user->hire_date && $user->hire_date->diffInMonths(now()) < 6) {
                    $eligible = false;
                    $message = 'Must have at least 6 months of service for maternity leave';
                }
                // Check sick leave balance for maternity
                elseif ($balance && $balance->sick_leave < 105) {
                    $requirements[] = 'Insufficient sick leave balance for 105-day maternity leave';
                }
                $requirements[] = 'Medical certificate of pregnancy required';
                $requirements[] = 'Expected date of delivery certificate required';
                break;

            case Leave::TYPE_PATERNITY:
                // Only male employees
                if ($user->gender !== 'Male') {
                    $eligible = false;
                    $message = 'Paternity leave is only for male employees';
                }
                // Must be married
                elseif ($user->marital_status !== 'married') {
                    $eligible = false;
                    $message = 'Only married male employees are eligible for paternity leave';
                }
                // Maximum of 4 deliveries
                elseif ($user->delivery_count >= 4) {
                    $eligible = false;
                    $message = 'Maximum of 4 deliveries reached for paternity leave';
                }
                // Must be within 60 days from delivery
                elseif ($user->last_delivery_date && $user->last_delivery_date->diffInDays(now()) > 60) {
                    $eligible = false;
                    $message = 'Paternity leave must be taken within 60 days from delivery';
                }
                // Check vacation leave balance
                elseif ($balance && $balance->vacation_leave < 7) {
                    $requirements[] = 'Insufficient vacation leave balance for 7-day paternity leave';
                }
                $requirements[] = 'Marriage certificate required';
                $requirements[] = 'Child birth certificate required';
                break;

            case Leave::TYPE_VACATION:
                // Check forced leave requirement
                $forcedLeaveTaken = $balance->forced_leave_taken ?? 0;
                if ($forcedLeaveTaken < 5) {
                    $requirements[] = 'Mandatory 5-day forced leave should be taken first';
                }
                // Check vacation leave balance
                if ($balance && $balance->vacation_leave <= 0) {
                    $eligible = false;
                    $message = 'Insufficient vacation leave balance';
                }
                // Check if applied at least 3 days in advance (except for emergency)
                $requirements[] = 'Apply at least 3 working days in advance';
                break;

            case Leave::TYPE_SICK:
                // Check sick leave balance
                if ($balance && $balance->sick_leave <= 0) {
                    $eligible = false;
                    $message = 'Insufficient sick leave balance';
                }
                // Medical certificate requirements
                $requirements[] = 'Medical certificate required for leaves exceeding 3 days';
                $requirements[] = 'Immediate notification required for emergency cases';
                break;

            case Leave::TYPE_SPECIAL_PRIVILEGE:
                // Check SLP balance (max 3 days per year)
                $slpUsed = $balance->special_leave_privileges ?? 0;
                if ($slpUsed >= 3) {
                    $eligible = false;
                    $message = 'Maximum 3 days of Special Leave Privilege already used this year';
                }
                $requirements[] = 'Supporting documents required based on SLP type';
                $requirements[] = 'Maximum 3 days per calendar year (non-cumulative)';
                break;

            case Leave::TYPE_STUDY:
                // Must have at least 2 years of service
                if ($user->hire_date && $user->hire_date->diffInYears(now()) < 2) {
                    $eligible = false;
                    $message = 'Must have at least 2 years of service for study leave';
                }
                $requirements[] = 'Study leave contract required';
                $requirements[] = 'Proof of enrollment/acceptance required';
                break;

            case Leave::TYPE_MANDATORY:
                // Must have at least 5 days VL balance
                if ($balance && $balance->vacation_leave < 5) {
                    $eligible = false;
                    $message = 'Minimum 5 days vacation leave required for mandatory leave';
                }
                $requirements[] = 'Must be taken within the calendar year';
                $requirements[] = 'Non-cumulative, forfeited if not taken';
                break;

            case Leave::TYPE_REHABILITATION:
                // Must be due to work-related injury/illness
                $requirements[] = 'Medical certificate detailing injury/illness required';
                $requirements[] = 'Police/accident report if applicable';
                $requirements[] = 'Written concurrence of government physician required';
                break;

            case Leave::TYPE_SPECIAL_WOMEN:
                // Only for gynecological surgeries
                if ($user->gender !== 'Female') {
                    $eligible = false;
                    $message = 'Special Leave Benefits for Women is only for female employees';
                }
                $requirements[] = 'Medical certificate from attending surgeon required';
                $requirements[] = 'Clinical summary and histopathological report required';
                break;

            case Leave::TYPE_VAWC:
                // Only for women employees
                if ($user->gender !== 'Female') {
                    $eligible = false;
                    $message = 'VAWC leave is only for women employees';
                }
                $requirements[] = 'Barangay Protection Order (BPO) or Court Protection Order required';
                $requirements[] = 'Police report and medical certificate if no protection order';
                break;

            case Leave::TYPE_ADOPTION:
                // Must have DSWD certification
                $requirements[] = 'DSWD Pre-Adoptive Placement Authority required';
                $requirements[] = 'Authenticated copy of adoption papers';
                break;

            case Leave::TYPE_TERMINAL:
                // Must be separating from service
                if (!in_array($user->user_status, ['Terminated', 'Retired'])) {
                    $eligible = false;
                    $message = 'Terminal leave is only for employees separating from service';
                }
                $requirements[] = 'Clearance from money, property, and work accountabilities';
                $requirements[] = 'Proof of retirement/resignation/separation';
                break;
        }

        // General requirements for all leaves
        if ($leaveType !== Leave::TYPE_SICK && $leaveType !== Leave::TYPE_EMERGENCY) {
            $requirements[] = 'Apply in advance whenever possible';
        }

        // For leaves 30 days or more
        if (in_array($leaveType, [Leave::TYPE_STUDY, Leave::TYPE_REHABILITATION, Leave::TYPE_TERMINAL])) {
            $requirements[] = 'Clearance from all accountabilities required';
        }

        return [
            'eligible' => $eligible,
            'message' => $message,
            'requirements' => $requirements,
        ];
    }

    /**
     * Compute leave credits deduction for approval
     */
    public function computeLeaveCredits(Leave $leave, User $user): array
    {
        $balance = $user->leaveBalances;
        if (!$balance) {
            $balance = new LeaveBalance();
            $balance->vacation_leave = 15; // Default annual vacation leave
            $balance->sick_leave = 15;     // Default annual sick leave
        }

        $daysToDeduct = $leave->equivalent_days_csc ?? $leave->days;
        $vacationLess = 0;
        $sickLess = 0;

        // Determine which leave type to deduct from based on CSC rules
        switch ($leave->type) {
            case Leave::TYPE_VACATION:
            case Leave::TYPE_MANDATORY:
            case Leave::TYPE_PATERNITY:
            case Leave::TYPE_SPECIAL_PRIVILEGE:
            case Leave::TYPE_SOLO_PARENT:
            case Leave::TYPE_STUDY:
            case Leave::TYPE_VAWC:
            case Leave::TYPE_ADOPTION:
            case Leave::TYPE_MONETIZATION:
            case Leave::TYPE_TERMINAL:
                $vacationLess = $daysToDeduct;
                break;

            case Leave::TYPE_SICK:
            case Leave::TYPE_MATERNITY:
            case Leave::TYPE_REHABILITATION:
            case Leave::TYPE_SPECIAL_WOMEN:
            case Leave::TYPE_EMERGENCY:
                $sickLess = $daysToDeduct;
                break;

            case Leave::TYPE_OTHER:
                // For other leave types, deduct from whichever has balance
                if ($balance->vacation_leave >= $daysToDeduct) {
                    $vacationLess = $daysToDeduct;
                } elseif ($balance->sick_leave >= $daysToDeduct) {
                    $sickLess = $daysToDeduct;
                } else {
                    // Not enough balance in either, will be LWOP
                    $leave->is_lwop = true;
                    $lwopData = $this->computeLwopDeduction($leave);
                    $leave->lwop_deduction_rate = $lwopData['lwop_deduction_rate'];
                    $leave->lwop_days_charged = $lwopData['lwop_days_charged'];
                }
                break;
        }

        // For LWOP, also charge to vacation leave based on CSC rates
        if ($leave->is_lwop && $leave->lwop_days_charged) {
            $vacationLess += $leave->lwop_days_charged;
        }

        // For teachers using service credits
        if ($leave->is_vacation_service && $leave->service_credits_used > 0) {
            $vacationLess = 0; // Not deducted from regular VL
            $sickLess = 0;     // Not deducted from SL
        }

        return [
            'vacation_earned' => $balance->vacation_leave,
            'sick_earned' => $balance->sick_leave,
            'vacation_less' => $vacationLess,
            'sick_less' => $sickLess,
            'vacation_balance' => max(0, $balance->vacation_leave - $vacationLess),
            'sick_balance' => max(0, $balance->sick_leave - $sickLess),
        ];
    }

    /**
     * Calculate maternity leave entitlement based on CSC rules
     */
    public function computeMaternityLeave(User $user, Leave $leave): array
    {
        $entitlement = 105; // Standard maternity leave
        
        // Additional 15 days for solo mothers
        if ($user->marital_status === 'single' || $user->marital_status === 'separated') {
            $entitlement += 15;
        }
        
        // For miscarriage or ectopic pregnancy
        if ($leave->is_miscarriage) {
            $entitlement = 60; // 60 days for miscarriage
        }
        
        // For 4th and subsequent deliveries
        if ($user->delivery_count >= 3) {
            $entitlement = 105; // Regular 105 days (no additional for 4th+)
        }
        
        // Check service requirement
        $serviceMonths = $user->hire_date ? $user->hire_date->diffInMonths(now()) : 0;
        if ($serviceMonths < 6) {
            return [
                'eligible' => false,
                'entitlement' => 0,
                'message' => 'Must have at least 6 months of service for maternity leave',
            ];
        }
        
        return [
            'eligible' => true,
            'entitlement' => $entitlement,
            'message' => "Entitled to {$entitlement} days maternity leave",
        ];
    }

    /**
     * Calculate forced/mandatory leave requirement
     */
    public function computeForcedLeave(User $user): array
    {
        $balance = $user->leaveBalances;
        $forcedLeaveTaken = $balance->forced_leave_taken ?? 0;
        $required = 5; // Minimum 5 days forced leave per year
        $remaining = max(0, $required - $forcedLeaveTaken);
        
        // Check if has enough VL for forced leave
        $hasEnoughVL = ($balance->vacation_leave ?? 0) >= $remaining;
        
        return [
            'required_days' => $required,
            'taken_days' => $forcedLeaveTaken,
            'remaining_days' => $remaining,
            'has_enough_vl' => $hasEnoughVL,
            'deadline' => Carbon::now()->endOfYear()->format('Y-m-d'),
            'message' => $remaining > 0 ? 
                "You need to take {$remaining} more days of forced leave before " . Carbon::now()->endOfYear()->format('F j, Y') :
                "Forced leave requirement satisfied for this year",
        ];
    }

    /**
     * Check Special Leave Privilege (SLP) eligibility
     */
    public function checkSlpEligibility(User $user, string $slpType, float $requestedDays): array
    {
        $balance = $user->leaveBalances;
        $slpUsed = $balance->special_leave_privileges ?? 0;
        $slpRemaining = max(0, 3 - $slpUsed);
        
        // Check if requested days exceed remaining SLP
        if ($requestedDays > $slpRemaining) {
            return [
                'eligible' => false,
                'message' => "Only {$slpRemaining} days of SLP remaining. You requested {$requestedDays} days.",
                'remaining_days' => $slpRemaining,
                'max_per_type' => $this->getSlpMaxDays($slpType),
            ];
        }
        
        // Check type-specific limitations
        $maxForType = $this->getSlpMaxDays($slpType);
        if ($requestedDays > $maxForType) {
            return [
                'eligible' => false,
                'message' => "Maximum {$maxForType} days allowed for {$slpType}",
                'remaining_days' => $slpRemaining,
                'max_per_type' => $maxForType,
            ];
        }
        
        return [
            'eligible' => true,
            'message' => "Eligible for {$requestedDays} days of SLP ({$slpType})",
            'remaining_days' => $slpRemaining,
            'max_per_type' => $maxForType,
        ];
    }
    
    /**
     * Get maximum days allowed per SLP type
     */
    private function getSlpMaxDays(string $slpType): int
    {
        $limits = [
            'funeral_mourning' => 3,
            'graduation' => 1,
            'enrollment' => 1,
            'wedding_anniversary' => 1,
            'birthday' => 1,
            'hospitalization' => 2,
            'accident' => 2,
            'relocation' => 2,
            'government_transaction' => 1,
            'calamity' => 2,
        ];
        
        return $limits[$slpType] ?? 1;
    }

    /**
     * Compute terminal leave monetization
     */
    public function computeTerminalLeave(User $user, float $accumulatedDays): array
    {
        // Get highest salary received
        $highestSalary = $user->salary; // This should be the highest salary during employment
        
        // Daily rate calculation (22 working days per month)
        $dailyRate = $highestSalary / 22;
        
        // Monetizable days (max 30 days per year of service)
        $serviceYears = $user->hire_date ? $user->hire_date->diffInYears(now()) : 0;
        $maxMonetizable = min($accumulatedDays, $serviceYears * 30);
        
        // Terminal leave pay
        $terminalPay = $maxMonetizable * $dailyRate;
        
        // Leave balance after monetization
        $remainingBalance = max(0, $accumulatedDays - $maxMonetizable);
        
        return [
            'accumulated_days' => $accumulatedDays,
            'service_years' => $serviceYears,
            'highest_salary' => $highestSalary,
            'daily_rate' => round($dailyRate, 2),
            'max_monetizable' => $maxMonetizable,
            'terminal_pay' => round($terminalPay, 2),
            'remaining_balance' => $remainingBalance,
            'formula' => "({$highestSalary} ÷ 22) × {$maxMonetizable} days",
        ];
    }

    /**
     * Validate medical certificate for CSC compliance
     */
    public function validateMedicalCertificate(Leave $leave, $certificateData): array
    {
        $valid = true;
        $issues = [];
        
        // Check required fields based on leave type
        switch ($leave->type) {
            case Leave::TYPE_SICK:
                $requiredFields = ['diagnosis', 'physician_name', 'license_number', 'issuance_date'];
                foreach ($requiredFields as $field) {
                    if (empty($certificateData[$field] ?? null)) {
                        $valid = false;
                        $issues[] = "Missing required field: {$field}";
                    }
                }
                
                // Check validity period (30 days for sick leave)
                if (!empty($certificateData['issuance_date'])) {
                    $issuanceDate = Carbon::parse($certificateData['issuance_date']);
                    if ($issuanceDate->diffInDays(now()) > 30) {
                        $valid = false;
                        $issues[] = "Medical certificate is more than 30 days old";
                    }
                }
                break;
                
            case Leave::TYPE_MATERNITY:
                $requiredFields = ['pregnancy_confirmation', 'expected_delivery_date', 'physician_name', 'hospital_name'];
                foreach ($requiredFields as $field) {
                    if (empty($certificateData[$field] ?? null)) {
                        $valid = false;
                        $issues[] = "Missing required field: {$field}";
                    }
                }
                break;
                
            case Leave::TYPE_REHABILITATION:
                $requiredFields = ['injury_details', 'treatment_plan', 'rehabilitation_period', 'government_physician_concurrence'];
                foreach ($requiredFields as $field) {
                    if (empty($certificateData[$field] ?? null)) {
                        $valid = false;
                        $issues[] = "Missing required field: {$field}";
                    }
                }
                break;
        }
        
        return [
            'valid' => $valid,
            'issues' => $issues,
            'certificate_type' => $leave->type,
            'requirements_met' => $valid ? 'All CSC requirements met' : implode(', ', $issues),
        ];
    }

    /**
     * Calculate actual service days for leave credit earning
     */
    public function calculateServiceDaysForLeaveEarning(User $user, Carbon $fromDate, Carbon $toDate): float
    {
        $totalDays = 0;
        $currentDate = $fromDate->copy();
        
        while ($currentDate->lte($toDate)) {
            // Count only weekdays (Monday to Friday)
            if (!$currentDate->isWeekend()) {
                // Check if not on official holiday (you need to implement holiday checking)
                // Check if not on approved leave (you need to check leave records)
                // For now, count all weekdays
                $totalDays++;
            }
            $currentDate->addDay();
        }
        
        // Adjust for part-time employees
        if ($user->employee_classification === 'part_time') {
            $workPercentage = $user->work_hours_per_week / 40;
            $totalDays = $totalDays * $workPercentage;
        }
        
        return round($totalDays, 4);
    }

    /**
     * Generate computation notes for audit trail
     */
    public function generateComputationNotes(Leave $leave): string
    {
        $notes = [];
        
        // Basic information
        $notes[] = "Employee Type: " . $leave->getCscEmployeeTypeDisplay();
        $notes[] = "Leave Basis: " . $leave->getLeaveBasisDisplay();
        $notes[] = "Requested Days: " . $leave->days;
        
        // CSC equivalent days if different
        if ($leave->equivalent_days_csc && $leave->equivalent_days_csc != $leave->days) {
            $notes[] = "CSC Equivalent Days: " . $leave->equivalent_days_csc;
        }
        
        // Work hours adjustment
        if ($leave->user->work_hours_per_day != 8) {
            $notes[] = "Work Hours/Day: " . $leave->user->work_hours_per_day;
        }
        
        // LWOP computation
        if ($leave->is_lwop) {
            $notes[] = "LWOP Deduction Rate: " . $leave->lwop_deduction_rate;
            $notes[] = "LWOP Days Charged: " . $leave->lwop_days_charged;
        }
        
        // Special cases
        if ($leave->is_vacation_service) {
            $notes[] = "Using Vacation Service Credits: " . $leave->service_credits_used;
        }
        
        if ($leave->slp_type !== 'none') {
            $notes[] = "Special Leave Privilege Type: " . $leave->getSlpTypeDisplay();
        }
        
        return implode(' | ', $notes);
    }
}