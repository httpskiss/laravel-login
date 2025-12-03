@extends('layouts.employee')

@section('title', 'Apply for Leave')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Apply for Leave</h1>
            <p class="text-gray-600">Fill out the CSC-compliant leave application form</p>
        </div>

        <!-- CSC Information Card -->
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                        <i class="fas fa-user-tie text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-indigo-900">CSC Leave Information</h3>
                        <p class="text-indigo-700 text-sm">Based on your employment classification</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">
                    {{ auth()->user()->getClassificationOptions()[auth()->user()->employee_classification] ?? 'Regular Employee' }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($leaveBalances->vacation_leave ?? 0, 2) }}</div>
                    <div class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Vacation Leave</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($leaveBalances->sick_leave ?? 0, 2) }}</div>
                    <div class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Sick Leave</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($leaveBalances->special_leave_privileges ?? 3, 2) }}</div>
                    <div class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Special Privileges</div>
                </div>
            </div>
        </div>

        <!-- Eligibility Check Section -->
        <div id="eligibilityCheck" class="hidden bg-gradient-to-r from-green-50 to-emerald-100 border border-green-200 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-green-900" id="eligibilityTitle">Eligibility Check</h3>
                    <p class="text-green-700 text-sm" id="eligibilityMessage">Checking your eligibility for this leave type...</p>
                </div>
            </div>
            <div id="requirementsList" class="mt-4 space-y-2"></div>
        </div>

        <!-- Application Form -->
        <form id="leaveForm" action="{{ route('employees.leaves.store') }}" method="POST" enctype="multipart/form-data" 
              class="bg-white rounded-xl shadow-sm p-6 space-y-8" x-data="cscLeaveApplicationForm()" @submit.prevent="submitForm">
            @csrf

            <!-- Section 1: Employee & CSC Information -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i> 
                    Employee & CSC Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employee Classification -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-1 text-gray-400"></i>
                            CSC Employee Type
                        </label>
                        <div class="relative">
                            <select name="csc_employee_type" x-model="cscEmployeeType" @change="updateLeaveBasis" required
                                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                                <option value="">Select CSC Classification</option>
                                @foreach(App\Models\User::getClassificationOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected(auth()->user()->employee_classification == $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-user-tie absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Leave Basis (Auto-calculated) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calculator mr-1 text-gray-400"></i>
                            CSC Leave Basis
                        </label>
                        <div class="relative">
                            <input type="text" x-model="leaveBasisDisplay" readonly
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg bg-gray-50">
                            <i class="fas fa-balance-scale absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                        <input type="hidden" name="leave_basis" x-model="leaveBasis">
                    </div>

                    <!-- Employee Details (Read-only) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee Details</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Full Name</p>
                                <p class="font-semibold text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Department</p>
                                <p class="font-semibold text-gray-900">{{ auth()->user()->department }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Position</p>
                                <p class="font-semibold text-gray-900">{{ auth()->user()->position }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Leave Details -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-green-600"></i> 
                    Leave Details
                </h3>

                <!-- Leave Type Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-clipboard-list mr-1 text-gray-400"></i>
                        Type of Leave (CSC)
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            $commonLeaves = ['vacation', 'sick', 'maternity', 'paternity', 'special_privilege'];
                            $otherLeaves = array_diff(array_keys(App\Models\Leave::getLeaveTypes()), $commonLeaves);
                        @endphp
                        
                        @foreach($commonLeaves as $value)
                            @if(isset(App\Models\Leave::getLeaveTypes()[$value]))
                                <label class="relative">
                                    <input type="radio" name="type" value="{{ $value }}" x-model="selectedLeaveType" 
                                           @change="onLeaveTypeChange" class="sr-only peer">
                                    <div class="cursor-pointer rounded-xl border-2 p-4 hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 
                                                @if($value === 'vacation') bg-blue-100 text-blue-600
                                                @elseif($value === 'sick') bg-green-100 text-green-600
                                                @elseif($value === 'maternity') bg-pink-100 text-pink-600
                                                @elseif($value === 'paternity') bg-yellow-100 text-yellow-600
                                                @else bg-purple-100 text-purple-600 @endif">
                                                <i class="fas 
                                                    @if($value === 'vacation') fa-umbrella-beach
                                                    @elseif($value === 'sick') fa-heartbeat
                                                    @elseif($value === 'maternity') fa-baby
                                                    @elseif($value === 'paternity') fa-child
                                                    @else fa-star @endif">
                                                </i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ App\Models\Leave::getLeaveTypes()[$value] }}</p>
                                                <p class="text-xs text-gray-500">
                                                    @if($value === 'maternity') 105 days
                                                    @elseif($value === 'paternity') 7 days
                                                    @elseif($value === 'special_privilege') 3 days/year
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>

                    <!-- Other Leave Types Dropdown -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Other Leave Types</label>
                        <select name="type_other" x-model="selectedLeaveType" @change="onLeaveTypeChange" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Other Leave Type</option>
                            @foreach($otherLeaves as $value)
                                <option value="{{ $value }}">{{ App\Models\Leave::getLeaveTypes()[$value] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- CSC-Specific Fields Based on Leave Type -->
                <template x-if="selectedLeaveType === 'maternity'">
                    <div class="bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-200 rounded-xl p-6 mb-6">
                        <h4 class="text-lg font-semibold text-pink-900 mb-4 flex items-center">
                            <i class="fas fa-baby mr-2"></i> Maternity Leave Details (CSC)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Expected Delivery Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="maternity_delivery_date" x-model="maternityDeliveryDate"
                                       @change="validateMaternityDate"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <p class="text-xs text-gray-500 mt-1">As per medical certificate</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Special Cases</label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="radio" name="is_miscarriage" value="0" x-model="isMiscarriage" 
                                               class="form-radio text-pink-600">
                                        <span class="ml-2 text-gray-700">Normal Pregnancy (105 days)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="is_miscarriage" value="1" x-model="isMiscarriage"
                                               class="form-radio text-pink-600">
                                        <span class="ml-2 text-gray-700">Miscarriage/Ectopic (60 days)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_solo_parent" value="1" 
                                               class="form-checkbox text-pink-600">
                                        <span class="ml-2 text-gray-700">Solo Parent (additional 15 days)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="selectedLeaveType === 'paternity'">
                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-6 mb-6">
                        <h4 class="text-lg font-semibold text-yellow-900 mb-4 flex items-center">
                            <i class="fas fa-child mr-2"></i> Paternity Leave Details (CSC)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Delivery Number <span class="text-red-500">*</span>
                                </label>
                                <select name="paternity_delivery_count" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select delivery number</option>
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" 
                                                @if(auth()->user()->delivery_count + 1 == $i) selected @endif>
                                            {{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }} Delivery
                                        </option>
                                    @endfor
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Maximum of 4 deliveries eligible</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                                <div class="space-y-2">
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        Must be married male employee
                                    </p>
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        7 days per delivery
                                    </p>
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        Within 60 days from delivery
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="selectedLeaveType === 'special_privilege'">
                    <div class="bg-gradient-to-r from-purple-50 to-violet-50 border border-purple-200 rounded-xl p-6 mb-6">
                        <h4 class="text-lg font-semibold text-purple-900 mb-4 flex items-center">
                            <i class="fas fa-star mr-2"></i> Special Leave Privilege (SLP)
                        </h4>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    SLP Type <span class="text-red-500">*</span>
                                </label>
                                <select name="slp_type" required x-model="slpType"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Select SLP Type</option>
                                    @foreach(App\Models\Leave::getSlpTypes() as $value => $label)
                                        @if($value !== 'none')
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="text-xs text-purple-600 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Maximum 3 days per year for any combination of SLP types
                                </p>
                            </div>
                            <div x-show="slpType">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Documentation Required</label>
                                <div id="slpRequirements" class="space-y-2">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Section 3: Dates & Duration (FIXED FOR HALF-DAY LEAVES) -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-clock mr-2 text-blue-600"></i> 
                    Dates & Duration (CSC Computation)
                </h3>

                <!-- Duration Type Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Duration Type</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="duration in durationTypes" :key="duration.value">
                            <label class="relative">
                                <input type="radio" name="duration_type" :value="duration.value" 
                                       x-model="durationType" @change="onDurationTypeChange" class="sr-only peer">
                                <div class="cursor-pointer rounded-xl border-2 p-4 hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                            <i class="fas" :class="duration.icon"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900" x-text="duration.label"></p>
                                            <p class="text-xs text-gray-500" x-text="duration.description"></p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Date & Time Fields -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" x-model="startDate" @change="calculateDays" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            End Date 
                            <span x-show="durationType !== 'half_day'" class="text-red-500">*</span>
                            <span x-show="durationType === 'half_day'" class="text-gray-400">(Auto-filled)</span>
                        </label>
                        <input type="date" name="end_date" x-model="endDate" 
                               :required="durationType !== 'half_day'"
                               :readonly="durationType === 'half_day'"
                               :class="durationType === 'half_day' ? 'bg-gray-50 cursor-not-allowed' : ''"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Half Day Time (Only for half-day) -->
                    <div x-show="durationType === 'half_day'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Half Day Period <span class="text-red-500">*</span></label>
                        <select name="half_day_time" x-model="halfDayTime" @change="updateHalfDayTimes" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select period</option>
                            <option value="morning">Morning (8:00 AM - 12:00 PM)</option>
                            <option value="afternoon">Afternoon (1:00 PM - 5:00 PM)</option>
                            <option value="custom">Custom Time</option>
                        </select>
                    </div>

                    <!-- Time Tracking for CSC Computation (Custom Time) -->
                    <div x-show="durationType === 'half_day' && halfDayTime === 'custom'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Custom Time Details</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Start Time <span class="text-red-500">*</span></label>
                                <input type="time" name="start_time" x-model="startTime" @change="calculateHours"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">End Time <span class="text-red-500">*</span></label>
                                <input type="time" name="end_time" x-model="endTime" @change="calculateHours"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">For CSC hour-to-day computation</p>
                    </div>

                    <!-- Days Input for multiple days -->
                    <div x-show="durationType === 'multiple_days'" class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Days 
                            <span class="text-xs text-gray-500 font-normal">(Calculated automatically from dates)</span>
                        </label>
                        <input type="number" name="days" x-model="calculatedDays" step="0.5" min="0.5" max="365" 
                               readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>

                <!-- Days Display Section -->
                <div class="mt-6">
                    <!-- Multiple Days Display -->
                    <div x-show="durationType === 'multiple_days'" class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm font-medium text-blue-800">Total Leave Duration</p>
                                <p class="text-2xl font-bold text-blue-900" x-text="calculatedDays + ' day' + (calculatedDays !== 1 ? 's' : '')"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-blue-800">CSC Equivalent</p>
                                <p class="text-2xl font-bold text-blue-900" x-text="equivalentDaysCsc.toFixed(4) + ' days'"></p>
                            </div>
                        </div>
                        <p class="text-xs text-blue-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Based on {{ auth()->user()->work_hours_per_day ?? 8 }} hours work day. Working days only.
                        </p>
                    </div>

                    <!-- Half Day & Full Day Display -->
                    <div x-show="durationType !== 'multiple_days'" class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Leave Duration</p>
                                <p class="text-3xl font-bold text-gray-900" x-text="calculatedDays + ' day' + (calculatedDays !== 1 ? 's' : '')"></p>
                                <template x-if="durationType === 'half_day'">
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span x-text="totalHours + ' hours'"></span>
                                        <span x-show="halfDayTime && halfDayTime !== 'custom'" x-text="'(' + halfDayTime + ')'"></span>
                                    </p>
                                </template>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">CSC Equivalent Days</p>
                                <p class="text-3xl font-bold text-blue-900" x-text="equivalentDaysCsc.toFixed(4)"></p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Based on {{ auth()->user()->work_hours_per_day ?? 8 }} hours work day
                                </p>
                            </div>
                        </div>
                        
                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="days" x-model="calculatedDays">
                        <div x-show="durationType === 'half_day'">
                            <input type="hidden" name="total_hours" x-model="totalHours">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Payment & Commutation -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-green-600"></i> 
                    Payment & Commutation
                </h3>

                <div class="space-y-6">
                    <!-- Commutation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Commutation</label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="commutation" value="requested" class="form-radio text-green-600">
                                <span class="ml-2 text-gray-700">Requested</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="commutation" value="not_requested" checked class="form-radio text-green-600">
                                <span class="ml-2 text-gray-700">Not Requested</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Commutation converts leave credits to cash (maximum 50% of accumulated leave credits)
                        </p>
                    </div>

                    <!-- Leave Without Pay (LWOP) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Leave Without Pay (LWOP)</label>
                        <div class="space-y-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_lwop" value="1" x-model="isLwop" 
                                       @change="calculateLwopDeduction"
                                       class="form-checkbox text-red-600">
                                <span class="ml-2 text-gray-700">This leave will be without pay (LWOP)</span>
                            </label>
                            
                            <div x-show="isLwop" class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-6">
                                <h5 class="font-semibold text-red-900 mb-3 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> CSC LWOP Deduction Rules
                                </h5>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-red-600" x-text="calculatedDays.toFixed(1)"></div>
                                            <div class="text-xs text-gray-600">LWOP Days</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-red-600" x-text="lwopDeductionRate.toFixed(2)"></div>
                                            <div class="text-xs text-gray-600">Deduction Rate</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-red-600" x-text="lwopDaysCharged.toFixed(2)"></div>
                                            <div class="text-xs text-gray-600">Days Charged to VL</div>
                                        </div>
                                    </div>
                                    <div class="bg-red-100 border border-red-200 rounded-lg p-4">
                                        <p class="text-sm text-red-800">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            CSC Rule: {1 day LWOP = 0.25 day VL deduction}, {2 days = 0.50}, 
                                            {3 days = 0.75}, {4+ days = 1.00 per day}
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" name="lwop_deduction_rate" x-model="lwopDeductionRate">
                                <input type="hidden" name="lwop_days_charged" x-model="lwopDaysCharged">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Reason & Supporting Documents -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-purple-600"></i> 
                    Reason & Supporting Documents
                </h3>

                <!-- Reason for Leave -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Leave <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" rows="4" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Provide detailed reason for your leave application. Include any relevant information that will help in the approval process."></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Be specific and include details such as location (if traveling), purpose, and any other relevant information.
                    </p>
                </div>

                <!-- Supporting Documents -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Supporting Documents</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Medical Certificate -->
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors">
                            <div class="text-center">
                                <i class="fas fa-file-medical text-3xl text-green-500 mb-3"></i>
                                <p class="font-medium text-gray-900 mb-1">Medical Certificate</p>
                                <p class="text-sm text-gray-600 mb-3">Required for sick leaves over 3 days</p>
                                <input type="file" name="medical_certificate" id="medicalCertificate" 
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="hidden">
                                <label for="medicalCertificate" class="cursor-pointer px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 inline-block">
                                    <i class="fas fa-upload mr-2"></i> Upload File
                                </label>
                                <p class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 2MB)</p>
                                <div id="medicalPreview" class="mt-3 hidden">
                                    <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-pdf text-green-600 mr-2"></i>
                                            <span class="text-sm text-green-800">medical_certificate.pdf</span>
                                        </div>
                                        <button type="button" onclick="removeFile('medicalCertificate')" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Travel Itinerary -->
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors">
                            <div class="text-center">
                                <i class="fas fa-route text-3xl text-blue-500 mb-3"></i>
                                <p class="font-medium text-gray-900 mb-1">Travel Itinerary</p>
                                <p class="text-sm text-gray-600 mb-3">Required for vacation leaves abroad</p>
                                <input type="file" name="travel_itinerary" id="travelItinerary" 
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="hidden">
                                <label for="travelItinerary" class="cursor-pointer px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 inline-block">
                                    <i class="fas fa-upload mr-2"></i> Upload File
                                </label>
                                <p class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 2MB)</p>
                                <div id="travelPreview" class="mt-3 hidden">
                                    <div class="flex items-center justify-between bg-blue-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-pdf text-blue-600 mr-2"></i>
                                            <span class="text-sm text-blue-800">travel_itinerary.pdf</span>
                                        </div>
                                        <button type="button" onclick="removeFile('travelItinerary')" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 6: Electronic Signature -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-signature mr-2 text-indigo-600"></i> 
                    Electronic Signature
                </h3>

                <!-- Applicant's Name Display -->
                <div class="mb-6 bg-gradient-to-r from-indigo-50 to-violet-50 border border-indigo-200 rounded-xl p-6">
                    <div class="text-center">
                        <p class="text-sm text-indigo-700 mb-2">Applicant's Name (As it will appear on CS Form No. 6)</p>
                        <p class="text-xl font-bold text-indigo-900 uppercase tracking-wider" id="applicantNameDisplay">
                            @php
                                $firstName = strtoupper(auth()->user()->first_name);
                                $middleInitial = auth()->user()->middle_name ? strtoupper(substr(auth()->user()->middle_name, 0, 1)) . '.' : '';
                                $lastName = strtoupper(auth()->user()->last_name);
                                echo "{$firstName} {$middleInitial} {$lastName}";
                            @endphp
                        </p>
                        <input type="hidden" name="applicant_name" value="{{ $firstName }} {{ $middleInitial }} {{ $lastName }}">
                    </div>
                </div>

                <!-- Signature Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Electronic Signature <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                         id="signatureDropZone" onclick="document.getElementById('electronicSignature').click()">
                        <input type="file" name="electronic_signature" id="electronicSignature" 
                               accept=".png,.jpg,.jpeg,.svg" required
                               class="hidden" onchange="previewSignature(event)">
                        
                        <div id="signatureUploadArea">
                            <i class="fas fa-signature text-4xl text-indigo-400 mb-3"></i>
                            <p class="text-lg text-gray-700 mb-2">
                                <span class="font-semibold text-indigo-600">Click to upload</span> your electronic signature
                            </p>
                            <p class="text-sm text-gray-500">PNG, JPG, SVG (Max: 1MB)</p>
                        </div>
                        
                        <div id="signaturePreview" class="hidden">
                            <div class="flex flex-col items-center">
                                <div class="relative mb-3">
                                    <img id="signaturePreviewImage" class="max-h-40 border-2 border-indigo-200 rounded-lg shadow-sm">
                                    <div class="absolute -top-2 -right-2">
                                        <button type="button" onclick="removeSignature()" 
                                                class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-green-600 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Signature uploaded successfully
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Signature Agreement -->
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold text-yellow-900 mb-2">Signature Agreement & Certification</p>
                                <p class="text-yellow-800">
                                    By submitting this application with your electronic signature, you certify that:
                                </p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-yellow-800">
                                    <li>All information provided is true and correct to the best of your knowledge</li>
                                    <li>You have read and understood the CSC Omnibus Rules on Leave</li>
                                    <li>You are aware of the penalties for falsifying leave applications</li>
                                    <li>The leave applied for is necessary and justifiable</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 7: CSC Requirements & Notes -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                    <i class="fas fa-clipboard-check mr-2"></i> 
                    CSC Requirements & Notes
                </h3>
                
                <div id="cscRequirements" class="space-y-3">
                    <!-- Will be populated dynamically based on leave type -->
                </div>
                
                <div class="mt-4 p-4 bg-blue-100 border border-blue-300 rounded-lg">
                    <p class="text-sm text-blue-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> This application follows CSC Memorandum Circular No. 41, s. 1998 
                        (Revised Omnibus Rules on Leave) and other relevant CSC issuances.
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <div>
                    <a href="{{ route('employees.leaves') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                    </a>
                </div>
                
                <div class="flex space-x-4">
                    <button type="button" onclick="resetForm()" 
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center">
                        <i class="fas fa-redo mr-2"></i> Reset Form
                    </button>
                    
                    <button type="submit" id="submitButton"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 flex items-center shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function cscLeaveApplicationForm() {
    return {
        // Form Data
        selectedLeaveType: '',
        cscEmployeeType: '{{ auth()->user()->employee_classification }}',
        leaveBasis: '{{ auth()->user()->getLeaveBasis() }}',
        leaveBasisDisplay: '',
        durationType: 'full_day',
        startDate: '',
        endDate: '',
        startTime: '08:00',
        endTime: '12:00',
        totalHours: 4,
        calculatedDays: 0,
        equivalentDaysCsc: 0,
        workHoursPerDay: {{ auth()->user()->work_hours_per_day ?? 8 }},
        halfDayTime: 'morning',
        isLwop: false,
        lwopDeductionRate: 0,
        lwopDaysCharged: 0,
        slpType: '',
        maternityDeliveryDate: '',
        isMiscarriage: false,
        
        // Duration Types
        durationTypes: [
            { value: 'half_day', label: 'Half Day', description: '4 hours (0.5 day)', icon: 'fa-clock' },
            { value: 'full_day', label: 'Full Day', description: '8 hours (1 day)', icon: 'fa-calendar-day' },
            { value: 'multiple_days', label: 'Multiple Days', description: '2+ calendar days', icon: 'fa-calendar-week' }
        ],
        
        // SLP Requirements
        slpRequirements: {
            'funeral_mourning': ['Death certificate', 'Proof of relationship'],
            'graduation': ['Invitation/Program', 'Proof of relationship'],
            'enrollment': ['School enrollment form', 'Proof of relationship'],
            'wedding_anniversary': ['Marriage certificate'],
            'birthday': ['Birth certificate'],
            'hospitalization': ['Medical certificate', 'Proof of relationship'],
            'accident': ['Police/Incident report', 'Medical certificate'],
            'relocation': ['Proof of new residence'],
            'government_transaction': ['Appointment/Transaction slip'],
            'calamity': ['Proof of residence in calamity area']
        },

        init() {
            this.updateLeaveBasis();
            this.setupEventListeners();
            this.setupFileUploads();
            this.updateHalfDayTimes();
            this.calculateDays();
        },

        setupEventListeners() {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            
            if (startDateInput) startDateInput.min = today;
            if (endDateInput) endDateInput.min = today;
        },

        setupFileUploads() {
            // Medical certificate
            const medicalCertInput = document.getElementById('medicalCertificate');
            if (medicalCertInput) {
                medicalCertInput.addEventListener('change', function(e) {
                    previewFile(e, 'medicalPreview', 'medicalCertificate');
                });
            }
            
            // Travel itinerary
            const travelInput = document.getElementById('travelItinerary');
            if (travelInput) {
                travelInput.addEventListener('change', function(e) {
                    previewFile(e, 'travelPreview', 'travelItinerary');
                });
            }
        },

        updateLeaveBasis() {
            const basisMap = {
                'regular': 'Standard VL/SL (Regular Employees)',
                'teacher': 'Teacher Proportional Vacation Pay (PVP)',
                'part_time': 'Part-time Proportional Computation',
                'contractual': 'Standard VL/SL (Contractual)',
                'local_elective': 'Special Law Coverage',
                'judicial': 'Special Law Coverage',
                'executive': 'Special Law Coverage',
                'faculty': 'Faculty Rules'
            };
            
            this.leaveBasisDisplay = basisMap[this.cscEmployeeType] || 'Standard VL/SL';
            
            // Update work hours for part-time
            if (this.cscEmployeeType === 'part_time') {
                this.workHoursPerDay = {{ auth()->user()->work_hours_per_day ?? 4 }};
            } else {
                this.workHoursPerDay = 8;
            }
            
            this.calculateEquivalentDays();
        },

        updateHalfDayTimes() {
            if (this.halfDayTime === 'morning') {
                this.startTime = '08:00';
                this.endTime = '12:00';
                this.totalHours = 4;
            } else if (this.halfDayTime === 'afternoon') {
                this.startTime = '13:00';
                this.endTime = '17:00';
                this.totalHours = 4;
            } else if (this.halfDayTime === 'custom') {
                // Keep existing custom times, but set default if empty
                if (!this.startTime) this.startTime = '08:00';
                if (!this.endTime) this.endTime = '12:00';
                this.calculateHours(); // Recalculate hours for custom time
            }
            this.calculateEquivalentDays();
        },

        onLeaveTypeChange() {
            this.showEligibilityCheck();
            this.updateCscRequirements();
            this.calculateDays();
        },

        onDurationTypeChange() {
            // Reset end date for half-day and full-day
            if (this.durationType === 'half_day') {
                this.endDate = this.startDate;
                this.calculatedDays = 0.5;
                this.updateHalfDayTimes();
            } else if (this.durationType === 'full_day') {
                this.calculatedDays = 1;
                this.endDate = this.startDate;
            } else {
                this.calculatedDays = 0;
                this.halfDayTime = ''; // Clear half-day time selection
            }
            
            this.calculateEquivalentDays();
        },

        showEligibilityCheck() {
            if (!this.selectedLeaveType) return;
            
            const eligibilitySection = document.getElementById('eligibilityCheck');
            const title = document.getElementById('eligibilityTitle');
            const message = document.getElementById('eligibilityMessage');
            const requirementsList = document.getElementById('requirementsList');
            
            if (!eligibilitySection || !title || !message || !requirementsList) return;
            
            // Simulate eligibility check
            const isEligible = this.checkEligibility();
            
            if (isEligible) {
                eligibilitySection.classList.remove('hidden');
                title.textContent = '✓ Eligible for this Leave Type';
                title.classList.add('text-green-900');
                message.textContent = 'You meet all the requirements for this leave type.';
                message.classList.add('text-green-700');
                
                requirementsList.innerHTML = `
                    <div class="flex items-center text-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Valid employee classification</span>
                    </div>
                    <div class="flex items-center text-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Sufficient leave credits available</span>
                    </div>
                `;
            } else {
                eligibilitySection.classList.remove('hidden');
                title.textContent = '⚠ Additional Requirements Needed';
                title.classList.add('text-yellow-900');
                message.textContent = 'Please ensure you meet the following requirements:';
                message.classList.add('text-yellow-700');
                
                requirementsList.innerHTML = `
                    <div class="flex items-center text-yellow-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Additional documentation required</span>
                    </div>
                `;
            }
        },

        checkEligibility() {
            // Simplified eligibility logic
            switch(this.selectedLeaveType) {
                case 'maternity':
                    return {{ auth()->user()->gender === 'Female' && auth()->user()->marital_status === 'married' ? 'true' : 'false' }};
                case 'paternity':
                    return {{ auth()->user()->gender === 'Male' && auth()->user()->marital_status === 'married' && auth()->user()->delivery_count < 4 ? 'true' : 'false' }};
                default:
                    return true;
            }
        },

        updateCscRequirements() {
            const requirementsDiv = document.getElementById('cscRequirements');
            if (!requirementsDiv) return;
            
            let requirements = [];
            
            switch(this.selectedLeaveType) {
                case 'sick':
                    requirements = [
                        'Medical certificate required for leaves exceeding 3 consecutive days',
                        'Immediate supervisor must be notified as soon as possible',
                        'Follow-up medical certificate may be required for extended leaves',
                        'Hospital confinement requires official hospital certificate'
                    ];
                    break;
                case 'maternity':
                    requirements = [
                        '105 days for normal delivery or miscarriage',
                        'Additional 15 days for solo parents',
                        'Certificate of pregnancy from physician required',
                        'Leave must be taken continuously'
                    ];
                    break;
                case 'vacation':
                    requirements = [
                        'Minimum 3 working days advance notice',
                        'Maximum 15 consecutive working days',
                        'Forced leave (5 days) must be taken first if applicable',
                        'Travel authority required for leaves abroad'
                    ];
                    break;
                case 'special_privilege':
                    requirements = [
                        'Maximum 3 days per year for any combination',
                        'Non-cumulative and non-commutative',
                        'Specific documentation required based on SLP type',
                        'Cannot be availed during probationary period'
                    ];
                    break;
                default:
                    requirements = [
                        'Ensure all required documents are submitted',
                        'Follow agency-specific guidelines',
                        'Comply with CSC Omnibus Rules on Leave'
                    ];
            }
            
            requirementsDiv.innerHTML = requirements.map(req => 
                `<div class="flex items-start">
                    <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                    <span class="text-blue-800">${req}</span>
                </div>`
            ).join('');
        },

        calculateDays() {
            if (this.durationType === 'half_day') {
                this.calculatedDays = 0.5;
                this.endDate = this.startDate; // Ensure end date matches start date for half-day
            } else if (this.durationType === 'full_day') {
                this.calculatedDays = 1;
                if (this.startDate && !this.endDate) {
                    this.endDate = this.startDate;
                }
            } else if (this.durationType === 'multiple_days' && this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                
                if (end < start) {
                    // Swap dates if end date is before start date
                    [this.startDate, this.endDate] = [this.endDate, this.startDate];
                    this.calculateDays(); // Recalculate with swapped dates
                    return;
                }
                
                // Calculate working days (excluding weekends)
                let workingDays = 0;
                const current = new Date(start);
                
                while (current <= end) {
                    const dayOfWeek = current.getDay();
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Skip Sunday (0) and Saturday (6)
                        workingDays++;
                    }
                    current.setDate(current.getDate() + 1);
                }
                
                this.calculatedDays = workingDays;
            }
            
            this.calculateEquivalentDays();
            if (this.isLwop) {
                this.calculateLwopDeduction();
            }
        },

        calculateHours() {
            if (this.durationType === 'half_day' && this.startTime && this.endTime && this.halfDayTime === 'custom') {
                try {
                    // Parse times
                    const startParts = this.startTime.split(':');
                    const endParts = this.endTime.split(':');
                    
                    if (startParts.length !== 2 || endParts.length !== 2) {
                        this.totalHours = 4;
                        this.calculateEquivalentDays();
                        return;
                    }
                    
                    const startHour = parseInt(startParts[0]) + (parseInt(startParts[1]) / 60);
                    const endHour = parseInt(endParts[0]) + (parseInt(endParts[1]) / 60);
                    
                    let hours = endHour - startHour;
                    
                    // Ensure hours are positive
                    if (hours < 0) {
                        hours += 24; // Handle overnight (unlikely for half-day but just in case)
                    }
                    
                    // Cap at 8 hours for half-day (though technically shouldn't exceed 4)
                    hours = Math.min(Math.max(0.5, hours), 8);
                    
                    this.totalHours = Math.round(hours * 2) / 2; // Round to nearest 0.5
                    
                } catch (error) {
                    console.error('Error calculating hours:', error);
                    this.totalHours = 4; // Default to 4 hours on error
                }
            } else if (this.durationType === 'half_day') {
                // For morning/afternoon periods, use predefined hours
                this.totalHours = 4;
            }
            
            this.calculateEquivalentDays();
        },

        calculateEquivalentDays() {
            if (this.durationType === 'half_day') {
                // For half-day: hours worked ÷ standard work hours per day
                this.equivalentDaysCsc = this.totalHours / this.workHoursPerDay;
            } else {
                // For full-day and multiple days: use calculated days
                this.equivalentDaysCsc = this.calculatedDays;
            }
            
            // Round to 4 decimal places for CSC compliance
            this.equivalentDaysCsc = Math.round(this.equivalentDaysCsc * 10000) / 10000;
            
            // Ensure minimum value
            if (this.equivalentDaysCsc < 0) {
                this.equivalentDaysCsc = 0;
            }
        },

        calculateLwopDeduction() {
            if (this.isLwop && this.calculatedDays > 0) {
                // CSC LWOP deduction rates
                const rates = {1: 0.25, 2: 0.50, 3: 0.75, 4: 1.00};
                const days = Math.min(this.calculatedDays, 4);
                this.lwopDeductionRate = rates[days] || 1.00;
                this.lwopDaysCharged = this.calculatedDays * this.lwopDeductionRate;
            } else {
                this.lwopDeductionRate = 0;
                this.lwopDaysCharged = 0;
            }
        },

        validateMaternityDate() {
            if (this.maternityDeliveryDate) {
                const deliveryDate = new Date(this.maternityDeliveryDate);
                const today = new Date();
                const maxDaysBefore = 60; // Can apply up to 60 days before delivery
                
                deliveryDate.setDate(deliveryDate.getDate() - maxDaysBefore);
                
                if (this.startDate && new Date(this.startDate) < deliveryDate) {
                    this.showToast('Maternity leave cannot start more than 60 days before the expected delivery date.', 'error');
                    this.startDate = '';
                }
            }
        },

        validateForm() {
            // Basic validation
            if (!this.startDate) {
                this.showToast('Please select a start date', 'error');
                return false;
            }
            
            // Check if start date is in the past
            const today = new Date().toISOString().split('T')[0];
            if (this.startDate < today) {
                this.showToast('Start date cannot be in the past', 'error');
                return false;
            }
            
            // Validate duration type specific requirements
            if (this.durationType === 'multiple_days') {
                if (!this.endDate) {
                    this.showToast('Please select an end date for multiple days leave', 'error');
                    return false;
                }
                
                if (this.endDate < this.startDate) {
                    this.showToast('End date cannot be before start date', 'error');
                    return false;
                }
                
                if (this.calculatedDays < 2) {
                    this.showToast('Multiple days leave must be at least 2 days', 'error');
                    return false;
                }
            }
            
            if (this.durationType === 'half_day') {
                if (!this.halfDayTime) {
                    this.showToast('Please select a half-day period', 'error');
                    return false;
                }
                
                if (this.halfDayTime === 'custom') {
                    if (!this.startTime || !this.endTime) {
                        this.showToast('Please specify custom time details', 'error');
                        return false;
                    }
                    
                    const start = new Date(`2000-01-01T${this.startTime}`);
                    const end = new Date(`2000-01-01T${this.endTime}`);
                    if (end <= start) {
                        this.showToast('End time must be after start time', 'error');
                        return false;
                    }
                    
                    // Validate time format
                    const timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
                    if (!timeRegex.test(this.startTime) || !timeRegex.test(this.endTime)) {
                        this.showToast('Please enter valid time format (HH:MM)', 'error');
                        return false;
                    }
                }
            }
            
            // Validate selected leave type
            if (!this.selectedLeaveType) {
                this.showToast('Please select a leave type', 'error');
                return false;
            }
            
            // Check signature
            const signatureInput = document.getElementById('electronicSignature');
            if (!signatureInput || !signatureInput.files.length) {
                this.showToast('Please upload your electronic signature', 'error');
                return false;
            }
            
            // Validate file sizes
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (signatureInput.files[0] && signatureInput.files[0].size > maxSize) {
                this.showToast('Signature file size must be less than 2MB', 'error');
                return false;
            }
            
            const medicalInput = document.getElementById('medicalCertificate');
            if (medicalInput && medicalInput.files[0] && medicalInput.files[0].size > maxSize) {
                this.showToast('Medical certificate file size must be less than 2MB', 'error');
                return false;
            }
            
            const travelInput = document.getElementById('travelItinerary');
            if (travelInput && travelInput.files[0] && travelInput.files[0].size > maxSize) {
                this.showToast('Travel itinerary file size must be less than 2MB', 'error');
                return false;
            }
            
            return true;
        },

        submitForm() {
            // Validate form
            if (!this.validateForm()) {
                return;
            }
            
            const form = document.getElementById('leaveForm');
            const submitButton = document.getElementById('submitButton');
            
            if (!form || !submitButton) {
                this.showToast('Form submission error. Please refresh the page and try again.', 'error');
                return;
            }
            
            const originalText = submitButton.innerHTML;
            
            // Disable submit button
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
            
            // Create FormData
            const formData = new FormData(form);
            
            // Add CSC computation data
            formData.append('equivalent_days_csc', this.equivalentDaysCsc);
            if (this.durationType === 'half_day') {
                formData.append('total_hours', this.totalHours);
                formData.append('half_day_time', this.halfDayTime);
            }
            if (this.isLwop) {
                formData.append('lwop_deduction_rate', this.lwopDeductionRate);
                formData.append('lwop_days_charged', this.lwopDaysCharged);
            }
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            // Submit via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // First, try to parse the response as JSON
                return response.json().then(data => {
                    // Return both the response status and parsed data
                    return {
                        status: response.status,
                        ok: response.ok,
                        data: data
                    };
                }).catch(() => {
                    // If response is not JSON, return basic info
                    return {
                        status: response.status,
                        ok: response.ok,
                        data: {
                            success: false,
                            message: 'Server returned an invalid response.'
                        }
                    };
                });
            })
            .then(({ status, ok, data }) => {
                if (ok || status === 422 || status === 500) {
                    // Handle successful responses and validation/error responses
                    if (data.success === true) {
                        this.showToast(data.message || 'Leave application submitted successfully!', 'success');
                        
                        // Redirect after delay
                        setTimeout(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                window.location.href = '{{ route("employees.leaves") }}';
                            }
                        }, 2000);
                    } else {
                        // Handle validation errors and server errors
                        let errorMessage = data.message || 'Please fix the errors below.';
                        
                        // Display field-specific errors if available
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0];
                            if (firstError && firstError[0]) {
                                errorMessage = firstError[0];
                            }
                            
                            // Highlight fields with errors
                            this.displayValidationErrors(data.errors);
                        } else if (data.error && status === 500) {
                            // Show server error in development, generic message in production
                            const isDebug = {{ config('app.debug') ? 'true' : 'false' }};
                            errorMessage = isDebug ? data.error : 'A server error occurred. Please try again later.';
                        }
                        
                        this.showToast(errorMessage, 'error');
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                } else {
                    // Handle other HTTP errors
                    throw new Error(data.message || `Server error (${status}). Please try again.`);
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                this.showToast(error.message || 'An error occurred while submitting. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        },

        // Helper method to display validation errors
        displayValidationErrors(errors) {
            // Clear previous error highlights
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
            });
            document.querySelectorAll('.error-message').forEach(el => {
                el.remove();
            });
            
            // Add new error highlights
            Object.keys(errors).forEach(fieldName => {
                // Find the input field - handle special cases
                let input;
                
                if (fieldName === 'csc_employee_type') {
                    input = document.querySelector('select[name="csc_employee_type"]');
                } else if (fieldName === 'leave_basis') {
                    input = document.querySelector('input[name="leave_basis"]');
                } else if (fieldName === 'type') {
                    // Handle radio buttons for leave type
                    const radioInputs = document.querySelectorAll('input[name="type"]');
                    if (radioInputs.length > 0) {
                        // Just highlight the first one
                        input = radioInputs[0].closest('label');
                    }
                } else if (fieldName === 'electronic_signature') {
                    input = document.getElementById('electronicSignature');
                } else {
                    input = document.querySelector(`[name="${fieldName}"]`);
                }
                
                if (input) {
                    // Add red border to input
                    input.classList.add('border-red-500');
                    
                    // Create error message element
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-red-600 text-sm mt-1';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i> ${errors[fieldName][0]}`;
                    
                    // Insert after input or its parent
                    if (input.parentNode) {
                        input.parentNode.appendChild(errorDiv);
                    } else {
                        input.after(errorDiv);
                    }
                }
            });
        },

        showToast(message, type = 'success') {
            // Remove existing toasts
            document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());
            
            const toast = document.createElement('div');
            toast.className = `toast-notification fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white flex items-center space-x-3 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            } z-50 transform transition-all duration-300 translate-x-full`;
            
            const icon = document.createElement('i');
            icon.className = type === 'success' ? 'fas fa-check-circle' :
                            type === 'error' ? 'fas fa-exclamation-circle' :
                            'fas fa-info-circle';
            toast.appendChild(icon);
            
            const text = document.createElement('span');
            text.textContent = message;
            toast.appendChild(text);
            
            document.body.appendChild(toast);
            
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
            });
            
            // Remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }
    };
}

// File preview functions
function previewFile(event, previewId, inputId) {
    const file = event.target.files[0];
    if (!file) return;
    
    const preview = document.getElementById(previewId);
    if (!preview) return;
    
    const fileName = file.name;
    const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
    
    preview.innerHTML = `
        <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg border border-green-200">
            <div class="flex items-center">
                <i class="fas fa-file-pdf text-green-600 mr-2"></i>
                <div>
                    <div class="text-sm font-medium text-green-800 truncate max-w-xs">${fileName}</div>
                    <div class="text-xs text-green-600">${fileSize} MB</div>
                </div>
            </div>
            <button type="button" onclick="removeFile('${inputId}')" class="text-red-500 hover:text-red-700 ml-2">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    preview.classList.remove('hidden');
}

function removeFile(inputId) {
    const input = document.getElementById(inputId);
    const previewId = inputId === 'medicalCertificate' ? 'medicalPreview' : 'travelPreview';
    const preview = document.getElementById(previewId);
    
    if (input) input.value = '';
    if (preview) {
        preview.classList.add('hidden');
        preview.innerHTML = '';
    }
}

function previewSignature(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Check file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Signature file size must be less than 2MB');
        event.target.value = '';
        return;
    }
    
    // Check file type
    const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
    if (!validTypes.includes(file.type)) {
        alert('Please upload a PNG, JPG, or SVG file');
        event.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewImage = document.getElementById('signaturePreviewImage');
        const uploadArea = document.getElementById('signatureUploadArea');
        const preview = document.getElementById('signaturePreview');
        
        if (previewImage) previewImage.src = e.target.result;
        if (uploadArea) uploadArea.classList.add('hidden');
        if (preview) preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removeSignature() {
    const input = document.getElementById('electronicSignature');
    const uploadArea = document.getElementById('signatureUploadArea');
    const preview = document.getElementById('signaturePreview');
    
    if (input) input.value = '';
    if (uploadArea) uploadArea.classList.remove('hidden');
    if (preview) preview.classList.add('hidden');
}

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
        document.getElementById('leaveForm').reset();
        window.location.reload();
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum dates to today
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.min = today;
    });
    
    // Initialize date inputs with today's date
    const startDateInput = document.querySelector('input[name="start_date"]');
    if (startDateInput && !startDateInput.value) {
        startDateInput.value = today;
    }
    
    // Add event listener for date changes to trigger calculations
    const dateInputs = document.querySelectorAll('input[name="start_date"], input[name="end_date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = document.querySelector('[x-data]');
            if (form && form.__x) {
                const alpineComponent = Object.values(form.__x.$data)[0];
                if (alpineComponent && alpineComponent.calculateDays) {
                    alpineComponent.calculateDays();
                }
            }
        });
    });
    
    // Initialize time inputs with validation
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = document.querySelector('[x-data]');
            if (form && form.__x) {
                const alpineComponent = Object.values(form.__x.$data)[0];
                if (alpineComponent && alpineComponent.calculateHours) {
                    alpineComponent.calculateHours();
                }
            }
        });
    });
    
    // Initialize form validation for required fields
    const form = document.getElementById('leaveForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // This prevents the default form submission since we're using AJAX
            e.preventDefault();
        });
    }
});
</script>

<style>
/* Custom styles for the leave application form */
.border-dashed {
    border-style: dashed;
}

.transition-colors {
    transition: border-color 0.2s, background-color 0.2s;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Smooth transitions */
.rounded-xl {
    transition: all 0.3s ease;
}

/* File upload hover effects */
#signatureDropZone:hover {
    border-color: #818cf8;
    background-color: #f8fafc;
}

/* Custom checkbox and radio styles */
.form-radio:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
}

.form-checkbox:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}

/* Toast notification styles */
.toast-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Validation error styles */
.border-red-500 {
    border-color: #ef4444 !important;
}

.error-message {
    animation: fadeIn 0.3s ease-in;
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .toast-notification {
        left: 1rem;
        right: 1rem;
        max-width: calc(100% - 2rem);
    }
}

/* Focus styles for accessibility */
input:focus, select:focus, textarea:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Disabled state styles */
input:disabled, select:disabled, textarea:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Highlight required fields */
input:required:not(:placeholder-shown):invalid,
select:required:not(:placeholder-shown):invalid,
textarea:required:not(:placeholder-shown):invalid {
    border-color: #ef4444;
}

/* Loading spinner animation */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush
@endsection