<div class="space-y-4">
    <div class="flex items-center space-x-4">
        <img class="h-12 w-12 rounded-full" src="{{ $user->profile_photo_url }}" alt="">
        <div>
            <h3 class="text-lg font-medium">{{ $user->first_name }} {{ $user->last_name }}</h3>
            <p class="text-sm text-gray-500">{{ $user->department }} • {{ $user->getRoleAttribute() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500">Today's Status</p>
            @if($user->todayAttendance)
                @if($user->todayAttendance->status === 'present')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                @elseif($user->todayAttendance->status === 'late')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Late</span>
                @elseif($user->todayAttendance->status === 'absent')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                @endif
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Not Recorded</span>
            @endif
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500">This Week</p>
            <p class="font-medium">{{ $user->weeklyPresentCount }}/{{ $user->workingDaysThisWeek }} days</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500">This Month</p>
            <p class="font-medium">{{ $user->monthlyPresentCount }}/{{ $user->workingDaysThisMonth }} days</p>
        </div>
    </div>

    <div>
        <h4 class="font-medium mb-2">Recent Attendance</h4>
        <div class="overflow-y-auto max-h-64">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($user->attendances as $attendance)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $attendance->date->format('M d, Y') }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            @if($attendance->status === 'present')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                            @elseif($attendance->status === 'late')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Late</span>
                            @elseif($attendance->status === 'absent')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>