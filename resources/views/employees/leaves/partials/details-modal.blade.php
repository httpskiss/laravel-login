<!-- Leave Details Modal Content -->
<div class="space-y-4">
    <!-- Employee & Leave Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Leave Type</p>
            <p class="text-lg font-bold text-gray-900">{{ $leave->getLeaveTypeDisplay() }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
            <span class="inline-block px-3 py-1 {{ $leave->getStatusBadgeClass() }} text-sm font-semibold rounded-full">
                {{ ucfirst($leave->status) }}
            </span>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Start Date</p>
            <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">End Date</p>
            <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Duration</p>
            <div class="space-y-1">
                <p class="text-lg font-bold text-gray-900">{{ $leave->days }} days</p>
                @if($leave->duration_type === 'half_day')
                <p class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $leave->getHalfDayTimeDisplay() }}
                </p>
                @endif
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Reason</p>
            <p class="text-sm text-gray-700 line-clamp-3">{{ $leave->reason }}</p>
        </div>
    </div>

    <!-- CSC Details (if applicable) -->
    @if($leave->followsCscRules())
    <div class="border-t pt-4">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
            <i class="fas fa-calculator mr-2 text-purple-600"></i>
            CSC Computation Details
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Employee Classification</p>
                <p class="text-sm text-gray-900">{{ $leave->getLeaveClassificationDisplay() ?? $leave->csc_employee_type }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Leave Basis</p>
                <p class="text-sm text-gray-900">{{ $leave->getLeaveBasisDisplay() }}</p>
            </div>
            @if($leave->equivalent_days_csc && $leave->equivalent_days_csc != $leave->days)
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Equivalent CSC Days</p>
                <p class="text-sm text-gray-900">{{ number_format($leave->equivalent_days_csc, 4) }} days</p>
            </div>
            @endif
            @if($leave->is_lwop)
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">LWOP Deduction</p>
                <p class="text-sm text-gray-900">{{ number_format($leave->lwop_days_charged, 4) }} days</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Supporting Documents -->
    @if($leave->medical_certificate_path || $leave->travel_itinerary_path)
    <div class="border-t pt-4">
        <h4 class="font-semibold text-gray-900 mb-3">Supporting Documents</h4>
        <div class="space-y-2">
            @if($leave->medical_certificate_path)
            <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank"
               class="flex items-center text-blue-600 hover:text-blue-900">
                <i class="fas fa-file-pdf mr-2"></i>
                Medical Certificate
            </a>
            @endif
            @if($leave->travel_itinerary_path)
            <a href="{{ Storage::url($leave->travel_itinerary_path) }}" target="_blank"
               class="flex items-center text-blue-600 hover:text-blue-900">
                <i class="fas fa-file-pdf mr-2"></i>
                Travel Itinerary
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Filing Date -->
    <div class="border-t pt-4">
        <p class="text-xs font-semibold text-gray-500 uppercase">Filed On</p>
        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($leave->filing_date)->format('M d, Y h:i A') }}</p>
    </div>
</div>
