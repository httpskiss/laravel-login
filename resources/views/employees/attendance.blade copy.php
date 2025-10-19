@extends('layouts.employee')

@section('title', 'My Attendance')

@section('content')

<div class="flex flex-col h-full" x-data="employeeAttendanceModule()" x-cloak>
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

    <!-- Today's Attendance Card -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                Today's Attendance - {{ now()->format('l, F j, Y') }}
            </h3>
        </div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <span class="text-sm font-medium text-gray-500 mr-3">Status:</span>
                        <span 
                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                            :class="{
                                'bg-green-50 text-green-700': todayAttendance && todayAttendance.status === 'present',
                                'bg-red-50 text-red-700': todayAttendance && todayAttendance.status === 'absent',
                                'bg-yellow-50 text-yellow-700': todayAttendance && todayAttendance.status === 'late',
                                'bg-blue-50 text-blue-700': todayAttendance && todayAttendance.status === 'on_leave',
                                'bg-purple-50 text-purple-700': todayAttendance && todayAttendance.status === 'half_day',
                                'bg-gray-50 text-gray-700': !todayAttendance
                            }"
                            x-text="todayAttendance ? todayAttendance.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not Recorded'"
                        ></span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-500 mr-3">Time In:</span>
                        <span x-text="todayAttendance && todayAttendance.time_in ? todayAttendance.time_in : '--:--'" class="text-sm text-gray-700"></span>
                    </div>
                    <div class="flex items-center mt-1">
                        <span class="text-sm font-medium text-gray-500 mr-3">Time Out:</span>
                        <span x-text="todayAttendance && todayAttendance.time_out ? todayAttendance.time_out : '--:--'" class="text-sm text-gray-700"></span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        @click="checkInOut('check-in')"
                        :disabled="todayAttendance && todayAttendance.time_in"
                        :class="{'opacity-50 cursor-not-allowed': todayAttendance && todayAttendance.time_in}"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i> Check In
                    </button>
                    <button 
                        @click="checkInOut('check-out')"
                        :disabled="!todayAttendance || !todayAttendance.time_in || todayAttendance.time_out"
                        :class="{'opacity-50 cursor-not-allowed': !todayAttendance || !todayAttendance.time_in || todayAttendance.time_out}"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                    >
                        <i class="fas fa-sign-out-alt mr-2"></i> Check Out
                    </button>
                </div>
            </div>
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
            <button 
                @click="openRegularizationModal()"
                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition duration-150 ease-in-out"
            >
                <i class="fas fa-edit mr-2"></i> Request Regularization
            </button>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In/Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition duration-150 ease-in-out" @click="sortAttendances('status')">
                            <div class="flex items-center">
                                Status
                                <i class="fas fa-sort ml-1.5 text-gray-400" :class="{'fa-sort-up text-blue-500': sortColumn === 'status' && sortDirection === 'asc', 'fa-sort-down text-blue-500': sortColumn === 'status' && sortDirection === 'desc'}"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="attendance in paginatedAttendances" :key="attendance.id">
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="new Date(attendance.date).toLocaleDateString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div x-show="attendance.time_in" class="flex items-center">
                                    <i class="fas fa-sign-in-alt text-green-500 mr-2"></i>
                                    <span x-text="attendance.time_in"></span>
                                </div>
                                <div x-show="attendance.time_out" class="flex items-center mt-1">
                                    <i class="fas fa-sign-out-alt text-red-500 mr-2"></i>
                                    <span x-text="attendance.time_out"></span>
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
                            <td class="px-6 py-4 text-sm text-gray-600" x-text="attendance.notes || '--'"></td>
                        </tr>
                    </template>
                    <template x-if="filteredAttendances.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
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

    <!-- Regularization Modal -->
    <div 
        x-show="isRegularizationModalOpen" 
        @keydown.escape.window="closeRegularizationModal()"
        @click.away="closeRegularizationModal()"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity duration-300 ease-in-out"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-xl rounded-xl bg-white" 
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Request Attendance Regularization</h3>
                <button @click="closeRegularizationModal()" class="text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <form @submit.prevent="submitRegularization()">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                            <input 
                                x-model="regularizationData.date"
                                name="date"
                                type="date" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out" 
                                required
                            >
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Time In *</label>
                                <input 
                                    x-model="regularizationData.time_in"
                                    name="time_in"
                                    type="time" 
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Time Out *</label>
                                <input 
                                    x-model="regularizationData.time_out"
                                    name="time_out"
                                    type="time" 
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                    required
                                >
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                            <textarea 
                                x-model="regularizationData.reason"
                                name="reason"
                                rows="3" 
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition duration-150 ease-in-out"
                                required
                                placeholder="Explain why you need to regularize this attendance"
                            ></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                        <button 
                            type="button" 
                            @click="closeRegularizationModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function employeeAttendanceModule() {
        return {
            // Data properties
            attendances: @json($attendances->items()),
            todayAttendance: @json($todayAttendance),
            searchQuery: '',
            startDate: '',
            endDate: '',
            selectedStatus: '',
            sortColumn: 'date',
            sortDirection: 'desc',
            currentPage: 1,
            pageSize: 10,
            maxVisiblePages: 5,
            
            // Modal state
            isRegularizationModalOpen: false,
            regularizationData: {
                date: '',
                time_in: '',
                time_out: '',
                reason: ''
            },
            
            // Computed properties
            get filteredAttendances() {
                let filtered = this.attendances;
                
                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(att => 
                        att.date.toLowerCase().includes(query) || 
                        att.status.toLowerCase().includes(query) ||
                        (att.time_in && att.time_in.toLowerCase().includes(query)) ||
                        (att.time_out && att.time_out.toLowerCase().includes(query))
                    );
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
                    let aValue = a[this.sortColumn] || '';
                    let bValue = b[this.sortColumn] || '';
                    
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
            
            sortAttendances(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            },
            
            checkInOut(type) {
                fetch("{{ route('employee.attendance.check') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (type === 'check-in') {
                            if (!this.todayAttendance) {
                                this.todayAttendance = data.attendance;
                                this.attendances.unshift(data.attendance);
                            } else {
                                this.todayAttendance = data.attendance;
                                const index = this.attendances.findIndex(att => att.id === data.attendance.id);
                                if (index !== -1) {
                                    this.attendances[index] = data.attendance;
                                }
                            }
                        } else if (type === 'check-out') {
                            this.todayAttendance = data.attendance;
                            const index = this.attendances.findIndex(att => att.id === data.attendance.id);
                            if (index !== -1) {
                                this.attendances[index] = data.attendance;
                            }
                        }
                        
                        this.showToast(data.message);
                    } else {
                        throw new Error(data.message || 'Failed to process attendance');
                    }
                })
                .catch(error => {
                    this.showToast(error.message, 'error');
                    console.error('Error:', error);
                });
            },
            
            openRegularizationModal() {
                this.regularizationData = {
                    date: new Date().toISOString().split('T')[0],
                    time_in: '',
                    time_out: '',
                    reason: ''
                };
                this.isRegularizationModalOpen = true;
                setTimeout(() => {
                    const modal = document.querySelector('[x-show="isRegularizationModalOpen"]');
                    if (modal) modal.style.display = 'block';
                }, 50);
            },
            
            closeRegularizationModal() {
                this.isRegularizationModalOpen = false;
                const modal = document.querySelector('[x-show="isRegularizationModalOpen"]');
                if (modal) modal.style.display = 'none';
            },
            
            submitRegularization() {
                fetch("{{ route('employee.attendance.regularization') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        attendance_id: this.todayAttendance ? this.todayAttendance.id : null,
                        date: this.regularizationData.date,
                        time_in: this.regularizationData.time_in,
                        time_out: this.regularizationData.time_out,
                        reason: this.regularizationData.reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (this.todayAttendance) {
                            const index = this.attendances.findIndex(att => att.id === data.attendance.id);
                            if (index !== -1) {
                                this.attendances[index] = data.attendance;
                            }
                        } else {
                            this.attendances.unshift(data.attendance);
                        }
                        
                        this.todayAttendance = data.attendance;
                        this.closeRegularizationModal();
                        this.showToast(data.message);
                    } else {
                        throw new Error(data.message || 'Failed to submit regularization request');
                    }
                })
                .catch(error => {
                    this.showToast(error.message, 'error');
                    console.error('Error:', error);
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