<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeLeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->leaves()->latest();

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->month) {
            $query->whereMonth('start_date', \Carbon\Carbon::parse($request->month)->month)
                  ->whereYear('start_date', \Carbon\Carbon::parse($request->month)->year);
        }

        $leaves = $query->paginate(10);
        $leaveBalances = Auth::user()->leaveBalances();
        
        return view('employees.leaves.index', compact('leaves', 'leaveBalances'));
    }

    public function create()
    {
        $user = Auth::user();
        $colleagues = User::where('department', $user->department)
                         ->where('id', '!=', $user->id)
                         ->where('user_status', 'Active')
                         ->get();
                         
        return view('employees.leaves.create', [
            'leaveBalances' => $user->leaveBalances(),
            'colleagues' => $colleagues
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|numeric|min:0.5',
            'reason' => 'required|string|max:1000',
            'commutation' => 'required|in:requested,not_requested',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'handover_person_id' => 'nullable|exists:users,id',
            'handover_notes' => 'nullable|string',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'travel_itinerary' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $leave = new Leave();
            $leave->user_id = Auth::id();
            $leave->department = Auth::user()->department;
            $leave->filing_date = now();
            $leave->position = Auth::user()->role;
            $leave->salary = 0;
            $leave->fill($validated);
            
            // Handle file uploads
            if ($request->hasFile('medical_certificate')) {
                $leave->medical_certificate_path = $request->file('medical_certificate')->store('medical_certificates');
            }
            
            if ($request->hasFile('travel_itinerary')) {
                $leave->travel_itinerary_path = $request->file('travel_itinerary')->store('travel_itineraries');
            }
            
            $leave->save();

            return response()->json([
                'success' => true,
                'message' => 'Leave application submitted successfully!',
                'leave' => $leave
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Leave $leave)
    {
        // Ensure the user can only view their own leaves
        if ($leave->user_id !== Auth::id()) {
            abort(403);
        }

        $leave->load(['handoverPerson', 'approvedBy']);
        $html = view('employees.leaves.partials.details', compact('leave'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function destroy(Leave $leave)
    {
        // Ensure the user can only delete their own pending leaves
        if ($leave->user_id !== Auth::id() || $leave->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete pending leave applications.'
            ], 403);
        }

        try {
            $leave->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Leave application deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a pending leave application
     */
    public function cancel(Leave $leave)
    {
        // Ensure the user can only cancel their own pending leaves
        if ($leave->user_id !== Auth::id() || $leave->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'You can only cancel pending leave applications.'
            ], 403);
        }

        try {
            $leave->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Leave application cancelled successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel leave application: ' . $e->getMessage()
            ], 500);
        }
    }
}