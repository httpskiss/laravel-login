@extends('layouts.employee')

@section('title', 'Employee Dashboard')

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
        
        /* Updated gradient to match sidebar blue */
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .glow-on-hover:hover {
            filter: drop-shadow(0 0 8px rgba(30, 58, 138, 0.5)); /* Updated to sidebar blue */
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
        
        /* Updated pulse animation for sidebar blue */
        .pulse {
            animation: pulse-blue 2s infinite;
        }
        
        @keyframes pulse-blue {
            0% {
                box-shadow: 0 0 0 0 rgba(30, 58, 138, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(30, 58, 138, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(30, 58, 138, 0);
            }
        }
    </style>

<main class="p-4 md:p-6">
   <!-- Welcome Banner -->
    <div class="container mx-auto px-4">
        <div class="gradient-bg text-white rounded-xl p-6 mb-6 shadow-lg animate-fade-in">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->first_name }}!</h2>
                    <p class="opacity-90 text-blue-100">Here's what's happening today.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="bg-blue-700/50 text-white px-4 py-2 rounded-lg text-sm font-medium backdrop-blur-sm">
                        {{ now()->format('l, F j, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Days Worked -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4 glow-on-hover">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Days Worked</p>
                        <h3 class="text-2xl font-bold">{{ $monthlyAttendance['present'] + $monthlyAttendance['late'] }}</h3>
                        <p class="text-green-500 text-xs">{{ $monthlyAttendance['attendance_rate'] }}% attendance rate</p>
                    </div>
                </div>
            </div>
            
            <!-- Average Hours -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4 glow-on-hover">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Average Hours</p>
                        <h3 class="text-2xl font-bold">8.5</h3>
                        <p class="text-sm {{ ($monthlyAttendance['present'] / $monthlyAttendance['total_days'] * 100) >= 90 ? 'text-green-500' : 'text-yellow-500' }}">
                            {{ ($monthlyAttendance['present'] / $monthlyAttendance['total_days'] * 100) >= 90 ? 'On track' : 'Below target' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Pay -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.3s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4 glow-on-hover">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">This Month's Pay</p>
                        <h3 class="text-2xl font-bold">₱ {{ number_format($payrollData['current']->net_salary ?? 0, 2) }}</h3>
                        <p class="text-green-500 text-xs">
                            @if($payrollData['next_payday'])
                                Next payday: {{ $payrollData['next_payday'] }}
                            @else
                                No upcoming payday
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Leave Balance -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.4s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4 glow-on-hover">
                        <i class="fas fa-umbrella-beach text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Leave Balance</p>
                        <h3 class="text-2xl font-bold">{{ $leaveData['balance'] }}</h3>
                        <p class="text-blue-500 text-xs">
                            @if($leaveData['pending'] > 0)
                                {{ $leaveData['pending'] }} pending request{{ $leaveData['pending'] > 1 ? 's' : '' }}
                            @elseif($leaveData['on_leave'])
                                Currently on leave
                            @else
                                No pending requests
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Department Distribution Chart -->
            <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2 animate-fade-in" style="animation-delay: 0.2s">
                 <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Today's Time Tracking</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium">Status: </span>
                        <span class="px-2 py-1 {{ $attendanceData['status_class'] }} text-xs rounded-full flex items-center">
                            <span class="status-indicator {{ $attendanceData['status_indicator'] }} mr-1"></span>
                            {{ $attendanceData['status'] }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">Check-in</span>
                            <span class="text-xs {{ $attendanceData['time_in_status_class'] }} px-2 py-1 rounded">
                                {{ $attendanceData['time_in_status'] }}
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-gray-800">
                            {{ $attendanceData['time_in_time'] ?? '--:-- --' }}
                        </div>
                    </div>
                    
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">Check-out</span>
                            <span class="text-xs {{ $attendanceData['time_out_status_class'] }} px-2 py-1 rounded">
                                {{ $attendanceData['time_out_status'] }}
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-gray-800">
                            {{ $attendanceData['time_out_time'] ?? '--:-- --' }}
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <form action="{{ route('attendance.check') }}" method="POST">
                        @csrf
                        <button type="submit" class="relative {{ $attendanceData['button_class'] }} text-white font-medium py-3 px-8 rounded-full transition-all duration-300 flex items-center {{ $attendanceData['button_pulse'] ? 'pulse' : '' }}">
                            <i class="fas fa-fingerprint mr-2"></i>
                            {{ $attendanceData['button_text'] }}
                        </button>
                    </form>
                </div>
                
                <div class="mt-6">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Today's Hours</span>
                        <span class="text-sm font-medium">{{ $attendanceData['hours_worked'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $attendanceData['hours_percentage'] }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Events Card -->
            <div class="bg-white rounded-xl shadow-md p-6 animate-fade-in" style="animation-delay: 0.3s">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                    Upcoming Events
                </h2>
                
                <div class="space-y-4">
                    @forelse($upcomingEvents as $event)
                    <a href="{{ route('employee.events.show', $event['event_id']) }}" class="block">
                        <div class="flex items-start p-3 rounded-lg border border-blue-100 bg-blue-50 hover:bg-blue-100 transition-colors">
                            <div class="{{ $event['icon_bg'] }} p-2 rounded-lg mr-3">
                                <i class="{{ $event['icon'] }}"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $event['title'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $event['date'] }} • {{ $event['time'] }}</p>
                                <p class="text-xs text-blue-600 mt-1">{{ $event['location'] }}</p>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-4 text-gray-500">
                        No upcoming events
                    </div>
                    @endforelse
                </div>
                
                <a href="{{ route('employee.events') }}" class="mt-4 w-full text-center text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-center">
                    View All Events <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

         <!-- Quick Actions -->
        <div class="mb-8 animate-fade-in" style="animation-delay: 0.4s">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <span class="w-1 h-6 bg-blue-500 rounded-full mr-2"></span>
                Quick Actions
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('employees.leaves') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-blue-50 group">
                    <div class="bg-blue-100 text-blue-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-blue-200 group-hover:scale-110">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Request Leave</span>
                    <span class="text-xs text-blue-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Submit now →</span>
                </a>
                
                <a href="{{ route('employees.payroll') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-green-50 group">
                    <div class="bg-green-100 text-green-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-green-200 group-hover:scale-110">
                        <i class="fas fa-file-invoice-dollar text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Payslips</span>
                    <span class="text-xs text-green-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">View history →</span>
                </a>
                
                <a href="{{ route('employees.tasks') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-purple-50 group">
                    <div class="bg-purple-100 text-purple-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-purple-200 group-hover:scale-110">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Tasks</span>
                    <span class="text-xs text-purple-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">See assignments →</span>
                </a>
                
                <a href="{{ route('profile.show') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-red-50 group">
                    <div class="bg-red-100 text-red-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-red-200 group-hover:scale-110">
                        <i class="fas fa-user-edit text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Update Profile</span>
                    <span class="text-xs text-red-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Edit details →</span>
                </a>
            </div>
        </div>

       <!-- Attendance and Tasks Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
             <!-- Monthly Attendance Summary -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in" style="animation-delay: 0.2s">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-calendar-week text-blue-500 mr-2"></i>
                        Monthly Attendance Summary
                    </h2>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Attendance Rate</span>
                            <span class="text-sm font-medium">{{ $monthlyAttendance['attendance_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-400 h-2.5 rounded-full" style="width: {{ $monthlyAttendance['attendance_rate'] }}%"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <div class="text-center">
                            <div class="text-xl font-bold">{{ $monthlyAttendance['present'] }}</div>
                            <div class="text-xs text-gray-500">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold">{{ $monthlyAttendance['late'] }}</div>
                            <div class="text-xs text-gray-500">Late</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold">{{ $monthlyAttendance['absent'] }}</div>
                            <div class="text-xs text-gray-500">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold">{{ $monthlyAttendance['on_leave'] }}</div>
                            <div class="text-xs text-gray-500">Leave</div>
                        </div>
                    </div>
                    
                    <div class="chart-container" style="position: relative; height: 200px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tasks Overview -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in" style="animation-delay: 0.3s">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-tasks text-green-500 mr-2"></i>
                        My Tasks
                    </h2>
                    @if($newTasksCount > 0)
                        <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded-full">{{ $newTasksCount }} New</span>
                    @endif
                </div>
                <div class="p-6">
                    <div class="space-y-4 max-h-64 overflow-y-auto pr-2 scrollbar-hide">
                        @forelse($tasks as $task)
                        <div class="flex items-start p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <div class="mt-1 mr-3">
                                <input type="checkbox" 
                                    class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300"
                                    {{ $task->status === 'completed' ? 'checked' : '' }}
                                    data-task-id="{{ $task->id }}">
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 {{ $task->status === 'completed' ? 'line-through' : '' }}">
                                    {{ $task->title }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    Due: {{ $task->due_date ? Carbon\Carbon::parse($task->due_date)->format('M j, Y g:i A') : 'No due date' }}
                                </p>
                                @if($task->status === 'completed')
                                    <div class="mt-2 flex items-center">
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Completed</span>
                                        @if($task->completed_at)
                                            <span class="text-xs text-gray-500 ml-2">
                                                {{ Carbon\Carbon::parse($task->completed_at)->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                @elseif($task->priority)
                                    <div class="mt-2 flex items-center">
                                        <span class="text-xs {{ 
                                            $task->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                            ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') 
                                        }} px-2 py-1 rounded-full">
                                            {{ ucfirst($task->priority) }} Priority
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500">
                            No tasks assigned
                        </div>
                        @endforelse
                    </div>
                    
                    <a href="{{ route('employees.tasks') }}" class="mt-4 w-full text-center text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-center">
                        View All Tasks <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in" style="animation-delay: 0.3s">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-history text-purple-500 mr-2"></i>
                        Recent Activities
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentActivities as $activity)
                        <div class="flex items-start">
                            <div class="{{ $activity['icon_bg'] }} p-2 rounded-full mr-3">
                                <i class="{{ $activity['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800">{{ $activity['title'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ $activity['time_ago'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <button class="mt-4 w-full text-center text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-center">
                        View Full Activity Log <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </button>
                </div>
            </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: ['Present', 'Late', 'Absent', 'On Leave'],
                datasets: [{
                    label: 'Days',
                    data: [
                        {{ $monthlyAttendance['present'] }},
                        {{ $monthlyAttendance['late'] }},
                        {{ $monthlyAttendance['absent'] }},
                        {{ $monthlyAttendance['on_leave'] }}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Task completion toggle
        document.querySelectorAll('[data-task-id]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.dataset.taskId;
                const isCompleted = this.checked;
                
                fetch(`/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: isCompleted ? 'completed' : 'pending',
                        _method: 'PUT'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection