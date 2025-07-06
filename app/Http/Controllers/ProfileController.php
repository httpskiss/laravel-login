<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', [
            'user' => $user,
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

    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:Male,Female,Other',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }
}