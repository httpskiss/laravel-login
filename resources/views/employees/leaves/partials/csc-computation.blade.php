<!-- CSC Computation Modal Content -->
<div class="space-y-4">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-calculator mr-2 text-purple-600"></i>
        CSC Leave Computation Details
    </h3>

    <!-- Employee Classification -->
    <div class="bg-gray-50 rounded-lg p-4">
        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Employee Classification</p>
        <div class="space-y-2">
            <p class="text-sm font-medium text-gray-900">
                {{ $leave->getLeaveClassificationDisplay() ?? $leave->csc_employee_type }}
            </p>
            <p class="text-xs text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Leave Basis: {{ $leave->getLeaveBasisDisplay() }}
            </p>
        </div>
    </div>

    <!-- Duration & Calculation -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-xs font-semibold text-blue-600 uppercase mb-2">Calendar Days</p>
            <p class="text-2xl font-bold text-blue-900">{{ $leave->days }}</p>
            <p class="text-xs text-blue-700 mt-1">
                {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} 
                to 
                {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
            </p>
        </div>

        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <p class="text-xs font-semibold text-purple-600 uppercase mb-2">CSC Equivalent Days</p>
            <p class="text-2xl font-bold text-purple-900">{{ number_format($leave->equivalent_days_csc, 4) }}</p>
            @if($leave->total_hours)
            <p class="text-xs text-purple-700 mt-1">
                {{ number_format($leave->total_hours, 2) }} hours
            </p>
            @endif
        </div>
    </div>

    <!-- Special Leave Details -->
    @if($leave->slp_type && $leave->slp_type !== 'none')
    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
        <p class="text-xs font-semibold text-yellow-600 uppercase mb-2">Special Leave Privilege (SLP)</p>
        <p class="text-sm font-medium text-yellow-900">{{ $leave->getSlpTypeDisplay() }}</p>
    </div>
    @endif

    <!-- LWOP Calculation (if applicable) -->
    @if($leave->is_lwop)
    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
        <p class="text-xs font-semibold text-orange-600 uppercase mb-3">Leave Without Pay (LWOP) Deduction</p>
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-sm text-orange-900">Deduction Rate:</span>
                <span class="font-semibold text-orange-900">{{ number_format($leave->lwop_deduction_rate * 100, 1) }}%</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-orange-900">Days Charged:</span>
                <span class="font-semibold text-orange-900">{{ number_format($leave->lwop_days_charged, 4) }} days</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Maternity/Paternity Details -->
    @if($leave->type === 'maternity' && $leave->maternity_delivery_date)
    <div class="bg-pink-50 rounded-lg p-4 border border-pink-200">
        <p class="text-xs font-semibold text-pink-600 uppercase mb-2">Maternity Leave Details</p>
        <div class="space-y-1">
            <p class="text-sm text-pink-900">
                <span class="font-medium">Delivery Date:</span> {{ \Carbon\Carbon::parse($leave->maternity_delivery_date)->format('M d, Y') }}
            </p>
            @if($leave->is_miscarriage)
            <p class="text-sm text-pink-900">
                <i class="fas fa-info-circle mr-1"></i>
                <span class="font-medium">Classification:</span> Miscarriage
            </p>
            @endif
        </div>
    </div>
    @endif

    @if($leave->type === 'paternity')
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <p class="text-xs font-semibold text-blue-600 uppercase mb-2">Paternity Leave Details</p>
        <div class="space-y-1">
            <p class="text-sm text-blue-900">
                <span class="font-medium">Delivery Count:</span> {{ $leave->paternity_delivery_count ?? 'N/A' }}
            </p>
        </div>
    </div>
    @endif

    <!-- Computation Notes -->
    @if($leave->computation_notes)
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <p class="text-xs font-semibold text-blue-600 uppercase mb-2">Computation Notes</p>
        <p class="text-sm text-blue-900 whitespace-pre-line">{{ $leave->computation_notes }}</p>
    </div>
    @endif

    <!-- CSC Compliance Badge -->
    <div class="flex items-center justify-center pt-2">
        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
            <i class="fas fa-check-circle mr-1"></i>
            CSC Omnibus Rules Compliant
        </span>
    </div>
</div>
