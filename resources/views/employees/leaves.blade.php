@extends('layouts.employee')

@section('title', 'My Leave Requests')

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
        
        /* Academic Leave Types */
        .type-conference {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        
        .type-research {
            background-color: #ecfccb;
            color: #3f6212;
        }
        
        .type-sabbatical {
            background-color: #f5f5f4;
            color: #57534e;
        }
        
        /* Leave Balance Cards */
        .balance-card {
            border-left: 4px solid;
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
    <!-- Leave Balances -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 stats-grid">
        <!-- Vacation Leave Balance -->
        <div class="bg-white rounded-lg shadow p-4 balance-card border-blue-500 leave-card animate-fade-in">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Vacation Leave</p>
                    <h3 class="text-2xl font-bold">15</h3>
                    <p class="text-xs text-gray-500">Days remaining</p>
                </div>
                <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-umbrella-beach text-lg"></i>
                </div>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
            </div>
        </div>
        
        <!-- Sick Leave Balance -->
        <div class="bg-white rounded-lg shadow p-4 balance-card border-purple-500 leave-card animate-fade-in" style="animation-delay: 0.1s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Sick Leave</p>
                    <h3 class="text-2xl font-bold">10</h3>
                    <p class="text-xs text-gray-500">Days remaining</p>
                </div>
                <div class="p-2 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-procedures text-lg"></i>
                </div>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-purple-600 h-2 rounded-full" style="width: 90%"></div>
            </div>
        </div>
        
        <!-- Emergency Leave Balance -->
        <div class="bg-white rounded-lg shadow p-4 balance-card border-orange-500 leave-card animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Emergency Leave</p>
                    <h3 class="text-2xl font-bold">5</h3>
                    <p class="text-xs text-gray-500">Days remaining</p>
                </div>
                <div class="p-2 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-orange-600 h-2 rounded-full" style="width: 100%"></div>
            </div>
        </div>
        
        <!-- Academic Leave Balance -->
        <div class="bg-white rounded-lg shadow p-4 balance-card border-green-500 leave-card animate-fade-in" style="animation-delay: 0.3s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Academic Leave</p>
                    <h3 class="text-2xl font-bold">30</h3>
                    <p class="text-xs text-gray-500">Days remaining</p>
                </div>
                <div class="p-2 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-graduation-cap text-lg"></i>
                </div>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: 60%"></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-col md:flex-row md:items-center gap-3 filter-grid">
            <div class="relative flex-grow">
                <input type="text" placeholder="Search my leave requests..." class="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                <option value="conference">Conference</option>
                <option value="research">Research</option>
                <option value="sabbatical">Sabbatical</option>
            </select>
            <select class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Years</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
            </select>
            <button id="requestLeaveBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i> Request Leave
            </button>
        </div>
    </div>

    <!-- My Leave Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left">Leave Type</th>
                        <th scope="col" class="px-4 py-3 text-left">Dates</th>
                        <th scope="col" class="px-4 py-3 text-left">Duration</th>
                        <th scope="col" class="px-4 py-3 text-left">Reason</th>
                        <th scope="col" class="px-4 py-3 text-left">Status</th>
                        <th scope="col" class="px-4 py-3 text-left">Approver</th>
                        <th scope="col" class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Leave Request 1 (Approved) -->
                    <tr class="leave-card hover:bg-gray-50">
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
                            <div class="text-xs text-gray-500 mt-1">May 10, 2023</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Rodriguez</div>
                            <div class="text-xs text-gray-500">Department Head</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Leave Request 2 (Pending) -->
                    <tr class="leave-card hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge type-conference">Conference</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">June 10 - June 15, 2023</div>
                            <div class="text-xs text-gray-500">6 days</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            4 working days
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900 truncate max-w-xs">International Academic Conference on Education</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge status-pending">Pending</span>
                            <div class="text-xs text-gray-500 mt-1">May 25, 2023</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Rodriguez</div>
                            <div class="text-xs text-gray-500">Department Head</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900 mr-3" title="Cancel Request">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Leave Request 3 (Rejected) -->
                    <tr class="leave-card hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge type-sick">Sick</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">April 5 - April 7, 2023</div>
                            <div class="text-xs text-gray-500">3 days</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            3 working days
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900 truncate max-w-xs">Medical procedure</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge status-rejected">Rejected</span>
                            <div class="text-xs text-gray-500 mt-1">April 1, 2023</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Rodriguez</div>
                            <div class="text-xs text-gray-500">Department Head</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Leave Request 4 (Sabbatical) -->
                    <tr class="leave-card hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge type-sabbatical">Sabbatical</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Aug 1 - Dec 15, 2023</div>
                            <div class="text-xs text-gray-500">4.5 months</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            Full semester
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900 truncate max-w-xs">Research sabbatical for book publication</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge status-pending">Pending</span>
                            <div class="text-xs text-gray-500 mt-1">March 15, 2023</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dean Martinez</div>
                            <div class="text-xs text-gray-500">College Dean</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900 mr-3" title="Cancel Request">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">12</span> results
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
                    3
                </button>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                    Next
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Request Leave Modal -->
<div id="requestLeaveModal" class="fixed inset-0 overflow-y-auto z-50 hidden">
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Request New Leave</h3>
                            <button id="closeRequestModalBtn" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <form id="leaveRequestForm">
                                <div class="mb-4">
                                    <label for="leaveType" class="block text-sm font-medium text-gray-700 mb-1">Leave Type *</label>
                                    <select id="leaveType" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Select leave type</option>
                                        <option value="vacation">Vacation Leave</option>
                                        <option value="sick">Sick Leave</option>
                                        <option value="emergency">Emergency Leave</option>
                                        <option value="maternity">Maternity Leave</option>
                                        <option value="paternity">Paternity Leave</option>
                                        <option value="conference">Conference/Workshop</option>
                                        <option value="research">Research Leave</option>
                                        <option value="sabbatical">Sabbatical Leave</option>
                                        <option value="study">Study Leave</option>
                                    </select>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                                        <input type="date" id="startDate" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    </div>
                                    <div>
                                        <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                                        <input type="date" id="endDate" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (days) *</label>
                                    <input type="number" id="duration" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Enter duration">
                                    <p class="text-xs text-gray-500 mt-1">Working days only (excluding weekends and holidays)</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                                    <textarea id="reason" rows="3" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Please provide details for your leave request"></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="contactNumber" class="block text-sm font-medium text-gray-700 mb-1">Contact Number During Leave *</label>
                                    <input type="tel" id="contactNumber" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="+63 912 345 6789">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Supporting Documents</label>
                                    <div class="mt-1 flex items-center">
                                        <input type="file" id="attachment" class="hidden">
                                        <label for="attachment" class="cursor-pointer bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-sm font-medium text-gray-700">
                                            <i class="fas fa-paperclip mr-2"></i>Upload File
                                        </label>
                                        <span id="fileName" class="ml-2 text-sm text-gray-500 truncate max-w-xs">No file chosen</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">For sick leave, conference, and special leaves, please attach supporting documents</p>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="acknowledge" name="acknowledge" type="checkbox" required class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="acknowledge" class="font-medium text-gray-700">I acknowledge that I have reviewed the university's leave policies and my request complies with all requirements.</label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" form="leaveRequestForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Submit Request
                </button>
                <button id="cancelRequestModalBtn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leave Details Modal -->
<div id="leaveDetailsModal" class="fixed inset-0 overflow-y-auto z-50 hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Leave Request Details</h3>
                            <button id="closeDetailsModalBtn" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <!-- Leave Details Content -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Request ID</h4>
                                    <p class="mt-1 text-sm text-gray-900">LV-2023-0042</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Date Submitted</h4>
                                    <p class="mt-1 text-sm text-gray-900">May 25, 2023 at 10:15 AM</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Leave Type</h4>
                                    <p class="mt-1 text-sm text-gray-900"><span class="status-badge type-conference">Conference/Workshop</span></p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                    <p class="mt-1 text-sm text-gray-900"><span class="status-badge status-pending">Pending</span></p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Leave Period</h4>
                                    <p class="mt-1 text-sm text-gray-900">June 10 - June 15, 2023 (6 days)</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Working Days</h4>
                                    <p class="mt-1 text-sm text-gray-900">4 days (excluding weekends)</p>
                                </div>
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-500">Reason</h4>
                                    <p class="mt-1 text-sm text-gray-900">Attending the International Academic Conference on Education in Singapore as a presenter of my research paper titled "Innovative Teaching Methods in Higher Education".</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Contact During Leave</h4>
                                    <p class="mt-1 text-sm text-gray-900">+63 912 345 6789</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Approver</h4>
                                    <p class="mt-1 text-sm text-gray-900">Dr. Maria Rodriguez (Department Head)</p>
                                </div>
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-500">Supporting Documents</h4>
                                    <div class="mt-2 flex items-center">
                                        <i class="fas fa-file-pdf text-red-500 text-xl mr-2"></i>
                                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Conference_Acceptance_Letter.pdf</a>
                                        <span class="text-xs text-gray-500 ml-2">(256 KB)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Approval Process Timeline -->
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Approval Process</h4>
                                <div class="relative">
                                    <!-- Timeline -->
                                    <div class="absolute left-4 top-0 h-full w-0.5 bg-gray-200"></div>
                                    
                                    <!-- Timeline Step 1 (Submitted) -->
                                    <div class="relative mb-6 pl-8">
                                        <div class="absolute left-0 top-0 h-4 w-4 rounded-full bg-blue-600 flex items-center justify-center">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                        <div class="flex justify-between">
                                            <h5 class="text-sm font-medium text-gray-900">Request Submitted</h5>
                                            <span class="text-xs text-gray-500">May 25, 10:15 AM</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">You submitted your leave request</p>
                                    </div>
                                    
                                    <!-- Timeline Step 2 (Department Head) -->
                                    <div class="relative mb-6 pl-8">
                                        <div class="absolute left-0 top-0 h-4 w-4 rounded-full bg-yellow-500 flex items-center justify-center">
                                            <i class="fas fa-clock text-white text-xs"></i>
                                        </div>
                                        <div class="flex justify-between">
                                            <h5 class="text-sm font-medium text-gray-900">Department Head Review</h5>
                                            <span class="text-xs text-gray-500">Pending</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Awaiting approval from Dr. Rodriguez</p>
                                    </div>
                                    
                                    <!-- Timeline Step 3 (Dean - only for long leaves) -->
                                    <div class="relative mb-6 pl-8 hidden" id="deanApprovalStep">
                                        <div class="absolute left-0 top-0 h-4 w-4 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-clock text-gray-500 text-xs"></i>
                                        </div>
                                        <div class="flex justify-between">
                                            <h5 class="text-sm font-medium text-gray-900">Dean Approval</h5>
                                            <span class="text-xs text-gray-500">Not required</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Only required for leaves over 2 weeks</p>
                                    </div>
                                    
                                    <!-- Timeline Step 4 (HR) -->
                                    <div class="relative pl-8">
                                        <div class="absolute left-0 top-0 h-4 w-4 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-clock text-gray-500 text-xs"></i>
                                        </div>
                                        <div class="flex justify-between">
                                            <h5 class="text-sm font-medium text-gray-900">HR Processing</h5>
                                            <span class="text-xs text-gray-500">Pending</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Final processing by HR after approvals</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    <i class="fas fa-print mr-2"></i> Print Approval
                </button>
                <button id="cancelDetailsModalBtn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const requestLeaveBtn = document.getElementById('requestLeaveBtn');
    const requestLeaveModal = document.getElementById('requestLeaveModal');
    const closeRequestModalBtn = document.getElementById('closeRequestModalBtn');
    const cancelRequestModalBtn = document.getElementById('cancelRequestModalBtn');
    
    const leaveDetailsModal = document.getElementById('leaveDetailsModal');
    const closeDetailsModalBtn = document.getElementById('closeDetailsModalBtn');
    const cancelDetailsModalBtn = document.getElementById('cancelDetailsModalBtn');
    
    // View buttons for leave details
    const viewDetailsButtons = document.querySelectorAll('[title="View Details"]');

    // Request Leave Modal
    if (requestLeaveBtn && requestLeaveModal) {
        requestLeaveBtn.addEventListener('click', () => {
            requestLeaveModal.classList.remove('hidden');
        });

        closeRequestModalBtn.addEventListener('click', () => {
            requestLeaveModal.classList.add('hidden');
        });

        cancelRequestModalBtn.addEventListener('click', () => {
            requestLeaveModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        requestLeaveModal.addEventListener('click', (e) => {
            if (e.target === requestLeaveModal) {
                requestLeaveModal.classList.add('hidden');
            }
        });
    }

    // Leave Details Modal
    if (viewDetailsButtons.length > 0) {
        viewDetailsButtons.forEach(button => {
            button.addEventListener('click', () => {
                leaveDetailsModal.classList.remove('hidden');
            });
        });

        closeDetailsModalBtn.addEventListener('click', () => {
            leaveDetailsModal.classList.add('hidden');
        });

        cancelDetailsModalBtn.addEventListener('click', () => {
            leaveDetailsModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        leaveDetailsModal.addEventListener('click', (e) => {
            if (e.target === leaveDetailsModal) {
                leaveDetailsModal.classList.add('hidden');
            }
        });
    }

    // File upload display
    const fileInput = document.getElementById('attachment');
    const fileNameDisplay = document.getElementById('fileName');

    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = 'No file chosen';
            }
        });
    }

    // Calculate duration when dates change in request form
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
    
    // Form submission
    const leaveRequestForm = document.getElementById('leaveRequestForm');
    if (leaveRequestForm) {
        leaveRequestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would typically send the form data to the server
            // For demonstration, we'll just close the modal and show a success message
            requestLeaveModal.classList.add('hidden');
            
            // Show success notification
            alert('Your leave request has been submitted successfully!');
        });
    }
});
</script>
@endpush

@endsection