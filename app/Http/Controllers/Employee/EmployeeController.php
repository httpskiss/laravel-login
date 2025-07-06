<?php

namespace App\Http\Controllers\Employee;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employee-list|employee-view-details', ['only' => ['index']]);
        $this->middleware('permission:employee-create', ['only' => ['store']]);
        $this->middleware('permission:employee-edit', ['only' => ['update']]);
        $this->middleware('permission:employee-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        // Only show employees in the same department for department heads
        if (auth()->user()->hasRole('Department Head')) {
            $employees = User::where('department', auth()->user()->department)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $employees = User::orderBy('created_at', 'desc')->get();
        }

        $departments = User::select('department')->distinct()->orderBy('department')->pluck('department');
        
        $employees->each(function ($employee) {
            $employee->append('profile_photo_url');
        });
        
        if (request()->wantsJson()) {
            return response()->json([
                'employees' => $employees,
                'departments' => $departments
            ]);
        }
        
        return view('admin.employees', [
            'employees' => $employees,
            'departments' => $departments,
            'roles' => Role::all() // Add roles for the form
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'required|string|max:50|unique:users',
            'department' => 'required|string|max:255',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'password' => 'nullable|string|min:8',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|string|exists:roles,name'
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $employee = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'employee_id' => $validated['employee_id'],
            'department' => $validated['department'],
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'address' => $validated['address'],
            'hire_date' => $validated['hire_date'],
            'password' => Hash::make($validated['password'] ?? 'password'),
            'profile_photo_path' => $profilePhotoPath,
            'user_status' => 'Active' // Default status
        ]);

        // Assign role
        $employee->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'employee' => $employee->load('roles')
        ]);
    }

    public function update(Request $request, User $employee)
    {
        // Check if user has permission to edit this employee
        if (auth()->user()->hasRole('Department Head') && 
            $employee->department !== auth()->user()->department) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit employees in your department'
            ], 403);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$employee->id,
            'phone' => 'sometimes|string|max:20',
            'employee_id' => 'sometimes|string|max:50|unique:users,employee_id,'.$employee->id,
            'department' => 'sometimes|string|max:255',
            'gender' => 'sometimes|string|in:Male,Female,Other',
            'dob' => 'sometimes|date',
            'address' => 'sometimes|string',
            'hire_date' => 'sometimes|date',
            'user_status' => 'sometimes|string|in:Active,On Leave,Suspended,Terminated',
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
            'role' => 'sometimes|string|exists:roles,name'
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($employee->profile_photo_path) {
                Storage::disk('public')->delete($employee->profile_photo_path);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo_path'] = $profilePhotoPath;
        } elseif ($request->has('remove_profile_photo')) {
            if ($employee->profile_photo_path) {
                Storage::disk('public')->delete($employee->profile_photo_path);
            }
            $validated['profile_photo_path'] = null;
        }

        $employee->update($validated);

        // Update role if provided
        if ($request->has('role')) {
            $employee->syncRoles([$validated['role']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'employee' => $employee->fresh()->append('profile_photo_url')->load('roles')
        ]);
    }

    public function destroy(User $employee)
    {
        // Check if user has permission to delete this employee
        if (auth()->user()->hasRole('Department Head') && 
            $employee->department !== auth()->user()->department) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete employees in your department'
            ], 403);
        }

        if ($employee->profile_photo_path) {
            Storage::disk('public')->delete($employee->profile_photo_path);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }
}