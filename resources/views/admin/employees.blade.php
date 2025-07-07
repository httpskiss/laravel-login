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

    <!-- Employee Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="w-full md:w-auto">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    x-model="searchQuery" 
                    @input="filterEmployees()"
                    type="text" 
                    class="block w-full md:w-64 pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                    placeholder="Search employees..."
                >
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <select 
                x-model="selectedDepartment" 
                @change="filterEmployees()"
                class="block w-full md:w-48 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
            >
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                @endforeach
            </select>
            <select 
                x-model="selectedStatus" 
                @change="filterEmployees()"
                class="block w-full md:w-40 pl-3 pr-10 py-2 text-base border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
            >
                <option value="">All Status</option>
                <option>Active</option>
                <option>On Leave</option>
                <option>Terminated</option>
                <option>Suspended</option>
            </select>
            @can('employee-create')
            <button 
                @click="openAddEmployeeModal()"
                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition duration-150 ease-in-out"
            >
                <i class="fas fa-plus mr-2"></i> Add Employee
            </button>
            @endcan
        </div>
    </div>

    <!-- View Toggle -->
    <div class="flex mb-6 border-b border-gray-100">
        <button 
            @click="viewMode = 'list'" 
            :class="{'border-b-2 border-blue-500 text-blue-600': viewMode === 'list', 'text-gray-500 hover:text-gray-700': viewMode !== 'list'}" 
            class="mr-6 py-3 px-1 text-sm font-medium flex items-center transition duration-150 ease-in-out"
        >
            <i class="fas fa-list mr-2"></i> List View
        </button>
        <button 
            @click="viewMode = 'grid'" 
            :class="{'border-b-2 border-blue-500 text-blue-600': viewMode === 'grid', 'text-gray-500 hover:text-gray-700': viewMode !== 'grid'}" 
            class="py-3 px-1 text-sm font-medium flex items-center transition duration-150 ease-in-out"
        >
            <i class="fas fa-th-large mr-2"></i> Grid View
        </button>
    </div>

    <!-- Employee List View -->
    <div x-show="viewMode === 'list'" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('id')">
                            <div class="flex items-center">
                                Employee ID
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'id' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'id' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('name')">
                            <div class="flex items-center">
                                Name
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'name' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'name' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('department')">
                            <div class="flex items-center">
                                Department
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'department' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'department' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('position')">
                            <div class="flex items-center">
                                Position
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'position' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'position' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortEmployees('user_status')">
                            <div class="flex items-center">
                                Status
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'status' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'status' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="employee in paginatedEmployees" :key="employee.id">
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="employee.employee_id"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                            :src="employee.profile_photo_url" 
                                            :alt="employee.first_name + ' ' + employee.last_name">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="employee.first_name + ' ' + employee.last_name"></div>
                                        <div class="text-sm text-gray-500" x-text="employee.email"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="employee.department"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="employee.role"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                    :class="{
                                        'bg-green-50 text-green-700': employee.user_status === 'Active',
                                        'bg-yellow-50 text-yellow-700': employee.user_status === 'On Leave',
                                        'bg-red-50 text-red-700': employee.user_status === 'Terminated',
                                        'bg-gray-50 text-gray-700': employee.user_status === 'Suspended'
                                    }"
                                    x-text="employee.user_status || 'Active'"
                                ></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    @can('employee-view-details')
                                    <button 
                                        @click="viewEmployee(employee.id)"
                                        class="text-blue-500 hover:text-blue-700 transition duration-150 ease-in-out"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @endcan
                                    
                                    @can('employee-edit')
                                    <button 
                                        @click="editEmployee(employee.id)"
                                        class="text-yellow-500 hover:text-yellow-700 transition duration-150 ease-in-out"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endcan
                                    
                                    @can('employee-delete')
                                    <button 
                                        @click="confirmDeleteEmployee(employee.id)"
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
                    <template x-if="filteredEmployees.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-user-slash text-3xl mb-2"></i>
                                    <p class="text-sm">No employees found matching your criteria.</p>
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
                        <span class="font-medium" x-text="Math.min(currentPage * pageSize, filteredEmployees.length)"></span> of 
                        <span class="font-medium" x-text="filteredEmployees.length"></span> employees
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

    <!-- Employee Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="employee in paginatedEmployees" :key="employee.id">
            <div class="employee-card bg-white rounded-xl shadow-sm overflow-hidden transition duration-300 ease-in-out hover:shadow-md border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center">
                        <img class="h-12 w-12 rounded-full mr-4 object-cover border-2 border-white shadow" 
                            :src="employee.profile_photo_url" 
                            :alt="employee.first_name + ' ' + employee.last_name">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" x-text="employee.first_name + ' ' + employee.last_name"></h3>
                            <p class="text-sm text-gray-500" x-text="employee.role"></p>
                            <span 
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                :class="{
                                    'bg-green-50 text-green-700': employee.user_status === 'Active',
                                    'bg-yellow-50 text-yellow-700': employee.user_status === 'On Leave',
                                    'bg-red-50 text-red-700': employee.user_status === 'Terminated',
                                    'bg-gray-50 text-gray-700': employee.user_status === 'Suspended'
                                }"
                                x-text="employee.user_status || 'Active'"
                            ></span>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-id-card mr-2 text-gray-400"></i>
                        <span x-text="employee.employee_id"></span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-building mr-2 text-gray-400"></i>
                        <span x-text="employee.department"></span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                        <span x-text="employee.email"></span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-phone mr-2 text-gray-400"></i>
                        <span x-text="employee.phone || 'N/A'"></span>
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
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none transition duration-150 ease-in-out"
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
                <p class="text-gray-500">Try adjusting your search or filter criteria</p>
            </div>
        </template>
    </div>

    <!-- Add/Edit Employee Modal -->
    <div 
        x-show="isEmployeeModalOpen" 
        @keydown.escape.window="closeEmployeeModal()"
        @click.away="closeEmployeeModal()"
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
                <h3 class="text-xl font-semibold text-gray-900" x-text="isEditing ? 'Edit Employee' : 'Add New Employee'"></h3>
                <button @click="closeEmployeeModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <form @submit.prevent="saveEmployee()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Personal Information -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-blue-500"></i>
                                Personal Information
                            </h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input 
                                x-model="currentEmployee.first_name"
                                name="first_name"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input 
                                x-model="currentEmployee.last_name"
                                name="last_name"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select 
                                x-model="currentEmployee.gender"
                                name="gender"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 bg-white transition duration-150 ease-in-out"
                            >
                                <option value="">Select Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input 
                                x-model="currentEmployee.dob"
                                name="dob"
                                type="date" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                            >
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="md:col-span-2 mt-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-address-card mr-2 text-blue-500"></i>
                                Contact Information
                            </h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input 
                                x-model="currentEmployee.email"
                                name="email"
                                type="email" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                            <input 
                                x-model="currentEmployee.phone"
                                name="phone"
                                type="tel" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                required
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea 
                                x-model="currentEmployee.address"
                                name="address"
                                rows="2" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                            ></textarea>
                        </div>
                        
                        <!-- Employment Information -->
                        <div class="md:col-span-2 mt-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-briefcase mr-2 text-blue-500"></i>
                                Employment Information
                            </h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID *</label>
                            <input 
                                x-model="currentEmployee.employee_id"
                                name="employee_id"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                required
                                :readonly="isEditing"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
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
                            >
                        </div>
                        
                        <!-- System Access -->
                        <div class="md:col-span-2 mt-4" x-show="!isEditing">
                            <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-key mr-2 text-blue-500"></i>
                                System Access
                            </h4>
                        </div>
                        <div x-show="!isEditing">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input 
                                x-model="currentEmployee.password"
                                name="password"
                                type="password" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                placeholder="Leave blank for default password"
                            >
                        </div>
                        
                        <!-- Profile Photo -->
                        <div class="md:col-span-2 mt-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-camera mr-2 text-blue-500"></i>
                                Profile Photo
                            </h4>
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <img x-bind:src="currentEmployee.profile_photo_url || 'https://ui-avatars.com/api/?name=' + currentEmployee.first_name + '+' + currentEmployee.last_name + '&background=random'" 
                                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 shadow-sm"
                                        :alt="currentEmployee.first_name + ' ' + currentEmployee.last_name">
                                </div>
                                <div>
                                    <input 
                                        type="file" 
                                        id="profile_photo"
                                        @change="handleProfilePhotoUpload"
                                        class="hidden"
                                        accept="image/*"
                                    >
                                    <label for="profile_photo" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                        <i class="fas fa-upload mr-2"></i> Upload Photo
                                    </label>
                                    <button 
                                        type="button" 
                                        @click="currentEmployee.profile_photo_url = null; currentEmployee.remove_profile_photo = true" 
                                        x-show="currentEmployee.profile_photo_url"
                                        class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                                    >
                                        <i class="fas fa-trash mr-2"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                        <button 
                            type="button" 
                            @click="closeEmployeeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            <span x-text="isEditing ? 'Update Employee' : 'Add Employee'"></span>
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
                <h3 class="text-xl font-semibold text-gray-900">Employee Details</h3>
                <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/3">
                        <div class="bg-gray-50 p-6 rounded-lg text-center shadow-sm">
                            <img 
                                x-bind:src="viewEmployeeData.profile_photo_url || 'https://ui-avatars.com/api/?name=' + viewEmployeeData.first_name + '+' + viewEmployeeData.last_name + '&background=random'" 
                                class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-md mb-4"
                                alt="Profile Photo"
                            >
                            <h3 class="text-xl font-semibold" x-text="viewEmployeeData.first_name + ' ' + viewEmployeeData.last_name"></h3>
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
                        
                        <div class="mt-4 bg-gray-50 p-6 rounded-lg shadow-sm">
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
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-4">
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
                                    <p class="text-sm font-medium text-gray-500">Position</p>
                                    <p x-text="viewEmployeeData.role" class="text-sm text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Hire Date</p>
                                    <p x-text="viewEmployeeData.hire_date ? new Date(viewEmployeeData.hire_date).toLocaleDateString() : 'N/A'" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-4">
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
                                    <p class="text-sm font-medium text-gray-500">Date of Birth</p>
                                    <p x-text="viewEmployeeData.dob ? new Date(viewEmployeeData.dob).toLocaleDateString() : 'N/A'" class="text-sm text-gray-700"></p>
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
            sortColumn: 'id',
            sortDirection: 'asc',
            currentPage: 1,
            pageSize: 10,
            maxVisiblePages: 5,
            
            // Modal states
            isEmployeeModalOpen: false,
            isEditing: false,
            isDeleteModalOpen: false,
            isViewModalOpen: false,
            employeeToDelete: null,
            
            // Employee data
            currentEmployee: {
                id: '',
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                address: '',
                gender: '',
                dob: '',
                employee_id: '',
                department: '',
                role: 'employee',
                user_status: 'Active',
                hire_date: '',
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
                        emp.employee_id.toLowerCase().includes(query)
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
            sortEmployees(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            },
            
            openAddEmployeeModal() {
                this.isEditing = false;
                this.currentEmployee = {
                    id: '',
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    address: '',
                    gender: '',
                    dob: '',
                    employee_id: '',
                    department: '',
                    role: 'employee',
                    user_status: 'Active',
                    hire_date: '',
                    profile_photo_path: null,
                    password: '',
                    remove_profile_photo: false
                };
                this.isEmployeeModalOpen = true;
                // Force Alpine to recognize the modal is now visible
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
                        last_name: employee.last_name,
                        email: employee.email,
                        phone: employee.phone || '',
                        address: employee.address || '',
                        gender: employee.gender || '',
                        dob: employee.dob ? employee.dob.split('T')[0] : '', // Format date for input
                        employee_id: employee.employee_id,
                        department: employee.department,
                        role: employee.role,
                        user_status: employee.user_status || 'Active',
                        hire_date: employee.hire_date ? employee.hire_date.split('T')[0] : '', // Format date for input
                        profile_photo_path: employee.profile_photo_path,
                        profile_photo_url: employee.profile_photo_url,
                        password: '', // Don't show password when editing
                        remove_profile_photo: false
                    };
                    this.isEditing = true;
                    this.isEmployeeModalOpen = true;
                    // Force Alpine to recognize the modal is now visible
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
                    // Force Alpine to recognize the modal is now visible
                    setTimeout(() => {
                        const modal = document.querySelector('[x-show="isViewModalOpen"]');
                        if (modal) modal.style.display = 'block';
                    }, 50);
                }
            },
            
            confirmDeleteEmployee(id) {
                this.employeeToDelete = id;
                this.isDeleteModalOpen = true;
                // Force Alpine to recognize the modal is now visible
                setTimeout(() => {
                    const modal = document.querySelector('[x-show="isDeleteModalOpen"]');
                    if (modal) modal.style.display = 'block';
                }, 50);
            },
            
            closeEmployeeModal() {
                this.isEmployeeModalOpen = false;
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
                    // Create a preview URL
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.currentEmployee.profile_photo_url = e.target.result;
                        // Also store the file for later upload
                        this.currentEmployee.profile_photo_file = file;
                        this.currentEmployee.remove_profile_photo = false;
                    };
                    reader.readAsDataURL(file);
                }
            },
            
            saveEmployee() {
                const url = this.isEditing 
                    ? "{{ route('admin.employees.update', ['employee' => ':id']) }}".replace(':id', this.currentEmployee.id)
                    : "{{ route('admin.employees.store') }}";
                const method = this.isEditing ? 'PUT' : 'POST';
                
                // Prepare form data
                const formData = new FormData();
                
                // Manually append all fields explicitly
                formData.append('first_name', this.currentEmployee.first_name || '');
                formData.append('last_name', this.currentEmployee.last_name || '');
                formData.append('email', this.currentEmployee.email || '');
                formData.append('phone', this.currentEmployee.phone || '');
                formData.append('employee_id', this.currentEmployee.employee_id || '');
                formData.append('department', this.currentEmployee.department || '');
                formData.append('role', this.currentEmployee.role || 'employee');
                formData.append('gender', this.currentEmployee.gender || '');
                formData.append('dob', this.currentEmployee.dob || '');
                formData.append('address', this.currentEmployee.address || '');
                formData.append('hire_date', this.currentEmployee.hire_date || '');
                formData.append('user_status', this.currentEmployee.user_status || 'Active');
                
                // Only append password if it's a new employee or being changed
                if (!this.isEditing && this.currentEmployee.password) {
                    formData.append('password', this.currentEmployee.password);
                }
                
                // Add profile photo if it's a file
                if (this.currentEmployee.profile_photo_file) {
                    formData.append('profile_photo', this.currentEmployee.profile_photo_file);
                }
                
                // Add _method for PUT requests if needed
                if (this.isEditing) {
                    formData.append('_method', 'PUT');
                }

                if (this.currentEmployee.remove_profile_photo) {
                    formData.append('remove_profile_photo', '1');
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
                            // Update existing employee in the list
                            const index = this.employees.findIndex(emp => emp.id === data.employee.id);
                            if (index !== -1) {
                                this.employees[index] = data.employee;
                            }
                        } else {
                            // Add new employee to the list
                            this.employees.unshift(data.employee);
                        }
                        
                        this.closeEmployeeModal();
                        this.showToast(data.message);
                        
                        // Reset the file input
                        const photoInput = document.getElementById('profile_photo');
                        if (photoInput) {
                            photoInput.value = '';
                        }
                    } else {
                        throw new Error(data.message || 'Failed to save employee');
                    }
                })
                .catch(error => {
                    this.showToast(error.message, 'error');
                    console.error('Error:', error);
                });
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
            },
            
            filterEmployees() {
                // Reset to first page when filters change
                this.currentPage = 1;
            }
        };
    }
</script>
@endpush
@endsection