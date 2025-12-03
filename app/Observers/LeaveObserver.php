<?php

namespace App\Observers;

use App\Models\Leave;
use App\Notifications\LeaveStatusNotification;

class LeaveObserver
{
    public function updated(Leave $leave)
    {
        if ($leave->isDirty('status')) {
            // Send notification to employee
            $leave->user->notify(new LeaveStatusNotification($leave));
            
            // Log activity
            activity()
                ->performedOn($leave)
                ->causedBy(auth()->user())
                ->log("Leave status changed to {$leave->status}");
        }
        
        if ($leave->isDirty('approved_for')) {
            // Update leave credits
            $this->deductLeaveCredits($leave);
        }
    }
}