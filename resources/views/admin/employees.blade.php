@extends('layouts.admin')

@section('title', 'Employee Management')

@section('content')

<div class="flex flex-col h-full" x-data="employeeModule()" x-cloak>
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

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="employees.filter(e => e.user_status === 'Active').length"></p>
                </div>
                <div class="p-2 bg-green-50 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">On Leave</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="employees.filter(e => e.user_status === 'On Leave').length"></p>
                </div>
                <div class="p-2 bg-yellow-50 rounded-lg">
                    <i class="fas fa-umbrella-beach text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Departments</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="new Set(employees.map(e => e.department)).size"></p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New This Month</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="getNewEmployeesThisMonth()"></p>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg">
                    <i class="fas fa-user-plus text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full lg:w-auto">
                <div class="relative flex-1 sm:flex-none">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        x-model="searchQuery" 
                        @input.debounce.300ms="filterEmployees()"
                        type="text" 
                        class="block w-full sm:w-64 pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                        placeholder="Search employees..."
                    >
                </div>
                
                <select 
                    x-model="selectedDepartment" 
                    @change="filterEmployees()"
                    class="block w-full sm:w-48 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                >
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
                
                <select 
                    x-model="selectedStatus" 
                    @change="filterEmployees()"
                    class="block w-full sm:w-40 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                >
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="On Leave">On Leave</option>
                    <option value="Terminated">Terminated</option>
                    <option value="Suspended">Suspended</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button 
                        @click="viewMode = 'list'" 
                        :class="{'bg-white shadow-sm text-blue-600': viewMode === 'list', 'text-gray-600': viewMode !== 'list'}" 
                        class="px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out"
                    >
                        <i class="fas fa-list mr-2"></i> List
                    </button>
                    <button 
                        @click="viewMode = 'grid'" 
                        :class="{'bg-white shadow-sm text-blue-600': viewMode === 'grid', 'text-gray-600': viewMode !== 'grid'}" 
                        class="px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out"
                    >
                        <i class="fas fa-th-large mr-2"></i> Grid
                    </button>
                </div>

                @can('employee-create')
                <button 
                    @click="openAddEmployeeModal()"
                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition duration-150 ease-in-out"
                >
                    <i class="fas fa-plus mr-2"></i> Add Employee
                </button>
                @endcan
            </div>
        </div>

        <!-- Active Filters -->
        <div x-show="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
            <template x-if="searchQuery">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                    Search: "<span x-text="searchQuery"></span>"
                    <button @click="searchQuery = ''; filterEmployees()" class="ml-1 hover:text-blue-900">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            </template>
            <template x-if="selectedDepartment">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                    Department: <span x-text="selectedDepartment"></span>
                    <button @click="selectedDepartment = ''; filterEmployees()" class="ml-1 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            </template>
            <template x-if="selectedStatus">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                    Status: <span x-text="selectedStatus"></span>
                    <button @click="selectedStatus = ''; filterEmployees()" class="ml-1 hover:text-purple-900">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            </template>
            <button 
                @click="clearAllFilters()"
                class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800 hover:bg-gray-200 transition duration-150 ease-in-out"
            >
                Clear all
            </button>
            
        </div>
    </div>

    <!-- Results Summary -->
    <div class="flex justify-between items-center mb-4">
        <div class="text-sm text-gray-600">
            Showing <span class="font-semibold" x-text="filteredEmployees.length"></span> employees
            <span x-show="hasActiveFilters">(filtered from <span x-text="employees.length"></span> total)</span>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <label for="pageSize">Show:</label>
                <select 
                    id="pageSize"
                    x-model="pageSize" 
                    @change="currentPage = 1"
                    class="border border-gray-200 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Employee List View -->
    <div x-show="viewMode === 'list'" class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('employee_id')">
                            <div class="flex items-center gap-1">
                                Employee ID
                                <i class="fas fa-sort text-gray-400 text-xs" :class="getSortIconClass('employee_id')"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('name')">
                            <div class="flex items-center gap-1">
                                Employee
                                <i class="fas fa-sort text-gray-400 text-xs" :class="getSortIconClass('name')"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('department')">
                            <div class="flex items-center gap-1">
                                Department
                                <i class="fas fa-sort text-gray-400 text-xs" :class="getSortIconClass('department')"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('position')">
                            <div class="flex items-center gap-1">
                                Position
                                <i class="fas fa-sort text-gray-400 text-xs" :class="getSortIconClass('position')"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('user_status')">
                            <div class="flex items-center gap-1">
                                Status
                                <i class="fas fa-sort text-gray-400 text-xs" :class="getSortIconClass('user_status')"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="employee in paginatedEmployees" :key="employee.id">
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-medium text-gray-900" x-text="employee.employee_id"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm" 
                                            :src="employee.profile_photo_url" 
                                            :alt="employee.first_name + ' ' + employee.last_name">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="employee.first_name + ' ' + employee.last_name"></div>
                                        <div class="text-sm text-gray-500" x-text="employee.email"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="employee.department"></div>
                                <div class="text-xs text-gray-500" x-text="employee.role"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="employee.position || 'Not specified'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                    :class="getStatusClasses(employee.user_status)"
                                    x-text="employee.user_status || 'Active'"
                                ></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    @can('employee-view-details')
                                    <button 
                                        @click="viewEmployee(employee.id)"
                                        class="text-blue-600 hover:text-blue-800 p-2 rounded-lg bg-blue-50 hover:bg-blue-100 transition duration-150 ease-in-out"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @endcan
                                    
                                    @can('employee-edit')
                                    <button 
                                        @click="editEmployee(employee.id)"
                                        class="text-green-600 hover:text-green-800 p-2 rounded-lg bg-green-50 hover:bg-green-100 transition duration-150 ease-in-out"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endcan
                                    
                                    @can('employee-delete')
                                    <button 
                                        @click="confirmDeleteEmployee(employee.id)"
                                        class="text-red-600 hover:text-red-800 p-2 rounded-lg bg-red-50 hover:bg-red-100 transition duration-150 ease-in-out"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredEmployees.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-user-slash text-4xl mb-3"></i>
                                    <p class="text-lg font-medium text-gray-500 mb-2">No employees found</p>
                                    <p class="text-sm text-gray-400 mb-4">Try adjusting your search criteria or clear filters</p>
                                    <button 
                                        @click="clearAllFilters()"
                                        class="text-blue-600 hover:text-blue-700 text-sm font-medium"
                                    >
                                        Clear all filters
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-100">
            <div class="flex-1 flex justify-between items-center">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium" x-text="((currentPage - 1) * pageSize) + 1"></span> to 
                    <span class="font-medium" x-text="Math.min(currentPage * pageSize, filteredEmployees.length)"></span> of 
                    <span class="font-medium" x-text="filteredEmployees.length"></span> results
                </div>
                
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <button 
                        @click="currentPage = 1"
                        :disabled="currentPage === 1"
                        :class="{'opacity-50 cursor-not-allowed': currentPage === 1, 'hover:bg-gray-50': currentPage !== 1}"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 transition duration-150 ease-in-out"
                    >
                        <span class="sr-only">First</span>
                        <i class="fas fa-angle-double-left"></i>
                    </button>
                    <button 
                        @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        :class="{'opacity-50 cursor-not-allowed': currentPage === 1, 'hover:bg-gray-50': currentPage !== 1}"
                        class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 transition duration-150 ease-in-out"
                    >
                        <span class="sr-only">Previous</span>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <template x-for="page in visiblePages" :key="page">
                        <button 
                            @click="currentPage = page"
                            :class="{'z-10 bg-blue-50 border-blue-500 text-blue-600': currentPage === page, 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': currentPage !== page}"
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition duration-150 ease-in-out"
                            x-text="page"
                        ></button>
                    </template>
                    
                    <button 
                        @click="currentPage = Math.min(totalPages, currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages, 'hover:bg-gray-50': currentPage !== totalPages}"
                        class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 transition duration-150 ease-in-out"
                    >
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button 
                        @click="currentPage = totalPages"
                        :disabled="currentPage === totalPages"
                        :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages, 'hover:bg-gray-50': currentPage !== totalPages}"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 transition duration-150 ease-in-out"
                    >
                        <span class="sr-only">Last</span>
                        <i class="fas fa-angle-double-right"></i>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Employee Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="employee in paginatedEmployees" :key="employee.id">
            <div class="employee-card bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-300 ease-in-out hover:shadow-md border border-gray-100 hover:border-gray-200">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center">
                        <img class="h-12 w-12 rounded-full mr-4 object-cover border-2 border-white shadow" 
                            :src="employee.profile_photo_url" 
                            :alt="employee.first_name + ' ' + employee.last_name">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate" x-text="employee.first_name + ' ' + employee.last_name"></h3>
                            <p class="text-sm text-gray-500 truncate" x-text="employee.role"></p>
                            <span 
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                :class="getStatusClasses(employee.user_status)"
                                x-text="employee.user_status || 'Active'"
                            ></span>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-id-card mr-2 text-gray-400 w-4"></i>
                        <span class="truncate" x-text="employee.employee_id"></span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-building mr-2 text-gray-400 w-4"></i>
                        <span class="truncate" x-text="employee.department"></span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-envelope mr-2 text-gray-400 w-4"></i>
                        <span class="truncate" x-text="employee.email"></span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-phone mr-2 text-gray-400 w-4"></i>
                        <span class="truncate" x-text="employee.phone || 'N/A'"></span>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 flex justify-end space-x-2">
                    @can('employee-view-details')
                    <button 
                        @click="viewEmployee(employee.id)"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none transition duration-150 ease-in-out"
                        title="View"
                    >
                        <i class="fas fa-eye mr-1"></i> View
                    </button>
                    @endcan
                    
                    @can('employee-edit')
                    <button 
                        @click="editEmployee(employee.id)"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none transition duration-150 ease-in-out"
                        title="Edit"
                    >
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    @endcan
                    
                    @can('employee-delete')
                    <button 
                        @click="confirmDeleteEmployee(employee.id)"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none transition duration-150 ease-in-out"
                        title="Delete"
                    >
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                    @endcan
                </div>
            </div>
        </template>
        <template x-if="filteredEmployees.length === 0">
            <div class="col-span-full text-center py-12">
                <div class="inline-flex items-center justify-center bg-gray-50 rounded-full h-16 w-16 mb-4">
                    <i class="fas fa-users-slash text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No employees found</h3>
                <p class="text-gray-500 mb-4">Try adjusting your search or filter criteria</p>
                <button 
                    @click="clearAllFilters()"
                    class="text-blue-600 hover:text-blue-700 text-sm font-medium"
                >
                    Clear all filters
                </button>
            </div>
        </template>
    </div>

    <!-- Loading State -->
    <div x-show="isLoading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Enhanced Edit Employee Modal -->
    <div 
        x-show="isEmployeeModalOpen" 
        @keydown.escape.window="closeEmployeeModal()"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity duration-300 ease-in-out"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-xl rounded-xl bg-white" 
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header with Progress Steps -->
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <h3 class="text-xl font-semibold text-gray-900" x-text="isEditing ? 'Edit Employee' : 'Add New Employee'"></h3>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium" x-text="currentStep + ' of 4'"></span>
                    </div>
                </div>
                <button @click="closeEmployeeModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Progress Steps -->
            <div class="mt-4 mb-6">
                <div class="flex items-center justify-between">
                    <template x-for="(step, index) in steps" :key="index">
                        <div class="flex items-center flex-1">
                            <button 
                                @click="currentStep = index + 1"
                                :class="{
                                    'bg-blue-600 text-white': currentStep > index + 1,
                                    'bg-blue-600 text-white': currentStep === index + 1,
                                    'bg-gray-200 text-gray-500': currentStep < index + 1
                                }"
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition duration-150 ease-in-out"
                                x-text="index + 1"
                            ></button>
                            <div class="flex-1 mx-2" x-show="index < steps.length - 1">
                                <div 
                                    :class="{
                                        'bg-blue-600': currentStep > index + 1,
                                        'bg-gray-200': currentStep <= index + 1
                                    }"
                                    class="h-1 rounded-full transition duration-150 ease-in-out"
                                ></div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex justify-between mt-2">
                    <template x-for="(step, index) in steps" :key="index">
                        <span 
                            :class="{
                                'text-blue-600 font-medium': currentStep === index + 1,
                                'text-gray-500': currentStep !== index + 1
                            }"
                            class="text-xs transition duration-150 ease-in-out"
                            x-text="step"
                        ></span>
                    </template>
                </div>
            </div>

            <div class="mt-4 max-h-[70vh] overflow-y-auto">
                <form @submit.prevent="saveEmployee()">
                    <!-- Step 1: Personal Information -->
                    <div x-show="currentStep === 1" class="space-y-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                Personal Information
                                <span class="ml-2 text-sm text-blue-600 font-normal">(Step 1 of 4)</span>
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Required Fields Group -->
                                <div class="md:col-span-2 lg:col-span-3 mb-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                        Required Information
                                    </h5>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        x-model="currentEmployee.first_name"
                                        name="first_name"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                        required
                                        placeholder="Enter first name"
                                    >
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        x-model="currentEmployee.last_name"
                                        name="last_name"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                        required
                                        placeholder="Enter last name"
                                    >
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                    <input 
                                        x-model="currentEmployee.middle_name"
                                        name="middle_name"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        placeholder="Optional"
                                    >
                                </div>

                                <!-- Additional Personal Info -->
                                <div class="md:col-span-2 lg:col-span-3 mt-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Additional Information</h5>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender Identity</label>
                                    <select 
                                        x-model="currentEmployee.gender"
                                        name="gender"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                                        @change="if ($event.target.value !== 'Other') currentEmployee.gender_other = ''"
                                    >
                                        <option value="">Select Gender Identity</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Non-Binary">Non-Binary</option>
                                        <option value="Prefer not to say">Prefer not to say</option>
                                        <option value="Other">Other (please specify)</option>
                                    </select>
                                </div>

                                <div x-show="currentEmployee.gender === 'Other'" class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Please specify</label>
                                    <input 
                                        x-model="currentEmployee.gender_other"
                                        name="gender_other"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        placeholder="Enter your gender identity"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                                    <select 
                                        x-model="currentEmployee.sex"
                                        name="sex"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                                    >
                                        <option value="">Select Sex</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                                    <select 
                                        x-model="currentEmployee.civil_status"
                                        name="civil_status"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                                    >
                                        <option value="">Select Civil Status</option>
                                        <option>Single</option>
                                        <option>Married</option>
                                        <option>Divorced</option>
                                        <option>Widowed</option>
                                        <option>Separated</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                    <input 
                                        x-model="currentEmployee.dob"
                                        name="dob"
                                        type="date" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        max="<?= date('Y-m-d') ?>"
                                    >
                                </div>

                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input 
                                            x-model="currentEmployee.is_pwd"
                                            name="is_pwd"
                                            type="checkbox" 
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="ml-2 text-sm font-medium text-gray-700">Person With Disability</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Highest Education</label>
                                    <select 
                                        x-model="currentEmployee.highest_educational_attainment"
                                        name="highest_educational_attainment"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                                    >
                                        <option value="">Select Education Level</option>
                                        <option>Elementary</option>
                                        <option>High School</option>
                                        <option>Vocational</option>
                                        <option>Associate Degree</option>
                                        <option>Bachelor's Degree</option>
                                        <option>Master's Degree</option>
                                        <option>Doctorate</option>
                                        <option>Post-Doctorate</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Contact Information -->
                    <div x-show="currentStep === 2" class="space-y-6">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border border-green-100">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-address-card text-white text-sm"></i>
                                </div>
                                Contact Information
                                <span class="ml-2 text-sm text-green-600 font-normal">(Step 2 of 4)</span>
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        x-model="currentEmployee.email"
                                        name="email"
                                        type="email" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                        required
                                        placeholder="employee@company.com"
                                    >
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        x-model="currentEmployee.phone"
                                        name="phone"
                                        type="tel" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                        required
                                        placeholder="+1 (555) 000-0000"
                                    >
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea 
                                        x-model="currentEmployee.address"
                                        name="address"
                                        rows="3" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        placeholder="Enter complete address"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Employment Information -->
                    <div x-show="currentStep === 3" class="space-y-6">
                        <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg p-6 border border-purple-100">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-briefcase text-white text-sm"></i>
                                </div>
                                Employment Information
                                <span class="ml-2 text-sm text-purple-600 font-normal">(Step 3 of 4)</span>
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Required Employment Fields -->
                                <div class="md:col-span-2 lg:col-span-3 mb-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                        Required Employment Information
                                    </h5>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Employee ID <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        x-model="currentEmployee.employee_id"
                                        name="employee_id"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                        required
                                        :readonly="isEditing"
                                        :class="{'bg-gray-100': isEditing}"
                                        placeholder="EMP001"
                                    >
                                    <p class="text-xs text-gray-500 mt-1" x-show="isEditing">Employee ID cannot be changed</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Department <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        x-model="currentEmployee.department"
                                        name="department"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out" 
                                        required
                                    >
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department }}">{{ $department }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Role <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        x-model="currentEmployee.role"
                                        name="role"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out" 
                                        required
                                    >
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Additional Employment Info -->
                                <div class="md:col-span-2 lg:col-span-3 mt-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Additional Employment Details</h5>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                    <input 
                                        x-model="currentEmployee.position"
                                        name="position"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        placeholder="e.g., Software Engineer"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                                    <input 
                                        x-model="currentEmployee.designation"
                                        name="designation"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        placeholder="e.g., Senior Developer"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                                    <input 
                                        x-model="currentEmployee.program"
                                        name="program"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        placeholder="e.g., Computer Science"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                                    <select 
                                        x-model="currentEmployee.employment_type"
                                        name="employment_type"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                                    >
                                        <option value="">Select Employment Type</option>
                                        <option>Permanent Employee</option>
                                        <option>Non-Permanent Employee</option>
                                        <option>Contract of Service</option>
                                        <option>Part-Time</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee Category</label>
                                    <select 
                                        x-model="currentEmployee.employee_category"
                                        name="employee_category"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                                    >
                                        <option value="">Select Category</option>
                                        <option>Teaching</option>
                                        <option>Non-Teaching</option>
                                        <option>Teaching/Non-Teaching</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select 
                                        x-model="currentEmployee.user_status"
                                        name="user_status"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                                    >
                                        <option value="Active">Active</option>
                                        <option value="On Leave">On Leave</option>
                                        <option value="Suspended">Suspended</option>
                                        <option value="Terminated">Terminated</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hire Date</label>
                                    <input 
                                        x-model="currentEmployee.hire_date"
                                        type="date" 
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                        max="<?= date('Y-m-d') ?>"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Profile & Access -->
                    <div x-show="currentStep === 4" class="space-y-6">
                        <!-- Profile Photo Section -->
                        <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg p-6 border border-pink-100">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-camera text-white text-sm"></i>
                                </div>
                                Profile Photo
                                <span class="ml-2 text-sm text-pink-600 font-normal">(Step 4 of 4)</span>
                            </h4>
                            
                            <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                                <div class="flex-shrink-0 relative">
                                    <img x-bind:src="currentEmployee.profile_photo_url || 'https://ui-avatars.com/api/?name=' + currentEmployee.first_name + '+' + currentEmployee.last_name + '&background=random'" 
                                        class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg"
                                        :alt="currentEmployee.first_name + ' ' + currentEmployee.last_name">
                                    <div x-show="currentEmployee.profile_photo_url" class="absolute -top-1 -right-1 bg-green-500 rounded-full p-1">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="space-y-3">
                                        <div>
                                            <input 
                                                type="file" 
                                                id="profile_photo"
                                                @change="handleProfilePhotoUpload"
                                                class="hidden"
                                                accept="image/*"
                                            >
                                            <label for="profile_photo" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                                <i class="fas fa-upload mr-2"></i> 
                                                <span x-text="currentEmployee.profile_photo_url ? 'Change Photo' : 'Upload Photo'"></span>
                                            </label>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG or GIF (Max. 2MB)</p>
                                        </div>
                                        
                                        <div x-show="currentEmployee.profile_photo_url">
                                            <button 
                                                type="button" 
                                                @click="currentEmployee.profile_photo_url = null; currentEmployee.remove_profile_photo = true" 
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                                            >
                                                <i class="fas fa-trash mr-1"></i> Remove Photo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Access Section (Only for new employees) -->
                        <div x-show="!isEditing" class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-6 border border-orange-100">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-key text-white text-sm"></i>
                                </div>
                                System Access
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <div class="relative">
                                        <input 
                                            x-model="currentEmployee.password"
                                            name="password"
                                            :type="showPassword ? 'text' : 'password'"
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out pr-10"
                                            placeholder="Leave blank for default password"
                                        >
                                        <button 
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                        >
                                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters. Leave blank to generate a random password.</p>
                                </div>
                                
                                <div class="flex items-end">
                                    <button 
                                        type="button"
                                        @click="generatePassword()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                                    >
                                        <i class="fas fa-dice mr-1"></i> Generate Password
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-6 border border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-blue-500 mr-2"></i>
                                Review Information
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="font-medium text-gray-700">Personal Information</p>
                                    <p class="text-gray-600" x-text="currentEmployee.first_name + ' ' + currentEmployee.last_name"></p>
                                    <p class="text-gray-600" x-text="currentEmployee.email"></p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Employment Details</p>
                                    <p class="text-gray-600" x-text="currentEmployee.department"></p>
                                    <p class="text-gray-600" x-text="currentEmployee.position || 'Not specified'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="mt-6 flex justify-between space-x-3 border-t border-gray-200 pt-4">
                        <button 
                            type="button" 
                            @click="previousStep()"
                            x-show="currentStep > 1"
                            class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            <i class="fas fa-arrow-left mr-2"></i> Previous
                        </button>
                        
                        <div class="flex-1" x-show="currentStep === 1"></div>

                        <button 
                            type="button" 
                            @click="nextStep()"
                            x-show="currentStep < 4"
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            Next <i class="fas fa-arrow-right ml-2"></i>
                        </button>

                        <button 
                            type="submit" 
                            x-show="currentStep === 4"
                            :disabled="isSubmitting"
                            :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out"
                        >
                            <i class="fas fa-save mr-2"></i>
                            <span x-text="isEditing ? (isSubmitting ? 'Updating...' : 'Update Employee') : (isSubmitting ? 'Creating...' : 'Create Employee')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div 
        x-show="isDeleteModalOpen" 
        @keydown.escape.window="closeDeleteModal()"
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
                <p class="text-gray-600 text-center">Are you sure you want to delete this employee? This action cannot be undone.</p>
            </div>
            <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                <button 
                    @click="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                >
                    Cancel
                </button>
                <button 
                    @click="deleteEmployee()"
                    class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                >
                    Delete Employee
                </button>
            </div>
        </div>
    </div>

    <!-- View Employee Modal -->
    <div 
        x-show="isViewModalOpen" 
        @keydown.escape.window="closeViewModal()"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity duration-300 ease-in-out"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-xl rounded-xl bg-white" 
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Employee Details</h3>
                <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/3">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-lg text-center shadow-sm border border-blue-200">
                            <img 
                                x-bind:src="viewEmployeeData.profile_photo_url || 'https://ui-avatars.com/api/?name=' + viewEmployeeData.first_name + '+' + viewEmployeeData.last_name + '&background=random'" 
                                class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-lg mb-4"
                                alt="Profile Photo"
                            >
                            <h3 class="text-xl font-semibold text-gray-900" x-text="viewEmployeeData.first_name + ' ' + viewEmployeeData.last_name"></h3>
                            <p class="text-gray-500" x-text="viewEmployeeData.role"></p>
                            <span 
                                class="mt-2 inline-block px-3 py-1 rounded-full text-sm font-semibold" 
                                :class="{
                                    'bg-green-50 text-green-700': viewEmployeeData.user_status === 'Active',
                                    'bg-yellow-50 text-yellow-700': viewEmployeeData.user_status === 'On Leave',
                                    'bg-red-50 text-red-700': viewEmployeeData.user_status === 'Terminated',
                                    'bg-gray-50 text-gray-700': viewEmployeeData.user_status === 'Suspended'
                                }"
                                x-text="viewEmployeeData.user_status || 'Active'"
                            ></span>
                        </div>
                        
                        <div class="mt-4 bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-address-book mr-2 text-blue-500"></i>
                                Contact Information
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p x-text="viewEmployeeData.email" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Phone</p>
                                    <p x-text="viewEmployeeData.phone || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Address</p>
                                    <p x-text="viewEmployeeData.address || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:w-2/3">
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-4 border border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-briefcase mr-2 text-blue-500"></i>
                                Employment Details
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Employee ID</p>
                                    <p x-text="viewEmployeeData.employee_id" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Department</p>
                                    <p x-text="viewEmployeeData.department" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Program</p>
                                    <p x-text="viewEmployeeData.program || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Position</p>
                                    <p x-text="viewEmployeeData.position || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Designation</p>
                                    <p x-text="viewEmployeeData.designation || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Employment Type</p>
                                    <p x-text="viewEmployeeData.employment_type || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Employee Category</p>
                                    <p x-text="viewEmployeeData.employee_category || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Hire Date</p>
                                    <p x-text="viewEmployeeData.hire_date ? new Date(viewEmployeeData.hire_date).toLocaleDateString() : 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-4 border border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2 flex items-center">
                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                Personal Information
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Gender</p>
                                    <p x-text="viewEmployeeData.gender || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Sex</p>
                                    <p x-text="viewEmployeeData.sex || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Civil Status</p>
                                    <p x-text="viewEmployeeData.civil_status || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date of Birth</p>
                                    <p x-text="viewEmployeeData.dob ? new Date(viewEmployeeData.dob).toLocaleDateString() : 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">PWD Status</p>
                                    <p x-text="viewEmployeeData.is_pwd ? 'Yes' : 'No'" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Highest Education</p>
                                    <p x-text="viewEmployeeData.highest_educational_attainment || 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
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
    function employeeModule() {
        return {
            // Data properties
            employees: @json($employees),
            departments: @json($departments),
            searchQuery: '',
            selectedDepartment: '',
            selectedStatus: '',
            viewMode: 'list',
            sortColumn: 'employee_id',
            sortDirection: 'asc',
            currentPage: 1,
            pageSize: 10,
            maxVisiblePages: 5,
            isLoading: false,
            
            // Modal states
            isEmployeeModalOpen: false,
            isEditing: false,
            isDeleteModalOpen: false,
            isViewModalOpen: false,
            employeeToDelete: null,
            
            // Enhanced edit feature properties
            currentStep: 1,
            steps: ['Personal', 'Contact', 'Employment', 'Profile & Access'],
            showPassword: false,
            isSubmitting: false,
            
            // Employee data
            currentEmployee: {
                id: '',
                first_name: '',
                middle_name: '',
                last_name: '',
                email: '',
                phone: '',
                address: '',
                gender: '',
                gender_other: '',
                sex: '',
                civil_status: '',
                dob: '',
                employee_id: '',
                department: '',
                program: '',
                position: '',
                designation: '',
                role: 'employee',
                user_status: 'Active',
                hire_date: '',
                employment_type: '',
                employee_category: '',
                highest_educational_attainment: '',
                is_pwd: false,
                profile_photo_path: null,
                password: '',
                remove_profile_photo: false
            },
            
            viewEmployeeData: {},
            
            // Computed properties
            get filteredEmployees() {
                let filtered = this.employees;
                
                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(emp => 
                        emp.first_name.toLowerCase().includes(query) || 
                        emp.last_name.toLowerCase().includes(query) ||
                        emp.email.toLowerCase().includes(query) ||
                        emp.employee_id.toLowerCase().includes(query) ||
                        (emp.department && emp.department.toLowerCase().includes(query)) ||
                        (emp.position && emp.position.toLowerCase().includes(query))
                    );
                }
                
                // Filter by department
                if (this.selectedDepartment) {
                    filtered = filtered.filter(emp => emp.department === this.selectedDepartment);
                }
                
                // Filter by status
                if (this.selectedStatus) {
                    filtered = filtered.filter(emp => (emp.user_status || 'Active') === this.selectedStatus);
                }
                
                // Sort employees
                return filtered.sort((a, b) => {
                    let aValue, bValue;
                    
                    if (this.sortColumn === 'name') {
                        aValue = `${a.first_name} ${a.last_name}`.toLowerCase();
                        bValue = `${b.first_name} ${b.last_name}`.toLowerCase();
                    } else {
                        aValue = a[this.sortColumn] || '';
                        bValue = b[this.sortColumn] || '';
                    }
                    
                    if (aValue < bValue) return this.sortDirection === 'asc' ? -1 : 1;
                    if (aValue > bValue) return this.sortDirection === 'asc' ? 1 : -1;
                    return 0;
                });
            },
            
            get hasActiveFilters() {
                return this.searchQuery || this.selectedDepartment || this.selectedStatus;
            },
            
            get totalPages() {
                return Math.ceil(this.filteredEmployees.length / this.pageSize);
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
            
            get paginatedEmployees() {
                const start = (this.currentPage - 1) * this.pageSize;
                return this.filteredEmployees.slice(start, start + this.pageSize);
            },
            
            // Methods
            getSortIconClass(column) {
                if (this.sortColumn !== column) return 'fa-sort';
                return this.sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
            },
            
            getStatusClasses(status) {
                const statusMap = {
                    'Active': 'bg-green-100 text-green-800',
                    'On Leave': 'bg-yellow-100 text-yellow-800',
                    'Terminated': 'bg-red-100 text-red-800',
                    'Suspended': 'bg-gray-100 text-gray-800'
                };
                return statusMap[status] || 'bg-gray-100 text-gray-800';
            },
            
            getNewEmployeesThisMonth() {
                const currentMonth = new Date().getMonth();
                const currentYear = new Date().getFullYear();
                return this.employees.filter(emp => {
                    if (!emp.created_at) return false;
                    const createdDate = new Date(emp.created_at);
                    return createdDate.getMonth() === currentMonth && createdDate.getFullYear() === currentYear;
                }).length;
            },
            
            sortEmployees(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
                this.currentPage = 1;
            },
            
            clearAllFilters() {
                this.searchQuery = '';
                this.selectedDepartment = '';
                this.selectedStatus = '';
                this.currentPage = 1;
            },
            
            filterEmployees() {
                this.currentPage = 1;
            },
            
            // Enhanced multi-step form methods
            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    this.currentStep = Math.min(this.currentStep + 1, 4);
                    this.scrollToTop();
                }
            },
            
            previousStep() {
                this.currentStep = Math.max(this.currentStep - 1, 1);
                this.scrollToTop();
            },
            
            validateStep(step) {
                switch (step) {
                    case 1:
                        if (!this.currentEmployee.first_name || !this.currentEmployee.last_name) {
                            this.showToast('Please fill in all required personal information', 'error');
                            return false;
                        }
                        return true;
                        
                    case 2:
                        if (!this.currentEmployee.email || !this.currentEmployee.phone) {
                            this.showToast('Please fill in all required contact information', 'error');
                            return false;
                        }
                        return true;
                        
                    case 3:
                        if (!this.currentEmployee.employee_id || !this.currentEmployee.department || !this.currentEmployee.role) {
                            this.showToast('Please fill in all required employment information', 'error');
                            return false;
                        }
                        return true;
                        
                    default:
                        return true;
                }
            },
            
            scrollToTop() {
                const modal = document.querySelector('[x-show="isEmployeeModalOpen"] .overflow-y-auto');
                if (modal) {
                    modal.scrollTop = 0;
                }
            },
            
            generatePassword() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
                let password = '';
                for (let i = 0; i < 12; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.currentEmployee.password = password;
                this.showPassword = true;
                this.showToast('Strong password generated!', 'success');
            },
            
            openAddEmployeeModal() {
                this.isEditing = false;
                this.currentStep = 1;
                this.currentEmployee = {
                    id: '',
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    address: '',
                    gender: '',
                    gender_other: '',
                    sex: '',
                    civil_status: '',
                    dob: '',
                    employee_id: '',
                    department: '',
                    program: '',
                    position: '',
                    designation: '',
                    role: 'employee',
                    user_status: 'Active',
                    hire_date: '',
                    employment_type: '',
                    employee_category: '',
                    highest_educational_attainment: '',
                    is_pwd: false,
                    profile_photo_path: null,
                    password: '',
                    remove_profile_photo: false
                };
                this.isEmployeeModalOpen = true;
                setTimeout(() => {
                    const modal = document.querySelector('[x-show="isEmployeeModalOpen"]');
                    if (modal) modal.style.display = 'block';
                }, 50);
            },

            editEmployee(id) {
                const employee = this.employees.find(emp => emp.id === id);
                if (employee) {
                    this.currentEmployee = {
                        id: employee.id,
                        first_name: employee.first_name,
                        middle_name: employee.middle_name || '',
                        last_name: employee.last_name,
                        email: employee.email,
                        phone: employee.phone || '',
                        address: employee.address || '',
                        gender: employee.gender || '',
                        gender_other: employee.gender_other || '',
                        sex: employee.sex || '',
                        civil_status: employee.civil_status || '',
                        dob: employee.dob ? employee.dob.split('T')[0] : '',
                        employee_id: employee.employee_id,
                        department: employee.department,
                        program: employee.program || '',
                        position: employee.position || '',
                        designation: employee.designation || '',
                        role: employee.role,
                        user_status: employee.user_status || 'Active',
                        hire_date: employee.hire_date ? employee.hire_date.split('T')[0] : '',
                        employment_type: employee.employment_type || '',
                        employee_category: employee.employee_category || '',
                        highest_educational_attainment: employee.highest_educational_attainment || '',
                        is_pwd: employee.is_pwd || false,
                        profile_photo_path: employee.profile_photo_path,
                        profile_photo_url: employee.profile_photo_url,
                        password: '',
                        remove_profile_photo: false
                    };
                    this.isEditing = true;
                    this.currentStep = 1;
                    this.isEmployeeModalOpen = true;
                    setTimeout(() => {
                        const modal = document.querySelector('[x-show="isEmployeeModalOpen"]');
                        if (modal) modal.style.display = 'block';
                    }, 50);
                }
            },
            
            viewEmployee(id) {
                const employee = this.employees.find(emp => emp.id === id);
                if (employee) {
                    this.viewEmployeeData = employee;
                    this.isViewModalOpen = true;
                    setTimeout(() => {
                        const modal = document.querySelector('[x-show="isViewModalOpen"]');
                        if (modal) modal.style.display = 'block';
                    }, 50);
                }
            },
            
            confirmDeleteEmployee(id) {
                this.employeeToDelete = id;
                this.isDeleteModalOpen = true;
                setTimeout(() => {
                    const modal = document.querySelector('[x-show="isDeleteModalOpen"]');
                    if (modal) modal.style.display = 'block';
                }, 50);
            },
            
            closeEmployeeModal() {
                this.isEmployeeModalOpen = false;
                this.currentStep = 1;
                this.isSubmitting = false;
                this.showPassword = false;
                const modal = document.querySelector('[x-show="isEmployeeModalOpen"]');
                if (modal) modal.style.display = 'none';
            },
            
            closeDeleteModal() {
                this.isDeleteModalOpen = false;
                this.employeeToDelete = null;
                const modal = document.querySelector('[x-show="isDeleteModalOpen"]');
                if (modal) modal.style.display = 'none';
            },
            
            closeViewModal() {
                this.isViewModalOpen = false;
                const modal = document.querySelector('[x-show="isViewModalOpen"]');
                if (modal) modal.style.display = 'none';
            },

            handleProfilePhotoUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.currentEmployee.profile_photo_url = e.target.result;
                        this.currentEmployee.profile_photo_file = file;
                        this.currentEmployee.remove_profile_photo = false;
                    };
                    reader.readAsDataURL(file);
                }
            },
            
            async saveEmployee() {
                if (this.isSubmitting) return;
                
                if (!this.validateStep(4)) {
                    return;
                }
                
                this.isSubmitting = true;
                
                try {
                    const url = this.isEditing 
                        ? "{{ route('admin.employees.update', ['employee' => ':id']) }}".replace(':id', this.currentEmployee.id)
                        : "{{ route('admin.employees.store') }}";
                    
                    const formData = new FormData();
                    
                    // Append all fields
                    formData.append('first_name', this.currentEmployee.first_name || '');
                    formData.append('middle_name', this.currentEmployee.middle_name || '');
                    formData.append('last_name', this.currentEmployee.last_name || '');
                    formData.append('email', this.currentEmployee.email || '');
                    formData.append('phone', this.currentEmployee.phone || '');
                    formData.append('employee_id', this.currentEmployee.employee_id || '');
                    formData.append('department', this.currentEmployee.department || '');
                    formData.append('program', this.currentEmployee.program || '');
                    formData.append('position', this.currentEmployee.position || '');
                    formData.append('designation', this.currentEmployee.designation || '');
                    formData.append('role', this.currentEmployee.role || 'employee');
                    formData.append('gender', this.currentEmployee.gender || '');
                    formData.append('gender_other', this.currentEmployee.gender_other || '');
                    formData.append('sex', this.currentEmployee.sex || '');
                    formData.append('civil_status', this.currentEmployee.civil_status || '');
                    formData.append('dob', this.currentEmployee.dob || '');
                    formData.append('address', this.currentEmployee.address || '');
                    formData.append('hire_date', this.currentEmployee.hire_date || '');
                    formData.append('user_status', this.currentEmployee.user_status || 'Active');
                    formData.append('employment_type', this.currentEmployee.employment_type || '');
                    formData.append('employee_category', this.currentEmployee.employee_category || '');
                    formData.append('highest_educational_attainment', this.currentEmployee.highest_educational_attainment || '');
                    formData.append('is_pwd', this.currentEmployee.is_pwd ? '1' : '0');
                    
                    if (!this.isEditing && this.currentEmployee.password) {
                        formData.append('password', this.currentEmployee.password);
                    }
                    
                    if (this.currentEmployee.profile_photo_file) {
                        formData.append('profile_photo', this.currentEmployee.profile_photo_file);
                    }
                    
                    if (this.isEditing) {
                        formData.append('_method', 'PUT');
                    }

                    if (this.currentEmployee.remove_profile_photo) {
                        formData.append('remove_profile_photo', '1');
                    }
                    
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        if (this.isEditing) {
                            const index = this.employees.findIndex(emp => emp.id === data.employee.id);
                            if (index !== -1) {
                                this.employees[index] = data.employee;
                            }
                        } else {
                            this.employees.unshift(data.employee);
                        }
                        
                        this.closeEmployeeModal();
                        this.showToast(data.message, 'success');
                        
                        const photoInput = document.getElementById('profile_photo');
                        if (photoInput) {
                            photoInput.value = '';
                        }
                    } else {
                        throw new Error(data.message || 'Failed to save employee');
                    }
                } catch (error) {
                    this.showToast(error.message, 'error');
                    console.error('Error:', error);
                } finally {
                    this.isSubmitting = false;
                }
            },
            
            deleteEmployee() {
                if (!this.employeeToDelete) return;
                
                fetch("{{ route('admin.employees.destroy', ['employee' => ':id']) }}".replace(':id', this.employeeToDelete), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const index = this.employees.findIndex(emp => emp.id === this.employeeToDelete);
                        if (index !== -1) {
                            this.employees.splice(index, 1);
                        }
                        this.showToast(data.message);
                    } else {
                        throw new Error(data.message || 'Failed to delete employee');
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
            
            showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white flex items-center ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } z-50 transition-all duration-300 ease-in-out`;
                
                const icon = document.createElement('i');
                icon.className = type === 'success' ? 'fas fa-check-circle mr-2' : 'fas fa-exclamation-circle mr-2';
                toast.appendChild(icon);
                
                const text = document.createElement('span');
                text.textContent = message;
                toast.appendChild(text);
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        };
    }
</script>
@endpush

<style>
.employee-card {
    transition: all 0.3s ease;
}

.employee-card:hover {
    transform: translateY(-2px);
}

.table-container::-webkit-scrollbar {
    height: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Smooth transitions for multi-step form */
[x-cloak] {
    display: none !important;
}
</style>
@endsection