<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Leave;
use App\Models\Activity;
use App\Models\Task;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
     public function employeeDashboard()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $today = now()->format('Y-m-d');
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Format attendance data
        $attendanceData = $this->formatAttendanceData($todayAttendance);

        // Monthly attendance stats
        $monthlyAttendance = $this->getMonthlyAttendanceStats($user->id, $currentMonth, $currentYear);

        // Payroll data
        $payrollData = $this->getPayrollData($user->id);

        // Leave data
        $leaveData = $this->getLeaveData($user->id);

        // Recent activities
        $recentActivities = Activity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($activity) {
                return $this->formatActivity($activity);
            });

        // Upcoming events (you'll need to create an events table)
        $upcomingEvents = $this->getUpcomingEvents($user->id);

        // Tasks data
        $tasks = Task::where('user_id', $user->id)
            ->orderBy('due_date')
            ->take(4)
            ->get();

        return view('employees.dashboard', [
            'user' => $user,
            'today' => $today,
            'attendanceData' => $attendanceData,
            'monthlyAttendance' => $monthlyAttendance,
            'payrollData' => $payrollData,
            'leaveData' => $leaveData,
            'recentActivities' => $recentActivities,
            'upcomingEvents' => $upcomingEvents,
            'tasks' => $tasks,
            'newTasksCount' => Task::where('user_id', $user->id)
                                ->where('status', '!=', 'completed')
                                ->whereDate('created_at', '>=', now()->subDays(3))
                                ->count()
        ]);
    }

    private function formatAttendanceData($attendance)
    {
        if (!$attendance) {
            return [
                'status' => 'Absent',
                'status_class' => 'bg-red-100 text-red-800',
                'status_indicator' => 'status-absent',
                'time_in_time' => null,
                'time_out_time' => null,
                'time_in_status' => 'Pending',
                'time_in_status_class' => 'bg-gray-100 text-gray-800',
                'time_out_status' => 'Pending',
                'time_out_status_class' => 'bg-gray-100 text-gray-800',
                'hours_worked' => '0h 0m',
                'hours_percentage' => 0,
                'button_text' => 'Check In Now',
                'button_class' => 'bg-green-600',
                'button_pulse' => false
            ];
        }

        // Calculate hours worked
        $hoursWorked = '0h 0m';
        $hoursPercentage = 0;
        
        if ($attendance->time_in && $attendance->time_out) {
            $checkIn = Carbon::parse($attendance->time_in);
            $checkOut = Carbon::parse($attendance->time_out);
            $diff = $checkOut->diff($checkIn);
            $hoursWorked = $diff->h . 'h ' . $diff->i . 'm';
            $hoursPercentage = min(100, ($diff->h * 60 + $diff->i) / 8 * 100);
        }

        // Determine button state
        $buttonText = $attendance->time_out ? 'Check In Now' : 'Check Out Now';
        $buttonClass = $attendance->time_out ? 'bg-green-600' : 'bg-blue-600';
        $buttonPulse = !$attendance->time_out;

        return [
            'status' => ucfirst(str_replace('_', ' ', $attendance->status)),
            'status_class' => $this->getStatusClass($attendance->status),
            'status_indicator' => 'status-' . strtolower($attendance->status),
            'time_in_time' => $attendance->time_in ? Carbon::parse($attendance->time_in)->format('h:i A') : null,
            'time_out_time' => $attendance->time_out ? Carbon::parse($attendance->time_out)->format('h:i A') : null,
            'time_in_status' => $attendance->time_in ? ($this->isOnTime($attendance->time_in) ? 'On Time' : 'Late') : 'Pending',
            'time_in_status_class' => $attendance->time_in ? 
                ($this->isOnTime($attendance->time_in) ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') : 
                'bg-gray-100 text-gray-800',
            'time_out_status' => $attendance->time_out ? 'Completed' : 'Pending',
            'time_out_status_class' => $attendance->time_out ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800',
            'hours_worked' => $hoursWorked,
            'hours_percentage' => $hoursPercentage,
            'button_text' => $buttonText,
            'button_class' => $buttonClass,
            'button_pulse' => $buttonPulse
        ];
    }

    private function getStatusClass($status)
    {
        $classes = [
            'present' => 'bg-green-100 text-green-800',
            'late' => 'bg-yellow-100 text-yellow-800',
            'absent' => 'bg-red-100 text-red-800',
            'on_leave' => 'bg-blue-100 text-blue-800'
        ];

        return $classes[strtolower($status)] ?? 'bg-gray-100 text-gray-800';
    }

    private function isOnTime($checkInTime)
    {
        $onTimeLimit = '08:30:00'; // Company's on-time cutoff
        return Carbon::parse($checkInTime)->format('H:i:s') <= $onTimeLimit;
    }

    private function getMonthlyAttendanceStats($userId, $month, $year)
    {
        $stats = Attendance::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('
                COUNT(CASE WHEN status = "present" THEN 1 END) as present,
                COUNT(CASE WHEN status = "late" THEN 1 END) as late,
                COUNT(CASE WHEN status = "absent" THEN 1 END) as absent,
                COUNT(CASE WHEN status = "on_leave" THEN 1 END) as on_leave
            ')
            ->first();

        $totalDays = now()->daysInMonth;
        $presentDays = $stats->present + $stats->late;
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        return [
            'present' => $stats->present,
            'late' => $stats->late,
            'absent' => $stats->absent,
            'on_leave' => $stats->on_leave,
            'attendance_rate' => $attendanceRate,
            'total_days' => $totalDays
        ];
    }

    private function getPayrollData($userId)
    {
        $currentPayroll = Payroll::where('user_id', $userId)
            ->where('payroll_status', 'paid')
            ->latest('payment_date')
            ->first();

        $nextPayroll = Payroll::where('user_id', $userId)
            ->where('payroll_status', 'pending')
            ->whereDate('payment_date', '>=', now())
            ->orderBy('payment_date')
            ->first();

        return [
            'current' => $currentPayroll,
            'next' => $nextPayroll,
            'next_payday' => $nextPayroll ? Carbon::parse($nextPayroll->payment_date)->format('F j') : null
        ];
    }

    private function getLeaveData($userId)
    {
        $leaveBalance = 15; // This should come from your leave policy system
        $pendingLeaves = Leave::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $approvedLeaves = Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();

        return [
            'balance' => $leaveBalance,
            'pending' => $pendingLeaves,
            'on_leave' => $approvedLeaves
        ];
    }

    private function getUpcomingEvents($userId)
    {
        return Event::whereHas('participants', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(2)
            ->get()
            ->map(function($event) {
                return [
                    'title' => $event->title,
                    'date' => $event->start_time->format('Y-m-d'),
                    'time' => $event->start_time->format('h:i A'),
                    'location' => $event->location,
                    'icon' => $event->icon,
                    'icon_bg' => $event->icon_bg_class,
                    'event_id' => $event->id
                ];
            });
    }

    private function formatActivity($activity)
    {
        // Map activity types to icons and colors
        $typeMap = [
            'attendance' => ['icon' => 'fas fa-clock', 'bg' => 'bg-blue-100 text-blue-600'],
            'leave' => ['icon' => 'fas fa-umbrella-beach', 'bg' => 'bg-purple-100 text-purple-600'],
            'payroll' => ['icon' => 'fas fa-money-bill-wave', 'bg' => 'bg-green-100 text-green-600'],
            'task' => ['icon' => 'fas fa-tasks', 'bg' => 'bg-yellow-100 text-yellow-600'],
            'default' => ['icon' => 'fas fa-info-circle', 'bg' => 'bg-gray-100 text-gray-600']
        ];

        $typeConfig = $typeMap[$activity->type] ?? $typeMap['default'];

        return [
            'title' => $activity->message,
            'description' => $activity->data ?? '',
            'time_ago' => $activity->created_at->diffForHumans(),
            'icon' => $typeConfig['icon'],
            'icon_bg' => $typeConfig['bg']
        ];
    }
}