@extends('layouts.admin')

@section('title', 'Leave Management')

@section('content')
    <style>
        /* Animation Styles */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        /* Card Styles */
        .leave-card {
            transition: all 0.3s ease;
        }
        
        .leave-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 9999px;
            font-weight: 500;
        }
        
        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background-color: #fef9c3;
            color: #854d0e;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-cancelled {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        
        /* Leave Type Badges */
        .type-vacation {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .type-sick {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        
        .type-emergency {
            background-color: #ffedd5;
            color: #9a3412;
        }
        
        .type-maternity {
            background-color: #fce7f3;
            color: #9d174d;
        }
        
        .type-paternity {
            background-color: #cffafe;
            color: #155e75;
        }
        
        /* Scrollbar Styles */
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        /* Compact Table Styles */
        .compact-table th, .compact-table td {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
        
        .compact-table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-grid {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .filter-grid > div {
                width: 100%;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>

<!-- Main Content -->
<main class="p-4">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 stats-grid">
        <!-- Total Leaves Card -->
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <i class="fas fa-calendar-check text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Leaves</p>
                <h3 class="text-2xl font-bold">142</h3>
                <p class="text-xs text-blue-600">+12% from last month</p>
            </div>
        </div>
        
        <!-- Pending Approval Card -->
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in" style="animation-delay: 0.1s">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Pending Approval</p>
                <h3 class="text-2xl font-bold">23</h3>
                <p class="text-xs text-yellow-600">5 new today</p>
            </div>
        </div>
        
        <!-- Approved Card -->
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in" style="animation-delay: 0.2s">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Approved</p>
                <h3 class="text-2xl font-bold">98</h3>
                <p class="text-xs text-green-600">92% approval rate</p>
            </div>
        </div>
        
        <!-- Rejected Card -->
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in" style="animation-delay: 0.3s">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Rejected</p>
                <h3 class="text-2xl font-bold">21</h3>
                <p class="text-xs text-red-600">8% rejection rate</p>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 filter-grid">
            <div class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <div class="relative flex-grow">
                    <input type="text" placeholder="Search employee..." class="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <select class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="vacation">Vacation</option>
                    <option value="sick">Sick</option>
                    <option value="emergency">Emergency</option>
                    <option value="maternity">Maternity</option>
                    <option value="paternity">Paternity</option>
                </select>
            </div>
            <div class="flex gap-2 action-buttons">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
                <button id="addLeaveBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i> Add Leave
                </button>
            </div>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 compact-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left">Employee</th>
                        <th scope="col" class="px-4 py-3 text-left">Leave Type</th>
                        <th scope="col" class="px-4 py-3 text-left">Dates</th>
                        <th scope="col" class="px-4 py-3 text-left">Duration</th>
                        <th scope="col" class="px-4 py-3 text-left">Reason</th>
                        <th scope="col" class="px-4 py-3 text-left">Status</th>
                        <th scope="col" class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Leave Request 1 -->
                    <tr class="leave-card hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/40x40" alt="">
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Juan Dela Cruz</div>
                                    <div class="text-xs text-gray-500">EMP-001</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge type-vacation">Vacation</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">May 15 - May 20, 2023</div>
                            <div class="text-xs text-gray-500">5 days</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            5 working days
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900 truncate max-w-xs">Family vacation out of town</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge status-approved">Approved</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Leave Request 2 -->
                    <tr class="leave-card hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/40x40" alt="">
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Maria Santos</div>
                                    <div class="text-xs text-gray-500">EMP-002</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge type-sick">Sick</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">June 1 - June 3, 2023</div>
                            <div class="text-xs text-gray-500">3 days</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            3 working days
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900 truncate max-w-xs">Flu with high fever</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge status-pending">Pending</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- More leave request rows would be dynamically generated here -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">23</span> results
            </div>
            <div class="flex space-x-1">
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                    Previous
                </button>
                <button class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                    1
                </button>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                    2
                </button>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                    Next
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Add Leave Modal -->
<div id="addLeaveModal" class="fixed inset-0 overflow-y-auto z-50 hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Leave</h3>
                            <button id="closeModalBtn" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <form>
                                <div class="mb-4">
                                    <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                                    <select id="employee" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>Select employee</option>
                                        <option>Juan Dela Cruz</option>
                                        <option>Maria Santos</option>
                                        <option>Pedro Reyes</option>
                                        <option>Ana Lopez</option>
                                        <option>Carlos Garcia</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="leaveType" class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                                    <select id="leaveType" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>Select leave type</option>
                                        <option>Vacation</option>
                                        <option>Sick</option>
                                        <option>Emergency</option>
                                        <option>Maternity</option>
                                        <option>Paternity</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" id="startDate" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    </div>
                                    <div>
                                        <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                        <input type="date" id="endDate" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (days)</label>
                                    <input type="number" id="duration" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Enter duration">
                                </div>
                                <div class="mb-4">
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                    <textarea id="reason" rows="3" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Enter reason for leave"></textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>Pending</option>
                                        <option>Approved</option>
                                        <option>Rejected</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save Leave
                </button>
                <button id="cancelModalBtn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>




@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const addLeaveBtn = document.getElementById('addLeaveBtn');
    const addLeaveModal = document.getElementById('addLeaveModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');

    if (addLeaveBtn && addLeaveModal) {
        addLeaveBtn.addEventListener('click', () => {
            addLeaveModal.classList.remove('hidden');
        });

        closeModalBtn.addEventListener('click', () => {
            addLeaveModal.classList.add('hidden');
        });

        cancelModalBtn.addEventListener('click', () => {
            addLeaveModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        addLeaveModal.addEventListener('click', (e) => {
            if (e.target === addLeaveModal) {
                addLeaveModal.classList.add('hidden');
            }
        });
    }

    // Calculate duration when dates change
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const durationInput = document.getElementById('duration');

    if (startDateInput && endDateInput && durationInput) {
        startDateInput.addEventListener('change', calculateDuration);
        endDateInput.addEventListener('change', calculateDuration);
    }

    function calculateDuration() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            // Calculate difference in days
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end dates
            
            durationInput.value = diffDays;
        }
    }
});
</script>
@endpush

@endsection