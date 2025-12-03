<div class="space-y-6">
    <!-- Header with Status -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-blue-900 mb-2">Leave Application Details</h2>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                        Application #{{ str_pad($leave->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="px-3 py-1 {{ $leave->getStatusBadgeClass() }} text-sm font-semibold rounded-full">
                        {{ ucfirst($leave->status) }}
                    </span>
                    @if($leave->followsCscRules())
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-semibold rounded-full">
                        CSC Compliant
                    </span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-700">Filed on</p>
                <p class="text-lg font-semibold text-blue-900">
                    {{ \Carbon\Carbon::parse($leave->filing_date)->format('F d, Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Employee & Leave Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Employee Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i> 
                    Employee Information
                </h3>
                <div class="flex items-start space-x-6">
                    <!-- Profile Photo -->
                    <div class="flex-shrink-0">
                        <img class="h-24 w-24 rounded-full ring-4 ring-white shadow-lg" 
                             src="{{ $leave->user->profile_photo_url }}" 
                             alt="{{ $leave->user->first_name }}">
                    </div>
                    
                    <!-- Employee Details -->
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900 mb-3">
                            {{ $leave->user->first_name }} {{ $leave->user->last_name }}
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Employee ID</p>
                                <p class="text-sm font-medium text-gray-900">{{ $leave->user->employee_id }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Department</p>
                                <p class="text-sm font-medium text-gray-900">{{ $leave->department }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Position</p>
                                <p class="text-sm font-medium text-gray-900">{{ $leave->position }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Salary Grade</p>
                                <p class="text-sm font-medium text-gray-900">₱{{ number_format($leave->salary, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-green-600"></i> 
                    Leave Details
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column: Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Leave Type</p>
                            <div class="flex items-center mt-2">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3
                                    @if($leave->type === App\Models\Leave::TYPE_VACATION) bg-blue-100 text-blue-600
                                    @elseif($leave->type === App\Models\Leave::TYPE_SICK) bg-green-100 text-green-600
                                    @elseif($leave->type === App\Models\Leave::TYPE_MATERNITY) bg-pink-100 text-pink-600
                                    @elseif($leave->type === App\Models\Leave::TYPE_PATERNITY) bg-yellow-100 text-yellow-600
                                    @elseif($leave->type === App\Models\Leave::TYPE_SPECIAL_PRIVILEGE) bg-purple-100 text-purple-600
                                    @else bg-gray-100 text-gray-600 @endif">
                                    <i class="fas 
                                        @if($leave->type === App\Models\Leave::TYPE_VACATION) fa-umbrella-beach
                                        @elseif($leave->type === App\Models\Leave::TYPE_SICK) fa-heartbeat
                                        @elseif($leave->type === App\Models\Leave::TYPE_MATERNITY) fa-baby
                                        @elseif($leave->type === App\Models\Leave::TYPE_PATERNITY) fa-child
                                        @elseif($leave->type === App\Models\Leave::TYPE_SPECIAL_PRIVILEGE) fa-star
                                        @else fa-calendar @endif">
                                    </i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ App\Models\Leave::getLeaveTypes()[$leave->type] ?? $leave->type }}
                                    </p>
                                    @if($leave->is_lwop)
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">LWOP</span>
                                    @endif
                                    @if($leave->is_forced_leave)
                                    <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">Forced Leave</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Duration</p>
                            <div class="mt-2 space-y-1">
                                <p class="text-lg font-bold text-gray-900">{{ $leave->days }} days</p>
                                @if($leave->duration_type === 'half_day')
                                <p class="text-sm text-gray-600 capitalize">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $leave->getHalfDayTimeDisplay() }}
                                </p>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Commutation</p>
                            <p class="mt-2 text-sm font-medium text-gray-900 capitalize">
                                <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                                {{ str_replace('_', ' ', $leave->commutation) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Right Column: Dates -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Date Period</p>
                            <div class="mt-2 bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Start Date</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}
                                        </p>
                                        @if($leave->start_time)
                                        <p class="text-sm text-gray-600">{{ $leave->start_time }}</p>
                                        @endif
                                    </div>
                                    <div class="text-gray-400">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">End Date</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                        </p>
                                        @if($leave->end_time)
                                        <p class="text-sm text-gray-600">{{ $leave->end_time }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 text-center">
                                    <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} calendar days
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CSC Computation Card -->
            @if($leave->followsCscRules())
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calculator mr-2 text-purple-600"></i> 
                    CSC Leave Computation
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- CSC Classification -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">CSC Employee Type</p>
                            <div class="mt-2 flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-tie text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $leave->getCscEmployeeTypeDisplay() }}</p>
                                    <p class="text-xs text-gray-600">{{ $leave->getLeaveBasisDisplay() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        @if($leave->equivalent_days_csc && $leave->equivalent_days_csc != $leave->days)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">CSC Equivalent Days</p>
                            <div class="mt-2 bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg p-4">
                                <p class="text-2xl font-bold text-purple-900">
                                    {{ number_format($leave->equivalent_days_csc, 4) }}
                                </p>
                                <p class="text-sm text-purple-700">Based on {{ $leave->user->work_hours_per_day ?? 8 }} hours work day</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Special Details -->
                    <div class="space-y-4">
                        @if($leave->slp_type !== 'none')
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Special Leave Privilege</p>
                            <div class="mt-2 flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-star text-pink-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $leave->getSlpTypeDisplay() }}</p>
                                    <p class="text-xs text-gray-600">Max 3 days per year | Non-cumulative</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($leave->is_lwop)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">LWOP Deduction</p>
                            <div class="mt-2 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg p-4">
                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <p class="text-xs text-gray-600">LWOP Days</p>
                                        <p class="text-lg font-bold text-red-900">{{ $leave->days }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Rate</p>
                                        <p class="text-lg font-bold text-red-900">{{ number_format($leave->lwop_deduction_rate, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Charged</p>
                                        <p class="text-lg font-bold text-red-900">{{ number_format($leave->lwop_days_charged, 2) }} days</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                @if($leave->computation_notes)
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm font-semibold text-blue-800 mb-1">Computation Notes:</p>
                    <p class="text-sm text-blue-700">{{ $leave->computation_notes }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Reason & Supporting Documents Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-indigo-600"></i> 
                    Reason & Supporting Documents
                </h3>
                
                <!-- Reason -->
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Reason for Leave</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $leave->reason }}</p>
                    </div>
                </div>
                
                <!-- Supporting Documents -->
                @if($leave->medical_certificate_path || $leave->travel_itinerary_path)
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Supporting Documents</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($leave->medical_certificate_path)
                        <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" 
                           class="group flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
                            <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-4 group-hover:bg-green-200">
                                <i class="fas fa-file-medical text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-green-800">Medical Certificate</p>
                                <p class="text-sm text-green-600">Click to view/download</p>
                            </div>
                            <i class="fas fa-external-link-alt text-green-400 group-hover:text-green-600"></i>
                        </a>
                        @endif
                        
                        @if($leave->travel_itinerary_path)
                        <a href="{{ Storage::url($leave->travel_itinerary_path) }}" target="_blank" 
                           class="group flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4 group-hover:bg-blue-200">
                                <i class="fas fa-route text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-blue-800">Travel Itinerary</p>
                                <p class="text-sm text-blue-600">Click to view/download</p>
                            </div>
                            <i class="fas fa-external-link-alt text-blue-400 group-hover:text-blue-600"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Status & Actions -->
        <div class="space-y-6">
            <!-- Status Timeline Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-history mr-2 text-orange-600"></i> 
                    Application Timeline
                </h3>
                
                <div class="space-y-6">
                    <!-- Filed -->
                    <div class="flex items-start">
                        <div class="relative">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-paper-plane text-green-600"></i>
                            </div>
                            <div class="absolute top-8 bottom-0 left-4 w-0.5 bg-green-200"></div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-semibold text-gray-900">Application Filed</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($leave->filing_date)->format('F d, Y \\a\\t h:i A') }}</p>
                            <p class="text-xs text-gray-500">Application submitted for review</p>
                        </div>
                    </div>
                    
                    <!-- Status Change -->
                    <div class="flex items-start">
                        <div class="relative">
                            <div class="w-8 h-8 rounded-full 
                                @if($leave->status === 'pending') bg-yellow-100
                                @elseif($leave->status === 'approved') bg-green-100
                                @else bg-red-100 @endif flex items-center justify-center">
                                <i class="fas 
                                    @if($leave->status === 'pending') fa-clock text-yellow-600
                                    @elseif($leave->status === 'approved') fa-check text-green-600
                                    @else fa-times text-red-600 @endif">
                                </i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-semibold text-gray-900">
                                @if($leave->status === 'pending') Under Review
                                @elseif($leave->status === 'approved') Application Approved
                                @else Application Rejected
                                @endif
                            </p>
                            @if($leave->approved_at)
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($leave->approved_at)->format('F d, Y \\a\\t h:i A') }}</p>
                            @if($leave->approvedBy)
                            <p class="text-xs text-gray-500">By {{ $leave->approvedBy->first_name }} {{ $leave->approvedBy->last_name }}</p>
                            @endif
                            @else
                            <p class="text-sm text-gray-600">Awaiting action</p>
                            @endif
                            
                            @if($leave->status === 'rejected' && $leave->disapproved_reason)
                            <div class="mt-2 bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-red-800">Reason for Rejection:</p>
                                <p class="text-sm text-red-700">{{ $leave->disapproved_reason }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- CS Form No. 6 Document Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-pdf mr-2 text-red-600"></i> 
                    Official Document
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mr-4">
                                <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-red-800">CS Form No. 6</p>
                                <p class="text-sm text-red-600">Leave Application Form</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        @if($leave->pdf_path && Storage::exists($leave->pdf_path))
                        <a href="{{ route('employees.leaves.download-pdf', $leave) }}" 
                           target="_blank"
                           class="w-full flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-download mr-2"></i>
                            Download PDF
                        </a>
                        
                        <button onclick="showPdfInfo()" 
                                class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-info-circle mr-2"></i>
                            Document Information
                        </button>
                        @else
                        <button onclick="generatePdf({{ $leave->id }})" 
                                class="w-full flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Generate PDF Document
                        </button>
                        @endif
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">About CS Form No. 6</p>
                                <p class="text-sm text-blue-700">
                                    This is the official Civil Service Commission leave application form. 
                                    It contains all your submitted information in the standard government format.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-600"></i> 
                    Quick Actions
                </h3>
                
                <div class="space-y-3">
                    @if($leave->status === 'pending')
                    <button onclick="cancelLeave({{ $leave->id }})" 
                            class="w-full flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-times mr-2"></i>
                        Cancel Application
                    </button>
                    @endif
                    
                    <button onclick="printApplication()" 
                            class="w-full flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-print mr-2"></i>
                        Print Application
                    </button>
                    
                    <a href="{{ route('employees.leaves.create') }}" 
                       class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Apply for New Leave
                    </a>
                </div>
            </div>

            <!-- Leave Credits Summary -->
            @if($leave->credit_as_of_date)
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-wallet mr-2 text-green-600"></i> 
                    Leave Credits Summary
                </h3>
                
                <div class="space-y-4">
                    <div class="text-center mb-3">
                        <p class="text-sm text-gray-600">As of {{ \Carbon\Carbon::parse($leave->credit_as_of_date)->format('M d, Y') }}</p>
                    </div>
                    
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 text-left text-xs font-semibold text-gray-500">Leave Type</th>
                                <th class="p-2 text-center text-xs font-semibold text-gray-500">Earned</th>
                                <th class="p-2 text-center text-xs font-semibold text-gray-500">Less</th>
                                <th class="p-2 text-center text-xs font-semibold text-gray-500">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="p-2 text-blue-700">Vacation Leave</td>
                                <td class="p-2 text-center">{{ $leave->vacation_earned }}</td>
                                <td class="p-2 text-center">{{ $leave->vacation_less }}</td>
                                <td class="p-2 text-center font-semibold text-blue-900">{{ $leave->vacation_balance }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 text-green-700">Sick Leave</td>
                                <td class="p-2 text-center">{{ $leave->sick_earned }}</td>
                                <td class="p-2 text-center">{{ $leave->sick_less }}</td>
                                <td class="p-2 text-center font-semibold text-green-900">{{ $leave->sick_balance }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Work Handover Section (if applicable) -->
    @if($leave->handoverPerson)
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-people-carry mr-2 text-yellow-600"></i> 
            Work Handover Arrangement
        </h3>
        
        <div class="flex items-start space-x-6">
            <div class="flex-shrink-0">
                <img class="h-16 w-16 rounded-full ring-2 ring-white shadow-md" 
                     src="{{ $leave->handoverPerson->profile_photo_url }}" 
                     alt="{{ $leave->handoverPerson->first_name }}">
            </div>
            
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $leave->handoverPerson->first_name }} {{ $leave->handoverPerson->last_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $leave->handoverPerson->position }}</p>
                        <p class="text-xs text-gray-500">{{ $leave->handoverPerson->department }}</p>
                    </div>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                        Handover Person
                    </span>
                </div>
                
                @if($leave->handover_notes)
                <div class="mt-4 bg-white border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm font-semibold text-yellow-800 mb-2">Handover Notes:</p>
                    <p class="text-sm text-yellow-700 whitespace-pre-line">{{ $leave->handover_notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Emergency Contact (if provided) -->
    @if($leave->emergency_contact_name)
    <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-phone-alt mr-2 text-red-600"></i> 
            Emergency Contact Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-user text-red-600 text-xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact Person</p>
                <p class="text-lg font-semibold text-gray-900">{{ $leave->emergency_contact_name }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-phone text-red-600 text-xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone Number</p>
                <p class="text-lg font-semibold text-gray-900">{{ $leave->emergency_contact_phone }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-handshake text-red-600 text-xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relationship</p>
                <p class="text-lg font-semibold text-gray-900">{{ $leave->emergency_contact_relationship }}</p>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function showPdfInfo() {
    const info = `
        CS Form No. 6 - Leave Application Document
        
        • Official Civil Service Commission form
        • Contains all submitted information
        • Standard government format
        • Includes electronic signature
        • Ready for administrative processing
        
        Document includes:
        - Employee information
        - Leave type and duration
        - Reason for leave
        - Supporting document references
        - CSC computation details (if applicable)
        
        @if($leave->status === 'approved' || $leave->status === 'rejected')
        - Approval/Rejection details
        - Administrative signatures
        - Leave credits computation
        @endif
        
        This document can be printed and submitted as part of official records.
    `;
    
    alert(info);
}

function printApplication() {
    window.print();
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
            alert('PDF generated successfully!');
            location.reload();
        } else {
            alert('Error generating PDF: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error generating PDF');
    });
}

function cancelLeave(leaveId) {
    if (confirm('Are you sure you want to cancel this leave application?')) {
        fetch(`/employees/leaves/${leaveId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Leave application cancelled successfully!');
                window.location.href = '{{ route('employees.leaves') }}';
            } else {
                alert('Error cancelling application: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error cancelling application');
        });
    }
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12pt;
    }
    
    .rounded-xl {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    
    .bg-gradient-to-r {
        background: none !important;
    }
}
</style>
@endpush