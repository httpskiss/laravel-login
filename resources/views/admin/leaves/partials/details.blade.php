<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Employee Information -->
    <div class="space-y-6">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
            <h4 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                <i class="fas fa-user-circle mr-2"></i> Employee Information
            </h4>
            <div class="flex items-center space-x-4">
                <img class="h-20 w-20 rounded-full ring-4 ring-white shadow-md" 
                     src="{{ $leave->user->profile_photo_url }}" 
                     alt="{{ $leave->user->first_name }}">
                <div class="space-y-2">
                    <h5 class="font-bold text-gray-900 text-lg">{{ $leave->user->first_name }} {{ $leave->user->last_name }}</h5>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-600 flex items-center">
                            <i class="fas fa-id-card mr-2 w-4"></i>
                            <span class="font-medium">ID:</span> {{ $leave->user->employee_id }}
                        </p>
                        <p class="text-sm text-gray-600 flex items-center">
                            <i class="fas fa-building mr-2 w-4"></i>
                            <span class="font-medium">Department:</span> {{ $leave->department }}
                        </p>
                        <p class="text-sm text-gray-600 flex items-center">
                            <i class="fas fa-briefcase mr-2 w-4"></i>
                            <span class="font-medium">Position:</span> {{ $leave->position }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Details -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calendar-alt mr-2"></i> Leave Details
            </h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Leave Type</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ App\Models\Leave::getLeaveTypes()[$leave->type] ?? $leave->type }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Start Date</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Duration</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $leave->days }} days</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</p>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full border mt-1
                            @if($leave->status === 'pending') bg-yellow-100 text-yellow-800 border-yellow-200
                            @elseif($leave->status === 'approved') bg-green-100 text-green-800 border-green-200
                            @else bg-red-100 text-red-800 border-red-200 @endif">
                            <i class="fas 
                                @if($leave->status === 'pending') fa-clock
                                @elseif($leave->status === 'approved') fa-check-circle
                                @else fa-times-circle @endif mr-1">
                            </i>
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">End Date</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Commutation</p>
                        <p class="text-sm font-medium text-gray-900 mt-1 capitalize">{{ str_replace('_', ' ', $leave->commutation) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Reason for Leave -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-file-alt mr-2"></i> Reason for Leave
            </h4>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-700 leading-relaxed">{{ $leave->reason }}</p>
            </div>
        </div>

        <!-- Supporting Documents -->
        @if($leave->medical_certificate_path || $leave->travel_itinerary_path)
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-paperclip mr-2"></i> Supporting Documents
            </h4>
            <div class="flex flex-wrap gap-3">
                @if($leave->medical_certificate_path)
                <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 hover:bg-blue-100 transition duration-200">
                    <i class="fas fa-file-medical mr-2"></i> Medical Certificate
                </a>
                @endif
                @if($leave->travel_itinerary_path)
                <a href="{{ Storage::url($leave->travel_itinerary_path) }}" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-green-700 hover:bg-green-100 transition duration-200">
                    <i class="fas fa-route mr-2"></i> TravelAuthorityItinerary
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- Emergency Contact -->
        @if($leave->emergency_contact_name)
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-phone-alt mr-2"></i> Emergency Contact
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $leave->emergency_contact_name }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $leave->emergency_contact_phone }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relationship</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $leave->emergency_contact_relationship }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Work Handover -->
        @if($leave->handoverPerson)
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-people-carry mr-2"></i> Work Handover
            </h4>
            <div class="space-y-4">
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <img class="h-12 w-12 rounded-full ring-2 ring-white" 
                         src="{{ $leave->handoverPerson->profile_photo_url }}" 
                         alt="{{ $leave->handoverPerson->first_name }}">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $leave->handoverPerson->first_name }} {{ $leave->handoverPerson->last_name }}</p>
                        <p class="text-sm text-gray-500">{{ $leave->handoverPerson->department }}</p>
                    </div>
                </div>
                @if($leave->handover_notes)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm font-semibold text-yellow-800 mb-2">Handover Notes:</p>
                    <p class="text-sm text-yellow-700 leading-relaxed">{{ $leave->handover_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Approval Information -->
@if($leave->status !== 'pending' && $leave->approvedBy)
<div class="mt-8 bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
    <h4 class="text-lg font-bold text-green-900 mb-4 flex items-center">
        <i class="fas fa-clipboard-check mr-2"></i> Approval Information
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-2">
            <p class="text-sm font-semibold text-green-700">Approved By</p>
            <p class="text-lg font-bold text-green-900">{{ $leave->approvedBy->first_name }} {{ $leave->approvedBy->last_name }}</p>
        </div>
        <div class="space-y-2">
            <p class="text-sm font-semibold text-green-700">Approved On</p>
            <p class="text-lg font-bold text-green-900">{{ \Carbon\Carbon::parse($leave->approved_at)->format('M d, Y \\a\\t h:i A') }}</p>
        </div>
        @if($leave->status === 'rejected' && $leave->disapproved_reason)
        <div class="space-y-2 md:col-span-3">
            <p class="text-sm font-semibold text-red-700">Reason for Rejection</p>
            <p class="text-red-800 bg-red-50 p-3 rounded-lg border border-red-200">{{ $leave->disapproved_reason }}</p>
        </div>
        @endif
    </div>
</div>
@endif