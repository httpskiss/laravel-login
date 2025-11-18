<?php

namespace App\Http\Controllers\Complaints;

use App\Http\Controllers\Controller;
use App\Models\EmployeeComplaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminComplaintController extends Controller
{
    public function index()
    {
        $complaints = EmployeeComplaint::with(['user', 'assignedHR', 'updates'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $complaints->count(),
            'pending' => $complaints->whereIn('status', ['submitted', 'under_review', 'investigation_started'])->count(),
            'resolved' => $complaints->whereIn('status', ['resolved', 'closed'])->count(),
            'high_priority' => $complaints->whereIn('priority', ['high', 'urgent'])->count(),
        ];

        $hrPersonnel = User::role(['HR Manager', 'Super Admin'])->get();

        return view('admin.complaints.index', compact('complaints', 'stats', 'hrPersonnel'));
    }

    public function show(EmployeeComplaint $complaint)
    {
        $complaint->load(['user', 'assignedHR', 'updates.updater']);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, EmployeeComplaint $complaint)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,investigation_started,resolved,rejected,closed',
            'remarks' => 'nullable|string|max:1000',
            'assigned_hr_id' => 'nullable|exists:users,id',
            'is_internal_note' => 'boolean'
        ]);

        $oldStatus = $complaint->status;
        
        $complaint->update([
            'status' => $validated['status'],
            'hr_remarks' => $validated['remarks'],
            'assigned_hr_id' => $validated['assigned_hr_id'] ?? $complaint->assigned_hr_id
        ]);

        // Add update record
        $complaint->addUpdate([
            'update_type' => 'status_change',
            'description' => "Status changed from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $validated['status'])) . ". " . ($validated['remarks'] ?? ''),
            'is_internal_note' => $request->boolean('is_internal_note', false),
            'metadata' => ['old_status' => $oldStatus, 'new_status' => $validated['status']]
        ]);

        return redirect()->back()->with('success', 'Complaint status updated successfully.');
    }

    public function addNote(Request $request, EmployeeComplaint $complaint)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'is_internal_note' => 'boolean'
        ]);

        $complaint->addUpdate([
            'update_type' => 'note',
            'description' => $validated['note'],
            'is_internal_note' => $request->boolean('is_internal_note', true)
        ]);

        return redirect()->back()->with('success', 'Note added successfully.');
    }
}