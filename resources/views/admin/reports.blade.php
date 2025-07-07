@extends('layouts.admin')

@section('title', 'Generate Reports')

@section('content')

<main class="p-6">
<!-- Quick Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="report-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Employees</p>
                <h3 class="text-2xl font-bold text-gray-800">1,245</h3>
                <p class="text-green-500 text-sm mt-1">
                    <i class="fas fa-arrow-up mr-1"></i> 5.2% from last month
                </p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-users text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="report-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Present Today</p>
                <h3 class="text-2xl font-bold text-gray-800">892</h3>
                <p class="text-green-500 text-sm mt-1">
                    <i class="fas fa-arrow-up mr-1"></i> 3.1% from yesterday
                </p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-user-check text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="report-card bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">On Leave</p>
                <h3 class="text-2xl font-bold text-gray-800">78</h3>
                <p class="text-red-500 text-sm mt-1">
                    <i class="fas fa-arrow-down mr-1"></i> 2.4% from last week
                </p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-calendar-minus text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="report-card bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Absent Today</p>
                <h3 class="text-2xl font-bold text-gray-800">45</h3>
                <p class="text-green-500 text-sm mt-1">
                    <i class="fas fa-arrow-down mr-1"></i> 1.8% from yesterday
                </p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-user-times text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Reports Section -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Generate Reports</h2>
            <button id="toggleFilters" class="flex items-center text-blue-600 hover:text-blue-800">
                <i class="fas fa-filter mr-2"></i> Filters
            </button>
        </div>
    </div>
    
    <!-- Filter Dropdown -->
    <div id="filterDropdown" class="filter-dropdown bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Attendance Summary</option>
                    <option>Leave Summary</option>
                    <option>Payroll Summary</option>
                    <option>Employee Directory</option>
                    <option>Custom Report</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Today</option>
                    <option>This Week</option>
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>Custom Range</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>All Departments</option>
                    <option>Administration</option>
                    <option>Faculty</option>
                    <option>IT Department</option>
                    <option>Finance</option>
                    <option>Human Resources</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4 flex justify-end space-x-3">
            <button class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                Reset
            </button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Apply Filters
            </button>
        </div>
    </div>
    
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Attendance Report Card -->
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-calendar-check text-blue-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Attendance Summary</h3>
                        <p class="text-sm text-gray-600 mt-1">Daily, weekly or monthly attendance reports</p>
                        <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Generate Report <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Leave Report Card -->
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-calendar-minus text-green-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Leave Summary</h3>
                        <p class="text-sm text-gray-600 mt-1">Leave balances and utilization reports</p>
                        <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Generate Report <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Payroll Report Card -->
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <i class="fas fa-money-bill-wave text-purple-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Payroll Summary</h3>
                        <p class="text-sm text-gray-600 mt-1">Salary, deductions and tax reports</p>
                        <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Generate Report <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Employee Directory Card -->
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start">
                    <div class="bg-yellow-100 p-3 rounded-full mr-4">
                        <i class="fas fa-users text-yellow-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Employee Directory</h3>
                        <p class="text-sm text-gray-600 mt-1">Complete employee listing with details</p>
                        <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Generate Report <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Custom Report Card -->
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <i class="fas fa-cog text-red-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Custom Report</h3>
                        <p class="text-sm text-gray-600 mt-1">Create your own custom report</p>
                        <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Generate Report <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Export Options Card -->
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start">
                    <div class="bg-indigo-100 p-3 rounded-full mr-4">
                        <i class="fas fa-file-export text-indigo-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Export Options</h3>
                        <p class="text-sm text-gray-600 mt-1">Export data in various formats</p>
                        <div class="mt-3 flex space-x-2">
                            <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
                                <i class="fas fa-file-excel text-green-600 mr-1"></i> Excel
                            </button>
                            <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
                                <i class="fas fa-file-pdf text-red-600 mr-1"></i> PDF
                            </button>
                            <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
                                <i class="fas fa-file-csv text-blue-600 mr-1"></i> CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Attendance Trend Chart -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Monthly Attendance Trend</h2>
            <select class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>Last 6 Months</option>
                <option>Last Year</option>
                <option>Custom Range</option>
            </select>
        </div>
        <div class="chart-container">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
    
    <!-- Leave Distribution Chart -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Leave Type Distribution</h2>
            <select class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>This Year</option>
                <option>Last Year</option>
                <option>All Time</option>
            </select>
        </div>
        <div class="chart-container">
            <canvas id="leaveChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Reports Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Recently Generated Reports</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Report Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Generated By
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date Generated
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-excel text-blue-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Attendance Summary - June 2023</div>
                                <div class="text-sm text-gray-500">Excel</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Admin User</div>
                        <div class="text-sm text-gray-500">HR Department</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Jun 30, 2023</div>
                        <div class="text-sm text-gray-500">10:45 AM</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Completed
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-pdf text-red-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Payroll Report - May 2023</div>
                                <div class="text-sm text-gray-500">PDF</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Finance Admin</div>
                        <div class="text-sm text-gray-500">Finance Department</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">May 31, 2023</div>
                        <div class="text-sm text-gray-500">4:30 PM</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Completed
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-csv text-green-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Employee Directory</div>
                                <div class="text-sm text-gray-500">CSV</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">IT Admin</div>
                        <div class="text-sm text-gray-500">IT Department</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Apr 15, 2023</div>
                        <div class="text-sm text-gray-500">9:15 AM</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Completed
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-excel text-yellow-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Leave Balance Report</div>
                                <div class="text-sm text-gray-500">Excel</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">HR Manager</div>
                        <div class="text-sm text-gray-500">HR Department</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Mar 28, 2023</div>
                        <div class="text-sm text-gray-500">2:00 PM</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Completed
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-pdf text-blue-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Annual Report 2022</div>
                                <div class="text-sm text-gray-500">PDF</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Admin User</div>
                        <div class="text-sm text-gray-500">HR Department</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Jan 15, 2023</div>
                        <div class="text-sm text-gray-500">11:30 AM</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Completed
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
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
                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">24</span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        1
                    </a>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        2
                    </a>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        3
                    </a>
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        8
                    </a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
</main>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle profile dropdown toggle
            const dropdown = document.querySelector('.dropdown');
            if (dropdown) {
                dropdown.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown-menu')) return;
                    this.querySelector('.dropdown-menu').classList.toggle('hidden');
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    const openDropdown = document.querySelector('.dropdown-menu:not(.hidden)');
                    if (openDropdown) openDropdown.classList.add('hidden');
                }
            });

            // Toggle Sidebar Functionality
            const toggleSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = document.getElementById('toggleSidebar');
                
                if (!sidebar || !toggleBtn) return;
                
                sidebar.classList.toggle('collapsed');
                
                const icon = toggleBtn.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                    toggleBtn.querySelector('.nav-text').textContent = 'Expand';
                } else {
                    icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
                    toggleBtn.querySelector('.nav-text').textContent = 'Collapse';
                }
                
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            };
            
            // Mobile sidebar toggle
            const toggleMobileSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('mobile-open');
                
                // On mobile, we don't want the collapsed state when opening
                if (sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('collapsed');
                }
            };
            
            // Toggle filter dropdown
            const toggleFilters = document.getElementById('toggleFilters');
            const filterDropdown = document.getElementById('filterDropdown');
            
            if (toggleFilters && filterDropdown) {
                toggleFilters.addEventListener('click', function() {
                    filterDropdown.classList.toggle('open');
                });
            }

            // Initialize sidebar state from localStorage
            const initializeSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = document.getElementById('toggleSidebar');
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                
                if (!sidebar || !toggleBtn) return;
                
                // Only apply collapsed state on desktop
                if (window.innerWidth > 768) {
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        const icon = toggleBtn.querySelector('i');
                        icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                        toggleBtn.querySelector('.nav-text').textContent = 'Expand';
                    }
                }
                
                // Set up event listeners
                toggleBtn.addEventListener('click', toggleSidebar);
                
                if (mobileMenuBtn) {
                    mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
                }
                
                // Close mobile sidebar when clicking outside
                document.addEventListener('click', function(e) {
                    const sidebar = document.getElementById('sidebar');
                    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                    
                    if (window.innerWidth <= 768 && 
                        !e.target.closest('#sidebar') && 
                        !e.target.closest('#mobileMenuBtn') &&
                        sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    }
                });
            };
            
            // Handle window resize
            const handleResize = () => {
                const sidebar = document.getElementById('sidebar');
                
                if (window.innerWidth > 768) {
                    // Desktop - remove mobile-open class if it exists
                    sidebar.classList.remove('mobile-open');
                } else {
                    // Mobile - ensure sidebar is hidden by default
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('collapsed');
                    }
                }
            };
            
            // Initialize on page load
            initializeSidebar();
            
            // Add resize event listener
            window.addEventListener('resize', handleResize);

            // Initialize charts
            const initCharts = () => {
                // Attendance Trend Chart
                const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
                const attendanceChart = new Chart(attendanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [
                            {
                                label: 'Present',
                                data: [85, 82, 88, 87, 90, 92],
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Absent',
                                data: [5, 8, 4, 3, 2, 3],
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Late',
                                data: [10, 10, 8, 10, 8, 5],
                                borderColor: '#F59E0B',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });

                // Leave Distribution Chart
                const leaveCtx = document.getElementById('leaveChart').getContext('2d');
                const leaveChart = new Chart(leaveCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Vacation', 'Sick', 'Maternity', 'Paternity', 'Bereavement', 'Others'],
                        datasets: [{
                            data: [35, 25, 15, 5, 10, 10],
                            backgroundColor: [
                                '#3B82F6',
                                '#10B981',
                                '#F59E0B',
                                '#EF4444',
                                '#8B5CF6',
                                '#EC4899'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            };

            // Initialize charts when page loads
            initCharts();
        });
    </script>
@endpush
@endsection