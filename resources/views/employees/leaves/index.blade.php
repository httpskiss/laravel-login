@extends('layouts.employee')

@section('title', 'My Leave Applications')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="employeeLeaveManagement()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Leave Applications</h1>
            <p class="text-gray-600">Apply for leave and track your applications</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('employees.leaves.create') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-plus mr-2"></i> Apply for Leave
            </a>
        </div>
    </div>

    <!-- Leave Balances -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Vacation Leave</p>
                        <h3 class="text-2xl font-bold">{{ $leaveBalances['vacation'] ?? 0 }} days</h3>
                    </div>
                </div>
                <span class="text-sm text-gray-500">Available</span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Sick Leave</p>
                        <h3 class="text-2xl font-bold">{{ $leaveBalances['sick'] ?? 0 }} days</h3>
                    </div>
                </div>
                <span class="text-sm text-gray-500">Available</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    @foreach(App\Models\Leave::getLeaveTypes() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
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
        @if($leaves->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
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
                            Applied On
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($leave->type === App\Models\Leave::TYPE_VACATION) bg-blue-100 text-blue-800
                                @elseif($leave->type === App\Models\Leave::TYPE_SICK) bg-green-100 text-green-800
                                @elseif($leave->type === App\Models\Leave::TYPE_MATERNITY) bg-purple-100 text-purple-800
                                @elseif($leave->type === App\Models\Leave::TYPE_PATERNITY) bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ App\Models\Leave::getLeaveTypes()[$leave->type] ?? $leave->type }}
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($leave->created_at)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button @click="viewLeave({{ $leave->id }})" 
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($leave->status === 'pending')
                            <button @click="deleteLeave({{ $leave->id }})" 
                                    class="text-red-600 hover:text-red-900"
                                    title="Delete Application">
                                <i class="fas fa-trash"></i>
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
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No leave applications found</h3>
            <p class="text-gray-500 mb-4">You haven't applied for any leave yet.</p>
            <a href="{{ route('employees.leaves.create') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Apply for Leave
            </a>
        </div>
        @endif
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
    function employeeLeaveManagement() {
        return {
            filters: {
                status: '',
                type: '',
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
                
                window.location.href = '{{ route('employees.leaves') }}?' + params.toString();
            },
            
            async viewLeave(leaveId) {
                try {
                    const response = await fetch(`/employees/leaves/${leaveId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.leaveDetailsContent = data.html;
                        this.isViewModalOpen = true;
                    }
                } catch (error) {
                    this.showToast('Error loading leave details', 'error');
                }
            },
            
            async deleteLeave(leaveId) {
                if (confirm('Are you sure you want to delete this leave application?')) {
                    try {
                        const response = await fetch(`/employees/leaves/${leaveId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.showToast('Leave application deleted successfully!', 'success');
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
@endsection