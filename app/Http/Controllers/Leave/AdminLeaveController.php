<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminLeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['user', 'handoverPerson', 'approvedBy'])->latest();

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->department) {
            $query->where('department', $request->department);
        }

        if ($request->month) {
            $query->whereMonth('start_date', \Carbon\Carbon::parse($request->month)->month)
                  ->whereYear('start_date', \Carbon\Carbon::parse($request->month)->year);
        }

        $leaves = $query->paginate(15);

        $stats = [
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
            'this_month' => Leave::whereMonth('created_at', now()->month)->count(),
        ];

        $departments = User::distinct()->pluck('department');

        return view('admin.leaves.index', compact('leaves', 'stats', 'departments'));
    }

    public function create()
    {
        // If you need a create method for admin
        return view('admin.leaves.create');
    }

    public function store(Request $request)
    {
        // If you need a store method for admin to create leaves
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|numeric|min:0.5',
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);
            
            $leave = new Leave();
            $leave->user_id = $user->id;
            $leave->department = $user->department;
            $leave->filing_date = now();
            $leave->position = $user->role;
            $leave->salary = 0;
            $leave->fill($validated);
            $leave->save();

            return response()->json([
                'success' => true,
                'message' => 'Leave application created successfully!',
                'leave' => $leave
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Leave $leave)
    {
        $leave->load(['user', 'handoverPerson', 'approvedBy']);
        
        $html = view('admin.leaves.partials.details', compact('leave'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function edit(Leave $leave)
    {
        $leave->load(['user', 'handoverPerson']);
        $departments = User::distinct()->pluck('department');
        
        return view('admin.leaves.edit', compact('leave', 'departments'));
    }

    public function update(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'approved_for' => 'nullable|in:with_pay,without_pay,others',
            'with_pay_days' => 'nullable|numeric|min:0',
            'without_pay_days' => 'nullable|numeric|min:0',
            'others_specify' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $leave->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Leave application updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Leave $leave)
    {
        try {
            $leave->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'recommendation' => 'approve'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave application approved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $leave->update([
                'status' => 'rejected',
                'disapproved_reason' => $request->reason,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'recommendation' => 'disapprove'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave application rejected!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Leave $leave)
    {
        try {
            // Delete associated files
            if ($leave->medical_certificate_path) {
                Storage::delete($leave->medical_certificate_path);
            }
            if ($leave->travel_itinerary_path) {
                Storage::delete($leave->travel_itinerary_path);
            }

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

    public function export(Request $request)
    {
        $query = Leave::with('user');

        // Apply filters same as index
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->department) {
            $query->where('department', $request->department);
        }
        if ($request->month) {
            $query->whereMonth('start_date', \Carbon\Carbon::parse($request->month)->month)
                  ->whereYear('start_date', \Carbon\Carbon::parse($request->month)->year);
        }

        $leaves = $query->get();

        // CSV export implementation
        $fileName = 'leaves_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($leaves) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Employee Name',
                'Department',
                'Leave Type',
                'Start Date',
                'End Date',
                'Days',
                'Status',
                'Reason',
                'Applied On'
            ]);

            // Data rows
            foreach ($leaves as $leave) {
                fputcsv($file, [
                    $leave->user->first_name . ' ' . $leave->user->last_name,
                    $leave->department,
                    $leave->type,
                    $leave->start_date->format('Y-m-d'),
                    $leave->end_date->format('Y-m-d'),
                    $leave->days,
                    ucfirst($leave->status),
                    $leave->reason,
                    $leave->created_at->format('Y-m-d')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}