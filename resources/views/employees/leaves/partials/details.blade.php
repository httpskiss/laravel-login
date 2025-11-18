<!-- resources/views/employees/leaves/partials/details.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Leave Information -->
    <div class="space-y-4">
        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Leave Information</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Leave Type</p>
                <p class="text-sm text-gray-900">{{ $leave->type }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status</p>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                    @if($leave->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($leave->status === 'approved') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($leave->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Start Date</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">End Date</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Duration</p>
                <p class="text-sm text-gray-900">{{ $leave->days }} days</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Commutation</p>
                <p class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $leave->commutation)) }}</p>
            </div>
        </div>
    </div>

    <!-- Reason for Leave -->
    <div>
        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Reason for Leave</h4>
        <p class="text-sm text-gray-700 mt-2 bg-gray-50 p-4 rounded-lg">{{ $leave->reason }}</p>
    </div>

    <!-- Supporting Documents -->
    @if($leave->medical_certificate_path || $leave->travel_itinerary_path)
    <div class="md:col-span-2">
        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Supporting Documents</h4>
        <div class="flex space-x-4 mt-2">
            @if($leave->medical_certificate_path)
            <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-medical mr-2"></i> Medical Certificate
            </a>
            @endif
            @if($leave->travel_itinerary_path)
            <a href="{{ Storage::url($leave->travel_itinerary_path) }}" target="_blank" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-route mr-2"></i> TravelAuthorityItinerary
            </a>
            @endif
        </div>
    </div>
    @endif
</div>