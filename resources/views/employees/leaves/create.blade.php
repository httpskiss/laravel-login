@extends('layouts.employee')

@section('title', 'Apply for Leave')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Apply for Leave</h1>
            <p class="text-gray-600">Fill out the leave application form below</p>
        </div>

        <!-- Leave Balances -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Your Leave Balances</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center">
                    <span class="text-blue-700">Vacation Leave:</span>
                    <span class="font-semibold text-blue-900">{{ $leaveBalances['vacation'] ?? 0 }} days</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-blue-700">Sick Leave:</span>
                    <span class="font-semibold text-blue-900">{{ $leaveBalances['sick'] ?? 0 }} days</span>
                </div>
            </div>
        </div>

        <form id="leaveForm" action="{{ route('employees.leaves.store') }}" method="POST" enctype="multipart/form-data" 
              class="bg-white rounded-xl shadow-sm p-6 space-y-6" x-data="leaveApplicationForm()">
            @csrf

            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee Name</label>
                        <input type="text" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <input type="text" value="{{ auth()->user()->department }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input type="text" value="{{ auth()->user()->role }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Filing</label>
                        <input type="text" value="{{ now()->format('M d, Y') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                </div>
            </div>

            <!-- Leave Type Selection -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Details</h3>
                
                <!-- Leave Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type of Leave</label>
                    <select name="type" x-model="selectedLeaveType" @change="onLeaveTypeChange" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Leave Type</option>
                        @foreach(App\Models\Leave::getLeaveTypes() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dynamic Fields Based on Leave Type -->
                <template x-if="selectedLeaveType === '{{ App\Models\Leave::TYPE_VACATION }}'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Leave Location</label>
                            <select name="leave_location" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="within_philippines">Within the Philippines</option>
                                <option value="abroad">Abroad</option>
                            </select>
                        </div>
                        <div x-show="showAbroadField">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Specify Country</label>
                            <input type="text" name="abroad_specify" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </template>

                <template x-if="selectedLeaveType === '{{ App\Models\Leave::TYPE_SICK }}'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sick Leave Type</label>
                            <select name="sick_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="in_hospital">In Hospital</option>
                                <option value="out_patient">Out Patient</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Specify Illness</label>
                            <input type="text" name="hospital_illness" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </template>

                <!-- Add more templates for other leave types as needed -->
            </div>

            <!-- Dates and Duration -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates and Duration</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" x-model="startDate" @change="calculateDays" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" x-model="endDate" @change="calculateDays" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Days</label>
                        <input type="number" name="days" x-model="calculatedDays" step="0.5" min="0.5" required readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>

            <!-- Commutation -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Commutation</h3>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="commutation" value="requested" class="form-radio text-blue-600">
                        <span class="ml-2">Requested</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="commutation" value="not_requested" checked class="form-radio text-blue-600">
                        <span class="ml-2">Not Requested</span>
                    </label>
                </div>
            </div>

            <!-- Reason for Leave -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reason for Leave</h3>
                <textarea name="reason" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Please provide detailed reason for your leave application..."></textarea>
            </div>

            <!-- Emergency Contact -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                        <input type="text" name="emergency_contact_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Work Handover -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Handover</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Handover Person</label>
                        <select name="handover_person_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select Colleague</option>
                            @foreach($colleagues as $colleague)
                                <option value="{{ $colleague->id }}">
                                    {{ $colleague->first_name }} {{ $colleague->last_name }} - {{ $colleague->position }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Handover Notes</label>
                        <textarea name="handover_notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                  placeholder="Provide instructions for work handover..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Supporting Documents</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medical Certificate</label>
                        <input type="file" name="medical_certificate" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max: 2MB)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">TravelAuthorityItinerary</label>
                        <input type="file" name="travel_itinerary" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max: 2MB)</p>
                    </div>
                </div>
            </div>

            <!-- Requirements Information -->
            <div x-show="selectedLeaveType && requirements.title" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-blue-900 mb-2" x-text="requirements.title"></h4>
                <ul class="list-disc list-inside space-y-1 text-blue-800">
                    <template x-for="item in requirements.items" :key="item">
                        <li x-text="item"></li>
                    </template>
                </ul>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('employees.leaves') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function leaveApplicationForm() {
        return {
            selectedLeaveType: '',
            startDate: '',
            endDate: '',
            calculatedDays: 0,
            showAbroadField: false,
            requirements: {},

            onLeaveTypeChange() {
                // Show/hide abroad field for vacation leave
                this.showAbroadField = this.selectedLeaveType === '{{ App\Models\Leave::TYPE_VACATION }}';
                
                // Load requirements for selected leave type
                if (this.selectedLeaveType) {
                    this.loadRequirements(this.selectedLeaveType);
                }
            },

            calculateDays() {
                if (this.startDate && this.endDate) {
                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    this.calculatedDays = diffDays > 0 ? diffDays : 0;
                } else {
                    this.calculatedDays = 0;
                }
            },

            async loadRequirements(leaveType) {
                try {
                    // You can fetch requirements from server or use client-side data
                    const requirementsMap = {
                        'vacation': {
                            title: 'Vacation Leave Requirements',
                            items: [
                                'Minimum 3 working days advance notice required',
                                'Maximum consecutive leave: 15 working days',
                                'Blackout periods may apply during peak seasons',
                                'Coordinate with your team before submission'
                            ]
                        },
                        'sick': {
                            title: 'Sick Leave Requirements',
                            items: [
                                'Medical certificate required for leaves exceeding 3 days',
                                'Notification should be sent as soon as possible',
                                'Follow-up documents may be requested by HR',
                                'Contact your supervisor immediately for emergencies'
                            ]
                        },
                        'maternity': {
                            title: 'Maternity Leave Requirements',
                            items: [
                                '105 days maternity leave as per R.A. No. 11210',
                                'Submit certificate of pregnancy from physician',
                                'Additional 15 days for solo mothers',
                                '30 days for miscarriage or ectopic pregnancy'
                            ]
                        }
                        // Add more requirements as needed
                    };

                    this.requirements = requirementsMap[leaveType] || {
                        title: 'Leave Requirements',
                        items: ['Please ensure all required documents are submitted', 'Follow agency-specific guidelines for this leave type']
                    };
                } catch (error) {
                    console.error('Error loading requirements:', error);
                }
            }
        };
    }

    // Handle form submission
    document.getElementById('leaveForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
            submitBtn.disabled = true;

            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast('Leave application submitted successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('employees.leaves') }}';
                }, 1500);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            showToast(error.message, 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    function showToast(message, type = 'success') {
        // Toast implementation (same as in index)
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white flex items-center ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } z-50 transition-all duration-300 ease-in-out`;
        
        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle mr-2' : 'fas fa-exclamation-circle mr-2';
        toast.appendChild(icon);
        
        const text = document.createElement('span');
        text.textContent = message;
        toast.appendChild(text);
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endpush
@endsection