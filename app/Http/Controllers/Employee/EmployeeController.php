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
            'travel_type' => 'required|string|in:official_time,official_business,personal_abroad,official_travel',
            'designation' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'duration_type' => 'required|string|in:single_day,multiple_days',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'inclusive_date_of_travel' => 'nullable|date',
            'purpose' => 'required|string',
            'transportation' => 'required|string|in:university_vehicle,public_conveyance,private_vehicle',
            'source_of_funds' => 'required|string|in:mooe,personal,other',
            'other_funds_specification' => 'nullable|string|max:255',
        ]);

        try {
            $travel = new TravelAuthority();
            $travel->user_id = Auth::id();
            $travel->fill($validated);
            
            // Set inclusive date based on duration type
            if ($validated['duration_type'] === 'single_day') {
                $travel->inclusive_date_of_travel = $validated['inclusive_date_of_travel'] ?? $validated['start_date'];
                $travel->start_date = $validated['start_date'];
                $travel->end_date = $validated['start_date'];
            } else {
                $travel->inclusive_date_of_travel = $validated['start_date'];
                $travel->start_date = $validated['start_date'];
                $travel->end_date = $validated['end_date'];
            }

            // Auto-set estimated expenses based on travel type
            if (in_array($validated['travel_type'], ['official_time', 'official_business', 'official_travel'])) {
                $travel->estimated_expenses = 'with_expenses';
            } else {
                $travel->estimated_expenses = 'official_time';
            }

            $travel->save();

            // Create initial approval records
            $this->createInitialApprovals($travel);

            return redirect()->route('employees.travel.show', $travel)
                ->with('success', 'Travel authority submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit travel authority: ' . $e->getMessage())
                ->withInput();
        }
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
            // Personal Information
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'is_pwd' => 'boolean',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$employee->id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            // FIX: Update this line to include all gender options
            'gender' => 'sometimes|string|in:Male,Female,Non-Binary,Genderqueer,Genderfluid,Agender,Bigender,Two-Spirit,Cisgender Male,Cisgender Female,Transgender Male,Transgender Female,Transmasculine,Transfeminine,Androgynous,Demiboy,Demigirl,Neutrois,Pangender,Gender Nonconforming,Questioning,Prefer not to say,Other',
            'sex' => 'sometimes|string|in:Male,Female',
            'civil_status' => 'sometimes|string|in:Single,Married,Divorced,Widowed,Separated',
            'dob' => 'sometimes|date',
            
            // Employment Information
            'employee_id' => 'sometimes|string|max:50|unique:users,employee_id,'.$employee->id,
            'department' => 'sometimes|string|max:255',
            'program' => 'nullable|string|max:255',
            'highest_educational_attainment' => 'nullable|string|in:Elementary,High School,Vocational,Associate Degree,Bachelor Degree,Master Degree,Doctorate,Post-Doctorate',
            'position' => 'sometimes|string|max:255',
            'designation' => 'nullable|string|max:255',
            'employee_type' => 'sometimes|string|max:255',
            'employment_type' => 'sometimes|string|in:Permanent Employee,Non-Permanent Employee,Contract of Service,Part-Time',
            'employee_category' => 'sometimes|string|in:Teaching,Non-Teaching,Teaching/Non-Teaching',
            'user_status' => 'sometimes|string|in:Active,On Leave,Suspended,Terminated',
            'hire_date' => 'sometimes|date',
            
            // Profile Photo
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
            'remove_profile_photo' => 'sometimes|boolean',
            'role' => 'sometimes|string|exists:roles,name'
        ]);

        // Handle profile photo
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($employee->profile_photo_path) {
                Storage::disk('public')->delete($employee->profile_photo_path);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo_path'] = $profilePhotoPath;
        } elseif ($request->has('remove_profile_photo') && $request->remove_profile_photo) {
            if ($employee->profile_photo_path) {
                Storage::disk('public')->delete($employee->profile_photo_path);
            }
            $validated['profile_photo_path'] = null;
        }

        // Convert boolean values
        if (isset($validated['is_pwd'])) {
            $validated['is_pwd'] = (bool)$validated['is_pwd'];
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


    /**
     * Export employees to Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'department' => $request->get('department'),
            'status' => $request->get('status'),
            'search' => $request->get('search')
        ];

        $filename = 'employees_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new EmployeesExport($filters), $filename);
    }

    /**
     * Export employees to PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = [
            'department' => $request->get('department'),
            'status' => $request->get('status'),
            'search' => $request->get('search')
        ];

        // Get filtered employees
        $query = User::query();

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['status'])) {
            $query->where('user_status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $employees = $query->with('roles')->get();
        $filterDescription = $this->getFilterDescription($filters);

        $pdf = PDF::loadView('exports.employees-pdf', [
            'employees' => $employees,
            'filters' => $filterDescription,
            'exportDate' => now()->format('F j, Y g:i A')
        ]);

        return $pdf->download('employees_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Get filter description for export
     */
    private function getFilterDescription($filters)
    {
        $descriptions = [];
        
        if (!empty($filters['department'])) {
            $descriptions[] = "Department: " . $filters['department'];
        }
        
        if (!empty($filters['status'])) {
            $descriptions[] = "Status: " . $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $descriptions[] = "Search: \"" . $filters['search'] . "\"";
        }

        return empty($descriptions) ? 'All Employees' : implode(', ', $descriptions);
    }

     private function createInitialApprovals(TravelAuthority $travel)
    {
        $approvalTypes = [
            'recommending_approval',
            'allotment_available', 
            'funds_available',
            'final_approval'
        ];

        // Define the expected roles for each approval type
        $approvalRoles = [
            'recommending_approval' => 'Department Head',
            'allotment_available' => 'Chief Administrative Officer-Finance',
            'funds_available' => 'Accountant',
            'final_approval' => 'University President'
        ];

        foreach ($approvalTypes as $type) {
            TravelAuthorityApproval::create([
                'travel_authority_id' => $travel->id,
                'approval_type' => $type,
                'approver_role' => $approvalRoles[$type],
                'status' => 'pending'
            ]);
        }
    }
}
