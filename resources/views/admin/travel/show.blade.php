@extends('layouts.admin')

@section('title', 'Travel Authority Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Travel Authority Details</h1>
            <a href="{{ route('admin.travel') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <!-- Travel Authority Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Travel Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Travel Authority No.</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $travel->travel_authority_no ?? 'Pending Assignment' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Employee</label>
                        <p class="mt-1 text-gray-900">
                            {{ $travel->user->first_name }} {{ $travel->user->last_name }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Designation</label>
                        <p class="mt-1 text-gray-900">{{ $travel->designation }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Destination</label>
                        <p class="mt-1 text-gray-900">{{ $travel->destination }}</p>
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Inclusive Date of Travel</label>
                        <p class="mt-1 text-gray-900">
                            {{ $travel->inclusive_date_of_travel->format('F d, Y') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Transportation</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            {{ $travel->transportation ? str_replace('_', ' ', $travel->transportation) : 'Not specified' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Estimated Expenses</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            {{ str_replace('_', ' ', $travel->estimated_expenses) }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Source of Funds</label>
                        <p class="mt-1 text-gray-900">{{ $travel->source_of_funds ?? 'Not specified' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Purpose</label>
                <p class="mt-1 text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $travel->purpose }}</p>
            </div>
        </div>

        <!-- Approval Workflow -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Approval Workflow</h2>
            
            <div class="space-y-4">
                @foreach(['recommending_approval', 'allotment_available', 'funds_available', 'final_approval'] as $step)
                @php
                    $approval = $travel->approvals->where('approval_type', $step)->first();
                    $stepLabels = [
                        'recommending_approval' => 'Recommending Approval',
                        'allotment_available' => 'Allotment Available',
                        'funds_available' => 'Funds Available',
                        'final_approval' => 'Final Approval'
                    ];
                @endphp
                
                <div class="flex items-center justify-between p-4 border rounded-lg 
                    @if($approval && $approval->status === 'approved') border-green-200 bg-green-50
                    @elseif($approval && $approval->status === 'rejected') border-red-200 bg-red-50
                    @else border-gray-200 bg-gray-50 @endif">
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mr-4
                            @if($approval && $approval->status === 'approved') bg-green-500 text-white
                            @elseif($approval && $approval->status === 'rejected') bg-red-500 text-white
                            @elseif($step === $approvableStep) bg-blue-500 text-white
                            @else bg-gray-300 text-gray-600 @endif">
                            @if($approval && $approval->status === 'approved')
                                <i class="fas fa-check text-xs"></i>
                            @elseif($approval && $approval->status === 'rejected')
                                <i class="fas fa-times text-xs"></i>
                            @else
                                <span class="text-xs">{{ $loop->iteration }}</span>
                            @endif
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $stepLabels[$step] }}</h3>
                            @if($approval && $approval->approver)
                            <p class="text-sm text-gray-600">
                                By: {{ $approval->approver->first_name }} {{ $approval->approver->last_name }}
                                @if($approval->approved_at)
                                - {{ $approval->approved_at->format('M d, Y g:i A') }}
                                @endif
                            </p>
                            @if($approval->comments)
                            <p class="text-sm text-gray-600 mt-1">Comments: {{ $approval->comments }}</p>
                            @endif
                            @endif
                        </div>
                    </div>

                    <div>
                        @if($approval && $approval->status === 'approved')
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                Approved
                            </span>
                        @elseif($approval && $approval->status === 'rejected')
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full">
                                Rejected
                            </span>
                        @elseif($step === $approvableStep)
                            <!-- Approval Form -->
                            <div class="flex space-x-2">
                                <form action="{{ route('admin.travel.approve', ['travel' => $travel, 'step' => $step]) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                        Approve
                                    </button>
                                </form>
                                
                                <button type="button" onclick="showRejectModal('{{ $step }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Reject
                                </button>
                            </div>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Reject Modal for this step -->
                @if($step === $approvableStep)
                <div id="rejectModal-{{ $step }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Travel Authority</h3>
                            <form action="{{ route('admin.travel.approve', ['travel' => $travel, 'step' => $step]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <div class="mb-4">
                                    <label for="comments-{{ $step }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        Reason for Rejection
                                    </label>
                                    <textarea name="comments" id="comments-{{ $step }}" rows="3" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                        placeholder="Please provide a reason for rejection..." required></textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="hideRejectModal('{{ $step }}')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                        Confirm Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        <!-- Current Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Current Status</h2>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-lg font-semibold 
                        @if($travel->status == 'approved') text-green-600
                        @elseif($travel->status == 'rejected') text-red-600
                        @elseif($travel->status == 'pending') text-yellow-600
                        @else text-blue-600 @endif">
                        {{ ucfirst(str_replace('_', ' ', $travel->status)) }}
                    </span>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($travel->status == 'pending')
                            Waiting for initial approval
                        @elseif($travel->status == 'recommending_approval')
                            Recommending approval completed, pending financial approvals
                        @elseif($travel->status == 'approved')
                            All approvals completed
                        @elseif($travel->status == 'rejected')
                            Travel authority has been rejected
                        @endif
                    </p>
                </div>
                
                @if($travel->remarks)
                <div class="text-right">
                    <label class="block text-sm font-medium text-gray-600">Remarks</label>
                    <p class="text-sm text-gray-900">{{ $travel->remarks }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showRejectModal(step) {
    document.getElementById('rejectModal-' + step).classList.remove('hidden');
}

function hideRejectModal(step) {
    document.getElementById('rejectModal-' + step).classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>

<style>
.fixed {
    backdrop-filter: blur(4px);
}
</style>
@endsection