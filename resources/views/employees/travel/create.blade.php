@extends('layouts.employee')

@section('title', 'New Travel Authority')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">New Travel Authority</h2>

            <form action="{{ route('employees.travel.store') }}" method="POST" x-data="travelApplicationForm()">
                @csrf

                <!-- Travel Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type of Travel</label>
                    <select name="travel_type" x-model="travelType" @change="onTravelTypeChange" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Travel Type</option>
                        @foreach(App\Models\TravelAuthority::getTravelTypes() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('travel_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Travel Type Information -->
                <div x-show="travelType" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_PERSONAL_ABROAD }}'">
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-2">Personal Travel Abroad Requirements</h4>
                            <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                                <li>Requires travel authority from the President</li>
                                <li>Without official time and official business</li>
                                <li>Personal funds will be used</li>
                            </ul>
                        </div>
                    </template>
                    <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_OFFICIAL_TIME }}'">
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-2">Official Time Travel</h4>
                            <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                                <li>Expenses covered by official funds</li>
                                <li>Source of funds: MOOE</li>
                            </ul>
                        </div>
                    </template>
                    <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_OFFICIAL_BUSINESS }}'">
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-2">Official Business Travel</h4>
                            <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                                <li>University-related business activities</li>
                                <li>Expenses covered by official funds</li>
                            </ul>
                        </div>
                    </template>
                    <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_OFFICIAL_TRAVEL }}'">
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-2">Official Travel</h4>
                            <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                                <li>Official university travel</li>
                                <li>May use university vehicle</li>
                            </ul>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                        <input type="text" name="designation" id="designation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('designation', auth()->user()->position) }}">
                        @error('designation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                        <input type="text" name="destination" id="destination" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('destination') }}">
                        @error('destination')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Duration Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="duration_type" value="single_day" x-model="durationType" @change="onDurationChange" class="mr-3">
                            <div>
                                <span class="block font-medium text-gray-900">Single Day</span>
                                <span class="text-sm text-gray-500">One day travel</span>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="duration_type" value="multiple_days" x-model="durationType" @change="onDurationChange" class="mr-3">
                            <div>
                                <span class="block font-medium text-gray-900">Multiple Days</span>
                                <span class="text-sm text-gray-500">Multiple days travel</span>
                            </div>
                        </label>
                    </div>
                    @error('duration_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" x-model="startDate" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('start_date') }}">
                        @error('start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="durationType === 'multiple_days'">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" x-model="endDate"
                            :required="durationType === 'multiple_days'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('end_date') }}">
                        @error('end_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Single Date Field -->
                    <div x-show="durationType === 'single_day'" class="md:col-span-2">
                        <label for="inclusive_date_of_travel" class="block text-sm font-medium text-gray-700 mb-2">Travel Date</label>
                        <input type="date" name="inclusive_date_of_travel" id="inclusive_date_of_travel" x-model="singleDate"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('inclusive_date_of_travel') }}">
                        @error('inclusive_date_of_travel')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose</label>
                    <textarea name="purpose" id="purpose" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Describe the purpose of your travel...">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transportation -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transportation</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach(App\Models\TravelAuthority::getTransportationTypes() as $value => $label)
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="transportation" value="{{ $value }}" 
                                   class="mr-3" {{ old('transportation') == $value ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('transportation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Source of Funds -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Source of Funds</label>
                    <div class="space-y-3">
                        @foreach(App\Models\TravelAuthority::getFundSources() as $value => $label)
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="source_of_funds" value="{{ $value }}" 
                                   x-model="fundSource"
                                   class="mr-3" {{ old('source_of_funds') == $value ? 'checked' : '' }}>
                            <div>
                                <span class="block font-medium text-gray-900">{{ $label }}</span>
                                <span class="text-sm text-gray-500">
                                    @if($value === 'mooe')
                                        Maintenance and Other Operating Expenses
                                    @elseif($value === 'personal')
                                        Personal funds for personal travel
                                    @else
                                        Other funding sources
                                    @endif
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('source_of_funds')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Other Funds Specification -->
                <div x-show="fundSource === 'other'" class="mb-6">
                    <label for="other_funds_specification" class="block text-sm font-medium text-gray-700 mb-2">Specify Other Funding Source</label>
                    <input type="text" name="other_funds_specification" id="other_funds_specification"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('other_funds_specification') }}"
                        placeholder="Please specify the source of funds...">
                    @error('other_funds_specification')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Auto-set fields based on travel type -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2">Automated Settings</h4>
                    <div class="text-sm text-gray-600">
                        <p x-show="travelType === 'personal_abroad'">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            This travel will require President's approval and will use personal funds.
                        </p>
                        <p x-show="travelType === 'official_time' || travelType === 'official_business'">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            This travel will use official funds (MOOE).
                        </p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('employees.travel') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Submit Travel Authority
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function travelApplicationForm() {
    return {
        travelType: '',
        durationType: 'single_day',
        startDate: '',
        endDate: '',
        singleDate: '',
        fundSource: '',

        init() {
            // Set initial fund source based on travel type if present
            if (this.travelType) {
                this.onTravelTypeChange();
            }
        },

        onTravelTypeChange() {
            // Auto-set fund source based on travel type
            if (this.travelType === 'personal_abroad') {
                this.fundSource = 'personal';
                // Also auto-select personal funds radio button
                const personalRadio = document.querySelector('input[name="source_of_funds"][value="personal"]');
                if (personalRadio) {
                    personalRadio.checked = true;
                }
            } else if (this.travelType === 'official_time' || this.travelType === 'official_business' || this.travelType === 'official_travel') {
                this.fundSource = 'mooe';
                // Also auto-select MOOE radio button
                const mooeRadio = document.querySelector('input[name="source_of_funds"][value="mooe"]');
                if (mooeRadio) {
                    mooeRadio.checked = true;
                }
            }
        },

        onDurationChange() {
            if (this.durationType === 'single_day') {
                this.endDate = '';
            }
        }
    };
}
</script>
@endpush
@endsection