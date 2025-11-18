@extends('layouts.employee')

@section('title', 'Attendance')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Attendance</h1>
            <p class="text-gray-600">Record your daily attendance</p>
        </div>

        <!-- Attendance Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Time In Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Time In</h3>
                        <p class="text-gray-600 text-sm">Record your arrival time</p>
                    </div>
                    <form action="{{ route('attendance.check') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="time_in">
                        <button type="submit" 
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-150">
                            <i class="fas fa-sign-in-alt mr-2"></i>Time In
                        </button>
                    </form>
                </div>
            </div>

            <!-- Time Out Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Time Out</h3>
                        <p class="text-gray-600 text-sm">Record your departure time</p>
                    </div>
                    <form action="{{ route('attendance.check') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="time_out">
                        <button type="submit" 
                                class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition duration-150">
                            <i class="fas fa-sign-out-alt mr-2"></i>Time Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Today's Status -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $todayAttendance->time_in ?? '--:--' }}</div>
                    <div class="text-sm text-gray-600">Time In</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $todayAttendance->time_out ?? '--:--' }}</div>
                    <div class="text-sm text-gray-600">Time Out</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $todayAttendance->total_hours ?? '0' }}h</div>
                    <div class="text-sm text-gray-600">Total Hours</div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Attendance</h3>
                <a href="{{ route('employees.attendance.all') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentAttendance as $attendance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '--:--' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '--:--' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->total_hours ?? '0' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($attendance->status === 'present') bg-green-100 text-green-800
                                    @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                    @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection