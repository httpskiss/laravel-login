@extends('layouts.' . (auth()->user()->hasRole('Super Admin|HR Manager|Department Head|Finance Officer') ? 'admin' : 'employee'))

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Profile Settings</h2>
            
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('profile.settings', ['tab' => 'preferences']) }}" 
                       class="{{ (request()->get('tab', 'preferences') == 'preferences') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Preferences
                    </a>
                    <a href="{{ route('profile.settings', ['tab' => 'privacy']) }}" 
                       class="{{ request()->get('tab') == 'privacy' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Privacy
                    </a>
                    <a href="{{ route('profile.settings', ['tab' => 'notifications']) }}" 
                       class="{{ request()->get('tab') == 'notifications' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Notifications
                    </a>
                </nav>
            </div>

            <!-- Preferences Tab -->
            @if(request()->get('tab', 'preferences') == 'preferences')
            <form action="{{ route('profile.settings.preferences') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                        <select name="language" id="language" class="mt-1 block w-full max-w-xs border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="en" {{ (auth()->user()->settings['preferences']['language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ (auth()->user()->settings['preferences']['language'] ?? '') == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ (auth()->user()->settings['preferences']['language'] ?? '') == 'fr' ? 'selected' : '' }}>French</option>
                        </select>
                    </div>

                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                        <select name="timezone" id="timezone" class="mt-1 block w-full max-w-xs border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @foreach(timezone_identifiers_list() as $timezone)
                            <option value="{{ $timezone }}" {{ (auth()->user()->settings['preferences']['timezone'] ?? 'UTC') == $timezone ? 'selected' : '' }}>
                                {{ $timezone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" id="email_notifications" 
                               value="1" {{ (auth()->user()->settings['preferences']['email_notifications'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                            Enable email notifications
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="push_notifications" id="push_notifications" 
                               value="1" {{ (auth()->user()->settings['preferences']['push_notifications'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="push_notifications" class="ml-2 block text-sm text-gray-900">
                            Enable push notifications
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-150">
                            Save Preferences
                        </button>
                    </div>
                </div>
            </form>
            @endif

            <!-- Privacy Tab -->
            @if(request()->get('tab') == 'privacy')
            <form action="{{ route('profile.settings.privacy') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="profile_visibility" class="block text-sm font-medium text-gray-700">Profile Visibility</label>
                        <select name="profile_visibility" id="profile_visibility" class="mt-1 block w-full max-w-xs border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="public" {{ (auth()->user()->settings['privacy']['profile_visibility'] ?? 'public') == 'public' ? 'selected' : '' }}>Public</option>
                            <option value="private" {{ (auth()->user()->settings['privacy']['profile_visibility'] ?? '') == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="contacts_only" {{ (auth()->user()->settings['privacy']['profile_visibility'] ?? '') == 'contacts_only' ? 'selected' : '' }}>Contacts Only</option>
                        </select>
                    </div>

                    <div>
                        <label for="email_visibility" class="block text-sm font-medium text-gray-700">Email Visibility</label>
                        <select name="email_visibility" id="email_visibility" class="mt-1 block w-full max-w-xs border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="public" {{ (auth()->user()->settings['privacy']['email_visibility'] ?? 'public') == 'public' ? 'selected' : '' }}>Public</option>
                            <option value="private" {{ (auth()->user()->settings['privacy']['email_visibility'] ?? '') == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="contacts_only" {{ (auth()->user()->settings['privacy']['email_visibility'] ?? '') == 'contacts_only' ? 'selected' : '' }}>Contacts Only</option>
                        </select>
                    </div>

                    <div>
                        <label for="phone_visibility" class="block text-sm font-medium text-gray-700">Phone Visibility</label>
                        <select name="phone_visibility" id="phone_visibility" class="mt-1 block w-full max-w-xs border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="public" {{ (auth()->user()->settings['privacy']['phone_visibility'] ?? 'private') == 'public' ? 'selected' : '' }}>Public</option>
                            <option value="private" {{ (auth()->user()->settings['privacy']['phone_visibility'] ?? 'private') == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="contacts_only" {{ (auth()->user()->settings['privacy']['phone_visibility'] ?? '') == 'contacts_only' ? 'selected' : '' }}>Contacts Only</option>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-150">
                            Save Privacy Settings
                        </button>
                    </div>
                </div>
            </form>
            @endif

            <!-- Notifications Tab -->
            @if(request()->get('tab') == 'notifications')
            <form action="{{ route('profile.settings.notifications') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Notification Preferences</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="email_notifications" id="email_notifications" 
                                   value="1" {{ (auth()->user()->settings['notifications']['email_notifications'] ?? true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                                Email Notifications
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="push_notifications" id="push_notifications" 
                                   value="1" {{ (auth()->user()->settings['notifications']['push_notifications'] ?? true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="push_notifications" class="ml-2 block text-sm text-gray-900">
                                Push Notifications
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="sms_notifications" id="sms_notifications" 
                                   value="1" {{ (auth()->user()->settings['notifications']['sms_notifications'] ?? false) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="sms_notifications" class="ml-2 block text-sm text-gray-900">
                                SMS Notifications
                            </label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-150">
                            Save Notification Settings
                        </button>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection