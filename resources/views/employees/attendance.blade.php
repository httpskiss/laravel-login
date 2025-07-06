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
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Your Information</p>
                                <h3 class="text-xl font-bold text-gray-800">Jane Doe</h3>
                                <p class="text-sm text-gray-600 mt-1">Marketing Department</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-user text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500">Employee ID: <span class="font-medium">EMP-1024</span></p>
                        </div>
                    </div>

                    <!-- Today's Date Card -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Today's Date</p>
                                <h3 class="text-2xl font-bold text-gray-800" id="currentDate">June 15, 2023</h3>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500">Day: <span class="font-medium">Thursday</span></p>
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
                                <h3 class="text-xl font-bold text-green-600">Present</h3>
                                <p class="text-sm text-gray-600 mt-1">Clocked in at 8:45 AM</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500">Hours worked: <span class="font-medium">6h 15m</span></p>
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
        
        <!-- Recent Attendance Records -->
        <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Your Recent Attendance</h2>
                    <button class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-download mr-1"></i> Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check In
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check Out
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hours Worked
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 custom-scrollbar" style="max-height: 400px; overflow-y: auto;">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    June 15, 2023
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    8:45 AM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    5:00 PM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Present
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    8h 15m
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    June 14, 2023
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    9:15 AM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    5:30 PM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    8h 15m
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    June 13, 2023
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    8:50 AM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    5:10 PM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Present
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    8h 20m
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    June 12, 2023
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    --
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    --
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        On Leave
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    0h 0m
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    June 11, 2023
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    8:55 AM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    5:05 PM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Present
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    8h 10m
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Attendance Calendar</h2>
                    <p class="text-sm text-gray-500">Your attendance overview</p>
                </div>
            </div>
            
            @include('components.calendar')
        </div>
        
    </main>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    </script>
    @endpush
@endsection