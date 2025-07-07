@extends('layouts.employees')

@section('title', 'My Payroll')

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
        <div class="flex-1 overflow-auto">

            <!-- Main Content Area -->
            <main class="p-4 md:p-6">

                <!-- Payroll Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Net Pay Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Net Pay (March 2023)</p>
                                <h3 class="text-2xl font-bold">₱38,500</h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-wallet text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-green-600 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>₱1,500 more than last month</span>
                        </div>
                    </div>

                    <!-- Basic Salary Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Basic Salary</p>
                                <h3 class="text-2xl font-bold">₱45,000</h3>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-600 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span>Monthly salary</span>
                        </div>
                    </div>

                    <!-- Deductions Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Deductions</p>
                                <h3 class="text-2xl font-bold">₱8,500</h3>
                            </div>
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-minus-circle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Tax:</span>
                                <span>₱6,200</span>
                            </div>
                            <div class="flex justify-between">
                                <span>SSS:</span>
                                <span>₱1,200</span>
                            </div>
                            <div class="flex justify-between">
                                <span>PhilHealth:</span>
                                <span>₱1,100</span>
                            </div>
                        </div>
                    </div>

                    <!-- Additions Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Additions</p>
                                <h3 class="text-2xl font-bold">₱2,000</h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-plus-circle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Overtime:</span>
                                <span>₱1,500</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Bonus:</span>
                                <span>₱500</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Details Section -->
                <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button id="payslipTab" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                Payslips
                            </button>
                            <button id="benefitsTab" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-taxi mr-2"></i>
                                Benefits
                            </button>
                            <button id="taxTab" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-receipt mr-2"></i>
                                Tax Documents
                            </button>
                        </nav>
                    </div>

                    <!-- Payslips Content -->
                    <div id="payslipContent" class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold">Your Payslips</h3>
                            <div class="relative">
                                <select class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded text-sm leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                                    <option>2023</option>
                                    <option>2022</option>
                                    <option>2021</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Payslips Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pay Period
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Basic Salary
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Deductions
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Additions
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Net Pay
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- March 2023 -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">March 1-31, 2023</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">₱45,000</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-red-600">-₱8,500</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-green-600">+₱2,000</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-bold">₱38,500</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- February 2023 -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">February 1-28, 2023</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">₱45,000</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-red-600">-₱7,000</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-green-600">+₱500</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-bold">₱38,500</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- January 2023 -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">January 1-31, 2023</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">₱45,000</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-red-600">-₱7,000</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-green-600">+₱0</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-bold">₱38,000</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">1</span>
                                        to
                                        <span class="font-medium">3</span>
                                        of
                                        <span class="font-medium">12</span>
                                        results
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
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits Content (Hidden by default) -->
                    <div id="benefitsContent" class="p-6 hidden">
                        <h3 class="text-lg font-semibold mb-6">Your Benefits Summary</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Health Insurance -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                        <i class="fas fa-heartbeat text-xl"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold">Health Insurance</h4>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Provider:</span>
                                        <span class="font-medium">PhilHealth</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Member Since:</span>
                                        <span class="font-medium">January 2015</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Monthly Contribution:</span>
                                        <span class="font-medium">₱1,100</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Retirement Plan -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                        <i class="fas fa-piggy-bank text-xl"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold">Retirement Plan</h4>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Provider:</span>
                                        <span class="font-medium">SSS</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Member Since:</span>
                                        <span class="font-medium">January 2015</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Monthly Contribution:</span>
                                        <span class="font-medium">₱1,200</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Life Insurance -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                        <i class="fas fa-umbrella text-xl"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold">Life Insurance</h4>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Provider:</span>
                                        <span class="font-medium">Company Plan</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Coverage Amount:</span>
                                        <span class="font-medium">₱500,000</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Premium:</span>
                                        <span class="font-medium">Fully Paid by Employer</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Education Benefits -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                        <i class="fas fa-graduation-cap text-xl"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold">Education Benefits</h4>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Type:</span>
                                        <span class="font-medium">Tuition Assistance</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Annual Limit:</span>
                                        <span class="font-medium">₱20,000</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Available</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Used This Year:</span>
                                        <span class="font-medium">₱0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Documents Content (Hidden by default) -->
                    <div id="taxContent" class="p-6 hidden">
                        <h3 class="text-lg font-semibold mb-6">Your Tax Documents</h3>
                        
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Document
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Year
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="fas fa-file-alt text-blue-500 mr-3"></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">BIR Form 2316</div>
                                                    <div class="text-sm text-gray-500">Certificate of Compensation Payment/Tax Withheld</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">2022</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Available</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="fas fa-file-alt text-blue-500 mr-3"></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">BIR Form 2316</div>
                                                    <div class="text-sm text-gray-500">Certificate of Compensation Payment/Tax Withheld</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">2021</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Available</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="fas fa-file-alt text-blue-500 mr-3"></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">BIR Form 2316</div>
                                                    <div class="text-sm text-gray-500">Certificate of Compensation Payment/Tax Withheld</div>
                                                </div>
                                            </div>
                                            </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">2020</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Available</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Your 2023 BIR Form 2316 will be available by January 31, 2024.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Payslips Cards -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Recent Payslips</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- March 2023 Payslip -->
                        <div class="payslip-card bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                            <div class="bg-blue-600 p-4 text-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold">March 2023</h4>
                                        <p class="text-sm opacity-80">Pay Period: March 1-31</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-white text-blue-600">Paid</span>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-xs opacity-80">Payment Date</p>
                                        <p class="text-sm font-medium">April 5, 2023</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs opacity-80">Net Pay</p>
                                        <p class="text-lg font-bold">₱38,500</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between mb-3">
                                    <span class="text-sm text-gray-600">Basic Salary</span>
                                    <span class="text-sm font-medium">₱45,000</span>
                                </div>
                                <div class="flex justify-between mb-3">
                                    <span class="text-sm text-gray-600">Deductions</span>
                                    <span class="text-sm font-medium text-red-600">-₱8,500</span>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <span class="text-sm text-gray-600">Additions</span>
                                    <span class="text-sm font-medium text-green-600">+₱2,000</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3 flex justify-between">
                                    <span class="text-sm font-semibold">Net Pay</span>
                                    <span class="text-sm font-bold">₱38,500</span>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 flex justify-between">
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i> View
                                </button>
                                <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    <i class="fas fa-download mr-1"></i> Download
                                </button>
                            </div>
                        </div>
                        
                        <!-- February 2023 Payslip -->
                        <div class="payslip-card bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                            <div class="bg-blue-600 p-4 text-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold">February 2023</h4>
                                        <p class="text-sm opacity-80">Pay Period: February 1-28</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-white text-blue-600">Paid</span>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-xs opacity-80">Payment Date</p>
                                        <p class="text-sm font-medium">March 5, 2023</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs opacity-80">Net Pay</p>
                                        <p class="text-lg font-bold">₱37,000</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between mb-3">
                                    <span class="text-sm text-gray-600">Basic Salary</span>
                                    <span class="text-sm font-medium">₱45,000</span>
                                </div>
                                <div class="flex justify-between mb-3">
                                    <span class="text-sm text-gray-600">Deductions</span>
                                    <span class="text-sm font-medium text-red-600">-₱7,000</span>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <span class="text-sm text-gray-600">Additions</span>
                                    <span class="text-sm font-medium text-green-600">+₱500</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3 flex justify-between">
                                    <span class="text-sm font-semibold">Net Pay</span>
                                    <span class="text-sm font-bold">₱38,500</span>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 flex justify-between">
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i> View
                                </button>
                                <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    <i class="fas fa-download mr-1"></i> Download
                                </button>
                            </div>
                        </div>
                        
                        <!-- January 2023 Payslip -->
                        <div class="payslip-card bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                            <div class="bg-blue-600 p-4 text-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold">January 2023</h4>
                                        <p class="text-sm opacity-80">Pay Period: January 1-31</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-white text-blue-600">Paid</span>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-xs opacity-80">Payment Date</p>
                                        <p class="text-sm font-medium">February 5, 2023</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs opacity-80">Net Pay</p>
                                        <p class="text-lg font-bold">₱38,000</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between mb-3">
                                    <span class="text-sm text-gray-600">Basic Salary</span>
                                    <span class="text-sm font-medium">₱45,000</span>
                                </div>
                                <div class="flex justify-between mb-3">
                                    <span class="text-sm text-gray-600">Deductions</span>
                                    <span class="text-sm font-medium text-red-600">-₱7,000</span>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <span class="text-sm text-gray-600">Additions</span>
                                    <span class="text-sm font-medium text-green-600">+₱0</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3 flex justify-between">
                                    <span class="text-sm font-semibold">Net Pay</span>
                                    <span class="text-sm font-bold">₱38,000</span>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 flex justify-between">
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i> View
                                </button>
                                <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    <i class="fas fa-download mr-1"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Statistics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Yearly Earnings Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Yearly Earnings</h3>
                            <div class="relative">
                                <select class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-1 px-3 pr-8 rounded text-sm leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                                    <option>2023</option>
                                    <option>2022</option>
                                    <option>2021</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="h-64 bg-gray-50 rounded flex items-center justify-center">
                            <p class="text-gray-500">Chart would be displayed here</p>
                        </div>
                    </div>
                    
                    <!-- Deductions Breakdown -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Deductions Breakdown</h3>
                            <div class="relative">
                                <select class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-1 px-3 pr-8 rounded text-sm leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                                    <option>March 2023</option>
                                    <option>February 2023</option>
                                    <option>January 2023</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="h-64 bg-gray-50 rounded flex items-center justify-center">
                            <p class="text-gray-500">Chart would be displayed here</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>

    <!-- Payslip Modal (Hidden by default) -->
    <div id="payslipModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Payslip - March 2023
                                </h3>
                                <button id="closePayslipModal" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg p-6 mb-4">
                                <div class="flex flex-col md:flex-row justify-between mb-6">
                                    <div>
                                        <h4 class="font-bold text-lg">Juan Dela Cruz</h4>
                                        <p class="text-gray-600">EMP-001</p>
                                        <p class="text-gray-600">Professor</p>
                                        <p class="text-gray-600">College of Arts</p>
                                    </div>
                                    <div class="mt-4 md:mt-0">
                                        <div class="flex justify-between md:justify-end space-x-8">
                                            <div>
                                                <p class="text-gray-600">Pay Period</p>
                                                <p class="font-medium">March 1-31, 2023</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600">Payment Date</p>
                                                <p class="font-medium">April 5, 2023</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Earnings -->
                                    <div>
                                        <h5 class="font-semibold mb-3 border-b pb-2">Earnings</h5>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span>Basic Salary</span>
                                                <span>₱45,000.00</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Overtime Pay</span>
                                                <span>₱1,500.00</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Bonus</span>
                                                <span>₱500.00</span>
                                            </div>
                                            <div class="flex justify-between font-semibold border-t pt-2 mt-2">
                                                <span>Total Earnings</span>
                                                <span>₱47,000.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Deductions -->
                                    <div>
                                        <h5 class="font-semibold mb-3 border-b pb-2">Deductions</h5>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span>Withholding Tax</span>
                                                <span>₱6,200.00</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>SSS Contribution</span>
                                                <span>₱1,200.00</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>PhilHealth</span>
                                                <span>₱1,100.00</span>
                                            </div>
                                            <div class="flex justify-between font-semibold border-t pt-2 mt-2">
                                                <span>Total Deductions</span>
                                                <span>₱8,500.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 pt-4 border-t">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Net Pay</span>
                                        <span>₱38,500.00</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    <p>Processed on April 3, 2023 at 10:15 AM</p>
                                    <p>Transaction ID: PAY-202303-001</p>
                                </div>
                                <div>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium flex items-center">
                                        <i class="fas fa-print mr-2"></i> Print Payslip
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        document.getElementById('sidebarClose').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
        });

        // Tab switching functionality
        document.getElementById('payslipTab').addEventListener('click', function() {
            document.getElementById('payslipContent').classList.remove('hidden');
            document.getElementById('benefitsContent').classList.add('hidden');
            document.getElementById('taxContent').classList.add('hidden');
            
            // Update tab styling
            this.classList.add('border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            document.getElementById('benefitsTab').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('benefitsTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            document.getElementById('taxTab').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('taxTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        document.getElementById('benefitsTab').addEventListener('click', function() {
            document.getElementById('payslipContent').classList.add('hidden');
            document.getElementById('benefitsContent').classList.remove('hidden');
            document.getElementById('taxContent').classList.add('hidden');
            
            // Update tab styling
            this.classList.add('border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            document.getElementById('payslipTab').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('payslipTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            document.getElementById('taxTab').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('taxTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        document.getElementById('taxTab').addEventListener('click', function() {
            document.getElementById('payslipContent').classList.add('hidden');
            document.getElementById('benefitsContent').classList.add('hidden');
            document.getElementById('taxContent').classList.remove('hidden');
            
            // Update tab styling
            this.classList.add('border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            document.getElementById('payslipTab').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('payslipTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            document.getElementById('benefitsTab').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('benefitsTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        // View Payslip Modal
        const viewButtons = document.querySelectorAll('button:has(.fa-eye)');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('payslipModal').classList.remove('hidden');
            });
        });

        // Close Payslip Modal
        document.getElementById('closePayslipModal').addEventListener('click', function() {
            document.getElementById('payslipModal').classList.add('hidden');
        });

        // Show toast notification
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

        // Simulate a download action
        const downloadButtons = document.querySelectorAll('button:has(.fa-download)');
        downloadButtons.forEach(button => {
            button.addEventListener('click', function() {
                showToast('Payslip download started', 'success');
            });
        });
    </script>

@endpush
@endsection