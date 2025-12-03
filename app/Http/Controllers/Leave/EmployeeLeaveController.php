<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveCreditEarning;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class EmployeeLeaveController extends Controller
{
    /**
     * Display a listing of leave applications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Leave::where('user_id', $user->id)
            ->with(['approvedBy', 'handoverPerson'])
            ->latest();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('csc_type')) {
            $query->where('csc_employee_type', $request->csc_type);
        }

        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereMonth('start_date', $month->month)
                  ->whereYear('start_date', $month->year);
        }

        $leaves = $query->paginate(15);
        $leaveBalances = $user->leaveBalances ?? new LeaveBalance([
            'vacation_leave' => 0,
            'sick_leave' => 0,
            'special_leave_privileges' => 3.00,
            'forced_leave_taken' => 0,
        ]);

        return view('employees.leaves.index', compact('leaves', 'leaveBalances'));
    }

    /**
     * Show the form for creating a new leave application
     */
    public function create()
    {
        $user = Auth::user();

        $leaveBalances = $user->leaveBalances ?? new LeaveBalance([
            'vacation_leave' => 0,
            'sick_leave' => 0,
            'special_leave_privileges' => 3.00,
            'forced_leave_taken' => 0,
        ]);

        return view('employees.leaves.create', compact('leaveBalances'));
    }

    /**
     * Store a newly created leave application
     */
    public function store(Request $request)
    {
        Log::info('Leave application submission started', [
            'user_id' => Auth::id(),
            'request_data' => $request->except(['electronic_signature', 'medical_certificate', 'travel_itinerary']),
            'files' => array_keys($request->files->all())
        ]);

        // Validate the request
        $validator = $this->validateLeaveRequest($request);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Please fix the validation errors.'
            ], 422);
        }

        Log::info('Validation passed');
        
        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            Log::info('Preparing leave data for user', ['user_id' => $user->id]);
            
            $data = $this->prepareLeaveData($request, $user);
            Log::info('Leave data prepared', array_merge(
                $data,
                ['electronic_signature_path' => isset($data['electronic_signature_path']) ? '***' : null]
            ));

            // Check for overlapping leaves
            if ($this->hasOverlappingLeaves($user, $data['start_date'], $data['end_date'])) {
                Log::warning('Overlapping leaves detected', [
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date']
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'You have overlapping approved leaves during this period.',
                    'errors' => [
                        'start_date' => ['You have overlapping approved leaves during this period.']
                    ]
                ], 422);
            }

            Log::info('No overlapping leaves found');

            // Check leave balance
            if (!$this->checkLeaveBalance($data, $user)) {
                Log::warning('Insufficient leave balance', [
                    'leave_type' => $data['type'],
                    'days_needed' => $data['equivalent_days_csc'] ?? 0
                ]);
                
                $leaveType = $data['type'];
                $balance = $user->leaveBalances;
                $available = 0;
                
                switch ($leaveType) {
                    case 'vacation':
                        $available = $balance ? $balance->vacation_leave : 0;
                        break;
                    case 'sick':
                        $available = $balance ? $balance->sick_leave : 0;
                        break;
                    case 'special_privilege':
                        $available = $balance ? $balance->special_leave_privileges : 0;
                        break;
                    case 'maternity':
                        $available = $balance ? $balance->maternity_leave : 105;
                        break;
                    case 'paternity':
                        $available = $balance ? $balance->paternity_leave_days : 7;
                        break;
                    default:
                        $available = 0;
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient leave credits for this application.',
                    'errors' => [
                        'type' => ["Insufficient leave credits. You need {$data['equivalent_days_csc']} days but only have {$available} days available."]
                    ]
                ], 422);
            }

            Log::info('Leave balance check passed');

            // Handle file uploads
            $data = $this->handleFileUploads($request, $data);
            Log::info('File uploads handled', [
                'has_signature' => isset($data['electronic_signature_path']),
                'has_medical' => isset($data['medical_certificate_path']),
                'has_travel' => isset($data['travel_itinerary_path'])
            ]);

            // Create the leave application
            $leave = Leave::create($data);
            Log::info('Leave created', ['leave_id' => $leave->id]);

            // Generate PDF
            $this->generateLeavePdf($leave);
            Log::info('PDF generated');

            DB::commit();
            Log::info('Transaction committed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Leave application submitted successfully!',
                'redirect_url' => route('employees.leaves.show', $leave)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Leave Application Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'data' => $request->except(['electronic_signature', 'medical_certificate', 'travel_itinerary'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your application. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Check for overlapping leaves
     */
    private function hasOverlappingLeaves(User $user, $startDate, $endDate)
    {
        return Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();
    }

    /**
     * Show leave application details
     */
    public function show(Leave $leave)
    {
        $this->authorize('view', $leave);
        
        return view('employees.leaves.show', compact('leave'));
    }

    /**
     * Validate leave request
     */
    private function validateLeaveRequest(Request $request)
    {
        $rules = [
            'type' => 'required|string|in:' . implode(',', array_keys(Leave::getLeaveTypes())),
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    try {
                        if (Carbon::parse($value)->isPast()) {
                            $fail('Start date cannot be in the past.');
                        }
                    } catch (\Exception $e) {
                        $fail('Invalid start date format.');
                    }
                }
            ],
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration_type' => 'required|in:half_day,full_day,multiple_days',
            'reason' => 'required|string|min:10|max:1000',
            'commutation' => 'required|in:requested,not_requested',
            'electronic_signature' => 'required|file|mimes:png,jpg,jpeg,svg|max:2048',
        ];

        // Conditional rules based on duration type
        if ($request->filled('duration_type')) {
            if ($request->duration_type === 'half_day') {
                $rules['half_day_time'] = 'required|in:morning,afternoon,custom';
                
                if ($request->filled('half_day_time') && $request->half_day_time === 'custom') {
                    $rules['start_time'] = 'required|date_format:H:i';
                    $rules['end_time'] = 'required|date_format:H:i|after:start_time';
                }
            }
        }

        // Conditional rules based on leave type
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'maternity':
                    $rules['maternity_delivery_date'] = [
                        'required',
                        'date',
                        function ($attribute, $value, $fail) use ($request) {
                            try {
                                if ($request->filled('start_date') && Carbon::parse($value)->lt(Carbon::parse($request->start_date))) {
                                    $fail('Delivery date must be on or after the start date.');
                                }
                            } catch (\Exception $e) {
                                $fail('Invalid delivery date format.');
                            }
                        }
                    ];
                    break;
                
                case 'paternity':
                    $rules['paternity_delivery_count'] = 'required|integer|min:1|max:4';
                    break;
                
                case 'special_privilege':
                    $slpTypes = Leave::getSlpTypes();
                    if ($slpTypes && !empty($slpTypes)) {
                        $rules['slp_type'] = 'required|in:' . implode(',', array_keys($slpTypes));
                    }
                    break;
                
                case 'sick':
                    // Calculate days to determine if medical cert is needed
                    $days = 0;
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        try {
                            $start = Carbon::parse($request->start_date);
                            $end = Carbon::parse($request->end_date);
                            $days = $start->diffInDays($end);
                            
                            // Add 1 for inclusive counting if multiple days
                            if ($request->filled('duration_type')) {
                                if ($request->duration_type === 'multiple_days') {
                                    $days += 1;
                                } elseif ($request->duration_type === 'full_day') {
                                    $days = 1;
                                } elseif ($request->duration_type === 'half_day') {
                                    $days = 0.5;
                                }
                            }
                        } catch (\Exception $e) {
                            Log::warning('Error calculating days for validation', ['error' => $e->getMessage()]);
                        }
                    }
                    
                    if ($days > 3) {
                        $rules['medical_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
                    }
                    break;
                
                case 'vacation':
                    // Check if leave is abroad
                    if ($request->filled('leave_location') && $request->leave_location === 'abroad') {
                        $rules['travel_itinerary'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
                    }
                    break;
            }
        }

        return Validator::make($request->all(), $rules, [
            'type.required' => 'Please select a leave type.',
            'type.in' => 'Invalid leave type selected.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'duration_type.required' => 'Please select a duration type.',
            'reason.required' => 'Please provide a reason for your leave.',
            'reason.min' => 'Reason must be at least 10 characters.',
            'commutation.required' => 'Please specify if you want commutation.',
            'electronic_signature.required' => 'Please upload your electronic signature.',
            'electronic_signature.file' => 'Electronic signature must be a file.',
            'electronic_signature.mimes' => 'Signature must be PNG, JPG, JPEG, or SVG format.',
            'electronic_signature.max' => 'Signature file size must be less than 2MB.',
            'half_day_time.required' => 'Please specify morning or afternoon for half-day leave.',
            'start_time.required' => 'Start time is required for custom half-day.',
            'start_time.date_format' => 'Start time must be in HH:mm format.',
            'end_time.required' => 'End time is required for custom half-day.',
            'end_time.date_format' => 'End time must be in HH:mm format.',
            'end_time.after' => 'End time must be after start time.',
            'medical_certificate.required' => 'Medical certificate is required for sick leaves over 3 days.',
            'medical_certificate.file' => 'Medical certificate must be a file.',
            'medical_certificate.mimes' => 'Medical certificate must be PDF, JPG, JPEG, or PNG format.',
            'travel_itinerary.required' => 'Travel itinerary is required for vacation leaves abroad.',
            'travel_itinerary.file' => 'Travel itinerary must be a file.',
            'travel_itinerary.mimes' => 'Travel itinerary must be PDF, JPG, JPEG, or PNG format.',
        ]);
    }

    /**
     * Prepare leave data for storage
     */
    private function prepareLeaveData(Request $request, User $user)
    {
        $data = $request->only([
            'type', 'reason', 'commutation', 'duration_type',
            'start_time', 'end_time',
            'maternity_delivery_date', 'is_miscarriage',
            'paternity_delivery_count', 'slp_type',
            'is_lwop', 'lwop_deduction_rate', 'lwop_days_charged'
        ]);

        // Handle half_day_time conversion
        if ($request->filled('duration_type') && $request->duration_type === 'half_day') {
            if ($request->filled('half_day_time')) {
                // Store the half-day time description instead of a time value
                // This will be used to identify morning/afternoon/custom
                $data['half_day_time'] = $request->half_day_time;
            }
        }

        // Set basic fields
        $data['user_id'] = $user->id;
        $data['department'] = $user->department ?? 'N/A';
        $data['position'] = $user->position ?? 'N/A';
        $data['filing_date'] = now();
        $data['status'] = 'pending';
        
        // Set dates
        $data['start_date'] = Carbon::parse($request->start_date);
        $data['end_date'] = Carbon::parse($request->end_date);
        
        // Calculate duration
        $duration = $this->calculateDuration($request, $data['start_date'], $data['end_date']);
        $data['days'] = $duration['days'];
        $data['total_hours'] = $duration['hours'];
        
        // Set CSC fields - get from user, not request
        $data['csc_employee_type'] = $user->employee_classification;
        $data['leave_basis'] = $user->getLeaveBasis();
        
        // Calculate CSC equivalent days
        $data['equivalent_days_csc'] = $this->calculateCscEquivalentDays(
            $duration['days'], 
            $duration['hours'], 
            $user
        );

        // Handle LWOP if applicable
        if ($request->filled('is_lwop') && $request->is_lwop) {
            $lwop = $this->calculateLwopDeduction($data['days']);
            $data['lwop_deduction_rate'] = $lwop['rate'];
            $data['lwop_days_charged'] = $lwop['charged'];
        } else {
            $data['is_lwop'] = false;
            $data['lwop_deduction_rate'] = 0;
            $data['lwop_days_charged'] = 0;
        }

        // Set leave credits from user's current balance
        $balance = $user->leaveBalances;
        if ($balance) {
            $data['vacation_earned'] = $balance->vacation_earned ?? 0;
            $data['vacation_less'] = 0;
            $data['vacation_balance'] = $balance->vacation_leave ?? 0;
            $data['sick_earned'] = $balance->sick_earned ?? 0;
            $data['sick_less'] = 0;
            $data['sick_balance'] = $balance->sick_leave ?? 0;
            $data['special_leave_privileges_balance'] = $balance->special_leave_privileges ?? 3.00;
        } else {
            // Default values if no balance record exists
            $data['vacation_earned'] = 0;
            $data['vacation_less'] = 0;
            $data['vacation_balance'] = 0;
            $data['sick_earned'] = 0;
            $data['sick_less'] = 0;
            $data['sick_balance'] = 0;
            $data['special_leave_privileges_balance'] = 3.00;
        }

        return $data;
    }

    /**
     * Calculate duration based on leave type
     */
    private function calculateDuration(Request $request, $startDate, $endDate)
    {
        $durationType = $request->duration_type;
        $halfDayTime = $request->half_day_time;
        
        if ($durationType === 'half_day') {
            $hours = 4; // Default half day hours
            
            if ($halfDayTime === 'custom' && $request->start_time && $request->end_time) {
                try {
                    $start = Carbon::parse($request->start_time);
                    $end = Carbon::parse($request->end_time);
                    
                    // Ensure we handle time correctly
                    if ($end->greaterThan($start)) {
                        $hours = $end->diffInHours($start);
                    } else {
                        // Handle overnight (shouldn't happen for half-day but just in case)
                        $hours = $end->diffInHours($start->copy()->addDay());
                    }
                    
                    // Cap at 8 hours max for half-day
                    $hours = min($hours, 8);
                    // Ensure minimum of 0.5 hours
                    $hours = max($hours, 0.5);
                } catch (\Exception $e) {
                    Log::error('Error calculating custom hours: ' . $e->getMessage());
                    $hours = 4; // Fallback to default
                }
            }
            
            return [
                'days' => 0.5,
                'hours' => $hours
            ];
        }
        
        if ($durationType === 'full_day') {
            return [
                'days' => 1,
                'hours' => 8
            ];
        }
        
        // Multiple days - calculate working days (excluding weekends)
        $days = 0;
        $current = clone $startDate;
        
        while ($current <= $endDate) {
            if (!$current->isWeekend()) {
                $days++;
            }
            $current->addDay();
        }
        
        // Ensure at least 1 day for multiple days leave
        $days = max($days, 1);
        
        return [
            'days' => $days,
            'hours' => $days * 8
        ];
    }

    /**
     * Calculate CSC equivalent days
     */
    private function calculateCscEquivalentDays($days, $hours, User $user)
    {
        if ($user->employee_classification === 'part_time') {
            // For part-time, convert hours to days based on their work hours
            $workHoursPerDay = $user->work_hours_per_day ?? 8;
            $equivalentDays = $hours / $workHoursPerDay;
        } else if ($user->isTeacher()) {
            // Teachers use different calculation (use days directly)
            $equivalentDays = $days;
        } else {
            // Regular employees
            $equivalentDays = $days;
        }
        
        return round($equivalentDays, 4);
    }

    /**
     * Calculate LWOP deduction
     */
    private function calculateLwopDeduction($days)
    {
        // CSC LWOP deduction rates
        $rates = [
            1 => 0.25,
            2 => 0.50,
            3 => 0.75,
            4 => 1.00
        ];
        
        $applicableDays = min(ceil($days), 4);
        $rate = $rates[$applicableDays] ?? 1.00;
        $charged = $days * $rate;
        
        return [
            'rate' => round($rate, 4),
            'charged' => round($charged, 4)
        ];
    }

    /**
     * Check leave balance
     */
    private function checkLeaveBalance(array $data, User $user)
    {
        $balance = $user->leaveBalances;
        if (!$balance) {
            Log::warning('No leave balance found for user: ' . $user->id);
            // For maternity and paternity leave, we need to check eligibility
            if (in_array($data['type'], ['maternity', 'paternity'])) {
                return false; // Need balance record for maternity/paternity
            }
            return true; // Allow creation for other types even without balance record
        }

        $daysNeeded = $data['equivalent_days_csc'] ?? 0;
        $leaveType = $data['type'];

        Log::info('Checking leave balance', [
            'user_id' => $user->id,
            'leave_type' => $leaveType,
            'days_needed' => $daysNeeded,
            'balance' => [
                'vacation' => $balance->vacation_leave ?? 0,
                'sick' => $balance->sick_leave ?? 0,
                'special_privilege' => $balance->special_leave_privileges ?? 0,
                'maternity' => $balance->maternity_leave ?? 105,
                'paternity' => $balance->paternity_leave_days ?? 7,
            ]
        ]);

        switch ($leaveType) {
            case 'vacation':
                return ($balance->vacation_leave ?? 0) >= $daysNeeded;
            
            case 'sick':
                return ($balance->sick_leave ?? 0) >= $daysNeeded;
            
            case 'special_privilege':
                return ($balance->special_leave_privileges ?? 0) >= $daysNeeded;
            
            case 'maternity':
                // Maternity leave has specific rules, usually 105 days
                $maternityBalance = $balance->maternity_leave ?? 105;
                return $maternityBalance >= $daysNeeded;
            
            case 'paternity':
                // Paternity leave is usually 7 days
                $paternityBalance = $balance->paternity_leave_days ?? 7;
                return $paternityBalance >= $daysNeeded;
            
            default:
                // For other leave types (study, rehabilitation, etc.), assume approved
                return true;
        }
    }

    /**
     * Handle file uploads
     */
    private function handleFileUploads(Request $request, array $data)
    {
        $uploads = [];

        // Electronic signature (required)
        if ($request->hasFile('electronic_signature')) {
            try {
                $path = $request->file('electronic_signature')->store('signatures', 'public');
                $uploads['electronic_signature_path'] = $path;
                Log::info('Signature uploaded successfully', ['path' => $path]);
            } catch (\Exception $e) {
                Log::error('Failed to upload signature: ' . $e->getMessage());
                throw new \Exception('Failed to upload electronic signature.');
            }
        }

        // Medical certificate (conditional)
        if ($request->hasFile('medical_certificate')) {
            try {
                $path = $request->file('medical_certificate')->store('medical_certificates', 'public');
                $uploads['medical_certificate_path'] = $path;
                Log::info('Medical certificate uploaded successfully', ['path' => $path]);
            } catch (\Exception $e) {
                Log::error('Failed to upload medical certificate: ' . $e->getMessage());
                throw new \Exception('Failed to upload medical certificate.');
            }
        }

        // Travel itinerary (conditional)
        if ($request->hasFile('travel_itinerary')) {
            try {
                $path = $request->file('travel_itinerary')->store('travel_itineraries', 'public');
                $uploads['travel_itinerary_path'] = $path;
                Log::info('Travel itinerary uploaded successfully', ['path' => $path]);
            } catch (\Exception $e) {
                Log::error('Failed to upload travel itinerary: ' . $e->getMessage());
                throw new \Exception('Failed to upload travel itinerary.');
            }
        }

        return array_merge($data, $uploads);
    }

    /**
     * Generate PDF for leave application
     */
    private function generateLeavePdf(Leave $leave)
    {
        try {
            $pdf = Pdf::loadView('pdf.leave-application', compact('leave'));
            $filename = 'leave-application-' . $leave->id . '-' . now()->format('Y-m-d') . '.pdf';
            $path = 'leave-applications/' . $filename;
            
            Storage::put('public/' . $path, $pdf->output());
            
            $leave->update(['pdf_path' => $path]);
            
            Log::info('PDF generated successfully', ['path' => $path]);
            
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            // Don't fail the whole process if PDF generation fails
        }
    }

    /**
     * View leave details (AJAX endpoint)
     */
    public function viewDetails(Leave $leave)
    {
        $this->authorize('view', $leave);
        
        $html = view('employees.leaves.partials.details-modal', compact('leave'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Get CSC computation details (AJAX endpoint)
     */
    public function cscComputation(Leave $leave)
    {
        $this->authorize('view', $leave);
        
        $computation = $leave->generateComputationDetails();
        $html = view('employees.leaves.partials.csc-computation', [
            'computation' => $computation,
            'leave' => $leave
        ])->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Delete leave application
     */
    public function destroy(Leave $leave)
    {
        $this->authorize('delete', $leave);
        
        if ($leave->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending leave applications can be deleted.'
            ], 422);
        }
        
        try {
            // Delete associated files
            if ($leave->electronic_signature_path) {
                Storage::delete('public/' . $leave->electronic_signature_path);
            }
            
            if ($leave->medical_certificate_path) {
                Storage::delete('public/' . $leave->medical_certificate_path);
            }
            
            if ($leave->travel_itinerary_path) {
                Storage::delete('public/' . $leave->travel_itinerary_path);
            }
            
            if ($leave->pdf_path) {
                Storage::delete('public/' . $leave->pdf_path);
            }
            
            $leave->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Leave application deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete Leave Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete leave application.'
            ], 500);
        }
    }

    /**
     * Download PDF
     */
    public function downloadPdf(Leave $leave)
    {
        $this->authorize('view', $leave);
        
        if (!$leave->pdf_path || !Storage::exists('public/' . $leave->pdf_path)) {
            abort(404, 'PDF not found');
        }
        
        return Storage::download('public/' . $leave->pdf_path, 
            'Leave-Application-' . $leave->id . '.pdf');
    }

    /**
     * Regenerate PDF
     */
    public function regeneratePdf(Leave $leave)
    {
        $this->authorize('view', $leave);
        
        $this->generateLeavePdf($leave);
        
        return response()->json([
            'success' => true,
            'message' => 'PDF regenerated successfully.'
        ]);
    }
    
    /**
     * Test endpoint for debugging
     */
    public function testStore(Request $request)
    {
        try {
            Log::info('Test store endpoint called', $request->all());
            
            // Simple validation
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'start_date' => 'required|date',
                'reason' => 'required',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $leave = Leave::create([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? $request->start_date,
                'reason' => $request->reason,
                'status' => 'pending',
                'filing_date' => now(),
                'department' => Auth::user()->department,
                'position' => Auth::user()->position,
                'csc_employee_type' => Auth::user()->employee_classification,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test successful - Leave created',
                'leave_id' => $leave->id,
                'redirect_url' => route('employees.leaves.show', $leave)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}