@extends('layouts.admin')

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
</style>

    <!-- Attendance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 slide-in">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Staff</h3>
                    <p class="text-2xl font-semibold text-blue-600">{{ number_format($totalStaff) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Present Today</h3>
                    <p class="text-2xl font-semibold text-green-600">{{ number_format($presentToday) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Late Arrivals</h3>
                    <p class="text-2xl font-semibold text-yellow-600">{{ number_format($lateToday) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Absent Today</h3>
                    <p class="text-2xl font-semibold text-red-600">{{ number_format($absentToday) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Department Summary -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow overflow-hidden slide-in">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Department Summary</h3>
                <p class="mt-1 text-sm text-gray-500">Attendance by department</p>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    @foreach($departmentStats as $dept)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ $dept['name'] }}</span>
                                <span>{{ $dept['percentage'] }}% present</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full 
                                    @if($dept['percentage'] >= 90) bg-green-500
                                    @elseif($dept['percentage'] >= 80) bg-green-400
                                    @elseif($dept['percentage'] >= 70) bg-yellow-500
                                    @else bg-red-500
                                    @endif" 
                                    style="width: {{ $dept['percentage'] }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        View All Departments
                    </button>
                </div>
            </div>
        </div>
      <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden slide-in">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Recent Attendance Activity</h3>
                    <p class="mt-1 text-sm text-gray-500">Latest check-ins and check-outs</p>
                </div>
                <div class="flex items-center space-x-3">
                    <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Today</option>
                        <option>Yesterday</option>
                        <option>This Week</option>
                        <option>This Month</option>
                    </select>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                    <button class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-filter mr-2"></i> Filters
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentActivity as $attendance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $attendance->user->profile_photo_url }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->user->first_name }} {{ $attendance->user->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->user->department }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($attendance->time_in)
                                    {{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($attendance->time_in && !$attendance->time_out)
                                    Check-in
                                @elseif($attendance->time_out)
                                    Check-out
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->status === 'present')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        On Time
                                    </span>
                                @elseif($attendance->status === 'late')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                @elseif($attendance->status === 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                @elseif($attendance->status === 'on_leave')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        On Leave
                                    </span>
                                @elseif($attendance->status === 'half_day')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                        Half Day
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button class="text-indigo-600 hover:text-indigo-900" onclick="showAttendanceModal({{ $attendance->id }})">View</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 text-right">
                <a href="{{ route('admin.attendance') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">View All Activity</a>
            </div>
        </div>
    </div>

    
    <!-- Detailed Attendance Records -->
    <div class="bg-white rounded-lg shadow overflow-hidden slide-in mb-6">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Staff Attendance Records</h3>
                <p class="mt-1 text-sm text-gray-500">Detailed attendance for all staff members</p>
            </div>
            <div class="flex space-x-3">
                <div class="relative">
                    <input type="text" placeholder="Search staff..." class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <button class="absolute right-3 top-2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option>All Departments</option>
                    @foreach($departments as $dept)
                        <option>{{ $dept->department }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Today</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">This Week</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">This Month</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($staffAttendance as $staff)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $staff->employee_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $staff->profile_photo_url }}" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $staff->first_name }} {{ $staff->last_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $staff->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $staff->department }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $staff->getRoleAttribute() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($staff->todayAttendance)
                                @if($staff->todayAttendance->status === 'present')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Present
                                    </span>
                                @elseif($staff->todayAttendance->status === 'late')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                @elseif($staff->todayAttendance->status === 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                @elseif($staff->todayAttendance->status === 'on_leave')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        On Leave
                                    </span>
                                @elseif($staff->todayAttendance->status === 'half_day')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                        Half Day
                                    </span>
                                @endif
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Not Recorded
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $staff->weeklyPresentCount }}/{{ $staff->workingDaysThisWeek }} days
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $staff->monthlyPresentCount }}/{{ $staff->workingDaysThisMonth }} days
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3" onclick="showAttendanceModal({{ $staff->id }}, 'user')">View</button>
                            <button class="text-blue-600 hover:text-blue-900">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">{{ $staffAttendance->firstItem() }}</span>
                        to
                        <span class="font-medium">{{ $staffAttendance->lastItem() }}</span>
                        of
                        <span class="font-medium">{{ $staffAttendance->total() }}</span>
                        results
                    </p>
                </div>
                <div>
                    {{ $staffAttendance->links() }}
                </div>
            </div>
        </div>
    </div>

     <!-- Pending Requests -->
    <div class="bg-white rounded-lg shadow overflow-hidden slide-in">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Pending Attendance Requests</h3>
            <p class="mt-1 text-sm text-gray-500">Regularization and leave requests requiring approval</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pendingRequests as $request)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">REQ-{{ $request->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $request->user->profile_photo_url }}" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->user->first_name }} {{ $request->user->last_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->user->department }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($request->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($request->reason, 30) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-green-600 hover:text-green-900 mr-3" onclick="approveRequest({{ $request->id }})">Approve</button>
                            <button class="text-red-600 hover:text-red-900" onclick="rejectRequest({{ $request->id }})">Reject</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 text-right">
            <a href="{{ route('admin.leaves') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">View All Requests</a>
        </div>
    </div>

    <!-- Modal for Attendance Details -->
    <div id="attendanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Attendance Details</h3>
                <button id="closeAttendanceModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="space-y-4">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Close
                </button>
                <button id="editRecordBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Edit Record
                </button>
            </div>
        </div>
    </div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    const animatedElements = document.querySelectorAll('.slide-in');
    animatedElements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('opacity-100');
        }, index * 100);
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.id === 'attendanceModal') {
            closeModal();
        }
    });

    // Close modal button
    document.getElementById('closeAttendanceModal').addEventListener('click', closeModal);
    });

    function showAttendanceModal(id, type = 'attendance') {
    const modal = document.getElementById('attendanceModal');
    const content = document.getElementById('modalContent');
    const editBtn = document.getElementById('editRecordBtn');

    // Show loading state
    content.innerHTML = '<div class="flex justify-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-indigo-600"></i></div>';

    // Fetch data via AJAX
    fetch(`/admin/attendance/${id}/details?type=${type}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            content.innerHTML = data.html;
            if (type === 'attendance') {
                editBtn.onclick = function() { editAttendance(data.id); };
                editBtn.style.display = 'inline-block';
            } else {
                editBtn.style.display = 'none';
            }
            modal.classList.remove('hidden');
        } else {
            showToast(data.message || 'Failed to load details', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to load attendance details', 'error');
    });
}


    function closeModal() {
    document.getElementById('attendanceModal').classList.add('hidden');
    }

    function approveRequest(requestId) {
    fetch(`/admin/attendance/requests/${requestId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Request approved successfully', 'success');
            document.querySelector(`tr[data-request="${requestId}"]`).remove();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to approve request', 'error');
    });
    }

    function rejectRequest(requestId) {
    fetch(`/admin/attendance/requests/${requestId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Request rejected', 'error');
            document.querySelector(`tr[data-request="${requestId}"]`).remove();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to reject request', 'error');
    });
    }

    function editAttendance(id) {
    // In a real app, this would open an edit form
    showToast('Edit mode activated for record #' + id, 'info');
    }

    function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    } z-50`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
    }
    </script>
@endpush
@endsection