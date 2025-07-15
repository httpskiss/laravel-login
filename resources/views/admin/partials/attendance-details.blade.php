<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-2">
        <div>
            <label class="block text-sm font-medium text-gray-500">Employee</label>
            <p class="mt-1 text-sm text-gray-900">{{ $attendance->user->first_name }} {{ $attendance->user->last_name }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Date</label>
            <p class="mt-1 text-sm text-gray-900">{{ $attendance->date->format('M d, Y') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Department</label>
            <p class="mt-1 text-sm text-gray-900">{{ $attendance->user->department }}</p>
        </div>
    </div>
    
    <div class="space-y-2">
        <div>
            <label class="block text-sm font-medium text-gray-500">Time In</label>
            <p class="mt-1 text-sm text-gray-900">{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Time Out</label>
            <p class="mt-1 text-sm text-gray-900">{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Total Hours</label>
            <p class="mt-1 text-sm text-gray-900">{{ $attendance->total_hours ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<div class="mt-4 space-y-2">
    <div>
        <label class="block text-sm font-medium text-gray-500">Status</label>
        <p class="mt-1 text-sm">
            @if($attendance->status === 'present')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
            @elseif($attendance->status === 'late')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Late</span>
            @elseif($attendance->status === 'absent')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
            @elseif($attendance->status === 'on_leave')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">On Leave</span>
            @elseif($attendance->status === 'half_day')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Half Day</span>
            @endif
        </p>
    </div>
    
    @if($attendance->is_regularized)
    <div>
        <label class="block text-sm font-medium text-gray-500">Regularized By</label>
        <p class="mt-1 text-sm text-gray-900">{{ $attendance->regularizedBy->name ?? 'System' }}</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-500">Regularization Reason</label>
        <p class="mt-1 text-sm text-gray-900">{{ $attendance->regularization_reason }}</p>
    </div>
    @endif
    
    @if($attendance->notes)
    <div>
        <label class="block text-sm font-medium text-gray-500">Notes</label>
        <p class="mt-1 text-sm text-gray-900">{{ $attendance->notes }}</p>
    </div>
    @endif
</div>