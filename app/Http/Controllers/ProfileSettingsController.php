<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return view('profile.settings', [
            'user' => $user,
            'activeTab' => request()->get('tab', 'preferences')
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'language' => 'in:en,es,fr',
            'timezone' => 'timezone',
        ]);

        // Store preferences in user meta or settings table
        $user = auth()->user();
        $settings = $user->settings ?? [];
        $settings['preferences'] = array_merge($settings['preferences'] ?? [], $validated);
        $user->settings = $settings;
        $user->save();

        return back()->with('success', 'Preferences updated successfully!');
    }

    public function updatePrivacy(Request $request)
    {
        $validated = $request->validate([
            'profile_visibility' => 'in:public,private,contacts_only',
            'email_visibility' => 'in:public,private,contacts_only',
            'phone_visibility' => 'in:public,private,contacts_only',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? [];
        $settings['privacy'] = array_merge($settings['privacy'] ?? [], $validated);
        $user->settings = $settings;
        $user->save();

        return back()->with('success', 'Privacy settings updated successfully!');
    }

    // Add this missing method
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? [];
        $settings['notifications'] = array_merge($settings['notifications'] ?? [], $validated);
        $user->settings = $settings;
        $user->save();

        return back()->with('success', 'Notification settings updated successfully!');
    }
}