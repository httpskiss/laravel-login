@extends('layouts.admin')

@section('title', 'Leave Management')

@section('content')

<!-- Main Content -->
<main class="p-4">
    <!-- Stats Cards - Now Dynamic -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 stats-grid">
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <i class="fas fa-calendar-check text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Leaves</p>
                <h3 class="text-2xl font-bold">{{ $stats['total'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in" style="animation-delay: 0.1s">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Pending Approval</p>
                <h3 class="text-2xl font-bold">{{ $stats['pending'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in" style="animation-delay: 0.2s">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Approved</p>
                <h3 class="text-2xl font-bold">{{ $stats['approved'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 flex items-center leave-card animate-fade-in" style="animation-delay: 0.3s">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Rejected</p>
                <h3 class="text-2xl font-bold">{{ $stats['rejected'] }}</h3>
            </div>
        </div>
    </div>


    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 filter-grid">
            <!-- Filter Form (only contains filter elements) -->
            <form method="GET" action="{{ route('admin.leaves') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <div class="relative flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employee..." class="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <select name="status" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    @foreach(['pending', 'approved', 'rejected', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <select name="type" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    @foreach(['vacation', 'sick', 'emergency', 'maternity', 'paternity', 'bereavement', 'other'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg whitespace-nowrap">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </form>

            <!-- Action Buttons (outside the form) -->
            <div class="flex gap-2 action-buttons">
                <a href="{{ route('admin.leaves.export') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <i class="fas fa-download mr-2"></i> Export
                </a>
                <button id="addLeaveBtn" type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i> Add Leave
                </button>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Leave Requests Table - Now Dynamic -->
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
                    @forelse($leaves as $leave)
                    <tr class="leave-card hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $leave->user->profile_photo_path ?? 'https://via.placeholder.com/40x40' }}" alt="">
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $leave->user->first_name }} {{ $leave->user->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $leave->user->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge type-{{ $leave->type }}">{{ ucfirst($leave->type) }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $leave->days }} days</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $leave->days }} working days
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900 truncate max-w-xs">{{ $leave->reason }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="status-badge status-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="openViewModal({{ $leave->id }})" class="text-blue-600 hover:text-blue-900 mr-3" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($leave->status == 'pending')
                            <form action="{{ route('admin.leaves.update-status', $leave->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-3" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.leaves.update-status', $leave->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-center text-gray-500">No leave requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination - Now Dynamic -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium">{{ $leaves->firstItem() }}</span> to <span class="font-medium">{{ $leaves->lastItem() }}</span> of <span class="font-medium">{{ $leaves->total() }}</span> results
            </div>
            <div class="flex space-x-1">
                @if($leaves->onFirstPage())
                    <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md text-sm">Previous</span>
                @else
                    <a href="{{ $leaves->previousPageUrl() }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Previous</a>
                @endif

                @foreach($leaves->getUrlRange(1, $leaves->lastPage()) as $page => $url)
                    @if($page == $leaves->currentPage())
                        <span class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">{{ $page }}</a>
                    @endif
                @endforeach

                @if($leaves->hasMorePages())
                    <a href="{{ $leaves->nextPageUrl() }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Next</a>
                @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md text-sm">Next</span>
                @endif
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
            <form id="leaveForm" action="{{ route('admin.leaves.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Leave</h3>
                                <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="mb-4">
                                    <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                                    <select id="employee" name="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                                        <option value="">Select employee</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->employee_id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="leaveType" class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                                    <select id="leaveType" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                                        <option value="">Select leave type</option>
                                        @foreach(['vacation', 'sick', 'emergency', 'maternity', 'paternity', 'bereavement', 'other'] as $type)
                                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" id="startDate" name="start_date" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                                    </div>
                                    <div>
                                        <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                        <input type="date" id="endDate" name="end_date" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (days)</label>
                                    <input type="number" id="duration" name="days" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Enter duration" readonly>
                                </div>
                                <div class="mb-4">
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                    <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Enter reason for leave" required></textarea>
                                </div>
                                @can('approve_leaves')
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        @foreach(['pending', 'approved', 'rejected'] as $status)
                                            <option value="{{ $status }}" {{ $status == 'pending' ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save Leave
                    </button>
                    <button type="button" id="cancelModalBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const addLeaveBtn = document.getElementById('addLeaveBtn');
    const addLeaveModal = document.getElementById('addLeaveModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');

        if (addLeaveBtn && addLeaveModal) {
            addLeaveBtn.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent any default behavior
                addLeaveModal.classList.remove('hidden');
            });

        // Close modal when close button is clicked
        closeModalBtn.addEventListener('click', () => {
            addLeaveModal.classList.add('hidden');
        });

        // Close modal when cancel button is clicked
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

    // Handle form submission
    const leaveForm = document.getElementById('leaveForm');
    if (leaveForm) {
        leaveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // You can add form validation here if needed
            
            // Submit the form via AJAX or let it submit normally
            this.submit();
        });
    }
});
</script>
@endpush

@endsection