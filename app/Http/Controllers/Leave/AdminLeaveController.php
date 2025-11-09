<?php
// [file name]: AdminLeaveController.php (Updated)
namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;

class AdminLeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with(['user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.leaves', compact('leaves'));
    }

    public function updateStatus(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string',
            'recommendation' => 'nullable|in:approve,disapprove',
            'disapproval_reason' => 'nullable|string',
            'approved_for' => 'nullable|in:with_pay,without_pay,others',
            'with_pay_days' => 'nullable|integer',
            'without_pay_days' => 'nullable|integer',
            'others_specify' => 'nullable|string',
            'disapproved_reason' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ];

        // Add administrative section data if provided
        if ($request->recommendation) {
            $updateData['recommendation'] = $request->recommendation;
            $updateData['disapproval_reason'] = $request->disapproval_reason;
        }

        if ($request->approved_for) {
            $updateData['approved_for'] = $request->approved_for;
            $updateData['with_pay_days'] = $request->with_pay_days;
            $updateData['without_pay_days'] = $request->without_pay_days;
            $updateData['others_specify'] = $request->others_specify;
            $updateData['disapproved_reason'] = $request->disapproved_reason;
        }

        $leave->update($updateData);

        $status = $request->status == 'approved' ? 'approved' : 'rejected';
        return redirect()->route('admin.leaves')->with('success', "Leave application {$status} successfully!");
    }

    public function show(Leave $leave)
    {
        $leave->load(['user', 'approvedBy']);
        return view('admin.leaves-show', compact('leave'));
    }

    // ... other methods remain the same ...
}