@extends('layouts.admin')

@section('title', 'Attendance Management')

@section('content')

<div class="flex flex-col h-full" x-data="attendanceModule()" x-cloak>
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong class="font-medium">Please fix these errors:</strong>
            </div>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Attendance Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Today's Present</p>
                    <h3 class="text-3xl font-bold mt-2" x-text="dashboardStats.today_present"></h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-4">
                <span x-text="dashboardStats.today_present_change >= 0 ? '+' + dashboardStats.today_present_change + '%' : dashboardStats.today_present_change + '%'" 
                      :class="{'text-green-500': dashboardStats.today_present_change >= 0, 'text-red-500': dashboardStats.today_present_change < 0}"></span> from yesterday
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Today's Absent</p>
                    <h3 class="text-3xl font-bold mt-2" x-text="dashboardStats.today_absent"></h3>
                </div>
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-user-slash text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-4">
                <span x-text="dashboardStats.today_absent_change >= 0 ? '+' + dashboardStats.today_absent_change + '%' : dashboardStats.today_absent_change + '%'" 
                      :class="{'text-green-500': dashboardStats.today_absent_change < 0, 'text-red-500': dashboardStats.today_absent_change >= 0}"></span> from yesterday
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Late Arrivals</p>
                    <h3 class="text-3xl font-bold mt-2" x-text="dashboardStats.today_late"></h3>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-4">
                <span x-text="dashboardStats.today_late_change >= 0 ? '+' + dashboardStats.today_late_change + '%' : dashboardStats.today_late_change + '%'" 
                      :class="{'text-green-500': dashboardStats.today_late_change < 0, 'text-red-500': dashboardStats.today_late_change >= 0}"></span> from yesterday
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 attendance-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">On Leave</p>
                    <h3 class="text-3xl font-bold mt-2" x-text="dashboardStats.today_on_leave"></h3>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-umbrella-beach text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-4">
                <span x-text="dashboardStats.today_on_leave_change >= 0 ? '+' + dashboardStats.today_on_leave_change + '%' : dashboardStats.today_on_leave_change + '%'" 
                      :class="{'text-green-500': dashboardStats.today_on_leave_change >= 0, 'text-red-500': dashboardStats.today_on_leave_change < 0}"></span> from yesterday
            </p>
        </div>
    </div>
    
    <!-- Attendance Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="w-full md:w-auto">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    x-model="searchQuery" 
                    @input="filterAttendances()"
                    type="text" 
                    class="block w-full md:w-64 pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                    placeholder="Search attendance..."
                >
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative">
                <input 
                    x-ref="dateRangePicker"
                    type="text" 
                    class="block w-full md:w-48 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out" 
                    placeholder="Select date range"
                    readonly
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fas fa-calendar text-gray-400"></i>
                </div>
            </div>
            <select 
                x-model="selectedDepartment" 
                @change="filterAttendances()"
                class="block w-full md:w-48 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
            >
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                @endforeach
            </select>
            <select 
                x-model="selectedEmployee" 
                @change="filterAttendances()"
                class="block w-full md:w-48 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
            >
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                @endforeach
            </select>
            <select 
                x-model="selectedStatus" 
                @change="filterAttendances()"
                class="block w-full md:w-40 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
            >
                <option value="">All Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="on_leave">On Leave</option>
                <option value="half_day">Half Day</option>
            </select>
            @can('attendance-create')
            <button 
                @click="openAddAttendanceModal()"
                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition duration-150 ease-in-out"
            >
                <i class="fas fa-plus mr-2"></i> Add Record
            </button>
            @endcan
            <a 
                href="{{ route('admin.attendance.export') }}"
                class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition duration-150 ease-in-out"
            >
                <i class="fas fa-file-export mr-2"></i> Export
            </a>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortAttendances('date')">
                            <div class="flex items-center">
                                Date
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'date' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'date' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortAttendances('user.first_name')">
                            <div class="flex items-center">
                                Employee
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'user.first_name' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'user.first_name' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortAttendances('user.department')">
                            <div class="flex items-center">
                                Department
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'user.department' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'user.department' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In/Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortAttendances('status')">
                            <div class="flex items-center">
                                Status
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'status' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'status' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="attendance in paginatedAttendances" :key="attendance.id">
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="new Date(attendance.date).toLocaleDateString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                            :src="attendance.user.profile_photo_url" 
                                            :alt="attendance.user.first_name + ' ' + attendance.user.last_name">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="attendance.user.first_name + ' ' + attendance.user.last_name"></div>
                                        <div class="text-sm text-gray-500" x-text="attendance.user.employee_id"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="attendance.user.department"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div x-show="attendance.time_in" class="flex items-center">
                                    <i class="fas fa-sign-in-alt text-green-500 mr-2"></i>
                                    <span x-text="attendance.time_in"></span>
                                    <span x-show="attendance.biometric_id" class="ml-2 text-xs text-blue-500 flex items-center" title="Biometric Verified">
                                        <i class="fas fa-fingerprint mr-1"></i>
                                    </span>
                                </div>
                                <div x-show="attendance.time_out" class="flex items-center mt-1">
                                    <i class="fas fa-sign-out-alt text-red-500 mr-2"></i>
                                    <span x-text="attendance.time_out"></span>
                                    <span x-show="attendance.biometric_id" class="ml-2 text-xs text-blue-500 flex items-center" title="Biometric Verified">
                                        <i class="fas fa-fingerprint mr-1"></i>
                                    </span>
                                </div>
                                <div x-show="!attendance.time_in && !attendance.time_out" class="text-gray-400">
                                    N/A
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="attendance.total_hours ? attendance.total_hours + ' hrs' : 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                    :class="{
                                        'bg-green-50 text-green-700': attendance.status === 'present',
                                        'bg-red-50 text-red-700': attendance.status === 'absent',
                                        'bg-yellow-50 text-yellow-700': attendance.status === 'late',
                                        'bg-blue-50 text-blue-700': attendance.status === 'on_leave',
                                        'bg-purple-50 text-purple-700': attendance.status === 'half_day'
                                    }"
                                    x-text="attendance.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"
                                ></span>
                                <div x-show="attendance.is_regularized" class="mt-1 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                    Regularized
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    @can('attendance-view-details')
                                    <button 
                                        @click="viewAttendance(attendance.id)"
                                        class="text-blue-500 hover:text-blue-700 transition duration-150 ease-in-out"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @endcan
                                    
                                    @can('attendance-edit')
                                    <button 
                                        @click="editAttendance(attendance.id)"
                                        class="text-yellow-500 hover:text-yellow-700 transition duration-150 ease-in-out"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endcan
                                    
                                    @can('attendance-delete')
                                    <button 
                                        @click="confirmDeleteAttendance(attendance.id)"
                                        class="text-red-500 hover:text-red-700 transition duration-150 ease-in-out"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredAttendances.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-calendar-times text-3xl mb-2"></i>
                                    <p class="text-sm">No attendance records found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-100">
            <div class="flex-1 flex justify-between sm:hidden">
                <button 
                    @click="currentPage = Math.max(1, currentPage - 1)"
                    :disabled="currentPage === 1"
                    :class="{'opacity-50 cursor-not-allowed': currentPage === 1}"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out"
                >
                    Previous
                </button>
                <button 
                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out"
                >
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium" x-text="(currentPage - 1) * pageSize + 1"></span> to 
                        <span class="font-medium" x-text="Math.min(currentPage * pageSize, filteredAttendances.length)"></span> of 
                        <span class="font-medium" x-text="filteredAttendances.length"></span> records
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button 
                            @click="currentPage = 1"
                            :disabled="currentPage === 1"
                            :class="{'opacity-50 cursor-not-allowed': currentPage === 1}"
                            class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-gray-200 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-150 ease-in-out"
                        >
                            <span class="sr-only">First</span>
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                        <button 
                            @click="currentPage = Math.max(1, currentPage - 1)"
                            :disabled="currentPage === 1"
                            :class="{'opacity-50 cursor-not-allowed': currentPage === 1}"
                            class="relative inline-flex items-center px-2 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-150 ease-in-out"
                        >
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <template x-for="page in visiblePages" :key="page">
                            <button 
                                @click="currentPage = page"
                                :class="{'z-10 bg-blue-50 border-blue-300 text-blue-600': currentPage === page}"
                                class="bg-white border-gray-200 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium transition duration-150 ease-in-out"
                                x-text="page"
                            ></button>
                        </template>
                        <button 
                            @click="currentPage = Math.min(totalPages, currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}"
                            class="relative inline-flex items-center px-2 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-150 ease-in-out"
                        >
                            <span class="sr-only">Next</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button 
                            @click="currentPage = totalPages"
                            :disabled="currentPage === totalPages"
                            :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}"
                            class="relative inline-flex items-center px-2 py-2 rounded-r-lg border border-gray-200 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-150 ease-in-out"
                        >
                            <span class="sr-only">Last</span>
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Attendance Modal -->
    <div 
        x-show="isAttendanceModalOpen" 
        @keydown.escape.window="closeAttendanceModal()"
        @click.away="closeAttendanceModal()"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity duration-300 ease-in-out"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-xl rounded-xl bg-white" 
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" x-text="isEditing ? 'Edit Attendance Record' : 'Add New Attendance Record'"></h3>
                <button @click="closeAttendanceModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <form @submit.prevent="saveAttendance()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                            <select 
                                x-model="currentAttendance.user_id"
                                name="user_id"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out" 
                                required
                                :disabled="isEditing"
                            >
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                            <input 
                                x-model="currentAttendance.date"
                                name="date"
                                type="date" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
                            <div class="flex items-center gap-2">
                                <input 
                                    x-model="currentAttendance.time_in"
                                    name="time_in"
                                    type="time" 
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                >
                                <button 
                                    type="button" 
                                    @click="scanFingerprint('time_in')"
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition duration-150 ease-in-out"
                                    title="Scan Fingerprint"
                                >
                                    <i class="fas fa-fingerprint"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
                            <div class="flex items-center gap-2">
                                <input 
                                    x-model="currentAttendance.time_out"
                                    name="time_out"
                                    type="time" 
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                >
                                <button 
                                    type="button" 
                                    @click="scanFingerprint('time_out')"
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition duration-150 ease-in-out"
                                    title="Scan Fingerprint"
                                >
                                    <i class="fas fa-fingerprint"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select 
                                x-model="currentAttendance.status"
                                name="status"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out" 
                                required
                            >
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                                <option value="on_leave">On Leave</option>
                                <option value="half_day">Half Day</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Regularized</label>
                            <select 
                                x-model="currentAttendance.is_regularized"
                                name="is_regularized"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                            >
                                <option value="false">No</option>
                                <option value="true">Yes</option>
                            </select>
                        </div>
                        <div class="md:col-span-2" x-show="currentAttendance.is_regularized === 'true'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Regularization Reason</label>
                            <textarea 
                                x-model="currentAttendance.regularization_reason"
                                name="regularization_reason"
                                rows="2" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                            ></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <div class="flex items-center gap-3">
                                <button 
                                    type="button" 
                                    @click="captureLocation()"
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-150 ease-in-out flex items-center"
                                >
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Capture Location
                                </button>
                                <span x-show="currentAttendance.latitude && currentAttendance.longitude" class="text-sm text-green-600 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Location captured
                                </span>
                                <input type="hidden" x-model="currentAttendance.latitude" name="latitude">
                                <input type="hidden" x-model="currentAttendance.longitude" name="longitude">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea 
                                x-model="currentAttendance.notes"
                                name="notes"
                                rows="2" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                            ></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                        <button 
                            type="button" 
                            @click="closeAttendanceModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            <span x-text="isEditing ? 'Update Record' : 'Add Record'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fingerprint Scanner Modal -->
    <div 
        x-show="isFingerprintModalOpen" 
        @keydown.escape.window="closeFingerprintModal()"
        @click.away="closeFingerprintModal()"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity duration-300 ease-in-out"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-xl rounded-xl bg-white" 
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Fingerprint Verification</h3>
                <button @click="closeFingerprintModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4 text-center">
                <div class="mb-6">
                    <div class="relative mx-auto w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center fingerprint-scan">
                        <div class="absolute inset-0 rounded-full border-4 border-blue-200 animate-pulse"></div>
                        <i class="fas fa-fingerprint text-5xl text-blue-500"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4" x-text="fingerprintMessage"></p>
                <div class="flex justify-center">
                    <button 
                        @click="simulateFingerprintScan()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out"
                    >
                        Simulate Scan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div 
        x-show="isDeleteModalOpen" 
        @keydown.escape.window="closeDeleteModal()"
        @click.away="closeDeleteModal()"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity duration-300 ease-in-out"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
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
                <button @click="closeDeleteModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
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
                    class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                >
                    Cancel
                </button>
                <button 
                    @click="deleteAttendance()"
                    class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                >
                    Delete Record
                </button>
            </div>
        </div>
    </div>

    <!-- View Attendance Modal -->
    <div 
        x-show="isViewModalOpen" 
        @keydown.escape.window="closeViewModal()"
        @click.away="closeViewModal()"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity duration-300 ease-in-out"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-xl rounded-xl bg-white" 
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Attendance Details</h3>
                <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/3">
                        <div class="bg-gray-50 p-6 rounded-lg text-center shadow-sm">
                            <img 
                                x-bind:src="viewAttendanceData.user.profile_photo_url || 'https://ui-avatars.com/api/?name=' + viewAttendanceData.user.first_name + '+' + viewAttendanceData.user.last_name + '&background=random'" 
                                class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-md mb-4"
                                alt="Profile Photo"
                            >
                            <h3 class="text-xl font-semibold" x-text="viewAttendanceData.user.first_name + ' ' + viewAttendanceData.user.last_name"></h3>
                            <p class="text-gray-500" x-text="viewAttendanceData.user.employee_id"></p>
                            <p class="text-gray-500" x-text="viewAttendanceData.user.department"></p>
                        </div>
                        
                        <div class="mt-4 bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                Attendance Summary
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date</p>
                                    <p x-text="new Date(viewAttendanceData.date).toLocaleDateString()" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <span 
                                        class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                        :class="{
                                            'bg-green-50 text-green-700': viewAttendanceData.status === 'present',
                                            'bg-red-50 text-red-700': viewAttendanceData.status === 'absent',
                                            'bg-yellow-50 text-yellow-700': viewAttendanceData.status === 'late',
                                            'bg-blue-50 text-blue-700': viewAttendanceData.status === 'on_leave',
                                            'bg-purple-50 text-purple-700': viewAttendanceData.status === 'half_day'
                                        }"
                                        x-text="viewAttendanceData.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"
                                    ></span>
                                </div>
                                <div x-show="viewAttendanceData.is_regularized">
                                    <p class="text-sm font-medium text-gray-500">Regularized</p>
                                    <p class="text-sm text-gray-700">Yes</p>
                                </div>
                                <div x-show="viewAttendanceData.regularized_by">
                                    <p class="text-sm font-medium text-gray-500">Regularized By</p>
                                    <p x-text="viewAttendanceData.regularized_by_user.first_name + ' ' + viewAttendanceData.regularized_by_user.last_name" class="text-sm text-gray-700"></p>
                                </div>
                                <div x-show="viewAttendanceData.regularized_at">
                                    <p class="text-sm font-medium text-gray-500">Regularized At</p>
                                    <p x-text="new Date(viewAttendanceData.regularized_at).toLocaleString()" class="text-sm text-gray-700"></p>
                                </div>
                                <div x-show="viewAttendanceData.biometric_id">
                                    <p class="text-sm font-medium text-gray-500">Biometric ID</p>
                                    <p x-text="viewAttendanceData.biometric_id" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:w-2/3">
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-4">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-clock mr-2 text-blue-500"></i>
                                Time Details
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Time In</p>
                                    <p x-text="viewAttendanceData.time_in || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Time Out</p>
                                    <p x-text="viewAttendanceData.time_out || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Hours</p>
                                    <p x-text="viewAttendanceData.total_hours ? viewAttendanceData.total_hours + ' hours' : 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-4">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-location-arrow mr-2 text-blue-500"></i>
                                Location Details
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">IP Address</p>
                                    <p x-text="viewAttendanceData.ip_address || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Device Info</p>
                                    <p x-text="viewAttendanceData.device_info || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div x-show="viewAttendanceData.location">
                                    <p class="text-sm font-medium text-gray-500">Location</p>
                                    <p x-text="viewAttendanceData.location" class="text-sm text-gray-700"></p>
                                </div>
                                <div x-show="viewAttendanceData.latitude && viewAttendanceData.longitude">
                                    <p class="text-sm font-medium text-gray-500">Coordinates</p>
                                    <p x-text="viewAttendanceData.latitude + ', ' + viewAttendanceData.longitude" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-4">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                                Notes
                            </h4>
                            <p x-text="viewAttendanceData.notes || 'No notes available'" class="text-sm text-gray-700"></p>
                        </div>
                        
                        <div x-show="viewAttendanceData.regularization_reason" class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-comment-alt mr-2 text-blue-500"></i>
                                Regularization Reason
                            </h4>
                            <p x-text="viewAttendanceData.regularization_reason" class="text-sm text-gray-700"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end border-t border-gray-200 pt-4">
                <button 
                    @click="closeViewModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function attendanceModule() {
        return {
            // Data properties
            attendances: @json($attendances->items()),
            employees: @json($employees),
            departments: @json($departments),
            dashboardStats: {
                today_present: 0,
                today_absent: 0,
                today_late: 0,
                today_on_leave: 0,
                today_present_change: 0,
                today_absent_change: 0,
                today_late_change: 0,
                today_on_leave_change: 0
            },
            searchQuery: '',
            selectedDepartment: '',
            selectedEmployee: '',
            selectedStatus: '',
            startDate: '',
            endDate: '',
            sortColumn: 'date',
            sortDirection: 'desc',
            currentPage: 1,
            pageSize: 20,
            maxVisiblePages: 5,
            
            // Modal states
            isAttendanceModalOpen: false,
            isEditing: false,
            isDeleteModalOpen: false,
            isViewModalOpen: false,
            isFingerprintModalOpen: false,
            attendanceToDelete: null,
            
            // Fingerprint scanning
            fingerprintMessage: 'Place your finger on the scanner',
            fingerprintType: '', // 'time_in' or 'time_out'
            
            // Attendance data
            currentAttendance: {
                id: '',
                user_id: '',
                date: new Date().toISOString().split('T')[0],
                time_in: '',
                time_out: '',
                status: 'present',
                notes: '',
                is_regularized: 'false',
                regularization_reason: '',
                biometric_id: '',
                latitude: null,
                longitude: null
            },
            
            viewAttendanceData: {},
            
            // Initialize component
            init() {
                this.initDateRangePicker();
                this.loadDashboardStats();
            },
            
            // Computed properties
            get filteredAttendances() {
                let filtered = this.attendances;
                
                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(att => 
                        att.user.first_name.toLowerCase().includes(query) || 
                        att.user.last_name.toLowerCase().includes(query) ||
                        att.user.employee_id.toLowerCase().includes(query) ||
                        att.user.department.toLowerCase().includes(query)
                    );
                }
                
                // Filter by department
                if (this.selectedDepartment) {
                    filtered = filtered.filter(att => att.user.department === this.selectedDepartment);
                }
                
                // Filter by employee
                if (this.selectedEmployee) {
                    filtered = filtered.filter(att => att.user_id == this.selectedEmployee);
                }
                
                // Filter by status
                if (this.selectedStatus) {
                    filtered = filtered.filter(att => att.status === this.selectedStatus);
                }
                
                // Filter by date range
                if (this.startDate && this.endDate) {
                    filtered = filtered.filter(att => {
                        const date = new Date(att.date);
                        return date >= new Date(this.startDate) && date <= new Date(this.endDate);
                    });
                }
                
                // Sort attendances
                return filtered.sort((a, b) => {
                    let aValue, bValue;
                    
                    if (this.sortColumn.includes('.')) {
                        // Handle nested properties (e.g., user.first_name)
                        const props = this.sortColumn.split('.');
                        aValue = props.reduce((obj, prop) => obj && obj[prop], a);
                        bValue = props.reduce((obj, prop) => obj && obj[prop], b);
                    } else {
                        aValue = a[this.sortColumn] || '';
                        bValue = b[this.sortColumn] || '';
                    }
                    
                    if (aValue < bValue) return this.sortDirection === 'asc' ? -1 : 1;
                    if (aValue > bValue) return this.sortDirection === 'asc' ? 1 : -1;
                    return 0;
                });
            },
            
            get totalPages() {
                return Math.ceil(this.filteredAttendances.length / this.pageSize);
            },
            
            get visiblePages() {
                const pages = [];
                let startPage = Math.max(1, this.currentPage - Math.floor(this.maxVisiblePages / 2));
                let endPage = Math.min(this.totalPages, startPage + this.maxVisiblePages - 1);
                
                if (endPage - startPage + 1 < this.maxVisiblePages) {
                    startPage = Math.max(1, endPage - this.maxVisiblePages + 1);
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    pages.push(i);
                }
                
                return pages;
            },
            
            get paginatedAttendances() {
                const start = (this.currentPage - 1) * this.pageSize;
                return this.filteredAttendances.slice(start, start + this.pageSize);
            },
            
            // Methods
            initDateRangePicker() {
                const picker = $(this.$refs.dateRangePicker).daterangepicker({
                    opens: 'left',
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                picker.on('apply.daterangepicker', (ev, picker) => {
                    this.startDate = picker.startDate.format('YYYY-MM-DD');
                    this.endDate = picker.endDate.format('YYYY-MM-DD');
                    $(this.$refs.dateRangePicker).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    this.filterAttendances();
                });

                picker.on('cancel.daterangepicker', () => {
                    this.startDate = '';
                    this.endDate = '';
                    $(this.$refs.dateRangePicker).val('');
                    this.filterAttendances();
                });
            },
            
            loadDashboardStats() {
                fetch("{{ route('admin.attendance.departments.data') }}")
                    .then(response => response.json())
                    .then(data => {
                        // Calculate today's stats (simplified for demo)
                        const today = new Date().toISOString().split('T')[0];
                        const todayAttendances = this.attendances.filter(att => att.date === today);
                        
                        this.dashboardStats.today_present = todayAttendances.filter(att => att.status === 'present').length;
                        this.dashboardStats.today_absent = todayAttendances.filter(att => att.status === 'absent').length;
                        this.dashboardStats.today_late = todayAttendances.filter(att => att.status === 'late').length;
                        this.dashboardStats.today_on_leave = todayAttendances.filter(att => att.status === 'on_leave').length;
                        
                        // Calculate change from yesterday (simplified for demo)
                        this.dashboardStats.today_present_change = Math.floor(Math.random() * 10) - 2;
                        this.dashboardStats.today_absent_change = Math.floor(Math.random() * 10) - 2;
                        this.dashboardStats.today_late_change = Math.floor(Math.random() * 10) - 2;
                        this.dashboardStats.today_on_leave_change = Math.floor(Math.random() * 10) - 2;
                    });
            },
            
            sortAttendances(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            },
            
            openAddAttendanceModal() {
                this.isEditing = false;
                this.currentAttendance = {
                    id: '',
                    user_id: '',
                    date: new Date().toISOString().split('T')[0],
                    time_in: '',
                    time_out: '',
                    status: 'present',
                    notes: '',
                    is_regularized: 'false',
                    regularization_reason: '',
                    biometric_id: '',
                    latitude: null,
                    longitude: null
                };
                this.isAttendanceModalOpen = true;
                setTimeout(() => {
                    const modal = document.querySelector('[x-show="isAttendanceModalOpen"]');
                    if (modal) modal.style.display = 'block';
                }, 50);
            },

            editAttendance(id) {
                const attendance = this.attendances.find(att => att.id === id);
                if (attendance) {
                    this.currentAttendance = {
                        id: attendance.id,
                        user_id: attendance.user_id,
                        date: attendance.date,
                        time_in: attendance.time_in,
                        time_out: attendance.time_out,
                        status: attendance.status,
                        notes: attendance.notes,
                        is_regularized: attendance.is_regularized ? 'true' : 'false',
                        regularization_reason: attendance.regularization_reason,
                        biometric_id: attendance.biometric_id,
                        latitude: attendance.latitude,
                        longitude: attendance.longitude
                    };
                    this.isEditing = true;
                    this.isAttendanceModalOpen = true;
                    setTimeout(() => {
                        const modal = document.querySelector('[x-show="isAttendanceModalOpen"]');
                        if (modal) modal.style.display = 'block';
                    }, 50);
                }
            },
            
            viewAttendance(id) {
                const attendance = this.attendances.find(att => att.id === id);
                if (attendance) {
                    this.viewAttendanceData = attendance;
                    this.isViewModalOpen = true;
                    setTimeout(() => {
                        const modal = document.querySelector('[x-show="isViewModalOpen"]');
                        if (modal) modal.style.display = 'block';
                    }, 50);
                }
            },
            
            confirmDeleteAttendance(id) {
                this.attendanceToDelete = id;
                this.isDeleteModalOpen = true;
                setTimeout(() => {
                    const modal = document.querySelector('[x-show="isDeleteModalOpen"]');
                    if (modal) modal.style.display = 'block';
                }, 50);
            },
            
            scanFingerprint(type) {
                this.fingerprintType = type;
                this.fingerprintMessage = 'Place your finger on the scanner';
                this.isFingerprintModalOpen = true;
                setTimeout(() => {
                    const modal = document.querySelector('[x-show="isFingerprintModalOpen"]');
                    if (modal) modal.style.display = 'block';
                }, 50);
            },
            
            simulateFingerprintScan() {
                this.fingerprintMessage = 'Scanning...';
                
                // Simulate fingerprint scan with delay
                setTimeout(() => {
                    // Generate a random fingerprint ID for simulation
                    const fingerprintId = 'fp_' + Math.random().toString(36).substr(2, 9);
                    
                    // Update the current time based on fingerprint type
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const currentTime = `${hours}:${minutes}`;
                    
                    if (this.fingerprintType === 'time_in') {
                        this.currentAttendance.time_in = currentTime;
                    } else {
                        this.currentAttendance.time_out = currentTime;
                    }
                    
                    // Set biometric ID
                    this.currentAttendance.biometric_id = fingerprintId;
                    
                    this.fingerprintMessage = 'Verification successful!';
                    
                    // Close modal after success
                    setTimeout(() => {
                        this.closeFingerprintModal();
                        this.showToast('Fingerprint verified successfully');
                    }, 1000);
                }, 1500);
            },
            
            captureLocation() {
                if (navigator.geolocation) {
                    this.showToast('Requesting location... Please allow location access if prompted.', 'info');
                    
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.currentAttendance.latitude = position.coords.latitude;
                            this.currentAttendance.longitude = position.coords.longitude;
                            this.showToast('Location captured successfully');
                        },
                        (error) => {
                            console.error('Geolocation error:', error);
                            let errorMessage = 'Failed to capture location';
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage = 'Location access was denied';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage = 'Location information is unavailable';
                                    break;
                                case error.TIMEOUT:
                                    errorMessage = 'The request to get location timed out';
                                    break;
                            }
                            this.showToast(errorMessage, 'error');
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                } else {
                    this.showToast('Geolocation is not supported by this browser', 'error');
                }
            },
            
            saveAttendance() {
                const url = this.isEditing 
                    ? "{{ route('admin.attendance.update', ['attendance' => ':id']) }}".replace(':id', this.currentAttendance.id)
                    : "{{ route('admin.attendance.store') }}";
                
                const formData = new FormData();
                formData.append('user_id', this.currentAttendance.user_id);
                formData.append('date', this.currentAttendance.date);
                formData.append('time_in', this.currentAttendance.time_in || '');
                formData.append('time_out', this.currentAttendance.time_out || '');
                formData.append('status', this.currentAttendance.status);
                formData.append('notes', this.currentAttendance.notes || '');
                formData.append('is_regularized', this.currentAttendance.is_regularized === 'true');
                formData.append('regularization_reason', this.currentAttendance.regularization_reason || '');
                formData.append('biometric_id', this.currentAttendance.biometric_id || '');
                
                if (this.currentAttendance.latitude && this.currentAttendance.longitude) {
                    formData.append('latitude', this.currentAttendance.latitude);
                    formData.append('longitude', this.currentAttendance.longitude);
                }
                
                if (this.isEditing) {
                    formData.append('_method', 'PUT');
                }
                
                fetch(url, {
                    method: this.isEditing ? 'POST' : 'POST', // Always POST, _method handles PUT
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (this.isEditing) {
                            // Update existing attendance in the list
                            const index = this.attendances.findIndex(att => att.id === data.attendance.id);
                            if (index !== -1) {
                                this.attendances[index] = data.attendance;
                            }
                        } else {
                            // Add new attendance to the list
                            this.attendances.unshift(data.attendance);
                        }
                        
                        this.closeAttendanceModal();
                        this.showToast(data.message);
                    } else {
                        throw new Error(data.message || 'Failed to save attendance record');
                    }
                })
                .catch(error => {
                    this.showToast(error.message, 'error');
                    console.error('Error:', error);
                });
            },
            
            deleteAttendance() {
                if (!this.attendanceToDelete) return;
                
                fetch("{{ route('admin.attendance.destroy', ['attendance' => ':id']) }}".replace(':id', this.attendanceToDelete), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const index = this.attendances.findIndex(att => att.id === this.attendanceToDelete);
                        if (index !== -1) {
                            this.attendances.splice(index, 1);
                        }
                        this.showToast(data.message);
                    } else {
                        throw new Error(data.message || 'Failed to delete attendance record');
                    }
                })
                .catch(error => {
                    this.showToast(error.message, 'error');
                    console.error('Error:', error);
                })
                .finally(() => {
                    this.closeDeleteModal();
                });
            },
            
            closeAttendanceModal() {
                this.isAttendanceModalOpen = false;
                const modal = document.querySelector('[x-show="isAttendanceModalOpen"]');
                if (modal) modal.style.display = 'none';
            },
            
            closeDeleteModal() {
                this.isDeleteModalOpen = false;
                this.attendanceToDelete = null;
                const modal = document.querySelector('[x-show="isDeleteModalOpen"]');
                if (modal) modal.style.display = 'none';
            },
            
            closeViewModal() {
                this.isViewModalOpen = false;
                const modal = document.querySelector('[x-show="isViewModalOpen"]');
                if (modal) modal.style.display = 'none';
            },
            
            closeFingerprintModal() {
                this.isFingerprintModalOpen = false;
                const modal = document.querySelector('[x-show="isFingerprintModalOpen"]');
                if (modal) modal.style.display = 'none';
            },
            
            showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white flex items-center ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' :
                    type === 'info' ? 'bg-blue-500' : 'bg-gray-500'
                } z-50 transition-all duration-300 ease-in-out`;
                
                const icon = document.createElement('i');
                icon.className = type === 'success' ? 'fas fa-check-circle mr-2' : 
                                type === 'error' ? 'fas fa-exclamation-circle mr-2' :
                                'fas fa-info-circle mr-2';
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
            
            filterAttendances() {
                // Reset to first page when filters change
                this.currentPage = 1;
            }
        };
    }
</script>
@endpush

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush
@endsection