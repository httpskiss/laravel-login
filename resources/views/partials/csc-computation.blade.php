<div class="space-y-4">
    <h4 class="text-lg font-semibold text-purple-900">CSC Leave Computation Details</h4>
    
    <div class="bg-gray-50 rounded-lg p-4">
        <table class="w-full text-sm">
            <tr>
                <td class="p-2 font-semibold text-gray-700">CSC Employee Type:</td>
                <td class="p-2">{{ $leave->getCscEmployeeTypeDisplay() }}</td>
            </tr>
            <tr>
                <td class="p-2 font-semibold text-gray-700">Leave Basis:</td>
                <td class="p-2">{{ $leave->getLeaveBasisDisplay() }}</td>
            </tr>
            <tr>
                <td class="p-2 font-semibold text-gray-700">Work Hours per Day:</td>
                <td class="p-2">{{ $leave->user->work_hours_per_day }} hours</td>
            </tr>
            @if($leave->equivalent_days_csc)
            <tr>
                <td class="p-2 font-semibold text-gray-700">CSC Equivalent Days:</td>
                <td class="p-2">{{ number_format($leave->equivalent_days_csc, 4) }} days</td>
            </tr>
            @endif
        </table>
    </div>
    
    @if($leave->computation_notes)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm font-semibold text-blue-800 mb-1">Computation Notes:</p>
        <p class="text-sm text-blue-700">{{ $leave->computation_notes }}</p>
    </div>
    @endif
</div>