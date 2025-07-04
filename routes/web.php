<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

// Redirect root URL to appropriate dashboard if logged in, or to login otherwise
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
        
        // Default for all other authenticated users (including 'Employee' role)
        return redirect()->route('employee.dashboard');
    }
    
    return redirect()->route('login');
});

// Auth routes (login, register, forgot password, etc.)
Auth::routes();

// Dashboard routes with auth middleware
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    Route::prefix('hr')->group(function () {
        Route::get('/dashboard', function () {
            return view('hr.dashboard');
        })->name('hr.dashboard');
    });

    Route::prefix('dept')->group(function () {
        Route::get('/dashboard', function () {
            return view('dept.dashboard');
        })->name('dept.dashboard');
    });

    Route::prefix('finance')->group(function () {
        Route::get('/dashboard', function () {
            return view('finance.dashboard');
        })->name('finance.dashboard');
    });

    Route::prefix('employee')->group(function () {
        Route::get('/dashboard', function () {
            return view('employee.dashboard');
        })->name('employee.dashboard');
    });
});