<?php

namespace App\Http\Controllers\Complaints;

use App\Http\Controllers\Controller;
use App\Models\EmployeeComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeComplaintController extends Controller
{
    public function index()
    {
        $complaints = EmployeeComplaint::where('user_id', Auth::id())
            ->with(['assignedHR', 'updates.updater'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $complaints->count(),
            'pending' => $complaints->whereIn('status', ['submitted', 'under_review', 'investigation_started'])->count(),
            'resolved' => $complaints->whereIn('status', ['resolved', 'closed'])->count(),
        ];

        return view('employees.complaints.index', compact('complaints', 'stats'));
    }

    public function create()
    {
        return view('employees.complaints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:harassment,discrimination,workplace_bullying,safety_concern,ethical_concern,work_environment,management_issue,other',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:20|max:2000',
            'incident_details' => 'nullable|string|max:1000',
            'incident_date' => 'nullable|date|before_or_equal:today',
            'location' => 'nullable|string|max:255',
            'involved_parties' => 'nullable|array',
            'involved_parties.*.name' => 'required|string|max:255',
            'involved_parties.*.role' => 'nullable|string|max:255',
            'is_anonymous' => 'boolean',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $complaint = EmployeeComplaint::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'incident_details' => $validated['incident_details'],
            'incident_date' => $validated['incident_date'],
            'location' => $validated['location'],
            'involved_parties' => $validated['involved_parties'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'priority' => $validated['priority'],
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        // Generate complaint number
        $complaint->generateComplaintNumber();

        // Create initial update
        $complaint->addUpdate([
            'update_type' => 'submission',
            'description' => 'Complaint submitted successfully.',
            'is_internal_note' => false
        ]);

        return redirect()->route('employees.complaints.index')
            ->with('success', 'Your complaint has been submitted successfully. We will review it shortly.');
    }

    public function show(EmployeeComplaint $complaint)
    {
        // Ensure user can only view their own complaints
        if ($complaint->user_id !== Auth::id()) {
            abort(403);
        }

        $complaint->load(['assignedHR', 'updates' => function($query) {
            $query->where('is_internal_note', false)
                  ->with('updater')
                  ->orderBy('created_at', 'desc');
        }]);

        return view('employees.complaints.show', compact('complaint'));
    }
}