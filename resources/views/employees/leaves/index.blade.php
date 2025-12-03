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

    <!-- CSC Leave Balances -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Vacation Leave -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl shadow-sm p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Vacation Leave</p>
                        <h3 class="text-2xl font-bold text-blue-900">
                            {{ number_format($leaveBalances->vacation_leave ?? 0, 2) }} days
                        </h3>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">CSC</span>
            </div>
        </div>

        <!-- Sick Leave -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl shadow-sm p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Sick Leave</p>
                        <h3 class="text-2xl font-bold text-green-900">
                            {{ number_format($leaveBalances->sick_leave ?? 0, 2) }} days
                        </h3>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">CSC</span>
            </div>
        </div>

        <!-- Special Leave Privileges -->
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl shadow-sm p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Special Privileges</p>
                        <h3 class="text-2xl font-bold text-purple-900">
                            {{ number_format($leaveBalances->special_leave_privileges ?? 3, 2) }} days
                        </h3>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded-full">CSC</span>
            </div>
        </div>

        <!-- Forced Leave Taken -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl shadow-sm p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Forced Leave Taken</p>
                        <h3 class="text-2xl font-bold text-orange-900">
                            {{ number_format($leaveBalances->forced_leave_taken ?? 0, 2) }}/5 days
                        </h3>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded-full">CSC</span>
            </div>
        </div>
    </div>

    <!-- CSC Classification Badge -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl p-4 border border-indigo-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                        <i class="fas fa-user-tie text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-indigo-700">CSC Classification</p>
                        <p class="font-semibold text-indigo-900">
                            {{ auth()->user()->getClassificationOptions()[auth()->user()->employee_classification] ?? 'Regular Employee' }}
                            @if(auth()->user()->is_teacher)
                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Teacher (PVP)</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-indigo-700">Work Hours</p>
                    <p class="font-semibold text-indigo-900">
                        {{ auth()->user()->work_hours_per_day }} hrs/day | {{ auth()->user()->work_hours_per_week }} hrs/week
                    </p>
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
                    @foreach(App\Models\Leave::getLeaveTypes() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CSC Employee Type</label>
                <select x-model="filters.csc_type" @change="filterLeaves" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Types</option>
                    @foreach(App\Models\Leave::getCscEmployeeTypes() as $value => $label)
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
                            Leave Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dates & Duration
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            CSC Details
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
                        <!-- Leave Details -->
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="flex items-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($leave->type === App\Models\Leave::TYPE_VACATION) bg-blue-100 text-blue-800
                                        @elseif($leave->type === App\Models\Leave::TYPE_SICK) bg-green-100 text-green-800
                                        @elseif($leave->type === App\Models\Leave::TYPE_MATERNITY) bg-purple-100 text-purple-800
                                        @elseif($leave->type === App\Models\Leave::TYPE_PATERNITY) bg-yellow-100 text-yellow-800
                                        @elseif($leave->type === App\Models\Leave::TYPE_SPECIAL_PRIVILEGE) bg-pink-100 text-pink-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ App\Models\Leave::getLeaveTypes()[$leave->type] ?? $leave->type }}
                                    </span>
                                    @if($leave->is_lwop)
                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            LWOP
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Applied: {{ \Carbon\Carbon::parse($leave->created_at)->format('M d, Y') }}
                                </p>
                                @if($leave->is_forced_leave)
                                <p class="text-xs text-orange-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Mandatory/Forced Leave
                                </p>
                                @endif
                            </div>
                        </td>

                        <!-- Dates & Duration -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}
                                    @if($leave->end_date != $leave->start_date)
                                        - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                    @endif
                                </p>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">{{ $leave->days }} days</span>
                                    @if($leave->duration_type === 'half_day')
                                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full capitalize">
                                            {{ $leave->getHalfDayTimeDisplay() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- CSC Details -->
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @if($leave->csc_employee_type)
                                <p class="text-xs text-gray-600">
                                    <span class="font-medium">Type:</span> 
                                    {{ $leave->getCscEmployeeTypeDisplay() }}
                                </p>
                                @endif
                                @if($leave->equivalent_days_csc && $leave->equivalent_days_csc != $leave->days)
                                <p class="text-xs text-blue-600">
                                    <span class="font-medium">CSC Equivalent:</span> 
                                    {{ number_format($leave->equivalent_days_csc, 4) }} days
                                </p>
                                @endif
                                @if($leave->slp_type !== 'none')
                                <p class="text-xs text-pink-600">
                                    <i class="fas fa-star mr-1"></i>
                                    SLP: {{ $leave->getSlpTypeDisplay() }}
                                </p>
                                @endif
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($leave->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($leave->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($leave->status) }}
                                </span>
                                @if($leave->approved_at)
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($leave->approved_at)->format('M d, Y') }}
                                </p>
                                @endif
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- View Details Button -->
                                <button @click="viewLeave({{ $leave->id }})" 
                                        class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- PDF Download Button -->
                                @if($leave->pdf_path && Storage::exists($leave->pdf_path))
                                <a href="{{ route('employees.leaves.download-pdf', $leave) }}" 
                                   class="p-2 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition"
                                   title="Download PDF"
                                   target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @else
                                <button onclick="generatePdf({{ $leave->id }})" 
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition"
                                        title="Generate PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                @endif
                                
                                <!-- CSC Computation Button -->
                                @if($leave->followsCscRules())
                                <button onclick="showCscComputation({{ $leave->id }})"
                                        class="p-2 text-purple-600 hover:text-purple-900 hover:bg-purple-50 rounded-lg transition"
                                        title="CSC Computation">
                                    <i class="fas fa-calculator"></i>
                                </button>
                                @endif

                                <!-- Delete Button (only for pending leaves) -->
                                @if($leave->status === 'pending')
                                <button @click="deleteLeave({{ $leave->id }})" 
                                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition"
                                        title="Delete Application">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
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

    <!-- CSC Computation Modal -->
    <div x-show="isCscModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-calculator mr-2 text-purple-600"></i>
                    CSC Leave Computation
                </h3>
                <button @click="closeCscModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mt-4" x-html="cscComputationContent"></div>
            
            <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                <button @click="closeCscModal" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
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
    function employeeLeaveManagement() {
        return {
            filters: {
                status: '',
                type: '',
                csc_type: '',
                month: ''
            },
            isViewModalOpen: false,
            isCscModalOpen: false,
            leaveDetailsContent: '',
            cscComputationContent: '',
            
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
            
            async showCscComputation(leaveId) {
                try {
                    const response = await fetch(`/employees/leaves/${leaveId}/csc-computation`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.cscComputationContent = data.html;
                        this.isCscModalOpen = true;
                    }
                } catch (error) {
                    this.showToast('Error loading CSC computation', 'error');
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
            
            closeCscModal() {
                this.isCscModalOpen = false;
                this.cscComputationContent = '';
            },
            
            showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white flex items-center ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' :
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
                } z-50 transition-all duration-300 ease-in-out`;
                
                const icon = document.createElement('i');
                icon.className = type === 'success' ? 'fas fa-check-circle mr-2' : 
                                type === 'error' ? 'fas fa-exclamation-circle mr-2' :
                                type === 'warning' ? 'fas fa-exclamation-triangle mr-2' : 'fas fa-info-circle mr-2';
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
    
    function generatePdf(leaveId) {
        fetch(`/employees/leaves/${leaveId}/regenerate-pdf`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Error generating PDF');
        });
    }
</script>
@endpush
@endsection