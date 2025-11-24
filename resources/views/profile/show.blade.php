@extends('layouts.' . (auth()->user()->hasRole('Super Admin|HR Manager|Department Head|Finance Officer') ? 'admin' : 'employee'))

@section('title', 'My Profile')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('profile.show', ['tab' => 'personal-info']) }}" 
                       class="{{ (request()->get('tab', 'personal-info') == 'personal-info') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Personal Information
                    </a>
                    <a href="{{ route('profile.show', ['tab' => 'employment-info']) }}" 
                       class="{{ request()->get('tab') == 'employment-info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Employment Information
                    </a>
                    <a href="{{ route('profile.show', ['tab' => 'security']) }}" 
                       class="{{ request()->get('tab') == 'security' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Security
                    </a>
                </nav>
            </div>

            <!-- Personal Information Tab -->
            @if(request()->get('tab', 'personal-info') == 'personal-info')
            <div class="space-y-6">
                <!-- Profile Photo Section -->
                <div class="flex items-center space-x-6 mb-6">
                    <div class="relative">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile Photo" 
                             class="w-32 h-32 rounded-full border-4 border-blue-100 object-cover">
                        <form id="deletePhotoForm" action="{{ route('profile.photo.delete') }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-medium text-gray-900">Profile Photo</h3>
                        <div class="flex space-x-2">
                            <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
                                @csrf
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                                <label for="profile_photo" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-150">
                                    Upload New Photo
                                </label>
                            </form>
                            @if(auth()->user()->profile_photo_path)
                            <button onclick="document.getElementById('deletePhotoForm').submit()" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition duration-150">
                                Remove Photo
                            </button>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">JPG, PNG or GIF (Max: 2MB)</p>
                    </div>
                </div>

                <!-- Personal Information Form -->
                <form action="{{ route('profile.personal.update') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <select name="gender" id="gender" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender', auth()->user()->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', auth()->user()->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', auth()->user()->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date" name="dob" id="dob" value="{{ old('dob', auth()->user()->dob ? auth()->user()->dob->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('dob')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-150">
                            Update Personal Information
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Employment Information Tab -->
            @if(request()->get('tab') == 'employment-info')
            <div class="space-y-6">
                @if(auth()->user()->hasRole('Super Admin|HR Manager'))
                <form action="{{ route('profile.employment.update') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', auth()->user()->employee_id) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('employee_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                            <input type="text" name="department" id="department" value="{{ old('department', auth()->user()->department) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user_status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="user_status" id="user_status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="Active" {{ old('user_status', auth()->user()->user_status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="On Leave" {{ old('user_status', auth()->user()->user_status) == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                                <option value="Suspended" {{ old('user_status', auth()->user()->user_status) == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="Terminated" {{ old('user_status', auth()->user()->user_status) == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                            @error('user_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', auth()->user()->hire_date ? auth()->user()->hire_date->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('hire_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-150">
                            Update Employment Information
                        </button>
                    </div>
                </form>
                @else
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->employee_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->department }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->user_status }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hire Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->hire_date ? auth()->user()->hire_date->format('M d, Y') : 'Not set' }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Contact HR to update employment information.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Security Tab -->
            @if(request()->get('tab') == 'security')
            <div class="space-y-6">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 max-w-md">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-150">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit photo when selected
    document.getElementById('profile_photo')?.addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush