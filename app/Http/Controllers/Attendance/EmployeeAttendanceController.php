<?php

namespace App\Http\Controllers\Attendance;

use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->format('Y-m-d');
        $user = auth()->user();
        
        // Get user-specific data
        $todayRecord = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
            
        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();
            
        $monthlySummary = $this->getMonthlySummary($user->id);

        // Filter attendance records
        $attendanceQuery = Attendance::with('user')
            ->when($request->date, function($query) use ($request) {
                $query->where('date', $request->date);
            })
            ->when($request->department, function($query) use ($request) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('department', $request->department);
                });
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            });

        // Handle sorting
        $sort = $request->sort;
        $direction = $request->direction ?? 'asc';
        
        if ($sort) {
            switch ($sort) {
                case 'employee':
                    $attendanceQuery->join('users', 'attendances.user_id', '=', 'users.id')
                        ->orderBy('users.full_name', $direction)
                        ->select('attendances.*');
                    break;
                case 'department':
                    $attendanceQuery->join('users', 'attendances.user_id', '=', 'users.id')
                        ->orderBy('users.department', $direction)
                        ->select('attendances.*');
                    break;
                case 'check_in':
                    $attendanceQuery->orderBy('check_in', $direction);
                    break;
                case 'check_out':
                    $attendanceQuery->orderBy('check_out', $direction);
                    break;
                case 'status':
                    $attendanceQuery->orderBy('status', $direction);
                    break;
                default:
                    $attendanceQuery->orderBy('date', 'desc');
            }
        } else {
            $attendanceQuery->orderBy('date', 'desc');
        }

        $attendanceRecords = $attendanceQuery->paginate(10);

        $response = response()->view('admin.attendance', [
            'todayRecord' => $todayRecord,
            'history' => $history,
            'monthlySummary' => $monthlySummary,
            'attendanceRecords' => $attendanceRecords,
            'departments' => User::whereNotNull('department')
                ->distinct()
                ->pluck('department'),
            'user' => $user,
            'todaySummary' => $this->getTodaySummary()
        ]);

        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
                       ->header('Pragma', 'no-cache')
                       ->header('Expires', '0');
    }

    public function clockIn(Request $request)
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        // Check if user already clocked in today
        $existingRecord = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existingRecord) {
            return back()->with('error', 'You have already clocked in today');
        }

        try {
            // Get approximate location
            $ip = $request->ip();
            $location = Location::get($ip);
            $locationData = $location ? $location->cityName . ', ' . $location->countryName : 'Unknown';
            
            // Determine status based on shift time
            $status = 'Present';
            $lateThreshold = '09:15:00';
            
            if ($currentTime > $lateThreshold) {
                $status = 'Late';
            }

            // Create new attendance record
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $currentTime,
                'status' => $status,
                'method' => $request->has('biometric') ? 'Biometric' : 'Manual',
                'location' => $locationData,
                'ip_address' => $ip
            ]);

            return back()->with('success', 'Successfully clocked in at ' . now()->format('h:i A'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clock in: ' . $e->getMessage());
        }
    }

    public function clockOut(Request $request)
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        // Find today's attendance record
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'You need to clock in first');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'You have already clocked out today');
        }

        try {
            // Get approximate location
            $ip = $request->ip();
            $location = Location::get($ip);
            $locationData = $location ? $location->cityName . ', ' . $location->countryName : 'Unknown';

            $attendance->update([
                'check_out' => $currentTime,
                'method' => $attendance->method ?: 'Manual',
                'location_out' => $locationData,
                'ip_address_out' => $ip
            ]);

            return back()->with('success', 'Successfully clocked out at ' . now()->format('h:i A'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clock out: ' . $e->getMessage());
        }
    }
    
    private function getTodaySummary()
    {
        $today = now()->format('Y-m-d');
        
        return Attendance::where('date', $today)
            ->selectRaw('
                COUNT(*) as total_employees,
                SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = "On_Leave" THEN 1 ELSE 0 END) as on_leave_count
            ')
            ->first();
    }

    public function edit(Attendance $attendance)
    {
        return response()->json([
            'success' => true,
            'record' => [
                'id' => $attendance->id,
                'date' => $attendance->date,
                'check_in' => $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : null,
                'check_out' => $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : null,
                'status' => $attendance->status
            ]
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:Present,Late,Absent,On_Leave'
        ]);

        try {
            $attendance->update([
                'date' => $validated['date'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance record updated successfully',
                'record' => $attendance->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attendance record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Attendance $attendance)
    {
        try {
            $attendance->delete();
            return response()->json([
                'success' => true,
                'message' => 'Attendance record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attendance record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getMonthlySummary($userId)
    {
        return Attendance::where('user_id', $userId)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent_days
            ')
            ->first();
    }

    public function export(Request $request)
    {
        $attendanceQuery = Attendance::with('user')
            ->when($request->date, function($query) use ($request) {
                $query->where('date', $request->date);
            })
            ->when($request->department, function($query) use ($request) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('department', $request->department);
                });
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('date', 'desc');

        $attendances = $attendanceQuery->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=attendance_records_' . now()->format('Y-m-d') . '.csv',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Department',
                'Date',
                'Check In',
                'Check Out',
                'Status',
                'Method'
            ]);

            // Add data rows
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->user->full_name,
                    $attendance->user->employee_id,
                    $attendance->user->department,
                    $attendance->date,
                    $attendance->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : '-',
                    $attendance->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : '-',
                    $attendance->status,
                    $attendance->method
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getMonthlyComparisonData(Request $request)
    {
        $months = $request->input('months', 12);
        $endDate = now()->endOfMonth();
        $startDate = now()->subMonths($months - 1)->startOfMonth();

        // Get all months in the range
        $monthsRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthsRange[] = $currentDate->format('Y-m');
            $currentDate->addMonth();
        }

        // Get attendance data for each month
        $monthlyData = [];
        foreach ($monthsRange as $month) {
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();

            $stats = Attendance::whereBetween('date', [$start, $end])
                ->selectRaw('
                    SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN status = "On_Leave" THEN 1 ELSE 0 END) as on_leave
                ')
                ->first();

            $monthlyData[] = [
                'month' => $start->format('M Y'),
                'present' => $stats->present ?? 0,
                'late' => $stats->late ?? 0,
                'absent' => $stats->absent ?? 0,
                'on_leave' => $stats->on_leave ?? 0
            ];
        }

        // Prepare data for chart
        $labels = array_column($monthlyData, 'month');
        $present = array_column($monthlyData, 'present');
        $late = array_column($monthlyData, 'late');
        $absent = array_column($monthlyData, 'absent');
        $on_leave = array_column($monthlyData, 'on_leave');

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'current' => [
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                    'on_leave' => $on_leave
                ]
            ]
        ]);
    }

    public function getCalendarData(Request $request)
{
    $month = $request->input('month', now()->month);
    $year = $request->input('year', now()->year);
    $userId = auth()->id();

    // Get all dates in the month
    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = Carbon::create($year, $month, 1)->endOfMonth();

    // Get attendance records for the month
    $attendanceRecords = Attendance::where('user_id', $userId)
        ->whereBetween('date', [$startDate, $endDate])
        ->get()
        ->keyBy(function ($record) {
            return Carbon::parse($record->date)->format('Y-n-j');
        });

    // Format the data for the calendar
    $formattedData = [];
    foreach ($attendanceRecords as $date => $record) {
        $formattedData[$date] = [
            'status' => $record->status,
            'check_in' => $record->check_in
        ];
    }

    // Get monthly summary
    $summary = Attendance::where('user_id', $userId)
        ->whereBetween('date', [$startDate, $endDate])
        ->selectRaw('
            SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present_days,
            SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late_days,
            SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent_days,
            SUM(CASE WHEN status = "On_Leave" THEN 1 ELSE 0 END) as on_leave_days,
            COUNT(*) as total_working_days
        ')
        ->first();

    return response()->json([
        'attendance' => $formattedData,
        'summary' => $summary
    ]);
}
}