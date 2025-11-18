@extends('layouts.employee')

@section('title', 'New Travel Authority')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">New Travel Authority</h2>

            <form action="{{ route('employees.travel.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                        <input type="text" name="designation" id="designation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('designation', auth()->user()->department) }}">
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

                <div class="mb-6">
                    <label for="inclusive_date_of_travel" class="block text-sm font-medium text-gray-700 mb-2">Inclusive Date of Travel</label>
                    <input type="date" name="inclusive_date_of_travel" id="inclusive_date_of_travel" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('inclusive_date_of_travel') }}">
                    @error('inclusive_date_of_travel')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Transportation</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="transportation" value="college_vehicle" class="mr-2" {{ old('transportation') == 'college_vehicle' ? 'checked' : '' }}>
                                <span>College Vehicle</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="transportation" value="public_conveyance" class="mr-2" {{ old('transportation') == 'public_conveyance' ? 'checked' : '' }}>
                                <span>Public Conveyance</span>
                            </label>
                        </div>
                        @error('transportation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Expenses</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="estimated_expenses" value="official_time" class="mr-2" {{ old('estimated_expenses', 'official_time') == 'official_time' ? 'checked' : '' }}>
                                <span>Official Time</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="estimated_expenses" value="with_expenses" class="mr-2" {{ old('estimated_expenses') == 'with_expenses' ? 'checked' : '' }}>
                                <span>With Expenses</span>
                            </label>
                        </div>
                        @error('estimated_expenses')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="source_of_funds" class="block text-sm font-medium text-gray-700 mb-2">Source of Funds</label>
                    <input type="text" name="source_of_funds" id="source_of_funds"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('source_of_funds') }}">
                    @error('source_of_funds')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
@endsection