<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Total staff count
        $totalStaff = User::count();
        
        // Present today (has attendance record for today)
        $presentToday = Attendance::whereDate('date', $today)
            ->where('status', '!=', 'absent')
            ->distinct('user_id')
            ->count('user_id');
            
        // Late arrivals today
        $lateToday = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->count();
            
        // Absent today (users without attendance record for today)
        $allUserIds = User::pluck('id');
        $presentUserIds = Attendance::whereDate('date', $today)->pluck('user_id');
        $absentToday = $allUserIds->diff($presentUserIds)->count();
        
        // Department summary
        $departments = User::select('department')
            ->selectRaw('count(*) as total')
            ->groupBy('department')
            ->get();
            
        $departmentStats = [];
        foreach ($departments as $dept) {
            $present = Attendance::whereDate('date', $today)
                ->whereHas('user', function($query) use ($dept) {
                    $query->where('department', $dept->department);
                })
                ->where('status', '!=', 'absent')
                ->distinct('user_id')
                ->count('user_id');
                
            $percentage = $dept->total > 0 ? round(($present / $dept->total) * 100) : 0;
            
            $departmentStats[] = [
                'name' => $dept->department,
                'total' => $dept->total,
                'present' => $present,
                'percentage' => $percentage
            ];
        }
        
        // Recent attendance activity
        $recentActivity = Attendance::with('user')
            ->whereDate('date', $today)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
            
        // Staff attendance records with stats
        $staffAttendance = User::with(['attendances' => function($query) {
                $query->whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
            }])
            ->with(['todayAttendance'])
            ->paginate(10);
            
        // Add weekly and monthly stats to each user
        $staffAttendance->each(function($user) {
            $user->weeklyPresentCount = $user->attendances
                ->where('status', '!=', 'absent')
                ->count();
                
            $user->monthlyPresentCount = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])
                ->where('status', '!=', 'absent')
                ->count();
                
            // Assuming 5 working days per week and 20 per month
            $user->workingDaysThisWeek = 5;
            $user->workingDaysThisMonth = 20;
        });
        
        // Pending requests (attendance regularizations and leave requests)
        $pendingRequests = Leave::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('admin.attendance', compact(
            'totalStaff',
            'presentToday',
            'lateToday',
            'absentToday',
            'departmentStats',
            'recentActivity',
            'staffAttendance',
            'pendingRequests',
            'departments'
        ));
    }
    
    public function showDetails($id, Request $request)
    {
        $type = $request->query('type', 'attendance');
        
        if ($type === 'attendance') {
            $attendance = Attendance::with('user')->findOrFail($id);
            
            $html = view('admin.attendance._modal_content', [
                'attendance' => $attendance
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'id' => $id
            ]);
        } else {
            $user = User::with(['attendances' => function($query) {
                $query->whereDate('date', today())
                    ->orderByDesc('created_at')
                    ->limit(1);
            }])->findOrFail($id);
            
            $html = view('admin.attendance._user_attendance', [
                'user' => $user,
                'attendance' => $user->attendances->first()
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'id' => $id
            ]);
        }
    }
    
    public function approveRequest($id)
    {
        $request = Leave::findOrFail($id);
        $request->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully'
        ]);
    }
    
    public function rejectRequest($id)
    {
        $request = Leave::findOrFail($id);
        $request->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Request rejected'
        ]);
    }
    
    
}