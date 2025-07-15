<?php

namespace App\Http\Controllers\Attendance;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(20);

        $todayAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', today())
            ->first();

        return view('employees.attendance', [
            'attendances' => $attendances,
            'todayAttendance' => $todayAttendance
        ]);
    }

    public function check(Request $request)
    {
        $user = Auth::user();
        $now = now();
        $today = today();

        // Check if today is a weekend
        if ($today->isWeekend()) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance cannot be marked on weekends'
            ], 400);
        }

        // Check if user is on leave today
        $leave = $user->leaves()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('status', 'approved')
            ->first();

        if ($leave) {
            return response()->json([
                'success' => false,
                'message' => 'You are on approved leave today'
            ], 400);
        }

        // Check existing attendance for today
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($request->type === 'check-in') {
            if ($attendance && $attendance->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already checked in today'
                ], 400);
            }

            // Determine if late (assuming work starts at 9:00 AM)
            $status = 'present';
            $lateTime = Carbon::createFromTime(9, 0, 0); // 9:00 AM
            if ($now->gt($lateTime)) {
                $status = 'late';
            }

            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'time_in' => $now->format('H:i:s'),
                    'status' => $status,
                    'ip_address' => $request->ip(),
                    'device_info' => $request->header('User-Agent'),
                ]);
            } else {
                $attendance->update([
                    'time_in' => $now->format('H:i:s'),
                    'status' => $status,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checked in successfully',
                'attendance' => $attendance
            ]);
        } else if ($request->type === 'check-out') {
            if (!$attendance || !$attendance->time_in) {
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

            $timeIn = Carbon::parse($attendance->time_in);
            $totalHours = round($now->diffInMinutes($timeIn) / 60, 2);

            $attendance->update([
                'time_out' => $now->format('H:i:s'),
                'total_hours' => $totalHours,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checked out successfully',
                'attendance' => $attendance
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid request'
        ], 400);
    }

    public function regularization(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i|after:time_in',
            'reason' => 'required|string|max:500',
        ]);

        $attendance = Attendance::findOrFail($request->attendance_id);

        // Check if the attendance belongs to the user
        if ($attendance->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Calculate total hours
        $start = Carbon::parse($request->time_in);
        $end = Carbon::parse($request->time_out);
        $totalHours = round($end->diffInMinutes($start) / 60, 2);

        $attendance->update([
            'date' => $request->date,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'total_hours' => $totalHours,
            'regularization_reason' => $request->reason,
            'is_regularized' => false, // Needs admin approval
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Regularization request submitted successfully',
            'attendance' => $attendance
        ]);
    }

    public function allRecords()
    {
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('employee.attendance-records', [
            'attendances' => $attendances
        ]);
    }
}