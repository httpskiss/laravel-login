<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use App\Services\LeavePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminLeaveController extends Controller
{
    protected $pdfService;

    public function __construct(LeavePdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

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

            // Generate PDF automatically
            $pdfPath = $this->pdfService->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $pdfPath]);

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
            'admin_notes' => 'nullable|string|max:1000',
            'leave_credits_vacation' => 'nullable|numeric|min:0',
            'leave_credits_sick' => 'nullable|numeric|min:0',
            'less_this_application_vacation' => 'nullable|numeric|min:0',
            'less_this_application_sick' => 'nullable|numeric|min:0',
            'balance_vacation' => 'nullable|numeric|min:0',
            'balance_sick' => 'nullable|numeric|min:0',
        ]);

        try {
            $leave->update($validated);

            // Regenerate PDF when admin updates leave credits or approval details
            $pdfPath = $this->pdfService->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $pdfPath]);

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

            // Regenerate PDF with approval details
            $pdfPath = $this->pdfService->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $pdfPath]);

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

            // Regenerate PDF with rejection details
            $pdfPath = $this->pdfService->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $pdfPath]);

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

    /**
     * Download PDF for admin
     */
    public function downloadPdf(Leave $leave)
    {
        return $this->pdfService->downloadCsFormNo6($leave);
    }

    /**
     * View PDF in browser for admin
     */
    public function viewPdf(Leave $leave)
    {
        $filePath = $leave->pdf_path;
        
        if (!$filePath || !Storage::exists($filePath)) {
            $filePath = $this->pdfService->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $filePath]);
        }
        
        return response()->file(Storage::path($filePath));
    }

    /**
     * Regenerate PDF for admin
     */
    public function regeneratePdf(Leave $leave)
    {
        try {
            $pdfPath = $this->pdfService->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $pdfPath]);

            return response()->json([
                'success' => true,
                'message' => 'PDF regenerated successfully!',
                'pdf_url' => route('admin.leaves.download-pdf', $leave)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete the PDF form with admin sections
     */
    public function completePdfForm(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            // Leave Credits Section
            'as_of_date' => 'required|date',
            'total_earned_vacation' => 'required|numeric|min:0',
            'total_earned_sick' => 'required|numeric|min:0',
            'less_this_application_vacation' => 'required|numeric|min:0',
            'less_this_application_sick' => 'required|numeric|min:0',
            'balance_vacation' => 'required|numeric|min:0',
            'balance_sick' => 'required|numeric|min:0',
            
            // Recommendation Section
            'recommendation' => 'required|in:approve,disapprove',
            'disapprove_reason' => 'required_if:recommendation,disapprove|string|max:500',
            
            // Approval Section
            'approved_for' => 'required|in:with_pay,without_pay,others',
            'with_pay_days' => 'required_if:approved_for,with_pay|numeric|min:0',
            'without_pay_days' => 'required_if:approved_for,without_pay|numeric|min:0',
            'others_specify' => 'required_if:approved_for,others|string|max:255',
            
            // Officer Information
            'authorized_officer_name' => 'required|string|max:255',
            'authorized_official_name' => 'required|string|max:255',
        ]);

        try {
            // Update leave with admin completion data
            $leave->update([
                'as_of_date' => $validated['as_of_date'],
                'total_earned_vacation' => $validated['total_earned_vacation'],
                'total_earned_sick' => $validated['total_earned_sick'],
                'less_this_application_vacation' => $validated['less_this_application_vacation'],
                'less_this_application_sick' => $validated['less_this_application_sick'],
                'balance_vacation' => $validated['balance_vacation'],
                'balance_sick' => $validated['balance_sick'],
                'recommendation' => $validated['recommendation'],
                'disapproved_reason' => $validated['disapprove_reason'] ?? null,
                'approved_for' => $validated['approved_for'],
                'with_pay_days' => $validated['with_pay_days'] ?? null,
                'without_pay_days' => $validated['without_pay_days'] ?? null,
                'others_specify' => $validated['others_specify'] ?? null,
                'authorized_officer_name' => $validated['authorized_officer_name'],
                'authorized_official_name' => $validated['authorized_official_name'],
                'status' => $validated['recommendation'] === 'approve' ? 'approved' : 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Regenerate PDF with completed admin sections
            $pdfPath = $this->pdfService->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $pdfPath]);

            return response()->json([
                'success' => true,
                'message' => 'PDF form completed successfully!',
                'pdf_url' => route('admin.leaves.download-pdf', $leave)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete PDF form: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Leave $leave)
    {
        try {
            // Delete associated files
            if ($leave->pdf_path && Storage::exists($leave->pdf_path)) {
                Storage::delete($leave->pdf_path);
            }
            if ($leave->medical_certificate_path && Storage::exists($leave->medical_certificate_path)) {
                Storage::delete($leave->medical_certificate_path);
            }
            if ($leave->travel_itinerary_path && Storage::exists($leave->travel_itinerary_path)) {
                Storage::delete($leave->travel_itinerary_path);
            }
            if ($leave->electronic_signature_path && Storage::exists($leave->electronic_signature_path)) {
                Storage::delete($leave->electronic_signature_path);
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
                'Applied On',
                'PDF Available'
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
                    $leave->created_at->format('Y-m-d'),
                    $leave->hasPdf() ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}