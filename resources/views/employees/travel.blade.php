@extends('layouts.employees')

@section('title', 'My Travel Orders')

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
    <!-- Stats Cards (Employee Perspective) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Total Requests</p>
                    <h3 class="text-2xl font-bold text-gray-800">7</h3>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <i class="fas fa-plane"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Your travel history</p>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <h3 class="text-2xl font-bold text-gray-800">2</h3>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Awaiting approval</p>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Approved</p>
                    <h3 class="text-2xl font-bold text-gray-800">4</h3>
                </div>
                <div class="p-2 bg-green-100 rounded-lg text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Upcoming travels</p>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Rejected</p>
                    <h3 class="text-2xl font-bold text-gray-800">1</h3>
                </div>
                <div class="p-2 bg-red-100 rounded-lg text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Needs revision</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Travel Requests List -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium text-gray-800">My Recent Travel Requests</h3>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center text-sm">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <button class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center text-sm">
                        <i class="fas fa-download mr-1"></i> Export
                    </button>
                    <button id="newRequestBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-plus mr-2"></i> New Travel Request
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
                            <p class="text-sm text-gray-600">Waiting for HR approval</p>
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
                            <p class="text-sm text-gray-600">Approved by HR Director</p>
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
                            <p class="text-sm text-gray-600">Insufficient budget</p>
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
                        <div>
                            <button class="text-xs text-blue-600 hover:text-blue-800 mr-3">
                                Edit & Resubmit
                            </button>
                            <button class="text-xs text-blue-600 hover:text-blue-800">
                                View Details <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
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
                            <p class="text-sm text-gray-600">Expense report submitted</p>
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
            </div>
            <div class="p-4 border-t border-gray-200 text-center">
                <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View All My Travel Requests <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Upcoming Travels -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-gray-800">My Upcoming Travels</h3>
                    <button class="text-sm text-blue-600 hover:text-blue-800">View All</button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-12 flex-shrink-0 text-center">
                            <div class="text-xs font-medium text-gray-500">NOV</div>
                            <div class="text-lg font-bold text-gray-800">8</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800">Research Meeting</p>
                            <p class="text-xs text-gray-500">Manila, Philippines • 2 days</p>
                            <span class="inline-block mt-1 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-12 flex-shrink-0 text-center">
                            <div class="text-xs font-medium text-gray-500">NOV</div>
                            <div class="text-lg font-bold text-gray-800">15</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800">International Conference</p>
                            <p class="text-xs text-gray-500">Tokyo, Japan • 3 days</p>
                            <span class="inline-block mt-1 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full animate-pulse">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-medium text-gray-800 mb-4">Quick Links</h3>
                <div class="space-y-2">
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-file-alt text-blue-500 mr-3"></i>
                        <span>Travel Policy Document</span>
                    </a>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-receipt text-green-500 mr-3"></i>
                        <span>Expense Report Form</span>
                    </a>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-question-circle text-purple-500 mr-3"></i>
                        <span>Travel FAQs</span>
                    </a>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-phone-alt text-orange-500 mr-3"></i>
                        <span>Contact Travel Office</span>
                    </a>
                </div>
            </div>
            
            <!-- Travel Checklist -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-medium text-gray-800 mb-4">Pre-Travel Checklist</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <input type="checkbox" id="check1" class="mt-1 mr-3">
                        <label for="check1" class="text-sm text-gray-700">Review university travel policy</label>
                    </div>
                    <div class="flex items-start">
                        <input type="checkbox" id="check2" class="mt-1 mr-3">
                        <label for="check2" class="text-sm text-gray-700">Book flights/accommodation</label>
                    </div>
                    <div class="flex items-start">
                        <input type="checkbox" id="check3" class="mt-1 mr-3">
                        <label for="check3" class="text-sm text-gray-700">Register with embassy (if international)</label>
                    </div>
                    <div class="flex items-start">
                        <input type="checkbox" id="check4" class="mt-1 mr-3">
                        <label for="check4" class="text-sm text-gray-700">Obtain travel insurance</label>
                    </div>
                    <div class="flex items-start">
                        <input type="checkbox" id="check5" class="mt-1 mr-3">
                        <label for="check5" class="text-sm text-gray-700">Prepare expense documentation</label>
                    </div>
                </div>
                <button class="mt-3 text-sm text-blue-600 hover:text-blue-800">
                    View Full Checklist <i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- New Travel Request Modal -->
    <div id="newRequestModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">New Travel Request</h3>
                        <p class="text-gray-600">Fill out all required fields</p>
                    </div>
                    <button id="closeNewRequestModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="travelRequestForm">
                    <!-- Basic Information -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-800 mb-4 border-b pb-2">Basic Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose of Travel*</label>
                                <select id="purpose" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select purpose</option>
                                    <option value="conference">Conference/Workshop</option>
                                    <option value="research">Research/Fieldwork</option>
                                    <option value="meeting">Official Meeting</option>
                                    <option value="training">Training/Seminar</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">Destination*</label>
                                <input type="text" id="destination" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="City, Country">
                            </div>
                            <div>
                                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date*</label>
                                <input type="date" id="startDate" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date*</label>
                                <input type="date" id="endDate" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Detailed Description*</label>
                            <textarea id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Explain the purpose and expected outcomes of this travel"></textarea>
                        </div>
                    </div>
                    
                    <!-- Financial Information -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-800 mb-4 border-b pb-2">Financial Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="estimatedCost" class="block text-sm font-medium text-gray-700 mb-1">Estimated Total Cost*</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" id="estimatedCost" class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center">
                                        <select class="h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option>USD</option>
                                            <option>PHP</option>
                                            <option>EUR</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="fundingSource" class="block text-sm font-medium text-gray-700 mb-1">Funding Source*</label>
                                <select id="fundingSource" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select funding source</option>
                                    <option value="university">University Funds</option>
                                    <option value="grant">Research Grant</option>
                                    <option value="department">Department Budget</option>
                                    <option value="personal">Personal Funds</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="advanceRequest" class="block text-sm font-medium text-gray-700 mb-1">Advance Requested</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" id="advanceRequest" class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents Upload -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-800 mb-4 border-b pb-2">Supporting Documents</h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Drag and drop files here or</p>
                                <button type="button" class="mt-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition">
                                    <i class="fas fa-folder-open mr-2"></i> Browse Files
                                </button>
                                <p class="text-xs text-gray-500 mt-2">Accepted file types: PDF, DOCX, XLSX, JPG, PNG (Max 5MB each)</p>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="border border-gray-200 rounded-lg p-3 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file-pdf text-red-500"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Conference_Invitation.pdf</p>
                                        <p class="text-xs text-gray-500">245 KB</p>
                                    </div>
                                </div>
                                <button class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submission -->
                    <div class="flex flex-wrap justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" id="cancelRequest" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Save as Draft
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Submit Request
                        </button>
                    </div>
                </form>
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
                    
                    <!-- Approval Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-3">Approval Status</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Current Status</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Department Head</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <span class="text-green-600">Approved</span> on Oct 27, 2023
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">HR Director</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <span class="text-yellow-600">Pending review</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Finance Office</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <span class="text-gray-500">Not reviewed</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Travel Timeline -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-800 mb-3">Approval Timeline</h4>
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
                
                <!-- Action Buttons (Employee Perspective) -->
                <div class="flex flex-wrap justify-end gap-3 pt-4 border-t border-gray-200">
                    <button class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-download mr-2"></i> Download Documents
                    </button>
                    <button class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                        <i class="fas fa-edit mr-2"></i> Edit Request
                    </button>
                    <button class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-trash mr-2"></i> Cancel Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // New Travel Request Modal
        const newRequestBtn = document.getElementById('newRequestBtn');
        const newRequestModal = document.getElementById('newRequestModal');
        const closeNewRequestModal = document.getElementById('closeNewRequestModal');
        const cancelRequest = document.getElementById('cancelRequest');
        
        if (newRequestBtn && newRequestModal && closeNewRequestModal && cancelRequest) {
            // Open modal
            newRequestBtn.addEventListener('click', function() {
                newRequestModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
            
            // Close modal
            function closeModal() {
                newRequestModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            
            closeNewRequestModal.addEventListener('click', closeModal);
            cancelRequest.addEventListener('click', closeModal);
            
            // Close when clicking outside
            newRequestModal.addEventListener('click', function(e) {
                if (e.target === newRequestModal) {
                    closeModal();
                }
            });
        }
        
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

        // Form submission
        const travelRequestForm = document.getElementById('travelRequestForm');
        if (travelRequestForm) {
            travelRequestForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // Here you would typically handle form submission via AJAX
                showToast('Travel request submitted successfully!');
                newRequestModal.classList.add('hidden');
                document.body.style.overflow = '';
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