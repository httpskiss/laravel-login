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
            'travel_type' => 'required|string|in:official_time,official_business,personal_abroad,official_travel',
            'designation' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'duration_type' => 'required|string|in:single_day,multiple_days',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'inclusive_date_of_travel' => 'nullable|date',
            'purpose' => 'required|string',
            'transportation' => 'required|string|in:university_vehicle,public_conveyance,private_vehicle',
            'source_of_funds' => 'required|string|in:mooe,personal,other',
            'other_funds_specification' => 'nullable|string|max:255',
        ]);

        try {
            $travel = new TravelAuthority();
            $travel->user_id = Auth::id();
            $travel->fill($validated);
            
            // Set inclusive date based on duration type
            if ($validated['duration_type'] === 'single_day') {
                $travel->inclusive_date_of_travel = $validated['inclusive_date_of_travel'] ?? $validated['start_date'];
                $travel->start_date = $validated['start_date'];
                $travel->end_date = $validated['start_date'];
            } else {
                $travel->inclusive_date_of_travel = $validated['start_date'];
                $travel->start_date = $validated['start_date'];
                $travel->end_date = $validated['end_date'];
            }

            // Auto-set estimated expenses based on travel type
            if (in_array($validated['travel_type'], ['official_time', 'official_business', 'official_travel'])) {
                $travel->estimated_expenses = 'with_expenses';
            } else {
                $travel->estimated_expenses = 'official_time';
            }

            $travel->save();

            return redirect()->route('employees.travel.show', $travel)
                ->with('success', 'Travel authority submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit travel authority: ' . $e->getMessage())
                ->withInput();
        }
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