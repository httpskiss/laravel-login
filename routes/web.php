<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSettingsController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\EmployeeDashboardController;
use App\Http\Controllers\Attendance\AdminAttendanceController;
use App\Http\Controllers\Attendance\EmployeeAttendanceController;
use App\Http\Controllers\Leave\AdminLeaveController;
use App\Http\Controllers\Leave\EmployeeLeaveController;
use App\Http\Controllers\Travel\AdminTravelController;
use App\Http\Controllers\Travel\EmployeeTravelController;
use App\Http\Controllers\Complaints\EmployeeComplaintController;
use App\Http\Controllers\Complaints\AdminComplaintController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\SocialLoginController;

// Social Login Routes
Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.login');
Route::get('auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])->name('social.callback');

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
    Route::prefix('admin')->middleware(['role:Super Admin|HR Manager'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // Employee Management
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('admin.employees');
            Route::post('/', [EmployeeController::class, 'store'])->name('admin.employees.store');
            Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
            Route::get('/{employee}', [EmployeeController::class, 'show'])->name('admin.employees.show');
            Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('admin.employees.update');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');
            Route::post('/{employee}/upload-photo', [EmployeeController::class, 'uploadPhoto'])->name('admin.employees.upload-photo');
        });

        // Admin attendance routes
        Route::prefix('attendance')->group(function () {
            Route::get('/', [AdminAttendanceController::class, 'index'])->name('admin.attendance');
            Route::post('/', [AdminAttendanceController::class, 'store'])->name('admin.attendance.store');
            Route::get('/departments/data', [AdminAttendanceController::class, 'getDepartmentData'])->name('admin.attendance.departments.data');
            Route::get('/{id}/details', [AdminAttendanceController::class, 'showDetails'])->name('admin.attendance.details');
            Route::put('/{attendance}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
            Route::delete('/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
            Route::get('/report', [AdminAttendanceController::class, 'report'])->name('admin.attendance.report');
            Route::get('/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.export');
        });

        // Admin leave routes
        Route::prefix('leaves')->group(function () {
            Route::get('/', [AdminLeaveController::class, 'index'])->name('admin.leaves');
            Route::get('/index', [AdminLeaveController::class, 'index'])->name('admin.leaves.index');
            Route::get('/create', [AdminLeaveController::class, 'create'])->name('admin.leaves.create');
            Route::post('/', [AdminLeaveController::class, 'store'])->name('admin.leaves.store');
            Route::get('/{leave}', [AdminLeaveController::class, 'show'])->name('admin.leaves.show');
            Route::get('/{leave}/edit', [AdminLeaveController::class, 'edit'])->name('admin.leaves.edit');
            Route::put('/{leave}', [AdminLeaveController::class, 'update'])->name('admin.leaves.update');
            Route::delete('/{leave}', [AdminLeaveController::class, 'destroy'])->name('admin.leaves.destroy');
            Route::post('/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('admin.leaves.approve');
            Route::post('/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('admin.leaves.reject');
            Route::post('/{leave}/cancel', [AdminLeaveController::class, 'cancel'])->name('admin.leaves.cancel');
            Route::get('/export', [AdminLeaveController::class, 'export'])->name('admin.leaves.export');
            Route::get('/report', [AdminLeaveController::class, 'report'])->name('admin.leaves.report');
        });

        // Admin travel routes
        Route::prefix('travel')->group(function () {
            Route::get('/', [AdminTravelController::class, 'index'])->name('admin.travel');
            Route::get('/{travel}', [AdminTravelController::class, 'show'])->name('admin.travel.show');
            Route::post('/{travel}/approve/{step}', [AdminTravelController::class, 'approveStep'])->name('admin.travel.approve');
            Route::get('/export', [AdminTravelController::class, 'export'])->name('admin.travel.export');
            Route::get('/report', [AdminTravelController::class, 'report'])->name('admin.travel.report');
        });

        // Admin complaints routes
        Route::prefix('complaints')->group(function () {
            Route::get('/', [AdminComplaintController::class, 'index'])->name('admin.complaints.index');
            Route::get('/{complaint}', [AdminComplaintController::class, 'show'])->name('admin.complaints.show');
            Route::post('/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('admin.complaints.update-status');
            Route::post('/{complaint}/note', [AdminComplaintController::class, 'addNote'])->name('admin.complaints.add-note');
        });

        // Other admin routes
        Route::get('/payroll', function () {
            return view('admin.payroll');
        })->name('admin.payroll');

        Route::get('/pds', function () {
            return view('admin.pds');
        })->name('admin.pds');

        Route::get('/saln', function () {
            return view('admin.saln');
        })->name('admin.saln');
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('admin.reports');
        
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('admin.settings');
    });

    // Employee attendance routes (for all authenticated employees)
    Route::prefix('employee/attendance')->group(function () {
        Route::get('/', [EmployeeAttendanceController::class, 'index'])->name('employees.attendance');
        Route::post('/check', [EmployeeAttendanceController::class, 'check'])->name('attendance.check');
        Route::post('/regularization', [EmployeeAttendanceController::class, 'regularization'])->name('employees.attendance.regularization');
        Route::get('/all', [EmployeeAttendanceController::class, 'allRecords'])->name('employees.attendance.all');
    });

    // Employee travel routes
    Route::prefix('employee/travel')->group(function () {
        Route::get('/', [EmployeeTravelController::class, 'index'])->name('employees.travel');
        Route::get('/create', [EmployeeTravelController::class, 'create'])->name('employees.travel.create');
        Route::post('/', [EmployeeTravelController::class, 'store'])->name('employees.travel.store');
        Route::get('/{travel}', [EmployeeTravelController::class, 'show'])->name('employees.travel.show');
        Route::delete('/{travel}', [EmployeeTravelController::class, 'destroy'])->name('employees.travel.destroy');
    });

    // Employee complaints routes
    Route::prefix('employee/complaints')->group(function () {
        Route::get('/', [EmployeeComplaintController::class, 'index'])->name('employees.complaints.index');
        Route::get('/create', [EmployeeComplaintController::class, 'create'])->name('employees.complaints.create');
        Route::post('/', [EmployeeComplaintController::class, 'store'])->name('employees.complaints.store');
        Route::get('/{complaint}', [EmployeeComplaintController::class, 'show'])->name('employees.complaints.show');
    });

    // HR routes
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
        
        // HR travel routes
        Route::prefix('travel')->group(function () {
            Route::get('/', [AdminTravelController::class, 'index'])->name('hr.travel');
            Route::get('/{travel}', [AdminTravelController::class, 'show'])->name('hr.travel.show');
            Route::post('/{travel}/approve/{step}', [AdminTravelController::class, 'approveStep'])->name('hr.travel.approve');
        });

        // HR complaints routes
        Route::prefix('complaints')->group(function () {
            Route::get('/', [AdminComplaintController::class, 'index'])->name('hr.complaints.index');
            Route::get('/{complaint}', [AdminComplaintController::class, 'show'])->name('hr.complaints.show');
            Route::post('/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('hr.complaints.update-status');
            Route::post('/{complaint}/note', [AdminComplaintController::class, 'addNote'])->name('hr.complaints.add-note');
        });
        
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

        // Department Head leave management
        Route::prefix('leaves')->group(function () {
            Route::get('/', [AdminLeaveController::class, 'index'])->name('dept.leaves');
            Route::get('/{leave}', [AdminLeaveController::class, 'show'])->name('dept.leaves.show');
            Route::post('/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('dept.leaves.approve');
            Route::post('/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('dept.leaves.reject');
        });

        // Department Head travel management
        Route::prefix('travel')->group(function () {
            Route::get('/', [AdminTravelController::class, 'index'])->name('dept.travel');
            Route::get('/{travel}', [AdminTravelController::class, 'show'])->name('dept.travel.show');
            Route::post('/{travel}/approve/{step}', [AdminTravelController::class, 'approveStep'])->name('dept.travel.approve');
        });

        // Department Head complaints access (view only)
        Route::prefix('complaints')->group(function () {
            Route::get('/', [AdminComplaintController::class, 'index'])->name('dept.complaints.index');
            Route::get('/{complaint}', [AdminComplaintController::class, 'show'])->name('dept.complaints.show');
        });

        Route::get('/attendance', function () {
            return view('admin.attendance');
        })->name('dept.attendance');
        
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
        
        // Finance travel routes
        Route::prefix('travel')->group(function () {
            Route::get('/', [AdminTravelController::class, 'index'])->name('finance.travel');
            Route::get('/{travel}', [AdminTravelController::class, 'show'])->name('finance.travel.show');
            Route::post('/{travel}/approve/{step}', [AdminTravelController::class, 'approveStep'])->name('finance.travel.approve');
        });
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('finance.reports');
        
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('finance.settings');
    });
    
    // Employee routes (for regular employees)
    Route::prefix('employees')->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'employeeDashboard'])->name('employees.dashboard');
        
        // Employee tasks
        Route::get('/tasks', [TasksController::class, 'index'])->name('employees.tasks');
        Route::put('/tasks/{task}', [TasksController::class, 'update'])->name('employees.tasks.update');

        // Employee events
        Route::get('/events', [EventController::class, 'index'])->name('employee.events');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('employee.events.show');
        Route::post('/events/{event}/status', [EventController::class, 'updateStatus'])->name('employee.events.status');
        
        // Employee payroll
        Route::get('/payroll', [PayrollController::class, 'index'])->name('employees.payroll');

        // Employee leaves
        Route::prefix('leaves')->group(function () {
            Route::get('/', [EmployeeLeaveController::class, 'index'])->name('employees.leaves');
            Route::get('/create', [EmployeeLeaveController::class, 'create'])->name('employees.leaves.create');
            Route::post('/', [EmployeeLeaveController::class, 'store'])->name('employees.leaves.store');
            Route::get('/{leave}', [EmployeeLeaveController::class, 'show'])->name('employees.leaves.show');
            Route::delete('/{leave}', [EmployeeLeaveController::class, 'destroy'])->name('employees.leaves.destroy');
        });

        // Employee complaints
        Route::prefix('complaints')->group(function () {
            Route::get('/', [EmployeeComplaintController::class, 'index'])->name('employees.complaints.index');
            Route::get('/create', [EmployeeComplaintController::class, 'create'])->name('employees.complaints.create');
            Route::post('/', [EmployeeComplaintController::class, 'store'])->name('employees.complaints.store');
            Route::get('/{complaint}', [EmployeeComplaintController::class, 'show'])->name('employees.complaints.show');
        });

        // Other employee routes
        Route::get('/pds', function () {
            return view('employees.pds');
        })->name('employees.pds');

        Route::get('/saln', function () {
            return view('employees.saln');
        })->name('employees.saln');

        Route::get('/reports', function () {
            return view('employees.reports');
        })->name('employees.reports');

        Route::get('/settings', function () {
            return view('employees.settings');
        })->name('employees.settings');
    });
});