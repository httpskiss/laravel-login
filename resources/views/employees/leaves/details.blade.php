<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Application Information Card -->
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-sm border border-blue-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-info-circle text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Application Information</h4>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Leave Type</p>
                <div class="flex items-center">
                    <i class="fas 
                        @if($leave->type === App\Models\Leave::TYPE_VACATION) fa-umbrella-beach text-blue-500
                        @elseif($leave->type === App\Models\Leave::TYPE_SICK) fa-heartbeat text-green-500
                        @elseif($leave->type === App\Models\Leave::TYPE_MATERNITY) fa-baby text-pink-500
                        @elseif($leave->type === App\Models\Leave::TYPE_PATERNITY) fa-child text-purple-500
                        @else fa-calendar text-gray-500 @endif mr-2">
                    </i>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ App\Models\Leave::getLeaveTypes()[$leave->type] ?? $leave->type }}
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border
                    @if($leave->status === 'pending') bg-yellow-100 text-yellow-800 border-yellow-200
                    @elseif($leave->status === 'approved') bg-green-100 text-green-800 border-green-200
                    @else bg-red-100 text-red-800 border-red-200 @endif">
                    <i class="fas 
                        @if($leave->status === 'pending') fa-clock mr-1
                        @elseif($leave->status === 'approved') fa-check-circle mr-1
                        @else fa-times-circle mr-1 @endif">
                    </i>
                    {{ ucfirst($leave->status) }}
                </span>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Start Date</p>
                <div class="flex items-center">
                    <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">End Date</p>
                <div class="flex items-center">
                    <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Duration</p>
                <div class="flex items-center">
                    <i class="fas fa-clock text-purple-500 mr-2"></i>
                    <p class="text-sm font-semibold text-gray-900">{{ $leave->days }} days</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Commutation</p>
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                    <p class="text-sm font-semibold text-gray-900 capitalize">
                        {{ str_replace('_', ' ', $leave->commutation) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Card -->
    <div class="bg-gradient-to-br from-white to-purple-50 rounded-2xl shadow-sm border border-purple-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-history text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Application Timeline</h4>
        </div>
        
        <div class="space-y-4">
            <!-- Timeline Item -->
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    <i class="fas fa-paper-plane text-green-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Application Filed</p>
                    <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($leave->filing_date)->format('F d, Y') }}</p>
                    <p class="text-xs text-gray-500">Application was submitted for review</p>
                </div>
            </div>

            @if($leave->approved_at)
            <!-- Timeline Item -->
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 
                    @if($leave->status === 'approved') bg-green-100
                    @elseif($leave->status === 'rejected') bg-red-100
                    @else bg-yellow-100 @endif rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    <i class="fas 
                        @if($leave->status === 'approved') fa-check text-green-600
                        @elseif($leave->status === 'rejected') fa-times text-red-600
                        @else fa-clock text-yellow-600 @endif text-sm">
                    </i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">
                        @if($leave->status === 'approved') Application Approved
                        @elseif($leave->status === 'rejected') Application Rejected
                        @else Under Review
                        @endif
                    </p>
                    <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($leave->approved_at)->format('F d, Y \\a\\t h:i A') }}</p>
                    @if($leave->approvedBy)
                    <p class="text-xs text-gray-500">By {{ $leave->approvedBy->first_name }} {{ $leave->approvedBy->last_name }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Current Status -->
            <div class="mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-1">Current Status</p>
                <div class="flex items-center justify-between">
                    <span class="text-lg font-bold 
                        @if($leave->status === 'approved') text-green-600
                        @elseif($leave->status === 'rejected') text-red-600
                        @else text-yellow-600 @endif">
                        {{ ucfirst($leave->status) }}
                    </span>
                    <div class="w-3 h-3 rounded-full 
                        @if($leave->status === 'approved') bg-green-500
                        @elseif($leave->status === 'rejected') bg-red-500
                        @else bg-yellow-500 animate-pulse @endif">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reason for Leave Card -->
    <div class="lg:col-span-2 bg-gradient-to-br from-white to-orange-50 rounded-2xl shadow-sm border border-orange-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-file-alt text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Reason for Leave</h4>
        </div>
        
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-xs">
            <div class="prose prose-sm max-w-none">
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $leave->reason }}</p>
            </div>
        </div>
    </div>

    <!-- Supporting Documents -->
    @if($leave->medical_certificate_path || $leave->travel_itinerary_path)
    <div class="lg:col-span-2 bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-sm border border-green-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-paperclip text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Supporting Documents</h4>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($leave->medical_certificate_path)
            <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" 
               class="group flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-xs hover:shadow-md hover:border-green-300 transition-all duration-200">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-file-medical text-green-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors">Medical Certificate</p>
                    <p class="text-sm text-gray-500">Click to view document</p>
                </div>
                <i class="fas fa-external-link-alt text-gray-400 group-hover:text-green-600 transition-colors"></i>
            </a>
            @endif
            
            @if($leave->travel_itinerary_path)
            <a href="{{ Storage::url($leave->travel_itinerary_path) }}" target="_blank" 
               class="group flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-xs hover:shadow-md hover:border-blue-300 transition-all duration-200">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-route text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">TravelAuthorityItinerary</p>
                    <p class="text-sm text-gray-500">Click to view document</p>
                </div>
                <i class="fas fa-external-link-alt text-gray-400 group-hover:text-blue-600 transition-colors"></i>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Emergency Contact -->
    @if($leave->emergency_contact_name)
    <div class="lg:col-span-2 bg-gradient-to-br from-white to-red-50 rounded-2xl shadow-sm border border-red-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-phone-alt text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Emergency Contact</h4>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-user text-red-600"></i>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Name</p>
                <p class="text-sm font-semibold text-gray-900">{{ $leave->emergency_contact_name }}</p>
            </div>
            
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-phone text-red-600"></i>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Phone</p>
                <p class="text-sm font-semibold text-gray-900">{{ $leave->emergency_contact_phone }}</p>
            </div>
            
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-handshake text-red-600"></i>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Relationship</p>
                <p class="text-sm font-semibold text-gray-900">{{ $leave->emergency_contact_relationship }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Work Handover -->
    @if($leave->handoverPerson)
    <div class="lg:col-span-2 bg-gradient-to-br from-white to-indigo-50 rounded-2xl shadow-sm border border-indigo-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-people-carry text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Work Handover</h4>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs">
                    <div class="flex items-center space-x-4">
                        <img class="h-14 w-14 rounded-full ring-2 ring-white shadow-md" 
                             src="{{ $leave->handoverPerson->profile_photo_url }}" 
                             alt="{{ $leave->handoverPerson->first_name }}">
                        <div>
                            <p class="font-bold text-gray-900">{{ $leave->handoverPerson->first_name }} {{ $leave->handoverPerson->last_name }}</p>
                            <p class="text-sm text-gray-600">{{ $leave->handoverPerson->department }}</p>
                            <p class="text-xs text-gray-500">{{ $leave->handoverPerson->position }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($leave->handover_notes)
            <div class="lg:col-span-2">
                <div class="bg-yellow-50 rounded-xl p-5 border border-yellow-200 shadow-xs h-full">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-sticky-note text-yellow-600"></i>
                        </div>
                        <p class="font-semibold text-yellow-800">Handover Notes</p>
                    </div>
                    <p class="text-sm text-yellow-700 leading-relaxed whitespace-pre-line">{{ $leave->handover_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Admin Notes -->
    @if($leave->admin_notes)
    <div class="lg:col-span-2 bg-gradient-to-br from-white to-yellow-50 rounded-2xl shadow-sm border border-yellow-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-yellow-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-comment-alt text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Admin Notes</h4>
        </div>
        
        <div class="bg-yellow-50 rounded-xl p-5 border border-yellow-200 shadow-xs">
            <div class="prose prose-sm max-w-none">
                <p class="text-yellow-800 leading-relaxed whitespace-pre-line">{{ $leave->admin_notes }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Disapproval Reason -->
    @if($leave->status === 'rejected' && $leave->disapproved_reason)
    <div class="lg:col-span-2 bg-gradient-to-br from-white to-red-50 rounded-2xl shadow-sm border border-red-100 p-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-times-circle text-white text-lg"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900">Reason for Rejection</h4>
        </div>
        
        <div class="bg-red-50 rounded-xl p-5 border border-red-200 shadow-xs">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 text-lg mr-4 mt-1"></i>
                <div class="prose prose-sm max-w-none">
                    <p class="text-red-800 leading-relaxed whitespace-pre-line font-medium">{{ $leave->disapproved_reason }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Quick Stats Bar -->
<div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $leave->days }}</div>
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Days</div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs text-center">
        <div class="text-2xl font-bold text-green-600">
            {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}
        </div>
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Calendar Days</div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs text-center">
        <div class="text-2xl font-bold text-purple-600">
            {{ \Carbon\Carbon::parse($leave->filing_date)->diffInDays(now()) }}
        </div>
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Days Since Filed</div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-xs text-center">
        <div class="text-2xl font-bold 
            @if($leave->status === 'approved') text-green-600
            @elseif($leave->status === 'rejected') text-red-600
            @else text-yellow-600 @endif">
            {{ ucfirst($leave->status) }}
        </div>
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Current Status</div>
    </div>
</div>