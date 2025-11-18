<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Models\TravelAuthority;
use App\Models\TravelAuthorityApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTravelController extends Controller
{
    public function index()
    {
        $travels = TravelAuthority::with(['user', 'approvals.approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.travel.index', compact('travels'));
    }

    public function show(TravelAuthority $travel)
    {
        $travel->load(['user', 'approvals.approver']);
        
        // Check if current user can approve any step
        $approvableStep = $travel->canBeApprovedBy(Auth::user());

        return view('admin.travel.show', compact('travel', 'approvableStep'));
    }

    public function approveStep(Request $request, TravelAuthority $travel, $step)
    {
        $validated = $request->validate([
            'comments' => 'nullable|string',
            'status' => 'required|in:approved,rejected'
        ]);

        // Check if user can approve this step
        $approvableStep = $travel->canBeApprovedBy(Auth::user());
        if ($approvableStep !== $step) {
            return redirect()->back()->with('error', 'You are not authorized to approve this step.');
        }

        // Update the approval
        $approval = $travel->approvals()
            ->where('approval_type', $step)
            ->where('status', 'pending')
            ->first();

        if ($approval) {
            $approval->update([
                'approved_by' => Auth::id(),
                'approver_role' => Auth::user()->getRoleNames()->first(),
                'status' => $validated['status'],
                'comments' => $validated['comments'],
                'approved_at' => now()
            ]);

            // If rejected, update travel status
            if ($validated['status'] === 'rejected') {
                $travel->update(['status' => 'rejected']);
            } else {
                // Check if all approvals are done for final approval
                $this->checkAllApprovals($travel);
            }

            return redirect()->back()->with('success', "Travel authority {$validated['status']} successfully!");
        }

        return redirect()->back()->with('error', 'Approval step not found.');
    }

    private function checkAllApprovals(TravelAuthority $travel)
    {
        $pendingApprovals = $travel->approvals()
            ->where('status', 'pending')
            ->count();

        // If no pending approvals, mark as approved
        if ($pendingApprovals === 0) {
            $travel->update(['status' => 'approved']);
        } elseif ($travel->approvals()->where('approval_type', 'recommending_approval')->where('status', 'approved')->exists()) {
            $travel->update(['status' => 'recommending_approval']);
        }
    }

    public function export()
    {
        // Implementation for exporting travel authorities
        // Similar to your leave export functionality
    }

    public function report()
    {
        $travels = TravelAuthority::with(['user', 'approvals.approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.travel.report', compact('travels'));
    }
}