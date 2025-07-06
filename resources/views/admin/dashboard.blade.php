@extends('layouts.admin')

@section('title', 'Admin Dashboard')

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
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4 glow-on-hover">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Users</p>
                        <h3 class="text-2xl font-bold">{{ number_format($stats['totalEmployees']) }}</h3>
                        <p class="text-green-500 text-xs">+12% from last month</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4 glow-on-hover">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Present Today</p>
                        <h3 class="text-2xl font-bold" data-stat="present">{{ $attendanceStats->present ?? 0 }}</h3>
                        <p class="text-sm {{ ($attendanceStats->present ?? 0) > 0 ? 'text-green-500' : 'text-gray-500' }}">
                            {{ $totalEmployees > 0 ? round(($attendanceStats->present ?? 0)/$totalEmployees*100) : 0 }}% of workforce
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.3s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4 glow-on-hover">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Monthly Payroll</p>
                        <h3 class="text-2xl font-bold">₱ {{ number_format($payrollData->total_payroll) }}</h3>
                        <p class="{{ $payrollPercentageChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-xs">
                            {{ $payrollPercentageChange >= 0 ? '+' : '' }}{{ $payrollPercentageChange }}% from last month
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300 animate-fade-in" style="animation-delay: 0.4s">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4 glow-on-hover">
                        <i class="fas fa-user-graduate text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Employees</p>
                        <h3 class="text-2xl font-bold">{{ number_format($stats['facultyCount']) }}</h3>
                        <p class="text-green-500 text-xs">+8% from last year</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Department Distribution Chart -->
            <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Employee Distribution by Department</h2>
                    <select id="departmentFilter" class="border rounded-lg px-3 py-1.5 text-sm bg-gray-50 focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option value="all">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
            
            <!-- Gender Distribution Chart -->
            <div class="bg-white rounded-xl shadow-md p-6 animate-fade-in" style="animation-delay: 0.3s">
                <h2 class="text-lg font-semibold mb-4">Gender Distribution</h2>
                <div class="chart-container" style="position: relative; height: 250px;">
                    <canvas id="genderChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    @php
                        $total = $genderStats['male'] + $genderStats['female'] + $genderStats['other'];
                        $malePercentage = $total > 0 ? round(($genderStats['male'] / $total) * 100) : 0;
                        $femalePercentage = $total > 0 ? round(($genderStats['female'] / $total) * 100) : 0;
                        $otherPercentage = $total > 0 ? round(($genderStats['other'] / $total) * 100) : 0;
                    @endphp
                    <div class="p-2">
                        <div class="text-xl font-bold text-blue-600">{{ $genderStats['male'] }}</div>
                        <div class="text-xs text-gray-500">Male ({{ $malePercentage }}%)</div>
                    </div>
                    <div class="p-2">
                        <div class="text-xl font-bold text-pink-600">{{ $genderStats['female'] }}</div>
                        <div class="text-xs text-gray-500">Female ({{ $femalePercentage }}%)</div>
                    </div>
                    <div class="p-2">
                        <div class="text-xl font-bold text-purple-600">{{ $genderStats['other'] }}</div>
                        <div class="text-xs text-gray-500">Other ({{ $otherPercentage }}%)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Actions -->
        <div class="mb-8 animate-fade-in" style="animation-delay: 0.4s">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <span class="w-1 h-6 bg-blue-500 rounded-full mr-2"></span>
                Quick Actions
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.dashboard') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-blue-50 group">
                    <div class="bg-blue-100 text-blue-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-blue-200 group-hover:scale-110">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Add Employee</span>
                    <span class="text-xs text-blue-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Get started →</span>
                </a>
                
                <a href="{{ route('admin.attendance') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-green-50 group">
                    <div class="bg-green-100 text-green-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-green-200 group-hover:scale-110">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Manage Attendance</span>
                    <span class="text-xs text-green-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">View calendar →</span>
                </a>
                
                <a href="{{ route('admin.reports') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-purple-50 group">
                    <div class="bg-purple-100 text-purple-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-purple-200 group-hover:scale-110">
                        <i class="fas fa-file-invoice-dollar text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Generate Reports</span>
                    <span class="text-xs text-purple-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Create now →</span>
                </a>
                
                <a href="{{ route('admin.dashboard') }}" class="quick-action-card bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center transition-all duration-300 hover:bg-red-50 group">
                    <div class="bg-red-100 text-red-600 p-4 rounded-full mb-4 transition-all duration-300 group-hover:bg-red-200 group-hover:scale-110">
                        <i class="fas fa-bell text-2xl"></i>
                    </div>
                    <span class="text-sm font-medium text-center text-gray-700">Send Announcement</span>
                    <span class="text-xs text-red-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Notify all →</span>
                </a>
            </div>
        </div>

        <!-- Attendance Summary Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Today's Summary Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in" style="animation-delay: 0.2s">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                        Today's Attendance Summary
                    </h2>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Attendance Rate</span>
                            <span class="text-sm font-medium">{{ $totalEmployees > 0 ? round(($attendanceStats->present ?? 0)/$totalEmployees*100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-400 h-2.5 rounded-full" style="width: {{ $totalEmployees > 0 ? round(($attendanceStats->present ?? 0)/$totalEmployees*100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="p-4 border border-gray-100 rounded-xl hover:bg-blue-50 transition-colors duration-200 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $attendanceStats->present ?? 0 }}</div>
                            <div class="text-xs text-gray-500 mt-1">Present</div>
                            <div class="mt-2 w-full bg-blue-100 rounded-full h-1">
                                <div class="bg-blue-500 h-1 rounded-full" style="width: 84%"></div>
                            </div>
                            <div class="text-xs text-blue-500 mt-1">On Time</div>
                        </div>
                        <div class="p-4 border border-gray-100 rounded-xl hover:bg-yellow-50 transition-colors duration-200 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $attendanceStats->on_leave ?? 0 }}</div>
                            <div class="text-xs text-gray-500 mt-1">On Leave</div>
                            <div class="mt-2 w-full bg-yellow-100 rounded-full h-1">
                                <div class="bg-yellow-500 h-1 rounded-full" style="width: 100%"></div>
                            </div>
                            <div class="text-xs text-yellow-500 mt-1">Approved</div>
                        </div>
                        <div class="p-4 border border-gray-100 rounded-xl hover:bg-red-50 transition-colors duration-200 text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $attendanceStats->absent ?? 0 }}</div>
                            <div class="text-xs text-gray-500 mt-1">Absent</div>
                            <div class="mt-2 w-full bg-red-100 rounded-full h-1">
                                <div class="bg-red-500 h-1 rounded-full" style="width: 16%"></div>
                            </div>
                            <div class="text-xs text-red-500 mt-1">Late</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Check-ins Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in" style="animation-delay: 0.3s">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-clock text-green-500 mr-2"></i>
                        Recent Check-ins
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2 scrollbar-hide">
                        @foreach($recentCheckins as $checkin)
                        <div class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="relative">
                                <img class="w-10 h-10 rounded-full mr-3" src="{{ $checkin->profile_photo_path }}" alt="{{ $checkin->first_name }} {{ $checkin->last_name }}">
                                <span class="absolute bottom-0 right-2 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">{{ $checkin->first_name }} {{ $checkin->last_name }}</p>
                                <p class="text-xs text-gray-500">{{ $checkin->department }}</p>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                {{ \Carbon\Carbon::parse($checkin->check_in)->format('h:i A') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Employees Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in" style="animation-delay: 0.4s">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-users text-purple-500 mr-2"></i>
                    Recent Employees
                </h2>
                <a href="{{ route('admin.employees') }}" class="text-sm bg-white hover:bg-gray-50 text-blue-600 px-3 py-1.5 rounded-lg flex items-center transition-colors duration-200 border border-gray-200">
                    <i class="fas fa-eye mr-2"></i> View All Employees
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentEmployees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" 
                                            src="{{ $employee->profile_photo_url }}" 
                                            alt="{{ $employee->first_name }} {{ $employee->last_name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $employee->department }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($employee->roles->isNotEmpty())
                                    {{ ucfirst($employee->roles->first()->name) }}
                                @else
                                    Employee
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.employees', $employee->id) }}" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.employees', $employee->id) }}" class="text-green-600 hover:text-green-900"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Department Chart
        const departmentLabels = @json($departmentLabels);
        const departmentData = @json($departmentData);
        const colorMap = @json($departmentColorMap);
        
        const backgroundColors = departmentLabels.map(label => colorMap[label].background);
        const borderColors = departmentLabels.map(label => colorMap[label].border);
        
        const ctx = document.getElementById('departmentChart').getContext('2d');
        const departmentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: departmentLabels,
                datasets: [{
                    label: 'Employees by Department',
                    data: departmentData,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = Math.round((value / total) * 100);
                                return `${label}${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });

        document.getElementById('departmentFilter').addEventListener('change', function() {
            const selectedDept = this.value;
            
            if (selectedDept === 'all') {
                departmentChart.data.labels = departmentLabels;
                departmentChart.data.datasets[0].data = departmentData;
                departmentChart.data.datasets[0].backgroundColor = backgroundColors;
                departmentChart.data.datasets[0].borderColor = borderColors;
            } else {
                const deptIndex = departmentLabels.indexOf(selectedDept);
                if (deptIndex !== -1) {
                    departmentChart.data.labels = [selectedDept];
                    departmentChart.data.datasets[0].data = [departmentData[deptIndex]];
                    departmentChart.data.datasets[0].backgroundColor = [backgroundColors[deptIndex]];
                    departmentChart.data.datasets[0].borderColor = [borderColors[deptIndex]];
                }
            }
            
            departmentChart.update();
        });

        // Gender Chart
        const genderCtx = document.getElementById('genderChart');
        if (genderCtx) {
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Male', 'Female', 'Other'],
                    datasets: [{
                        data: [
                            {{ $genderStats['male'] }},
                            {{ $genderStats['female'] }},
                            {{ $genderStats['other'] }}
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
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
                            position: 'bottom',
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } z-50 animate-fade-in`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });
</script>
@endpush
@endsection