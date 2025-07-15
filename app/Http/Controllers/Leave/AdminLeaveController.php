<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AdminLeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with(['user', 'approver'])
            ->latest()
            ->paginate(10);
        
        $stats = [
            'total' => Leave::count(),
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
        ];

        // Get active users for the dropdown
        $users = User::where('user_status', 'Active')
            ->select('id', 'first_name', 'last_name', 'employee_id')
            ->orderBy('first_name')
            ->get();

        return view('admin.leaves', compact('leaves', 'stats', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:vacation,sick,emergency,maternity,paternity,bereavement,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'status' => 'sometimes|in:pending,approved,rejected,cancelled',
        ]);

        // Calculate days difference
        $start = new \DateTime($validated['start_date']);
        $end = new \DateTime($validated['end_date']);
        $validated['days'] = $start->diff($end)->days + 1; // Include both start and end days

        // Only set approved_by if status is approved
        if ($validated['status'] === 'approved') {
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
        }

        Leave::create($validated);

        return redirect()->route('admin.leaves')
            ->with('success', 'Leave request created successfully.');
    }

    public function updateStatus(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        // Only update approval info if status is being changed to approved
        if ($request->status === 'approved') {
            $updateData['approved_by'] = Auth::id();
            $updateData['approved_at'] = now();
        }

        $leave->update($updateData);

        return back()->with('success', 'Leave status updated successfully.');
    }

    public function details(Leave $leave)
    {
        return response()->json([
            'leave' => $leave->load(['user', 'approver']),
            'user' => $leave->user,
            'approver' => $leave->approver,
        ]);
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return back()->with('success', 'Leave request deleted successfully.');
    }

    public function export(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leaves-export-'.date('Y-m-d').'.csv"',
        ];

        $leaves = Leave::with(['user', 'approver'])
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('first_name', 'like', '%'.$request->search.'%')
                      ->orWhere('last_name', 'like', '%'.$request->search.'%')
                      ->orWhere('employee_id', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->get();

        $callback = function() use ($leaves) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add CSV headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Leave Type',
                'Start Date',
                'End Date',
                'Duration (Days)',
                'Reason',
                'Status',
                'Admin Notes',
                'Approved By',
                'Approved At',
                'Created At'
            ]);

            // Add data rows
            foreach ($leaves as $leave) {
                fputcsv($file, [
                    $leave->user->first_name.' '.$leave->user->last_name,
                    $leave->user->employee_id,
                    ucfirst($leave->type),
                    $leave->start_date->format('Y-m-d'),
                    $leave->end_date->format('Y-m-d'),
                    $leave->days,
                    $leave->reason,
                    ucfirst($leave->status),
                    $leave->admin_notes ?? '',
                    $leave->approver ? $leave->approver->first_name.' '.$leave->approver->last_name : '',
                    $leave->approved_at ? $leave->approved_at->format('Y-m-d H:i:s') : '',
                    $leave->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}