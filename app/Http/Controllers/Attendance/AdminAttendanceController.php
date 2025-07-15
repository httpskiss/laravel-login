<?php

namespace App\Http\Controllers\Attendance;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:attendance-list|attendance-view-details', ['only' => ['index']]);
        $this->middleware('permission:attendance-create', ['only' => ['store']]);
        $this->middleware('permission:attendance-edit', ['only' => ['update']]);
        $this->middleware('permission:attendance-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Attendance::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by date range if provided
        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // Filter by department if user is department head
        if (auth()->user()->hasRole('Department Head')) {
            $query->whereHas('user', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }

        // Filter by employee if provided
        if ($request->has('employee_id')) {
            $query->where('user_id', $request->employee_id);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(20);

        $employees = User::orderBy('first_name')->get();
        $departments = User::select('department')->distinct()->orderBy('department')->pluck('department');

        if ($request->wantsJson()) {
            return response()->json([
                'attendances' => $attendances,
                'employees' => $employees,
                'departments' => $departments
            ]);
        }

        return view('admin.attendance', [
            'attendances' => $attendances,
            'employees' => $employees,
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'status' => 'required|in:present,absent,late,on_leave,half_day',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate total hours if both time_in and time_out are provided
        $totalHours = null;
        if ($request->time_in && $request->time_out) {
            $start = \Carbon\Carbon::parse($request->time_in);
            $end = \Carbon\Carbon::parse($request->time_out);
            $totalHours = round($end->diffInMinutes($start) / 60, 2);
        }

        $attendance = Attendance::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'total_hours' => $totalHours,
            'status' => $request->status,
            'notes' => $request->notes,
            'ip_address' => $request->ip(),
            'device_info' => $request->header('User-Agent'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance record created successfully',
            'attendance' => $attendance->load('user')
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        // Check if user has permission to edit this attendance
        if (auth()->user()->hasRole('Department Head') && 
            $attendance->user->department !== auth()->user()->department) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit attendance for employees in your department'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'status' => 'sometimes|in:present,absent,late,on_leave,half_day',
            'notes' => 'nullable|string|max:500',
            'is_regularized' => 'sometimes|boolean',
            'regularization_reason' => 'required_if:is_regularized,true|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['date', 'time_in', 'time_out', 'status', 'notes', 'is_regularized', 'regularization_reason']);

        // Calculate total hours if both time_in and time_out are provided
        if ($request->has('time_in') || $request->has('time_out')) {
            $timeIn = $request->time_in ?? $attendance->time_in;
            $timeOut = $request->time_out ?? $attendance->time_out;
            
            if ($timeIn && $timeOut) {
                $start = \Carbon\Carbon::parse($timeIn);
                $end = \Carbon\Carbon::parse($timeOut);
                $data['total_hours'] = round($end->diffInMinutes($start) / 60, 2);
            } else {
                $data['total_hours'] = null;
            }
        }

        if ($request->is_regularized) {
            $data['regularized_by'] = auth()->id();
            $data['regularized_at'] = now();
        }

        $attendance->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Attendance record updated successfully',
            'attendance' => $attendance->fresh()->load('user')
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        // Check if user has permission to delete this attendance
        if (auth()->user()->hasRole('Department Head') && 
            $attendance->user->department !== auth()->user()->department) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete attendance for employees in your department'
            ], 403);
        }

        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully'
        ]);
    }

    public function getDepartmentData(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $departments = User::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $data = [];
        foreach ($departments as $department) {
            $users = User::where('department', $department)->pluck('id');
            
            $present = Attendance::whereIn('user_id', $users)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count();

            $absent = Attendance::whereIn('user_id', $users)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->count();

            $late = Attendance::whereIn('user_id', $users)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'late')
                ->count();

            $onLeave = Attendance::whereIn('user_id', $users)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'on_leave')
                ->count();

            $halfDay = Attendance::whereIn('user_id', $users)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'half_day')
                ->count();

            $data[] = [
                'department' => $department,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'on_leave' => $onLeave,
                'half_day' => $halfDay,
            ];
        }

        return response()->json($data);
    }

    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $department = $request->input('department');
        $employeeId = $request->input('employee_id');

        $query = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($department) {
            $query->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $attendances = $query->orderBy('date')->get();

        return view('admin.attendance-report', [
            'attendances' => $attendances,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'department' => $department,
            'employee_id' => $employeeId
        ]);
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $department = $request->input('department');
        $employeeId = $request->input('employee_id');

        $query = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($department) {
            $query->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $attendances = $query->orderBy('date')->get();

        $fileName = 'attendance-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Department',
                'Date',
                'Time In',
                'Time Out',
                'Total Hours',
                'Status',
                'Notes',
                'Regularized',
                'Regularized Reason'
            ]);

            // Data rows
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->user->employee_id,
                    $attendance->user->first_name . ' ' . $attendance->user->last_name,
                    $attendance->user->department,
                    $attendance->date,
                    $attendance->time_in,
                    $attendance->time_out,
                    $attendance->total_hours,
                    ucfirst(str_replace('_', ' ', $attendance->status)),
                    $attendance->notes,
                    $attendance->is_regularized ? 'Yes' : 'No',
                    $attendance->regularization_reason
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}