<?php
namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeLeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::where('user_id', auth()->id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('employees.leaves', compact('leaves'));
    }

    public function create()
    {
        return view('employees.leaves-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Section 1-5
            'department' => 'required|string',
            'filing_date' => 'required|date',
            'position' => 'required|string',
            'salary' => 'required|numeric',
            
            // Section 6.A
            'type' => 'required|in:vacation,mandatory,sick,maternity,paternity,special_privilege,solo_parent,study,vawc,rehabilitation,special_women,emergency,adoption,monetization,terminal,other',
            
            // Section 6.B (dynamic validation based on type)
            'leave_location' => 'nullable|string',
            'abroad_specify' => 'nullable|string',
            'sick_type' => 'nullable|string',
            'hospital_illness' => 'nullable|string',
            'outpatient_illness' => 'nullable|string',
            'special_women_illness' => 'nullable|string',
            'study_purpose' => 'nullable|string',
            'other_purpose_specify' => 'nullable|string',
            'emergency_details' => 'nullable|string',
            'other_leave_details' => 'nullable|string',
            
            // Section 6.C & 6.D
            'days' => 'required|numeric|min:0.5',
            'commutation' => 'required|in:requested,not_requested',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'signature_data' => 'required|string',
        ]);

        // Add user_id and default status
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        // Calculate leave credits (this should be more sophisticated in production)
        $user = auth()->user();
        $leaveBalances = $user->leaveBalances();
        
        if ($validated['type'] == 'vacation' || $validated['type'] == 'mandatory' || $validated['type'] == 'special_privilege') {
            $validated['vacation_less'] = $validated['days'];
            $validated['vacation_balance'] = $leaveBalances['vacation'] - $validated['days'];
        } elseif ($validated['type'] == 'sick') {
            $validated['sick_less'] = $validated['days'];
            $validated['sick_balance'] = $leaveBalances['sick'] - $validated['days'];
        }

        $validated['vacation_earned'] = $leaveBalances['vacation'];
        $validated['sick_earned'] = $leaveBalances['sick'];
        $validated['credit_as_of_date'] = now();

        try {
            Leave::create($validated);
            return redirect()->route('employees.leaves')->with('success', 'Leave application submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit leave application. Please try again.');
        }
    }

    public function show(Leave $leave)
    {
        // Ensure user can only view their own leaves
        if ($leave->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('employees.leaves-show', compact('leave'));
    }

    public function cancel(Request $request, Leave $leave)
    {
        if ($leave->user_id !== auth()->id()) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leave applications can be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);

        return redirect()->route('employees.leaves')->with('success', 'Leave application cancelled successfully.');
    }

    // NEW METHODS FOR LEAVE APPLICATION HISTORY

    /**
     * Show leave application details for modal (AJAX)
     */
    public function showDetails($id)
    {
        $application = Leave::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (request()->ajax()) {
            $html = view('employees.partials.leave-application-details', compact('application'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return view('employees.leave-details', compact('application'));
    }

    /**
     * Cancel leave application via AJAX
     */
    public function cancelApplication($id)
    {
        $application = Leave::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $application->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application cancelled successfully'
        ]);
    }

    /**
     * Get leave balances for the current user
     */
    private function getLeaveBalances()
    {
        // This should be replaced with your actual leave balance calculation
        return [
            'vacation' => 15,
            'sick' => 10,
            'maternity' => 105,
            'paternity' => 7
        ];
    }
}