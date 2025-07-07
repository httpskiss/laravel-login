@extends('layouts.employees')

@section('title', 'Attendance Management')

@section('content')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    
    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .glow-on-hover:hover {
        filter: drop-shadow(0 0 8px rgba(99, 102, 241, 0.5));
    }
    
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    
    .status-present {
        background-color: #10B981;
    }
    
    .status-late {
        background-color: #F59E0B;
    }
    
    .status-absent {
        background-color: #EF4444;
    }
    
    .has-attendance::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10B981;
    }
    
    .today-highlight {
        background-color: #EFF6FF;
        border: 1px solid #3B82F6;
        border-radius: 50%;
    }
</style>

<main class="flex-1 overflow-y-auto p-4 bg-gray-50">
    <!-- Attendance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 slide-in">
        <!-- Today's Status Card -->
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Today's Status</h3>
                    <p class="text-2xl font-semibold text-blue-600">
                        @isset($todayAttendance)
                            @if($todayAttendance->status)
                                {{ ucfirst(str_replace('_', ' ', $todayAttendance->status)) }}
                            @else
                                Not Checked In
                            @endif
                        @else
                            Not Checked In
                        @endisset
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Hours Card -->
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Hours</h3>
                    <p class="text-2xl font-semibold text-green-600">
                        @if($todayAttendance->exists && $todayAttendance->total_hours)
                            {{ $todayAttendance->total_hours }} hrs
                        @elseif($todayAttendance->exists && $todayAttendance->time_in)
                            In Progress
                        @else
                            0 hrs
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Month Attendance Card -->
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-calendar-week text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Month Attendance</h3>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $presentCount }}/{{ $totalWorkingDays }} days</p>
                </div>
            </div>
        </div>
        
        <!-- Late Arrivals Card -->
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Late Arrivals</h3>
                    <p class="text-2xl font-semibold text-red-600">{{ $lateCount }} times</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Check In/Out Section -->
        <div class="col-span-1 bg-white rounded-lg shadow overflow-hidden slide-in">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Clock In/Out</h3>
                <p class="mt-1 text-sm text-gray-500">Record your daily attendance</p>
            </div>
            <div class="p-6">
                <div class="flex flex-col items-center">
                    <div class="text-center mb-6">
                        <div class="text-2xl font-semibold text-gray-800" id="currentTime"></div>
                        <div class="text-gray-500 mt-1" id="currentDate"></div>
                    </div>
                    
                    <div class="w-full flex justify-around mb-4">
                        <button id="checkInBtn" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 pulse-animation">
                            <i class="fas fa-sign-in-alt mr-2"></i> Check In
                        </button>
                        <button id="checkOutBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50" disabled>
                            <i class="fas fa-sign-out-alt mr-2"></i> Check Out
                        </button>
                    </div>
                    
                    <div id="attendanceStatus" class="w-full p-4 rounded-lg hidden">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span id="statusMessage"></span>
                        </div>
                    </div>
                    
                    <div class="w-full mt-4">
                        <button id="biometricBtn" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-fingerprint mr-2"></i> Biometric Verification
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar Section -->
        <div class="col-span-1 bg-white rounded-lg shadow overflow-hidden slide-in">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Attendance Calendar</h3>
                <p class="mt-1 text-sm text-gray-500">Your attendance history</p>
            </div>
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <button class="p-2 rounded-full hover:bg-gray-100 prev-month">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h4 class="font-semibold current-month">{{ now()->format('F Y') }}</h4>
                    <button class="p-2 rounded-full hover:bg-gray-100 next-month">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-xs text-center mb-2">
                    <div class="font-medium text-gray-500 py-1">Sun</div>
                    <div class="font-medium text-gray-500 py-1">Mon</div>
                    <div class="font-medium text-gray-500 py-1">Tue</div>
                    <div class="font-medium text-gray-500 py-1">Wed</div>
                    <div class="font-medium text-gray-500 py-1">Thu</div>
                    <div class="font-medium text-gray-500 py-1">Fri</div>
                    <div class="font-medium text-gray-500 py-1">Sat</div>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-sm calendar-days">
                    <!-- Days will be populated by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Today's Schedule -->
        <div class="col-span-1 bg-white rounded-lg shadow overflow-hidden slide-in">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Today's Schedule</h3>
                <p class="mt-1 text-sm text-gray-500">{{ now()->format('F d, Y') }}</p>
            </div>
            <div class="p-4">
                <div class="timeline">
                    <!-- Schedule items would be populated dynamically -->
                    <div class="timeline-item">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex justify-between">
                                <span class="font-medium">Morning Shift</span>
                                <span class="text-sm text-gray-600">08:00 - 12:00</span>
                            </div>
                            <div class="flex items-center mt-2 text-sm">
                                <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                                <span>Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Records -->
    <div class="bg-white rounded-lg shadow overflow-hidden slide-in">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Recent Attendance</h3>
                <p class="mt-1 text-sm text-gray-500">Your last 10 attendance records</p>
            </div>
            <div>
                <select id="filterRecords" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Records</option>
                    <option value="month">This Month</option>
                    <option value="week">This Week</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentAttendance as $record)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->date->format('l') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->time_in ? Carbon\Carbon::parse($record->time_in)->format('h:i A') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->time_out ? Carbon\Carbon::parse($record->time_out)->format('h:i A') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->total_hours ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($record->status === 'present') bg-green-100 text-green-800
                                @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                @elseif($record->status === 'absent') bg-red-100 text-red-800
                                @elseif($record->status === 'on_leave') bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900">Details</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 text-right">
            <a href="{{ route('employee.attendance.all') }}" class="text-blue-600 hover:text-blue-900 font-medium">View All Records</a>
        </div>
    </div>
    
    <!-- Regularization Request -->
    <div class="mt-6 bg-white rounded-lg shadow overflow-hidden slide-in">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Attendance Regularization</h3>
            <p class="mt-1 text-sm text-gray-500">Request regularization for missing attendance</p>
        </div>
        <div class="p-6">
            <form id="regularizationForm" action="{{ route('employee.attendance.regularization') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="regularizationDate" class="block text-sm font-medium text-gray-700 mb-1">Missing Date</label>
                    <input type="date" name="date" id="regularizationDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="regularizationReason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <select name="reason" id="regularizationReason" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select a reason</option>
                        <option value="forgot">Forgot to check in/out</option>
                        <option value="system_error">System error</option>
                        <option value="official_travel">Official travel</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="regularizationDetails" class="block text-sm font-medium text-gray-700 mb-1">Details</label>
                    <textarea name="details" id="regularizationDetails" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Provide additional details" required></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="regularizationProof" class="block text-sm font-medium text-gray-700 mb-1">Proof/Attachment</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="file-upload" name="proof" type="file" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- Biometric Modal -->
<div id="biometricModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Biometric Verification</h3>
            <button id="closeBiometricModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="qr-scanner biometric-placeholder mb-4">
            <div class="qr-overlay">
                <span>Position your face in the frame</span>
            </div>
            <div class="bg-gray-200 w-full h-64 flex items-center justify-center">
                <i class="fas fa-camera text-4xl text-gray-400"></i>
            </div>
        </div>
        <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Verify Identity
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update current time and date
        function updateDateTime() {
            const now = new Date();
            const timeElement = document.getElementById('currentTime');
            const dateElement = document.getElementById('currentDate');
            
            // Format time (HH:MM AM/PM)
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            
            timeElement.textContent = `${hours}:${minutes} ${ampm}`;
            
            // Format date (Day, Month DD, YYYY)
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            
            const dayName = days[now.getDay()];
            const monthName = months[now.getMonth()];
            const date = now.getDate();
            const year = now.getFullYear();
            
            dateElement.textContent = `${dayName}, ${monthName} ${date}, ${year}`;
        }
        
        // Initial call
        updateDateTime();
        
        // Update every minute
        setInterval(updateDateTime, 60000);
        
        // Set initial button states based on today's attendance
        @if($todayAttendance->exists)
            @if($todayAttendance->time_in && !$todayAttendance->time_out)
                document.getElementById('checkInBtn').disabled = true;
                document.getElementById('checkInBtn').classList.remove('pulse-animation');
                document.getElementById('checkOutBtn').disabled = false;
            @elseif($todayAttendance->time_in && $todayAttendance->time_out)
                document.getElementById('checkInBtn').disabled = true;
                document.getElementById('checkInBtn').classList.remove('pulse-animation');
                document.getElementById('checkOutBtn').disabled = true;
            @endif
        @endif
        
        // Attendance check-in/out functionality
        const checkInBtn = document.getElementById('checkInBtn');
        const checkOutBtn = document.getElementById('checkOutBtn');
        const attendanceStatus = document.getElementById('attendanceStatus');
        const statusMessage = document.getElementById('statusMessage');
        
        checkInBtn.addEventListener('click', function() {
            fetch('{{ route("employee.attendance.check") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'in' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkInBtn.disabled = true;
                    checkInBtn.classList.remove('pulse-animation');
                    checkOutBtn.disabled = false;
                    
                    attendanceStatus.classList.remove('hidden', 'bg-red-100', 'border-red-200');
                    attendanceStatus.classList.add('bg-green-100', 'border-green-200');
                    statusMessage.textContent = data.message;
                    
                    showToast(data.message, 'success');
                    
                    // Reload to update all data
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred', 'error');
                console.error('Error:', error);
            });
        });
        
        checkOutBtn.addEventListener('click', function() {
            fetch('{{ route("employee.attendance.check") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'out' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkOutBtn.disabled = true;
                    
                    attendanceStatus.classList.remove('hidden', 'bg-green-100', 'border-green-200');
                    attendanceStatus.classList.add('bg-blue-100', 'border-blue-200');
                    statusMessage.textContent = data.message;
                    
                    showToast(data.message, 'success');
                    
                    // Reload to update all data
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred', 'error');
                console.error('Error:', error);
            });
        });
        
        // Biometric modal handling
        const biometricBtn = document.getElementById('biometricBtn');
        const biometricModal = document.getElementById('biometricModal');
        const closeBiometricModal = document.getElementById('closeBiometricModal');
        
        if (biometricBtn) {
            biometricBtn.addEventListener('click', function() {
                biometricModal.classList.remove('hidden');
            });
        }
        
        if (closeBiometricModal) {
            closeBiometricModal.addEventListener('click', function() {
                biometricModal.classList.add('hidden');
            });
        }
        
        // Calendar functionality
        function renderCalendar(month, year) {
            const daysContainer = document.querySelector('.calendar-days');
            daysContainer.innerHTML = '';
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDay = firstDay.getDay();
            
            // Previous month days
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = 0; i < startingDay; i++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'py-2 text-gray-400 text-center';
                dayElement.textContent = prevMonthLastDay - startingDay + i + 1;
                daysContainer.appendChild(dayElement);
            }
            
            // Current month days
            for (let i = 1; i <= daysInMonth; i++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'py-2 text-center relative';
                dayElement.textContent = i;
                
                const currentDate = new Date(year, month, i);
                const dateString = currentDate.toISOString().split('T')[0];
                
                // Check if this date has attendance data
                @foreach($monthAttendance as $attendance)
                    @if($attendance->date)
                        if (dateString === '{{ $attendance->date->toDateString() }}') {
                            dayElement.classList.add('has-attendance');
                            
                            const statusDot = document.createElement('span');
                            statusDot.className = 'status-indicator absolute bottom-1 left-1/2 transform -translate-x-1/2 ' + 
                                (@if($attendance->status === 'present') 'status-present'
                                @elseif($attendance->status === 'late') 'status-late'
                                @elseif($attendance->status === 'absent') 'status-absent'
                                @elseif($attendance->status === 'on_leave') 'bg-blue-500' @endif);
                            
                            dayElement.appendChild(statusDot);
                        }
                    @endif
                @endforeach
                
                // Highlight today
                const today = new Date();
                if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayElement.classList.add('today-highlight');
                }
                
                daysContainer.appendChild(dayElement);
            }
            
            // Next month days
            const daysLeft = 42 - (startingDay + daysInMonth); // 6 rows x 7 days
            for (let i = 1; i <= daysLeft; i++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'py-2 text-gray-400 text-center';
                dayElement.textContent = i;
                daysContainer.appendChild(dayElement);
            }
            
            // Update month/year display
            document.querySelector('.current-month').textContent = 
                new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });
        }
        
        // Initial render
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        renderCalendar(currentMonth, currentYear);
        
        // Month navigation
        document.querySelector('.prev-month').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        });
        
        document.querySelector('.next-month').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
        });
        
        // Filter attendance records
        const filterRecords = document.getElementById('filterRecords');
        if (filterRecords) {
            filterRecords.addEventListener('change', function() {
                // In a real app, this would filter records from the server
                showToast('Filter applied: ' + this.value, 'info');
            });
        }
        
        // Regularization form submission
        const regularizationForm = document.getElementById('regularizationForm');
        if (regularizationForm) {
            regularizationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        this.reset();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('An error occurred', 'error');
                    console.error('Error:', error);
                });
            });
        }
        
        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            } z-50 transition-all duration-300`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Initialize animations for elements
        const animatedElements = document.querySelectorAll('.slide-in');
        animatedElements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('opacity-100');
            }, index * 100);
        });
    });
</script>
@endsection