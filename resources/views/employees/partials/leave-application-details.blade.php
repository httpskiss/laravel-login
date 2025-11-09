<div class="space-y-6">
    <!-- Application Header -->
    <div class="flex items-center justify-between">
        <div>
            <h4 class="text-lg font-semibold text-gray-800 capitalize">
                {{ str_replace('_', ' ', $application->type) }} Leave
            </h4>
            <span class="px-3 py-1 rounded-full text-sm font-medium status-{{ $application->status }}">
                {{ ucfirst($application->status) }}
            </span>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Applied on</p>
            <p class="font-medium">{{ $application->created_at->format('F j, Y') }}</p>
        </div>
    </div>

    <!-- Application Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500">Duration</label>
                <p class="font-medium">{{ $application->days }} day(s)</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Start Date</label>
                <p class="font-medium">{{ \Carbon\Carbon::parse($application->start_date)->format('F j, Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Commutation</label>
                <p class="font-medium">{{ $application->commutation === 'requested' ? 'Requested' : 'Not Requested' }}</p>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500">End Date</label>
                <p class="font-medium">{{ \Carbon\Carbon::parse($application->end_date)->format('F j, Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Office/Department</label>
                <p class="font-medium">{{ $application->department }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Position</label>
                <p class="font-medium">{{ $application->position }}</p>
            </div>
        </div>
    </div>

    <!-- Reason -->
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-2">Reason for Leave</label>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-700">{{ $application->reason }}</p>
        </div>
    </div>

    <!-- Leave Credits Used -->
    @if($application->vacation_less || $application->sick_less)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h5 class="font-medium text-blue-800 mb-2">Leave Credits Used</h5>
        <div class="grid grid-cols-2 gap-4 text-sm">
            @if($application->vacation_less)
            <div>
                <span class="text-blue-600">Vacation Leave:</span>
                <span class="font-medium">{{ $application->vacation_less }} days</span>
            </div>
            @endif
            @if($application->sick_less)
            <div>
                <span class="text-blue-600">Sick Leave:</span>
                <span class="font-medium">{{ $application->sick_less }} days</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($application->status === 'rejected' && $application->rejection_reason)
    <div>
        <label class="block text-sm font-medium text-red-500 mb-2">Rejection Reason</label>
        <div class="bg-red-50 rounded-lg p-4">
            <p class="text-red-700">{{ $application->rejection_reason }}</p>
        </div>
    </div>
    @endif

    @if($application->status === 'approved' && $application->approved_at)
    <div>
        <label class="block text-sm font-medium text-green-500 mb-2">Approved On</label>
        <p class="font-medium">{{ \Carbon\Carbon::parse($application->approved_at)->format('F j, Y') }}</p>
    </div>
    @endif

    @if($application->status === 'rejected' && $application->rejected_at)
    <div>
        <label class="block text-sm font-medium text-red-500 mb-2">Rejected On</label>
        <p class="font-medium">{{ \Carbon\Carbon::parse($application->rejected_at)->format('F j, Y') }}</p>
    </div>
    @endif

    @if($application->status === 'cancelled' && $application->cancelled_at)
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-2">Cancelled On</label>
        <p class="font-medium">{{ \Carbon\Carbon::parse($application->cancelled_at)->format('F j, Y') }}</p>
    </div>
    @endif
</div>