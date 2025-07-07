<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Auth;

class EmployeeAttendanceController extends Controller
{
  
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        

        $todayAttendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => 'not_checked']
        );
        
        // Set default status if no record exists
        if (!$todayAttendance->exists) {
            $todayAttendance->status = null;
        }
            
        // Get recent attendance records (last 10)
        $recentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();
            
        // Calculate monthly stats
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        
        $monthAttendance = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get();
            
        $presentCount = $monthAttendance->where('status', 'present')->count();
        $lateCount = $monthAttendance->where('status', 'late')->count();
        $totalWorkingDays = $monthStart->diffInWeekdays($monthEnd);
        
        return view('employees.attendance', [
            'todayAttendance' => $todayAttendance ?? new Attendance(),
            'recentAttendance' => $recentAttendance,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'totalWorkingDays' => $totalWorkingDays,
            'monthAttendance' => $monthAttendance
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'type' => 'required|in:in,out'
        ]);
        
        $user = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();
        
        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'date' => $today
        ]);
        
        if ($request->type === 'in') {
            // Check in logic
            if ($attendance->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already checked in today'
                ], 400);
            }
            
            $attendance->time_in = $now->toTimeString();
            $attendance->ip_address = $request->ip();
            $attendance->device_info = $request->userAgent();
            
            // Determine if late (after 8:15 AM for example)
            $lateThreshold = Carbon::createFromTime(8, 15, 0);
            $attendance->status = $now->gt($lateThreshold) ? 'late' : 'present';
            
            $message = $attendance->status === 'late' 
                ? 'Checked in (Late)' 
                : 'Checked in successfully';
        } else {
            // Check out logic
            if (!$attendance->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to check in first'
                ], 400);
            }
            
            if ($attendance->time_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already checked out today'
                ], 400);
            }
            
            $attendance->time_out = $now->toTimeString();
            
            // Calculate total hours worked
            $timeIn = Carbon::parse($attendance->time_in);
            $timeOut = Carbon::parse($attendance->time_out);
            $totalHours = $timeOut->diffInMinutes($timeIn) / 60;
            $attendance->total_hours = round($totalHours, 2);
            
            $message = 'Checked out successfully';
        }
        
        $attendance->save();
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $attendance
        ]);
    }

    public function regularization(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'reason' => 'required|string|max:255',
            'details' => 'required|string',
            'proof' => 'nullable|file|mimes:jpg,png,pdf|max:10240'
        ]);
        
        $user = Auth::user();
        $date = Carbon::parse($request->date);
        
        // Check if attendance already exists for this date
        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->exists();
            
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record already exists for this date'
            ], 400);
        }
        
        // Handle file upload
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('attendance-proofs');
        }
        
        // Create regularization request
        $attendance = new Attendance();
        $attendance->user_id = $user->id;
        $attendance->date = $date;
        $attendance->status = 'pending_regularization';
        $attendance->notes = "Regularization Request\nReason: {$request->reason}\nDetails: {$request->details}";
        $attendance->proof_path = $proofPath;
        $attendance->save();
        
        // Here you would typically notify HR/admin about the request
        
        return response()->json([
            'success' => true,
            'message' => 'Regularization request submitted successfully',
            'data' => $attendance
        ]);
    }

    public function allRecords()
    {
        $user = Auth::user();
        
        $allAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20);
            
        return view('employees.attendance-all', [
            'allAttendance' => $allAttendance
        ]);
    }

    /**
     * Calculate working days between two dates (excluding weekends)
     */
    protected function calculateWorkingDays($startDate, $endDate)
    {
        $workingDays = 0;
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        return $workingDays;
    }
}