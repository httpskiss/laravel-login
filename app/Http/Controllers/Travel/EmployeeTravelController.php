<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Models\TravelAuthority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTravelController extends Controller
{
    public function index()
    {
        $travels = TravelAuthority::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employees.travel.index', compact('travels'));
    }

    public function create()
    {
        return view('employees.travel.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'inclusive_date_of_travel' => 'required|date',
            'purpose' => 'required|string',
            'transportation' => 'nullable|in:college_vehicle,public_conveyance',
            'estimated_expenses' => 'required|in:official_time,with_expenses',
            'source_of_funds' => 'nullable|string'
        ]);

        $travel = TravelAuthority::create([
            'user_id' => Auth::id(),
            'designation' => $validated['designation'],
            'destination' => $validated['destination'],
            'inclusive_date_of_travel' => $validated['inclusive_date_of_travel'],
            'purpose' => $validated['purpose'],
            'transportation' => $validated['transportation'],
            'estimated_expenses' => $validated['estimated_expenses'],
            'source_of_funds' => $validated['source_of_funds'],
            'status' => 'pending'
        ]);

        // Generate travel authority number
        $travel->generateTravelAuthorityNo();

        // Create initial approval records based on user's department/role
        $this->createInitialApprovals($travel);

        return redirect()->route('employees.travel')
            ->with('success', 'Travel authority submitted successfully!');
    }

    public function show(TravelAuthority $travel)
    {
        // Ensure user can only view their own travel authorities
        if ($travel->user_id !== Auth::id() && !Auth::user()->hasRole(['Super Admin', 'HR Manager'])) {
            abort(403);
        }

        return view('employees.travel.show', compact('travel'));
    }

    public function destroy(TravelAuthority $travel)
    {
        // Ensure user can only delete their own pending travel authorities
        if ($travel->user_id !== Auth::id() || $travel->status !== 'pending') {
            abort(403);
        }

        $travel->delete();

        return redirect()->route('employees.travel')
            ->with('success', 'Travel authority cancelled successfully!');
    }

    private function createInitialApprovals(TravelAuthority $travel)
    {
        $approvalTypes = [
            'recommending_approval',
            'allotment_available', 
            'funds_available',
            'final_approval'
        ];

        // Define the expected roles for each approval type
        $approvalRoles = [
            'recommending_approval' => 'Department Head',
            'allotment_available' => 'Chief Administrative Officer-Finance',
            'funds_available' => 'Accountant',
            'final_approval' => 'University President'
        ];

        foreach ($approvalTypes as $type) {
            $travel->approvals()->create([
                'approval_type' => $type,
                'approver_role' => $approvalRoles[$type], // Add the expected role
                'status' => 'pending'
            ]);
        }
    }
}