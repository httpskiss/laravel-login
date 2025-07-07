@extends('layouts.admin')

@section('title', 'Travel Order')

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
        .travel-card {
            transition: all 0.3s ease;
        }
        
        .travel-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        /* Status Badges */
        .status-badge {
            @apply px-2 py-1 text-xs rounded-full font-medium;
        }
        
        .status-approved {
            @apply bg-green-100 text-green-800;
        }
        
        .status-pending {
            @apply bg-yellow-100 text-yellow-800;
        }
        
        .status-rejected {
            @apply bg-red-100 text-red-800;
        }
        
        .status-completed {
            @apply bg-blue-100 text-blue-800;
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

<main class="p-4">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Total Requests</p>
                    <h3 class="text-2xl font-bold text-gray-800">48</h3>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <i class="fas fa-plane"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2"><span class="text-green-500">+12%</span> from last month</p>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <h3 class="text-2xl font-bold text-gray-800">12</h3>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2"><span class="text-red-500">+3</span> new today</p>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Approved</p>
                    <h3 class="text-2xl font-bold text-gray-800">28</h3>
                </div>
                <div class="p-2 bg-green-100 rounded-lg text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2"><span class="text-green-500">+8</span> this week</p>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Rejected</p>
                    <h3 class="text-2xl font-bold text-gray-800">8</h3>
                </div>
                <div class="p-2 bg-red-100 rounded-lg text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2"><span class="text-red-500">+2</span> this month</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
         <!-- Travel Requests List -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h3 class="font-medium text-gray-800">Recent Travel Requests</h3>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium">
                            <i class="fas fa-plus"></i> New Request
                        </button>
                        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm font-medium">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm font-medium">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto custom-scrollbar">
                <!-- Travel Request Card - Pending -->
                <div class="travel-card p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full animate-pulse">Pending</span>
                                <span class="text-xs text-gray-500">#TRV-2023-0015</span>
                            </div>
                            <h4 class="font-medium text-gray-800">International Conference on Education</h4>
                            <p class="text-sm text-gray-600">Dr. Maria Santos</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">Nov 15-18, 2023</p>
                            <p class="text-xs text-gray-500">3 days</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i> Tokyo, Japan
                            </span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-dollar-sign mr-1"></i> 2,500
                            </span>
                        </div>
                        <button class="text-xs text-blue-600 hover:text-blue-800">
                            View Details <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Travel Request Card - Approved -->
                <div class="travel-card p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                                <span class="text-xs text-gray-500">#TRV-2023-0014</span>
                            </div>
                            <h4 class="font-medium text-gray-800">Research Collaboration Meeting</h4>
                            <p class="text-sm text-gray-600">Prof. Juan Dela Cruz</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">Nov 8-10, 2023</p>
                            <p class="text-xs text-gray-500">2 days</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i> Manila, Philippines
                            </span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-dollar-sign mr-1"></i> 800
                            </span>
                        </div>
                        <button class="text-xs text-blue-600 hover:text-blue-800">
                            View Details <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Travel Request Card - Rejected -->
                <div class="travel-card p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Rejected</span>
                                <span class="text-xs text-gray-500">#TRV-2023-0013</span>
                            </div>
                            <h4 class="font-medium text-gray-800">Workshop on Digital Learning</h4>
                            <p class="text-sm text-gray-600">Dr. Anna Reyes</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">Oct 28-30, 2023</p>
                            <p class="text-xs text-gray-500">2 days</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i> Cebu, Philippines
                            </span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-dollar-sign mr-1"></i> 1,200
                            </span>
                        </div>
                        <button class="text-xs text-blue-600 hover:text-blue-800">
                            View Details <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Travel Request Card - Completed -->
                <div class="travel-card p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Completed</span>
                                <span class="text-xs text-gray-500">#TRV-2023-0012</span>
                            </div>
                            <h4 class="font-medium text-gray-800">Faculty Exchange Program</h4>
                            <p class="text-sm text-gray-600">Prof. Robert Lim</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">Oct 15-22, 2023</p>
                            <p class="text-xs text-gray-500">7 days</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i> Singapore
                            </span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-dollar-sign mr-1"></i> 3,000
                            </span>
                        </div>
                        <button class="text-xs text-blue-600 hover:text-blue-800">
                            View Details <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Travel Request Card - Pending -->
                <div class="travel-card p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full animate-pulse">Pending</span>
                                <span class="text-xs text-gray-500">#TRV-2023-0011</span>
                            </div>
                            <h4 class="font-medium text-gray-800">Curriculum Development Workshop</h4>
                            <p class="text-sm text-gray-600">Dr. Susan Tan</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">Dec 5-7, 2023</p>
                            <p class="text-xs text-gray-500">2 days</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i> Davao, Philippines
                            </span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                <i class="fas fa-dollar-sign mr-1"></i> 900
                            </span>
                        </div>
                        <button class="text-xs text-blue-600 hover:text-blue-800">
                            View Details <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 text-center">
                <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View All Travel Requests <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Travel Details and Actions -->
        <div class="space-y-6">
            <!-- Travel Calendar -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-gray-800">Travel Calendar</h3>
                    <button class="text-sm text-blue-600 hover:text-blue-800">View All</button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-12 flex-shrink-0 text-center">
                            <div class="text-xs font-medium text-gray-500">NOV</div>
                            <div class="text-lg font-bold text-gray-800">15</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800">International Conference</p>
                            <p class="text-xs text-gray-500">Tokyo, Japan • Dr. Maria Santos</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-12 flex-shrink-0 text-center">
                            <div class="text-xs font-medium text-gray-500">NOV</div>
                            <div class="text-lg font-bold text-gray-800">8</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800">Research Meeting</p>
                            <p class="text-xs text-gray-500">Manila, Philippines • Prof. Juan Dela Cruz</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-12 flex-shrink-0 text-center">
                            <div class="text-xs font-medium text-gray-500">DEC</div>
                            <div class="text-lg font-bold text-gray-800">5</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800">Curriculum Workshop</p>
                            <p class="text-xs text-gray-500">Davao, Philippines • Dr. Susan Tan</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Travel Map Preview -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-medium text-gray-800">Upcoming Travel Locations</h3>
                </div>
                <div class="travel-map p-4 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500 text-sm">Interactive travel map</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-medium text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                    <button class="p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition flex flex-col items-center">
                        <i class="fas fa-check-circle text-xl mb-1"></i>
                        <span class="text-xs">Approve</span>
                    </button>
                    <button class="p-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition flex flex-col items-center">
                        <i class="fas fa-times-circle text-xl mb-1"></i>
                        <span class="text-xs">Reject</span>
                    </button>
                    <button class="p-3 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition flex flex-col items-center">
                        <i class="fas fa-file-invoice-dollar text-xl mb-1"></i>
                        <span class="text-xs">Expenses</span>
                    </button>
                    <button class="p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition flex flex-col items-center">
                        <i class="fas fa-file-export text-xl mb-1"></i>
                        <span class="text-xs">Reports</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Travel Details Modal (hidden by default) -->
    <div id="travelDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">International Conference on Education</h3>
                        <p class="text-gray-600">#TRV-2023-0015 • Submitted on Oct 25, 2023</p>
                    </div>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Traveler Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-3">Traveler Information</h4>
                        <div class="flex items-start space-x-3 mb-3">
                            <img src="https://via.placeholder.com/48x48" alt="Profile" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-medium text-gray-800">Dr. Maria Santos</p>
                                <p class="text-sm text-gray-500">Professor, College of Education</p>
                                <p class="text-sm text-gray-500">msantos@bipsu.edu.ph</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Employee ID:</span>
                                <span class="text-gray-800">EMP-2020-0142</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Department:</span>
                                <span class="text-gray-800">Education</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Position:</span>
                                <span class="text-gray-800">Full Professor</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Travel Details -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-3">Travel Details</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Destination</p>
                                <p class="text-sm font-medium text-gray-800">Tokyo, Japan</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Purpose</p>
                                <p class="text-sm font-medium text-gray-800">Present research paper at international conference</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-xs text-gray-500">Start Date</p>
                                    <p class="text-sm font-medium text-gray-800">Nov 15, 2023</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">End Date</p>
                                    <p class="text-sm font-medium text-gray-800">Nov 18, 2023</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Duration</p>
                                <p class="text-sm font-medium text-gray-800">3 days</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Financial Details -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-3">Financial Details</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Estimated Cost</p>
                                <p class="text-sm font-medium text-gray-800">$2,500.00</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Funding Source</p>
                                <p class="text-sm font-medium text-gray-800">University Research Grant</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Advance Requested</p>
                                <p class="text-sm font-medium text-gray-800">$1,500.00</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Expense Report</p>
                                <p class="text-sm font-medium text-gray-800">Not submitted yet</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Travel Timeline -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-800 mb-3">Travel Timeline</h4>
                    <div class="space-y-3">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm font-medium text-gray-800">Travel request submitted</p>
                            <p class="text-xs text-gray-500">Oct 25, 2023 • 10:15 AM</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm font-medium text-gray-800">Department head reviewed</p>
                            <p class="text-xs text-gray-500">Oct 27, 2023 • 2:30 PM</p>
                            <p class="text-xs text-gray-500 mt-1">Recommended for approval</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm font-medium text-gray-800">Pending final approval</p>
                            <p class="text-xs text-gray-500">Waiting for HR director</p>
                        </div>
                    </div>
                </div>
                
                <!-- Documents and Attachments -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-800 mb-3">Documents & Attachments</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Conference Invitation</p>
                                    <p class="text-xs text-gray-500">PDF • 245 KB</p>
                                </div>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-file-word text-blue-500 text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Research Abstract</p>
                                    <p class="text-xs text-gray-500">DOCX • 128 KB</p>
                                </div>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-file-excel text-green-500 text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Budget Breakdown</p>
                                    <p class="text-xs text-gray-500">XLSX • 89 KB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap justify-end gap-3 pt-4 border-t border-gray-200">
                    <button class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-download mr-2"></i> Download Documents
                    </button>
                    <button class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-times mr-2"></i> Reject Request
                    </button>
                    <button class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                        <i class="fas fa-check mr-2"></i> Approve Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Travel details modal functionality
        const travelCards = document.querySelectorAll('.travel-card');
        const travelDetailsModal = document.getElementById('travelDetailsModal');
        const closeModalBtn = document.getElementById('closeModal');

        if (travelCards.length && travelDetailsModal && closeModalBtn) {
            // Open modal when clicking on a travel card
            travelCards.forEach(card => {
                card.addEventListener('click', function() {
                    travelDetailsModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            });

            // Close modal when clicking close button
            closeModalBtn.addEventListener('click', function() {
                travelDetailsModal.classList.add('hidden');
                document.body.style.overflow = '';
            });

            // Close modal when clicking outside
            travelDetailsModal.addEventListener('click', function(e) {
                if (e.target === travelDetailsModal) {
                    travelDetailsModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
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
@endpush

@endsection