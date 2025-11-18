@extends('layouts.admin')

@section('title', 'Travel Authority Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Travel Authority Management</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.travel.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-download mr-2"></i> Export
            </a>
            <a href="{{ route('admin.travel.report') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-chart-bar mr-2"></i> Reports
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TA No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($travels as $travel)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $travel->travel_authority_no ?? 'Pending' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $travel->user->first_name }} {{ $travel->user->last_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $travel->destination }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $travel->inclusive_date_of_travel->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($travel->status == 'approved') bg-green-100 text-green-800
                                @elseif($travel->status == 'rejected') bg-red-100 text-red-800
                                @elseif($travel->status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $travel->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.travel.show', $travel) }}" class="text-blue-600 hover:text-blue-900">View/Approve</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No travel authorities found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection