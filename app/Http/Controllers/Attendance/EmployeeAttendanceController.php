<?php
// app/Http/Controllers/Attendance/EmployeeAttendanceController.php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        $recentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('employees.attendance.index', compact('todayAttendance', 'recentAttendance'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'type' => 'required|in:time_in,time_out'
        ]);

        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        try {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->user_id = $user->id;
                $attendance->date = $today;
                $attendance->status = 'present';
            }

            if ($request->type === 'time_in') {
                $attendance->time_in = $now;
            } else {
                $attendance->time_out = $now;
                
                // Calculate total hours if both time in and time out are set
                if ($attendance->time_in) {
                    $timeIn = Carbon::parse($attendance->time_in);
                    $timeOut = Carbon::parse($attendance->time_out);
                    $attendance->total_hours = $timeOut->diffInHours($timeIn);
                }
            }

            $attendance->save();

            return redirect()->route('employees.attendance')
                ->with('success', ucfirst(str_replace('_', ' ', $request->type)) . ' recorded successfully!');

        } catch (\Exception $e) {
            return redirect()->route('employees.attendance')
                ->with('error', 'Failed to record attendance: ' . $e->getMessage());
        }
    }

    public function allRecords()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('employees.attendance.all', compact('attendance'));
    }

    public function regularization(Request $request)
    {
        // Implementation for attendance regularization
    }
}