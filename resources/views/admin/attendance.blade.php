@extends('layouts.admin')

@section('title', 'Attendance')

@section('content')
    <style>
        /* Custom styles for biometric scanner animation */
        .biometric-scanner {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
        }
        .biometric-scanner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(transparent, rgba(59, 130, 246, 0.5), transparent);
            animation: scan 2s linear infinite;
        }
        @keyframes scan {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 3px;
        }

        .loading-spinner {
            display: inline-block;
            width: 1em;
            height: 1em;
            border: 2px solid rgba(0,0,0,.1);
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-4 bg-gray-50" x-data="attendanceModule()">
        <!-- Top Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- User Information Card -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Your Information</p>
                        <h3 class="text-xl font-bold text-gray-800">{{ auth()->user()->full_name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ auth()->user()->department }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">Employee ID: <span class="font-medium">{{ auth()->user()->employee_id }}</span></p>
                </div>
            </div>

            <!-- Today's Date Card -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Today's Date</p>
                        <h3 class="text-2xl font-bold text-gray-800" id="currentDate">{{ now()->format('F j, Y') }}</h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">Day: <span class="font-medium">{{ now()->format('l') }}</span></p>
                </div>
            </div>

            <!-- Current Time Card -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Current Time</p>
                        <h3 class="text-2xl font-bold text-gray-800" id="currentTime">Loading...</h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-clock text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">Shift: <span class="font-medium">9:00 AM - 5:00 PM</span></p>
                </div>
            </div>
            
            <!-- Attendance Status Card -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Your Status</p>
                        @if($todayRecord)
                            <h3 class="text-xl font-bold {{ $todayRecord->status === 'Present' ? 'text-green-600' : ($todayRecord->status === 'Late' ? 'text-yellow-600' : ($todayRecord->status === 'Absent' ? 'text-red-600' : 'text-purple-600')) }}">
                                {{ str_replace('_', ' ', $todayRecord->status) }}
                            </h3>
                            @if($todayRecord->check_in)
                                <p class="text-sm text-gray-600 mt-1">Clocked in at {{ \Carbon\Carbon::parse($todayRecord->check_in)->format('h:i A') }}</p>
                            @endif
                        @else
                            <h3 class="text-xl font-bold text-gray-600">Not Checked In</h3>
                            <p class="text-sm text-gray-600 mt-1">--:--</p>
                        @endif
                    </div>
                    <div class="{{ $todayRecord && $todayRecord->status === 'Present' ? 'bg-green-100' : ($todayRecord && $todayRecord->status === 'Late' ? 'bg-yellow-100' : ($todayRecord && $todayRecord->status === 'Absent' ? 'bg-red-100' : ($todayRecord && $todayRecord->status === 'On_Leave' ? 'bg-purple-100' : 'bg-gray-100'))) }} p-3 rounded-full">
                        <i class="fas {{ $todayRecord && $todayRecord->status === 'Present' ? 'fa-check-circle text-green-600' : ($todayRecord && $todayRecord->status === 'Late' ? 'fa-clock text-yellow-600' : ($todayRecord && $todayRecord->status === 'Absent' ? 'fa-times-circle text-red-600' : ($todayRecord && $todayRecord->status === 'On_Leave' ? 'fa-umbrella-beach text-purple-600' : 'fa-question-circle text-gray-600'))) }} text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">Hours worked: 
                        <span class="font-medium">
                            @if($todayRecord && $todayRecord->check_in && $todayRecord->check_out)
                                {{ $todayRecord->hours_worked }}
                            @elseif($todayRecord && $todayRecord->check_in)
                                Calculating...
                            @else
                                0h 0m
                            @endif
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Biometric and Manual Attendance Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Biometric Scanner -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Biometric Attendance</h2>
                    <div class="text-blue-500">
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                
                <div class="biometric-scanner h-48 flex flex-col items-center justify-center mb-4 cursor-pointer" id="biometricScanner">
                    <div class="text-center p-4">
                        <i class="fas fa-fingerprint text-5xl text-blue-500 mb-2"></i>
                        <p class="text-gray-600 font-medium">Place your finger on the scanner</p>
                        <p class="text-xs text-gray-400 mt-1">Ensure your finger is clean and dry</p>
                    </div>
                </div>
                
                <div class="flex justify-between space-x-3">
                    <button class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-3 rounded-lg font-medium flex items-center justify-center transition duration-200">
                        <i class="fas fa-sync-alt mr-2"></i> Retry
                    </button>
                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium flex items-center justify-center transition duration-200">
                        <i class="fas fa-check-circle mr-2"></i> Verify
                    </button>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-blue-500"></i> Your biometric data is securely encrypted
                    </p>
                </div>
            </div>
            
            <!-- Manual Attendance section -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Manual Attendance</h2>
                    <div class="text-blue-500 text-xl">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
                
                <div class="flex flex-col items-center justify-center mb-6 p-6 bg-blue-50 rounded-lg border border-blue-100">
                    <i class="fas fa-clock text-5xl text-blue-500 mb-3"></i>
                    <p class="text-gray-700 font-medium text-center">Manual Time Recording</p>
                    <p class="text-sm text-gray-500 mt-1 text-center">Click below to record your attendance</p>
                </div>
                
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-3">
                    <form action="{{ route('attendance.clockIn') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-medium flex items-center justify-center transition duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-sign-in-alt mr-2"></i> Clock In
                        </button>
                    </form>

                    <form action="{{ route('attendance.clockOut') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-medium flex items-center justify-center transition duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-sign-out-alt mr-2"></i> Clock Out
                        </button>
                    </form>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-400"></i> 
                        Your attendance will be recorded with your device information and approximate location for security purposes.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Today's Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                    <p class="text-sm text-gray-500">Present</p>
                    <p class="text-2xl font-bold text-blue-600">42</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                    <p class="text-sm text-gray-500">Late</p>
                    <p class="text-2xl font-bold text-yellow-600">8</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                    <p class="text-sm text-gray-500">Absent</p>
                    <p class="text-2xl font-bold text-red-600">5</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                    <p class="text-sm text-gray-500">On Leave</p>
                    <p class="text-2xl font-bold text-green-600">3</p>
                </div>
            </div>
        </div>

        <!-- Attendance Chart Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Attendance Overview</h2>
                    <p class="text-sm text-gray-500">Monthly attendance comparison</p>
                </div>
                <div class="mt-2 md:mt-0">
                    <select id="chart-period-selector" class="appearance-none bg-gray-100 border-0 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="2" selected>Last 2 Months</option>
                        <option value="4">Last 4 Months</option>
                        <option value="6">Last 6 Months</option>
                        <option value="8">Last 8 Months</option>
                        <option value="12">Last 12 Months</option>
                    </select>
                </div>
            </div>
            
            <div class="chart-container" style="height: 400px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Attendance Records Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Filters and Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <form id="attendance-filter-form" method="GET" action="{{ route('admin.attendance') }}">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="date" name="date" value="{{ request('date') }}" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="relative">
                                <select name="department" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>{{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative">
                                <select name="status" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>Present</option>
                                    <option value="Late" {{ request('status') == 'Late' ? 'selected' : '' }}>Late</option>
                                    <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="On_Leave" {{ request('status') == 'On_Leave' ? 'selected' : '' }}>On Leave</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                            <a href="{{ route('admin.attendance') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-sync-alt mr-2"></i> Reset
                            </a>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('attendance.export') }}?{{ http_build_query(request()->query()) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </a>
                            <button type="button" onclick="openManualEntryModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-plus mr-2"></i> Add Manual
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Attendance Records Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable('employee')">
                                    Employee <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable('department')">
                                    Department <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable('check_in')">
                                    Check In <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable('check_out')">
                                    Check Out <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable('status')">
                                    Status <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                         <tbody class="bg-white divide-y divide-gray-200 custom-scrollbar" style="max-height: 400px; overflow-y: auto;">
                            @foreach($attendanceRecords as $record)
                            <tr x-data="{ record: {{ json_encode($record) }} }">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" 
                                                 :src="record.user.profile_photo_url || '{{ asset('images/default-avatar.png') }}'" 
                                                 :alt="record.user.full_name"
                                                 onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="record.user.full_name"></div>
                                            <div class="text-sm text-gray-500" x-text="record.user.email"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900" x-text="record.user.department"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span x-text="record.check_in ? formatTime(record.check_in) : '-'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span x-text="record.check_out ? formatTime(record.check_out) : '-'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template x-if="record.status == 'Present'">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                    </template>
                                    <template x-if="record.status == 'Late'">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Late</span>
                                    </template>
                                    <template x-if="record.status == 'Absent'">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                    </template>
                                    <template x-if="record.status == 'On_Leave'">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">On Leave</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900" x-text="record.location || 'N/A'"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center space-x-3">
                                        <button 
                                            @click="openEditModal(record)"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                            title="Edit"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            @click="confirmDelete(record.id)"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                {{ $attendanceRecords->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Edit Attendance Modal -->
        <div x-show="isEditModalOpen" 
            @keydown.escape.window="closeEditModal()"
            @click.away="closeEditModal()"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            style="display: none;"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-xl rounded-xl bg-white"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Attendance Record</h3>
                    <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Validation Errors Container -->
                <div x-show="validationErrors || Object.keys(fieldErrors).length > 0" 
                    class="mt-4 mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm text-left">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                        <div>
                            <span x-text="validationErrors" x-show="validationErrors"></span>
                            <template x-if="Object.keys(fieldErrors).length > 0">
                                <div class="mt-1">
                                    <template x-for="(errors, field) in fieldErrors" :key="field">
                                        <div class="flex items-baseline">
                                            <span class="font-medium capitalize" x-text="field.replace('_', ' ')"></span>:
                                            <span class="ml-1" x-text="errors[0]"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <!-- Date Field -->
                    <div class="mb-4">
                        <label for="edit_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input x-model="currentRecord.date" type="date" id="edit_date" 
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                            :class="{'border-red-300': fieldErrors.date}">
                        <p x-show="fieldErrors.date" class="mt-1 text-sm text-red-600" x-text="fieldErrors.date[0]"></p>
                    </div>
                    
                    <!-- Check In Field -->
                    <div class="mb-4">
                        <label for="edit_check_in" class="block text-sm font-medium text-gray-700 mb-1">Check In</label>
                        <input x-model="currentRecord.check_in" type="time" id="edit_check_in" 
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                            :class="{'border-red-300': fieldErrors.check_in}">
                        <p x-show="fieldErrors.check_in" class="mt-1 text-sm text-red-600" x-text="fieldErrors.check_in[0]"></p>
                    </div>
                    
                    <!-- Check Out Field -->
                    <div class="mb-4">
                        <label for="edit_check_out" class="block text-sm font-medium text-gray-700 mb-1">Check Out</label>
                        <input x-model="currentRecord.check_out" type="time" id="edit_check_out" 
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                            :class="{'border-red-300': fieldErrors.check_out}">
                        <p x-show="fieldErrors.check_out" class="mt-1 text-sm text-red-600" x-text="fieldErrors.check_out[0]"></p>
                    </div>
                    
                    <!-- Status Field -->
                    <div class="mb-4">
                        <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="currentRecord.status" id="edit_status" 
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                            :class="{'border-red-300': fieldErrors.status}">
                            <option value="Present">Present</option>
                            <option value="Late">Late</option>
                            <option value="Absent">Absent</option>
                            <option value="On_Leave">On Leave</option>
                        </select>
                        <p x-show="fieldErrors.status" class="mt-1 text-sm text-red-600" x-text="fieldErrors.status[0]"></p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                    <button @click="closeEditModal()"
                            :disabled="isSaving"
                            class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancel
                    </button>
                    <button @click="saveRecord()"
                            :disabled="isSaving"
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSaving">Save Changes</span>
                        <span x-show="isSaving" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="isDeleteModalOpen" 
            @keydown.escape.window="closeDeleteModal()"
            @click.away="closeDeleteModal()"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            style="display: none;"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-xl rounded-xl bg-white" 
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Confirm Deletion</h3>
                    <button @click="closeDeleteModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 text-center">Are you sure you want to delete this attendance record? This action cannot be undone.</p>
                </div>
                <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                    <button 
                        @click="closeDeleteModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button 
                        @click="deleteRecord()"
                        :disabled="isDeleting"
                        :class="{'opacity-50 cursor-not-allowed': isDeleting}"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        <span x-show="!isDeleting">Delete Record</span>
                        <span x-show="isDeleting" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>      
    </main>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize real-time clock
            function updateClock() {
                const now = new Date();
                document.getElementById('currentTime').textContent = 
                    now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Initialize attendance chart
            let attendanceChart = null;
            
            function initializeChart() {
                const ctx = document.getElementById('attendanceChart').getContext('2d');
                
                if (attendanceChart) {
                    attendanceChart.destroy();
                }
                
                // Get initial data (2 months by default)
                fetchAttendanceData(2);
            }
            
            function fetchAttendanceData(months) {
                fetch(`/admin/attendance/monthly-comparison?months=${months}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            renderChart(data.data);
                        }
                    });
            }
            
            function renderChart(chartData) {
                const ctx = document.getElementById('attendanceChart').getContext('2d');
                
                attendanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Present',
                                data: chartData.current.present,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Late',
                                data: chartData.current.late,
                                backgroundColor: 'rgba(234, 179, 8, 0.7)',
                                borderColor: 'rgba(234, 179, 8, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Absent',
                                data: chartData.current.absent,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'On Leave',
                                data: chartData.current.on_leave,
                                backgroundColor: 'rgba(168, 85, 247, 0.7)',
                                borderColor: 'rgba(168, 85, 247, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: false,
                            },
                            y: {
                                stacked: false,
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    }
                });
            }
            
            // Initialize chart on page load
            initializeChart();
            
            // Handle period selector change
            document.getElementById('chart-period-selector').addEventListener('change', function() {
                fetchAttendanceData(this.value);
            });
        });

        function attendanceModule() {
            return {
                // Data properties
                isEditModalOpen: false,
                isDeleteModalOpen: false,
                isSaving: false,
                isDeleting: false,
                validationErrors: null,
                fieldErrors: {},
                recordToDelete: null,
                currentRecord: {
                    id: '',
                    date: '',
                    check_in: '',
                    check_out: '',
                    status: 'Present'
                },
                
                // Methods
                openEditModal(record) {
                    this.validationErrors = null;
                    this.fieldErrors = {};
                    this.currentRecord = {
                        id: record.id,
                        date: record.date,
                        check_in: record.check_in ? this.formatTimeForInput(record.check_in) : '',
                        check_out: record.check_out ? this.formatTimeForInput(record.check_out) : '',
                        status: record.status
                    };
                    this.isEditModalOpen = true;
                },
                
                formatTimeForInput(timeString) {
                    // Convert "HH:MM:SS" to "HH:MM" for time inputs
                    return timeString.substring(0, 5);
                },
                
                closeEditModal() {
                    if (!this.isSaving) {
                        this.isEditModalOpen = false;
                        this.validationErrors = null;
                        this.fieldErrors = {};
                    }
                },
                
                confirmDelete(id) {
                    this.recordToDelete = id;
                    this.isDeleteModalOpen = true;
                },
                
                closeDeleteModal() {
                    this.isDeleteModalOpen = false;
                    this.recordToDelete = null;
                },

                async saveRecord() {
                    this.isSaving = true;
                    this.validationErrors = null;
                    this.fieldErrors = {};

                    try {
                        const response = await fetch(`/admin/attendance/${this.currentRecord.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                date: this.currentRecord.date,
                                check_in: this.currentRecord.check_in,
                                check_out: this.currentRecord.check_out,
                                status: this.currentRecord.status   
                            })
                        });
                    
                        const data = await response.json();
                        
                        if (!response.ok) {
                            if (response.status === 422) {
                                // Validation errors
                                this.fieldErrors = data.errors || {};
                                this.validationErrors = 'Please fix the validation errors below.';
                                return;
                            }
                            throw new Error(data.message || 'Failed to update record');
                        }
                        
                        // Success handling
                        this.showToast('Record updated successfully', 'success');
                        this.closeEditModal();
                        
                        // Force a full page reload with cache busting
                        window.location.href = window.location.href.split('?')[0] + '?refresh=' + new Date().getTime();
                        
                    } catch (error) {
                        this.validationErrors = error.message;
                        console.error('Error:', error);
                    } finally {
                        this.isSaving = false;
                    }
                },

        
                async deleteRecord() {
                    this.isDeleting = true;
                    try {
                        const response = await fetch(`/admin/attendance/${this.recordToDelete}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Failed to delete record');
                        }
                        
                        this.showToast('Record deleted successfully', 'success');
                        this.closeDeleteModal();
                        
                        // Force a full page reload with cache busting
                        window.location.href = window.location.href.split('?')[0] + '?refresh=' + new Date().getTime();
                        
                    } catch (error) {
                        this.showToast(error.message, 'error');
                        console.error('Error:', error);
                    } finally {
                        this.isDeleting = false;
                    }
                },
               
                showToast(message, type = 'success') {
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white flex items-center ${
                        type === 'success' ? 'bg-green-500' : 'bg-red-500'
                    } z-50 transition-all duration-300 ease-in-out`;
                    
                    const icon = document.createElement('i');
                    icon.className = type === 'success' ? 'fas fa-check-circle mr-2' : 'fas fa-exclamation-circle mr-2';
                    toast.appendChild(icon);
                    
                    const text = document.createElement('span');
                    text.textContent = message;
                    toast.appendChild(text);
                    
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        toast.classList.add('opacity-0', 'translate-y-2');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                },
                
                formatTime(timeString) {
                    if (!timeString) return '-';
                    const time = new Date(`2000-01-01T${timeString}`);
                    return time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
    @endpush
@endsection