<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Activity;

class AdminDashboardController extends Controller
{
    public function adminDashboard()
    {
        // Get authenticated user
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get today's date
        $today = now()->format('Y-m-d');

        // Get current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        // Get comprehensive payroll stats
        $payrollData = DB::table('payrolls')
            ->select(
                DB::raw('SUM(net_salary) as total_payroll'),
                DB::raw('SUM(CASE WHEN payroll_status = "paid" THEN net_salary ELSE 0 END) as processed'),
                DB::raw('SUM(CASE WHEN payroll_status = "pending" THEN net_salary ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN EXISTS (SELECT 1 FROM model_has_roles WHERE model_has_roles.model_id = users.id AND model_has_roles.role_id IN (SELECT id FROM roles WHERE name = "Employee")) THEN payrolls.net_salary ELSE 0 END) as employee_payroll'),
                DB::raw('SUM(CASE WHEN EXISTS (SELECT 1 FROM model_has_roles WHERE model_has_roles.model_id = users.id AND model_has_roles.role_id IN (SELECT id FROM roles WHERE name = "HR Manager")) THEN payrolls.net_salary ELSE 0 END) as hr_payroll')
            )
            ->leftJoin('users', 'payrolls.user_id', '=', 'users.id')
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->first();

        // Get last month's payroll for comparison
        $lastMonthPayroll = DB::table('payrolls')
            ->whereMonth('payment_date', $lastMonth)
            ->whereYear('payment_date', $lastMonthYear)
            ->where('payroll_status', 'paid')
            ->sum('net_salary');

        // Calculate percentage change
        $payrollPercentageChange = $lastMonthPayroll > 0 
            ? round(($payrollData->processed - $lastMonthPayroll) / $lastMonthPayroll * 100)
            : 0;

        // Get notification count
        $unreadNotifications = $user->unreadNotifications()->count();

        // Update faculty count to use roles
        $stats = [
            'totalEmployees' => User::count(),
            'facultyCount' => User::role('Employee')->count(),
        ];

        // Get attendance stats
        $attendanceStats = DB::table('attendances')
            ->select(
                DB::raw('COUNT(DISTINCT CASE WHEN status = "present" THEN user_id END) as present'),
                DB::raw('COUNT(DISTINCT CASE WHEN status = "absent" THEN user_id END) as absent'),
                DB::raw('COUNT(DISTINCT CASE WHEN status = "on_leave" THEN user_id END) as on_leave')
            )
            ->where('date', $today)
            ->first();

        // Get recent check-ins with proper user photo URL
        $recentCheckins = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->select(
                'users.first_name',
                'users.last_name',
                'users.department',
                DB::raw('COALESCE(users.profile_photo_path, "default-profile.jpg") as profile_photo_path'),
                'attendances.time_in'
            )
            ->where('attendances.date', $today)
            ->whereNotNull('attendances.time_in')
            ->orderBy('attendances.time_in', 'desc')
            ->take(5)
            ->get();

        // Get upcoming payments
        $upcomingPayments = DB::table('payrolls')
            ->join('users', 'payrolls.user_id', '=', 'users.id')
            ->select(
                'users.first_name',
                'users.last_name',
                'users.department',
                'users.profile_photo_path',
                'payrolls.net_salary',
                'payrolls.payment_date'
            )
            ->where('payrolls.payroll_status', 'pending')
            ->whereDate('payrolls.payment_date', '>=', now())
            ->orderBy('payrolls.payment_date')
            ->take(3)
            ->get();

        // Get department distribution
        $departmentDistribution = User::select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get();

        // Prepare color palette for chart
        $colorPalette = [
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 99, 132, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)',
            'rgba(199, 199, 199, 0.6)',
            'rgba(83, 102, 255, 0.6)'
        ];

        $borderPalette = [
            'rgba(54, 162, 235, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(199, 199, 199, 1)',
            'rgba(83, 102, 255, 1)'
        ];

        $deptLabels = [];
        $deptData = [];
        $colorMap = [];

        foreach ($departmentDistribution as $index => $dept) {
            $deptLabels[] = $dept->department;
            $deptData[] = $dept->count;
            $colorIndex = $index % count($colorPalette);
            $colorMap[$dept->department] = [
                'background' => $colorPalette[$colorIndex],
                'border' => $borderPalette[$colorIndex]
            ];
        }

        // Get unique departments for filter dropdown
        $departments = User::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        // Get recent employees
        $recentEmployees = User::with('roles')->latest()->take(5)->get();

        // Get comprehensive gender distribution
        $genderDistributionQuery = User::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();

        $genderDistribution = [];
        foreach ($genderDistributionQuery as $item) {
            $genderDistribution[$item->gender] = $item->count;
        }

        // Get sex distribution
        $sexDistributionQuery = User::select('sex', DB::raw('count(*) as count'))
            ->groupBy('sex')
            ->get();

        $sexDistribution = [];
        foreach ($sexDistributionQuery as $item) {
            $sexDistribution[$item->sex] = $item->count;
        }

        // Ensure all gender options are represented (even if count is 0)
        $allGenderOptions = [
            'Male', 'Female', 'Non-Binary', 'Genderqueer', 'Genderfluid', 'Agender',
            'Bigender', 'Two-Spirit', 'Cisgender Male', 'Cisgender Female',
            'Transgender Male', 'Transgender Female', 'Transmasculine', 'Transfeminine',
            'Androgynous', 'Demiboy', 'Demigirl', 'Neutrois', 'Pangender',
            'Gender Nonconforming', 'Questioning', 'Prefer not to say', 'Other'
        ];

        $completeGenderDistribution = [];
        foreach ($allGenderOptions as $gender) {
            $completeGenderDistribution[$gender] = $genderDistribution[$gender] ?? 0;
        }

        // Handle null/empty values for gender
        $nullGenderCount = ($genderDistribution[''] ?? 0) + ($genderDistribution[null] ?? 0);
        if ($nullGenderCount > 0) {
            $completeGenderDistribution['Not Specified'] = $nullGenderCount;
        }

        // Handle null/empty values for sex
        $nullSexCount = ($sexDistribution[''] ?? 0) + ($sexDistribution[null] ?? 0);
        if ($nullSexCount > 0) {
            $sexDistribution['Not Specified'] = $nullSexCount;
        }

        // Ensure sex distribution has all expected keys
        $completeSexDistribution = [
            'Male' => $sexDistribution['Male'] ?? 0,
            'Female' => $sexDistribution['Female'] ?? 0,
            'Not Specified' => $sexDistribution['Not Specified'] ?? 0
        ];
        
        // Get PWD statistics
        $pwdStats = [
            'Persons With Disabilities' => User::where('is_pwd', true)->count(),
            'Without Disabilities' => User::where('is_pwd', false)->count()
        ];


        return view('admin.dashboard', [
            'user' => $user,
            'unreadNotifications' => $unreadNotifications,
            'stats' => $stats,
            'payrollData' => $payrollData,
            'payrollPercentageChange' => $payrollPercentageChange,
            'attendanceStats' => $attendanceStats,
            'recentCheckins' => $recentCheckins,
            'upcomingPayments' => $upcomingPayments,
            'departmentDistribution' => $departmentDistribution,
            'departmentLabels' => $deptLabels,
            'departmentData' => $deptData,
            'departmentColorMap' => $colorMap,
            'departments' => $departments,
            'recentEmployees' => $recentEmployees,
            'genderDistribution' => $completeGenderDistribution,
            'sexDistribution' => $completeSexDistribution,
            'pwdStats' => $pwdStats,
            'totalEmployees' => $stats['totalEmployees']
        ]);
    }
}