@extends('layouts.employee')

@section('title', 'Travel Authority Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Travel Authority Details</h1>
            <a href="{{ route('employees.travel') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
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
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Purpose</label>
                <p class="mt-1 text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $travel->purpose }}</p>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Source of Funds</label>
                <p class="mt-1 text-gray-900">{{ $travel->source_of_funds ?? 'Not specified' }}</p>
            </div>
        </div>

        <!-- Approval Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Approval Status</h2>
            
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
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Overall Status -->
            <div class="mt-6 p-4 border rounded-lg 
                @if($travel->status == 'approved') border-green-200 bg-green-50
                @elseif($travel->status == 'rejected') border-red-200 bg-red-50
                @else border-yellow-200 bg-yellow-50 @endif">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">Overall Status</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            @if($travel->status == 'pending')
                                Your travel authority is pending initial approval
                            @elseif($travel->status == 'recommending_approval')
                                Recommending approval completed, pending financial approvals
                            @elseif($travel->status == 'approved')
                                Your travel authority has been fully approved
                            @elseif($travel->status == 'rejected')
                                Your travel authority has been rejected
                            @endif
                        </p>
                    </div>
                    <span class="px-4 py-2 rounded-full font-semibold
                        @if($travel->status == 'approved') bg-green-100 text-green-800
                        @elseif($travel->status == 'rejected') bg-red-100 text-red-800
                        @elseif($travel->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $travel->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if($travel->status == 'pending')
        <div class="mt-6 flex justify-end">
            <form action="{{ route('employees.travel.destroy', $travel) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg" 
                        onclick="return confirm('Are you sure you want to cancel this travel authority?')">
                    Cancel Travel Authority
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection