@extends('layouts.admin')

@section('title', 'Payroll Management')

@section('content')
       
<main class="p-6">
    <!-- Payroll Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Payroll Summary Cards -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Payroll</p>
                    <h3 class="text-2xl font-bold">₱1,245,678</h3>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-green-600 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>12% from last month</span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Employees Paid</p>
                    <h3 class="text-2xl font-bold">248</h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-green-600 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>5 new employees</span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Approvals</p>
                    <h3 class="text-2xl font-bold">12</h3>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-red-600 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                <span>Requires attention</span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Tax Deductions</p>
                    <h3 class="text-2xl font-bold">₱245,890</h3>
                </div>
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600 flex items-center">
                <i class="fas fa-info-circle mr-1"></i>
                <span>Monthly tax filing</span>
            </div>
        </div>
    </div>
    
    <!-- Payroll Actions and Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-lg font-semibold">Payroll Processing</h2>
                <p class="text-gray-500 text-sm">Manage employee payroll for current period</p>
            </div>
            
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">
                <div class="relative">
                    <select class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                        <option>Select Pay Period</option>
                        <option>January 2023</option>
                        <option>February 2023</option>
                        <option selected>March 2023</option>
                        <option>April 2023</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <button class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded flex items-center">
                    <i class="fas fa-calculator mr-2"></i>
                    <span>Process Payroll</span>
                </button>
                
                <button class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded flex items-center">
                    <i class="fas fa-file-export mr-2"></i>
                    <span>Export</span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Payroll Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                    <i class="fas fa-list mr-2"></i>
                    All Payroll
                </button>
                <button class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-clock mr-2"></i>
                    Pending
                </button>
                <button class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-check-circle mr-2"></i>
                    Approved
                </button>
                <button class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-money-check-alt mr-2"></i>
                    Paid
                </button>
            </nav>
        </div>
        
        <!-- Payroll Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position
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
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/40" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Juan Dela Cruz</div>
                                    <div class="text-sm text-gray-500">EMP-001</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Professor</div>
                            <div class="text-sm text-gray-500">College of Arts</div>
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
                                <i class="fas fa-print"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/40" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Maria Santos</div>
                                    <div class="text-sm text-gray-500">EMP-002</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Associate Professor</div>
                            <div class="text-sm text-gray-500">College of Science</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">₱38,000</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-red-600">-₱7,200</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-green-600">+₱1,500</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold">₱32,300</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Approved</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900">
                                <i class="fas fa-print"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/40" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Pedro Reyes</div>
                                    <div class="text-sm text-gray-500">EMP-003</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Assistant Professor</div>
                            <div class="text-sm text-gray-500">College of Engineering</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">₱32,000</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-red-600">-₱6,100</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-green-600">+₱1,200</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold">₱27,100</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Row 4 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/40" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Ana Lopez</div>
                                    <div class="text-sm text-gray-500">EMP-004</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Instructor</div>
                            <div class="text-sm text-gray-500">College of Education</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">₱28,000</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-red-600">-₱5,300</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-green-600">+₱800</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold">₱23,500</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
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
                        <span class="font-medium">1</span>
                        to
                        <span class="font-medium">4</span>
                        of
                        <span class="font-medium">248</span>
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
    
    <!-- Payroll Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Payroll Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Payroll Distribution</h3>
                <div class="relative">
                    <select class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-1 px-3 pr-8 rounded text-sm leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Year</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
            <div class="h-64">
                <canvas id="payrollDistributionChart"></canvas>
            </div>
        </div>
        
        <!-- Payroll Processing Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Payroll Processing Status</h3>
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
            <div class="h-64">
                <canvas id="payrollStatusChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Payslips -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold">Recent Payslips</h3>
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                View All Payslips
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Payslip 1 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-blue-600 p-4 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold">Juan Dela Cruz</h4>
                            <p class="text-sm opacity-80">EMP-001</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-white text-blue-600">Paid</span>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs opacity-80">Pay Period</p>
                            <p class="text-sm font-medium">March 1-31, 2023</p>
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
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
            
            <!-- Payslip 2 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-blue-600 p-4 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold">Maria Santos</h4>
                            <p class="text-sm opacity-80">EMP-002</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-white text-blue-600">Paid</span>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs opacity-80">Pay Period</p>
                            <p class="text-sm font-medium">March 1-31, 2023</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs opacity-80">Net Pay</p>
                            <p class="text-lg font-bold">₱32,300</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between mb-3">
                        <span class="text-sm text-gray-600">Basic Salary</span>
                        <span class="text-sm font-medium">₱38,000</span>
                    </div>
                    <div class="flex justify-between mb-3">
                        <span class="text-sm text-gray-600">Deductions</span>
                        <span class="text-sm font-medium text-red-600">-₱7,200</span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="text-sm text-gray-600">Additions</span>
                        <span class="text-sm font-medium text-green-600">+₱1,500</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <span class="text-sm font-semibold">Net Pay</span>
                        <span class="text-sm font-bold">₱32,300</span>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 flex justify-between">
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i> View
                    </button>
                    <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
            
            <!-- Payslip 3 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-blue-600 p-4 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold">Pedro Reyes</h4>
                            <p class="text-sm opacity-80">EMP-003</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs opacity-80">Pay Period</p>
                            <p class="text-sm font-medium">March 1-31, 2023</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs opacity-80">Net Pay</p>
                            <p class="text-lg font-bold">₱27,100</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between mb-3">
                        <span class="text-sm text-gray-600">Basic Salary</span>
                        <span class="text-sm font-medium">₱32,000</span>
                    </div>
                    <div class="flex justify-between mb-3">
                        <span class="text-sm text-gray-600">Deductions</span>
                        <span class="text-sm font-medium text-red-600">-₱6,100</span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="text-sm text-gray-600">Additions</span>
                        <span class="text-sm font-medium text-green-600">+₱1,200</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <span class="text-sm font-semibold">Net Pay</span>
                        <span class="text-sm font-bold">₱27,100</span>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 flex justify-between">
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i> View
                    </button>
                    <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                        <i class="fas fa-check mr-1"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payroll Processing Modal (Hidden by default) -->
    <div id="payrollProcessingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-calculator text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Process Payroll
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to process payroll for March 2023? This action will calculate salaries for all active employees.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Pay Period</span>
                            <span class="text-sm text-gray-500">March 1-31, 2023</span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Employees</span>
                            <span class="text-sm text-gray-500">248 active employees</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Cut-off Date</span>
                            <span class="text-sm text-gray-500">March 31, 2023</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Estimated Total Payroll</span>
                            <span class="text-sm font-bold">₱1,245,678</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Estimated Tax Deductions</span>
                            <span class="text-sm font-bold text-red-600">₱245,890</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Process Payroll
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        const initCharts = () => {
            // Payroll Distribution Chart
            const payrollDistributionCtx = document.getElementById('payrollDistributionChart').getContext('2d');
            const payrollDistributionChart = new Chart(payrollDistributionCtx, {
                type: 'bar',
                data: {
                    labels: ['Faculty', 'Admin Staff', 'Maintenance', 'Security', 'Others'],
                    datasets: [{
                        label: 'Salary Distribution',
                        data: [850000, 320000, 45000, 30000, 678],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(139, 92, 246, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Payroll Status Chart
            const payrollStatusCtx = document.getElementById('payrollStatusChart').getContext('2d');
            const payrollStatusChart = new Chart(payrollStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Approved', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [180, 56, 12, 0],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)'
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

        // Process Payroll button click handler
        const processPayrollBtn = document.querySelector('button:contains("Process Payroll")');
        if (processPayrollBtn) {
            processPayrollBtn.addEventListener('click', function() {
                document.getElementById('payrollProcessingModal').classList.remove('hidden');
            });
        }

        // Close modal button
        const closeModalBtn = document.querySelector('#payrollProcessingModal button:contains("Cancel")');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                document.getElementById('payrollProcessingModal').classList.add('hidden');
            });
        }
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
</script>
@endpush