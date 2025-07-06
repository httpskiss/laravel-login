@extends('layouts.employees')

@section('title', 'Attendance')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        /* Pulse animation for biometric scanner */
        .animate-pulse {
            animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Content Area -->
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">Employee Attendance</h1>
                <div class="flex items-center space-x-4">
                    <button class="p-2 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="relative">
                        <button class="flex items-center space-x-2">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium">Jane Doe</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4">
            <div class="max-w-7xl mx-auto">
                <!-- Dashboard Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- User Information Card -->
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

                        <div id="successMessage" class="hidden mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                            <i class="fas fa-check-circle mr-2"></i> Attendance recorded successfully!
                        </div>

                        <div id="errorMessage" class="hidden mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i> Error recording attendance!
                        </div>

                        <div class="space-y-3">
                            <button id="clockInBtn" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-medium flex items-center justify-center transition duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-sign-in-alt mr-2"></i> Clock In
                            </button>

                            <button id="clockOutBtn" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-medium flex items-center justify-center transition duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-sign-out-alt mr-2"></i> Clock Out
                            </button>
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-400"></i> Your attendance will be recorded immediately
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

                <!-- Monthly Summary -->
                <div class="mt-8 bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Your Monthly Summary</h2>
                            <p class="text-sm text-gray-500">Attendance overview for June 2023</p>
                        </div>
                        <div>
                            <select class="appearance-none bg-gray-100 border-0 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option>June 2023</option>
                                <option>May 2023</option>
                                <option>April 2023</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Present Days</p>
                            <p class="text-2xl font-bold text-blue-600">18</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Late Days</p>
                            <p class="text-2xl font-bold text-yellow-600">2</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Absent Days</p>
                            <p class="text-2xl font-bold text-red-600">0</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Leave Days</p>
                            <p class="text-2xl font-bold text-purple-600">2</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Total Working Days: 22</span>
                            <span class="text-sm font-medium text-gray-700">80% Attendance</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize current time display
            function updateCurrentTime() {
                const now = new Date();
                const hours = now.getHours();
                const minutes = now.getMinutes();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const formattedHours = hours % 12 || 12;
                const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
                document.getElementById('currentTime').textContent = 
                    `${formattedHours}:${formattedMinutes} ${ampm}`;
            }

            updateCurrentTime();
            setInterval(updateCurrentTime, 60000);

            // Biometric scanner interaction
            const scanner = document.getElementById('biometricScanner');
            if (scanner) {
                scanner.addEventListener('click', function() {
                    // Simulate scanning process
                    this.querySelector('i').classList.add('animate-pulse');
                    this.querySelector('p').textContent = 'Scanning...';

                    setTimeout(() => {
                        this.querySelector('i').classList.remove('animate-pulse');
                        this.querySelector('p').textContent = 'Scan completed!';

                        // Show success message
                        setTimeout(() => {
                            showToast('Employee identified: Jane Doe\nAttendance recorded successfully!', 'success');
                            this.querySelector('p').textContent = 'Place your finger on the scanner';
                        }, 1000);
                    }, 2000);
                });
            }

            // Clock In/Out buttons
            const clockInBtn = document.getElementById('clockInBtn');
            const clockOutBtn = document.getElementById('clockOutBtn');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');

            if (clockInBtn) {
                clockInBtn.addEventListener('click', function() {
                    // Simulate API call
                    this.innerHTML = '<span class="loading-spinner mr-2"></span> Processing...';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Clock In';
                        this.disabled = false;
                        
                        // Show success message
                        successMessage.classList.remove('hidden');
                        errorMessage.classList.add('hidden');
                        showToast('Clock In recorded successfully!', 'success');
                    }, 1500);
                });
            }

            if (clockOutBtn) {
                clockOutBtn.addEventListener('click', function() {
                    // Simulate API call
                    this.innerHTML = '<span class="loading-spinner mr-2"></span> Processing...';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Clock Out';
                        this.disabled = false;
                        
                        // Show success message
                        successMessage.classList.remove('hidden');
                        errorMessage.classList.add('hidden');
                        showToast('Clock Out recorded successfully!', 'success');
                    }, 1500);
                });
            }

            // Toast notification function
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white ${
                    type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                } z-50`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>