<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSettingsController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\EmployeeDashboardController;
use App\Http\Controllers\Attendance\AdminAttendanceController;
use App\Http\Controllers\Attendance\EmployeeAttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;

// Redirect root URL to appropriate dashboard
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('HR Manager')) {
            return redirect()->route('hr.dashboard');
        } elseif ($user->hasRole('Department Head')) {
            return redirect()->route('dept.dashboard');
        } elseif ($user->hasRole('Finance Officer')) {
            return redirect()->route('finance.dashboard');
        }
        
        return redirect()->route('employees.dashboard');
    }
    
    return redirect()->route('login');
});

// Auth routes
Auth::routes(['verify' => true]);

// Profile routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deleteProfilePhoto'])->name('profile.photo.delete');
    Route::get('/profile/settings', [ProfileSettingsController::class, 'index'])->name('profile.settings');
});

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::prefix('admin')->middleware('role:Super Admin|HR Manager')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        
        Route::prefix('admin')->middleware(['auth', 'verified', 'role:Super Admin|HR Manager'])->group(function () {
            // Employee Management
            Route::prefix('employees')->group(function () {
                Route::get('/', [EmployeeController::class, 'index'])->name('admin.employees'); // This matches your blade file
                Route::post('/', [EmployeeController::class, 'store'])->name('admin.employees.store');
                Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
                Route::get('/{employee}', [EmployeeController::class, 'show'])->name('admin.employees.show');
                Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
                Route::put('/{employee}', [EmployeeController::class, 'update'])->name('admin.employees.update');
                Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');
                Route::post('/{employee}/upload-photo', [EmployeeController::class, 'uploadPhoto'])->name('admin.employees.upload-photo');
            });
        });


        Route::prefix('attendance')->group(function () {
        Route::get('/', [AdminAttendanceController::class, 'index'])->name('admin.attendance');
        Route::post('/clock-in', [AdminAttendanceController::class, 'clockIn'])->name('attendance.clockIn');
        Route::post('/clock-out', [AdminAttendanceController::class, 'clockOut'])->name('attendance.clockOut');
        Route::get('/export', [AdminAttendanceController::class, 'export'])->name('attendance.export');
        Route::get('/monthly-comparison-data', [AdminAttendanceController::class, 'getMonthlyComparisonData'])->name('attendance.monthlyComparison');
        Route::get('/{attendance}/edit', [AdminAttendanceController::class, 'edit'])->name('admin.attendance.edit');
        Route::put('/{attendance}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
        Route::delete('/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
        
        Route::get('/attendance/calendar-data', [EmployeeAttendanceController::class, 'getCalendarData']);
    });     

        
        Route::get('/leaves', function () {
            return view('admin.leaves');
        })->name('admin.leaves');
        
        Route::get('/travel', function () {
            return view('admin.travel');
        })->name('admin.travel');
        
        Route::get('/payroll', function () {
            return view('admin.payroll');
        })->name('admin.payroll');
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('admin.reports');
        
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('admin.settings');
    });

    // HR routes (same views as admin but different route names)
    Route::prefix('hr')->middleware('role:HR Manager')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('hr.dashboard');
        
        Route::get('/attendance', function () {
            return view('admin.attendance');
        })->name('hr.attendance');
        
        Route::get('/leaves', function () {
            return view('admin.leaves');
        })->name('hr.leaves');
        
        Route::get('/travel', function () {
            return view('admin.travel');
        })->name('hr.travel');
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('hr.reports');
        
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('hr.settings');
    });

    // Department Head routes
    Route::prefix('dept')->middleware('role:Department Head')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dept.dashboard');
        
        // Limited employee access
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('dept.employees.index');
            Route::get('/{employee}', [EmployeeController::class, 'show'])->name('dept.employees.show')->middleware('permission:employee-view-details');
            Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('dept.employees.edit')->middleware('permission:employee-edit');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('dept.employees.update')->middleware('permission:employee-edit');
        });

        Route::get('/attendance', function () {
            return view('admin.attendance');
        })->name('dept.attendance');
        
        Route::get('/leaves', function () {
            return view('admin.leaves');
        })->name('dept.leaves');
        
        Route::get('/travel', function () {
            return view('admin.travel');
        })->name('dept.travel');
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('dept.reports');
        
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('dept.settings');
    });

    // Finance routes
    Route::prefix('finance')->middleware('role:Finance Officer')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('finance.dashboard');
        
        Route::get('/attendance', function () {
            return view('admin.attendance');
        })->name('finance.attendance');
        
        Route::get('/leaves', function () {
            return view('admin.leaves');
        })->name('finance.leaves');
        
        Route::get('/travel', function () {
            return view('admin.travel');
        })->name('finance.travel');
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('finance.reports');
        
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('finance.settings');
    });
    
    // Employee routes
    Route::prefix('employees')->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'employeeDashboard'])->name('employees.dashboard');
        
        Route::get('/employees/payroll', [PayrollController::class, 'index'])->name('employees.payroll');
        Route::get('/employees/tasks', [TasksController::class, 'index'])->name('employees.tasks');
        Route::post('/attendance/check', [EmployeeAttendanceController::class, 'check'])->name('attendance.check');

        Route::get('/tasks', [TasksController::class, 'index'])->name('employee.tasks');
        Route::put('/tasks/{task}', [TasksController::class, 'update'])->name('employee.tasks.update');

        Route::get('/events', [EventController::class, 'index'])->name('employee.events');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('employee.events.show');
        Route::post('/events/{event}/status', [EventController::class, 'updateStatus'])->name('employee.events.status');
        
        // Attendance
        Route::get('/attendance', function () {
            return view('employees.attendance');
        })->name('employees.attendance');

        // Leave
        Route::get('/leave', function () {
            return view('employees.leaves');
        })->name('employees.leaves');

        // Travel
        Route::get('/travel', function () {
            return view('employees.travel');
        })->name('employees.travel');

        Route::get('/payroll', function () {
            return view('employees.payroll');
        })->name('employees.payroll');

        // Reports
        Route::get('/reports', function () {
            return view('employees.reports');
        })->name('employees.reports');

        // Settings
        Route::get('/settings', function () {
            return view('employees.settings');
        })->name('employees.settings');
    });
});