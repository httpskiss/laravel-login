@extends('layouts.admin')

@section('title', 'Leave Management')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="leaveManagement()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Leave Management</h1>
            <p class="text-gray-600">Manage and approve employee leave applications</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button @click="exportLeaves" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Pending</p>
                    <h3 class="text-2xl font-bold">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Approved</p>
                    <h3 class="text-2xl font-bold">{{ $stats['approved'] }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-times"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Rejected</p>
                    <h3 class="text-2xl font-bold">{{ $stats['rejected'] }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">This Month</p>
                    <h3 class="text-2xl font-bold">{{ $stats['this_month'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" @change="filterLeaves" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select x-model="filters.type" @change="filterLeaves" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Types</option>
                    <option value="Vacation Leave">Vacation Leave</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Maternity Leave">Maternity Leave</option>
                    <option value="Paternity Leave">Paternity Leave</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select x-model="filters.department" @change="filterLeaves" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <input type="month" x-model="filters.month" @change="filterLeaves" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>
    </div>

    <!-- Leaves Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Leave Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dates
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Duration
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($leaves as $leave)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full mr-3" 
                                     src="{{ $leave->user->profile_photo_url }}" 
                                     alt="{{ $leave->user->first_name }}">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $leave->user->first_name }} {{ $leave->user->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $leave->user->department }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($leave->type === 'Vacation Leave') bg-blue-100 text-blue-800
                                @elseif($leave->type === 'Sick Leave') bg-green-100 text-green-800
                                @elseif($leave->type === 'Maternity Leave') bg-purple-100 text-purple-800
                                @elseif($leave->type === 'Paternity Leave') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $leave->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $leave->days }} days
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($leave->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($leave->status === 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button @click="viewLeave({{ $leave->id }})" 
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($leave->status === 'pending')
                            <button @click="approveLeave({{ $leave->id }})" 
                                    class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-check"></i>
                            </button>
                            <button @click="rejectLeave({{ $leave->id }})" 
                                    class="text-red-600 hover:text-red-900">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $leaves->links() }}
        </div>
    </div>

    <!-- Leave Details Modal -->
    <div x-show="isViewModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">Leave Application Details</h3>
                <button @click="closeViewModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mt-4" x-html="leaveDetailsContent"></div>
            
            <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                <button @click="closeViewModal" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function leaveManagement() {
        return {
            filters: {
                status: '',
                type: '',
                department: '',
                month: ''
            },
            isViewModalOpen: false,
            leaveDetailsContent: '',
            
            filterLeaves() {
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.append(key, this.filters[key]);
                    }
                });
                
                window.location.href = '{{ route('admin.leaves.index') }}?' + params.toString();
            },
            
            async viewLeave(leaveId) {
                try {
                    const response = await fetch(`/admin/leaves/${leaveId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.leaveDetailsContent = data.html;
                        this.isViewModalOpen = true;
                    }
                } catch (error) {
                    this.showToast('Error loading leave details', 'error');
                }
            },
            
            async approveLeave(leaveId) {
                if (confirm('Are you sure you want to approve this leave application?')) {
                    try {
                        const response = await fetch(`/admin/leaves/${leaveId}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.showToast('Leave application approved successfully!', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        this.showToast(error.message, 'error');
                    }
                }
            },
            
            async rejectLeave(leaveId) {
                const reason = prompt('Please provide reason for rejection:');
                if (reason) {
                    try {
                        const response = await fetch(`/admin/leaves/${leaveId}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ reason })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.showToast('Leave application rejected!', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        this.showToast(error.message, 'error');
                    }
                }
            },
            
            closeViewModal() {
                this.isViewModalOpen = false;
                this.leaveDetailsContent = '';
            },
            
            exportLeaves() {
                const params = new URLSearchParams(this.filters);
                window.open('{{ route('admin.leaves.export') }}?' + params.toString(), '_blank');
            },
            
            showToast(message, type = 'success') {
                // Toast implementation
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
@endsection