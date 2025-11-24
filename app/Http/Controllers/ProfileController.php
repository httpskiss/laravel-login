<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $leaveBalances = $user->leaveBalances();
        
        return view('profile.show', [
            'user' => $user,
            'leaveBalances' => $leaveBalances,
            'activeTab' => request()->get('tab', 'personal-info')
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Delete old photo if exists
            if (auth()->user()->profile_photo_path) {
                Storage::disk('public')->delete(auth()->user()->profile_photo_path);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            // Update user record
            auth()->user()->update(['profile_photo_path' => $path]);

            return back()->with('success', 'Profile photo updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile photo: '.$e->getMessage());
        }
    }

    public function deleteProfilePhoto(Request $request)
    {
        try {
            $user = auth()->user();
            
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            $user->update(['profile_photo_path' => null]);
            
            return back()->with('success', 'Profile photo removed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove profile photo: '.$e->getMessage());
        }
    }

    public function updatePersonalInfo(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:Male,Female,Other',
            'dob' => 'nullable|date',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Personal information updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.show', ['tab' => 'security'])
                ->withErrors($validator)
                ->with('error', 'Please fix the errors below.');
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile.show', ['tab' => 'security'])
                ->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.show', ['tab' => 'security'])
            ->with('success', 'Password updated successfully!');
    }

    public function updateEmploymentInfo(Request $request)
    {
        // Only allow certain roles to update employment info
        if (!auth()->user()->hasRole(['Super Admin', 'HR Manager'])) {
            return back()->with('error', 'You are not authorized to update employment information.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|string|max:255|unique:users,employee_id,' . auth()->id(),
            'department' => 'required|string|max:255',
            'user_status' => 'required|in:Active,On Leave,Suspended,Terminated',
            'hire_date' => 'nullable|date',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Employment information updated successfully!');
    }
}