<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileSettingsController extends Controller
{
    public function index()
    {
        return view('profile.settings', [
            'user' => auth()->user()
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image', // 2MB max
        ]);

        // Delete old photo if exists
        if (auth()->user()->profile_photo_path) {
            Storage::disk('public')->delete(auth()->user()->profile_photo_path);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        // Update user record
        auth()->user()->update([
            'profile_photo_path' => $path
        ]);

        return back()->with('success', 'Profile photo updated successfully!');
    }
}