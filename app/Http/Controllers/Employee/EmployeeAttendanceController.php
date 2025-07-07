<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', today()->format('Y-m-d'));
        $month = $request->input('month', now()->month);
        $year = $request->input('year', default: now()->year);
        
        // Today's attendance
        $todayAttendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->orderBy('time_in', 'desc')
            ->paginate(10);
            
        // Monthly stats for the chart
        $monthlyStats = Attendance::select(
            DB::raw('DAY(date) as day'),
            DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
            DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
            DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
            DB::raw('SUM(CASE WHEN status = "on_leave" THEN 1 ELSE 0 END) as on_leave')
        )
        ->whereMonth('date', $month)
        ->whereYear('date', $year)
        ->groupBy('day')
        ->get()
        ->keyBy('day');
            
        // Summary counts
        $presentCount = Attendance::whereDate('date', $date)->where('status', 'present')->count();
        $absentCount = Attendance::whereDate('date', $date)->where('status', 'absent')->count();
        $lateCount = Attendance::whereDate('date', $date)->where('status', 'late')->count();
        $onLeaveCount = Attendance::whereDate('date', $date)->where('status', 'on_leave')->count();

        // Add calendar data
        $calendarDays = [];
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Get all attendance for the month
        $monthAttendances = Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->date)->day;
            });
        
        // Generate calendar days with attendance status
        for ($day = 1; $day <= $endOfMonth->daysInMonth; $day++) {
            $date = $startOfMonth->copy()->addDays($day - 1);
            $calendarDays[$day] = [
                'date' => $date,
                'is_today' => $date->isToday(),
                'attendance' => $monthAttendances[$day] ?? null,
                'is_weekend' => $date->isWeekend(),
                'is_holiday' => false // You can implement holiday checking
            ];
        }
        
        return view('admin.attendance', compact(
            'todayAttendances',
            'monthlyStats',
            'presentCount',
            'absentCount',
            'lateCount',
            'onLeaveCount',
            'date',
            'month',
            'year',
            'calendarDays',
            'startOfMonth',
            'endOfMonth'
        ));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'status' => 'required|in:present,absent,late,on_leave,half_day',
            'notes' => 'nullable|string|max:255',
        ]);
        
        $attendance = Attendance::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $request->date],
            $request->only(['time_in', 'time_out', 'status', 'notes'])
        );
        
        if ($attendance->time_in && $attendance->time_out) {
            $attendance->calculateTotalHours();
        }
        
        return redirect()->back()->with('success', 'Attendance record saved successfully.');
    }
    
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'status' => 'required|in:present,absent,late,on_leave,half_day',
            'notes' => 'nullable|string|max:255',
        ]);
        
        $attendance->update($request->only(['time_in', 'time_out', 'status', 'notes']));
        
        if ($attendance->time_in && $attendance->time_out) {
            $attendance->calculateTotalHours();
        }
        
        return redirect()->back()->with('success', 'Attendance record updated successfully.');
    }
    
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->back()->with('success', 'Attendance record deleted successfully.');
    }
    
    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->input('user_id');
        
        $query = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc');
            
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $attendances = $query->paginate(25);
        
        $users = User::active()->get();
        
        return view('admin.attendance.report', compact('attendances', 'users', 'startDate', 'endDate', 'userId'));
    }
    
    public function export(Request $request)
    {
        // Implement export functionality (CSV, Excel, etc.)
    }
}