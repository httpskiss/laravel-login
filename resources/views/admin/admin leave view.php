@extends('layouts.admin')

@section('title', 'Leave Applications Management')

@section('content')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-approved { background-color: #d1fae5; color: #065f46; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
    .status-cancelled { background-color: #e5e7eb; color: #374151; }
    
    .leave-type-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
    }
    
    .modal-overlay.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Simple Calendar Styles */
    .calendar-popup {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        max-width: 800px;
        width: 95%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background-color: #e5e7eb;
    }

    .calendar-day-header {
        background-color: #f8fafc;
        padding: 0.75rem;
        text-align: center;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .calendar-day {
        background-color: white;
        min-height: 80px;
        padding: 0.5rem;
        position: relative;
        transition: background-color 0.2s;
    }

    .calendar-day:hover {
        background-color: #f9fafb;
    }

    .calendar-day.other-month {
        background-color: #f9fafb;
        color: #9ca3af;
    }

    .calendar-day.today {
        background-color: #eff6ff;
        border: 2px solid #3b82f6;
    }

    .day-number {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .leave-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin: 1px;
        display: inline-block;
    }

    .leave-dot.vacation { background-color: #3b82f6; }
    .leave-dot.sick { background-color: #10b981; }
    .leave-dot.maternity { background-color: #ec4899; }
    .leave-dot.paternity { background-color: #8b5cf6; }
    .leave-dot.other { background-color: #f59e0b; }

    .leave-count {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        background-color: #f9fafb;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .leaves-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .leave-item {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .leave-item:hover {
        background-color: #f9fafb;
    }

    .leave-item:last-child {
        border-bottom: none;
    }
</style>

<main class="p-4 md:p-6">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-6 animate-fade-in">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                <div>
                    <!-- Title and subtitle removed but space preserved -->
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                    <!-- Calendar Button -->
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center show-calendar">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        View Leave Calendar
                    </button>
                    <div class="bg-green-50 px-3 py-2 rounded-lg">
                        <span class="text-sm text-green-600 font-medium">Today: {{ now()->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-800">12</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-800">45</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-red-100 text-red-600 mr-3">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-gray-800">8</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">On Leave Today</p>
                        <p class="text-2xl font-bold text-gray-800">5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rest of your existing content (filters, table, etc.) -->
        <div class="bg-white rounded-xl p-4 shadow-lg mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" placeholder="Search by employee name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex gap-2">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Leave Types</option>
                        <option value="vacation">Vacation Leave</option>
                        <option value="sick">Sick Leave</option>
                        <option value="maternity">Maternity Leave</option>
                        <option value="paternity">Paternity Leave</option>
                    </select>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Leave Applications Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-fade-in">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Filed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Your existing table rows -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        JD
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">John Doe</div>
                                        <div class="text-sm text-gray-500">HR Department</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="leave-type-badge bg-blue-100 text-blue-800">Vacation Leave</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Dec 15 - Dec 20, 2024
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                5 days
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge status-pending">Pending</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Dec 10, 2024
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3 view-application" data-id="1">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="text-green-600 hover:text-green-900 mr-3 approve-application" data-id="1">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                                <button class="text-red-600 hover:text-red-900 reject-application" data-id="1">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Calendar Popup Modal -->
<div id="calendarModal" class="modal-overlay">
    <div class="calendar-popup">
        <div class="calendar-header">
            <div class="flex items-center space-x-4">
                <button class="p-2 rounded-lg hover:bg-white/20 transition-colors prev-month">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h3 class="text-lg font-semibold current-month">December 2024</h3>
                <button class="p-2 rounded-lg hover:bg-white/20 transition-colors next-month">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-1 bg-white/20 rounded-lg hover:bg-white/30 transition-colors text-sm today-btn">
                    Today
                </button>
                <button class="p-2 rounded-lg hover:bg-white/20 transition-colors close-calendar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="p-4">
            <!-- Selected Date Info -->
            <div id="selectedDateInfo" class="mb-4 p-4 bg-blue-50 rounded-lg hidden">
                <h4 class="font-semibold text-blue-800 mb-2" id="selectedDateTitle">Leaves on December 15, 2024</h4>
                <div class="leaves-list" id="selectedDateLeaves">
                    <!-- Leaves will be populated here -->
                </div>
            </div>

            <!-- Calendar -->
            <div class="calendar-grid">
                <!-- Day Headers -->
                <div class="calendar-day-header">Sun</div>
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>

                <!-- Calendar Days -->
                <div id="calendarDays" class="col-span-7 grid grid-cols-7 gap-1 bg-gray-200">
                    <!-- Calendar days will be generated here -->
                </div>
            </div>

            <!-- Legend -->
            <div class="calendar-legend">
                <div class="legend-item">
                    <div class="legend-color bg-blue-500"></div>
                    <span>Vacation Leave</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-green-500"></div>
                    <span>Sick Leave</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-pink-500"></div>
                    <span>Maternity Leave</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-purple-500"></div>
                    <span>Paternity Leave</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-yellow-500"></div>
                    <span>Other Leave</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include your existing modals (View Application, Approval, Rejection) -->


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modals = {
        view: document.getElementById('viewApplicationModal'),
        approval: document.getElementById('approvalModal'),
        rejection: document.getElementById('rejectionModal')
    };

    // Close modal function
    function closeAllModals() {
        Object.values(modals).forEach(modal => {
            modal.classList.remove('active');
        });
    }

    // View application
    document.querySelectorAll('.view-application').forEach(button => {
        button.addEventListener('click', function() {
            const applicationId = this.dataset.id;
            // Here you would typically fetch application data
            modals.view.classList.add('active');
        });
    });

    // Approve application
    document.querySelectorAll('.approve-application').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const applicationId = this.dataset.id;
            modals.approval.classList.add('active');
        });
    });

    // Reject application
    document.querySelectorAll('.reject-application').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const applicationId = this.dataset.id;
            modals.rejection.classList.add('active');
        });
    });

    // Close modals
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', closeAllModals);
    });

    // Modal overlay click to close
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAllModals();
            }
        });
    });

    // Confirm approval
    document.querySelector('.confirm-approval').addEventListener('click', function() {
        // Here you would typically send approval to backend
        alert('Leave application approved successfully!');
        closeAllModals();
        // Refresh the page or update the table
        location.reload();
    });

    // Confirm rejection
    document.querySelector('.confirm-rejection').addEventListener('click', function() {
        const reason = document.querySelector('#rejectionModal textarea').value;
        if (!reason.trim()) {
            alert('Please provide a reason for rejection.');
            return;
        }
        
        // Here you would typically send rejection to backend
        alert('Leave application rejected successfully!');
        closeAllModals();
        // Refresh the page or update the table
        location.reload();
    });

    // Escape key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Calendar functionality
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedDate = null;

    // Sample leave data
    const leaveData = [
        {
            id: 1,
            employee: 'John Doe',
            department: 'HR Department',
            type: 'vacation',
            startDate: new Date(2024, 11, 15),
            endDate: new Date(2024, 11, 20),
            status: 'approved'
        },
        {
            id: 2,
            employee: 'Maria Santos',
            department: 'Finance Department',
            type: 'sick',
            startDate: new Date(2024, 11, 15),
            endDate: new Date(2024, 11, 17),
            status: 'approved'
        },
        {
            id: 3,
            employee: 'Robert Johnson',
            department: 'IT Department',
            type: 'paternity',
            startDate: new Date(2024, 11, 12),
            endDate: new Date(2024, 11, 19),
            status: 'approved'
        },
        {
            id: 4,
            employee: 'Sarah Wilson',
            department: 'Marketing Department',
            type: 'vacation',
            startDate: new Date(2024, 11, 22),
            endDate: new Date(2024, 11, 29),
            status: 'approved'
        }
    ];

    // Show calendar modal
    document.querySelector('.show-calendar').addEventListener('click', function() {
        generateCalendar(currentMonth, currentYear);
        document.getElementById('calendarModal').classList.add('active');
    });

    // Close calendar modal
    document.querySelector('.close-calendar').addEventListener('click', function() {
        document.getElementById('calendarModal').classList.remove('active');
    });

    // Calendar navigation
    document.querySelector('.prev-month').addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        generateCalendar(currentMonth, currentYear);
    });

    document.querySelector('.next-month').addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        generateCalendar(currentMonth, currentYear);
    });

    document.querySelector('.today-btn').addEventListener('click', function() {
        currentDate = new Date();
        currentMonth = currentDate.getMonth();
        currentYear = currentDate.getFullYear();
        generateCalendar(currentMonth, currentYear);
    });

    // Generate calendar
    function generateCalendar(month, year) {
        const calendarDays = document.getElementById('calendarDays');
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        // Update month header
        document.querySelector('.current-month').textContent = `${monthNames[month]} ${year}`;

        // Clear previous calendar
        calendarDays.innerHTML = '';

        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startingDay = firstDay.getDay();
        const monthLength = lastDay.getDate();

        // Get previous month's length
        const prevMonthLastDay = new Date(year, month, 0).getDate();

        // Add previous month's days
        for (let i = startingDay - 1; i >= 0; i--) {
            const dayElement = createDayElement(prevMonthLastDay - i, true);
            calendarDays.appendChild(dayElement);
        }

        // Add current month's days
        const today = new Date();
        for (let i = 1; i <= monthLength; i++) {
            const dayElement = createDayElement(i, false);
            if (year === today.getFullYear() && month === today.getMonth() && i === today.getDate()) {
                dayElement.classList.add('today');
            }
            calendarDays.appendChild(dayElement);
        }

        // Calculate remaining cells for next month
        const totalCells = 42;
        const remainingCells = totalCells - (startingDay + monthLength);
        
        // Add next month's days
        for (let i = 1; i <= remainingCells; i++) {
            const dayElement = createDayElement(i, true);
            calendarDays.appendChild(dayElement);
        }
    }

    function createDayElement(dayNumber, isOtherMonth) {
        const dayElement = document.createElement('div');
        dayElement.className = `calendar-day ${isOtherMonth ? 'other-month' : ''}`;
        
        const dayNumberElement = document.createElement('div');
        dayNumberElement.className = 'day-number';
        dayNumberElement.textContent = dayNumber;
        dayElement.appendChild(dayNumberElement);

        if (!isOtherMonth) {
            // Get leaves for this day
            const currentDate = new Date(currentYear, currentMonth, dayNumber);
            const leavesOnThisDay = leaveData.filter(leave => {
                return currentDate >= leave.startDate && currentDate <= leave.endDate && leave.status === 'approved';
            });

            // Add leave dots
            if (leavesOnThisDay.length > 0) {
                const dotsContainer = document.createElement('div');
                leavesOnThisDay.forEach(leave => {
                    const dot = document.createElement('span');
                    dot.className = `leave-dot ${leave.type}`;
                    dot.title = `${leave.employee} - ${leave.type} leave`;
                    dotsContainer.appendChild(dot);
                });
                dayElement.appendChild(dotsContainer);

                // Add leave count
                const countElement = document.createElement('div');
                countElement.className = 'leave-count';
                countElement.textContent = `${leavesOnThisDay.length} leave${leavesOnThisDay.length > 1 ? 's' : ''}`;
                dayElement.appendChild(countElement);
            }

            // Add click event
            dayElement.addEventListener('click', function() {
                selectDate(currentYear, currentMonth, dayNumber, leavesOnThisDay);
            });
        }

        return dayElement;
    }

    function selectDate(year, month, day, leaves) {
        selectedDate = new Date(year, month, day);
        const dateString = selectedDate.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        const infoSection = document.getElementById('selectedDateInfo');
        const titleElement = document.getElementById('selectedDateTitle');
        const leavesElement = document.getElementById('selectedDateLeaves');

        titleElement.textContent = `Leaves on ${dateString}`;
        
        if (leaves.length > 0) {
            leavesElement.innerHTML = leaves.map(leave => `
                <div class="leave-item">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-gray-800">${leave.employee}</div>
                            <div class="text-sm text-gray-600">${leave.department}</div>
                            <div class="text-xs text-gray-500">${formatDate(leave.startDate)} - ${formatDate(leave.endDate)}</div>
                        </div>
                        <span class="leave-type-badge bg-${getLeaveColor(leave.type)}-100 text-${getLeaveColor(leave.type)}-800">
                            ${leave.type.charAt(0).toUpperCase() + leave.type.slice(1)} Leave
                        </span>
                    </div>
                </div>
            `).join('');
            infoSection.classList.remove('hidden');
        } else {
            leavesElement.innerHTML = '<div class="text-center text-gray-500 py-4">No leaves scheduled for this date</div>';
            infoSection.classList.remove('hidden');
        }

        // Scroll to info section
        infoSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function getLeaveColor(type) {
        const colors = {
            vacation: 'blue',
            sick: 'green',
            maternity: 'pink',
            paternity: 'purple',
            other: 'yellow'
        };
        return colors[type] || 'gray';
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric' 
        });
    }

    // Close modal when clicking outside
    document.getElementById('calendarModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('calendarModal').classList.remove('active');
        }
    });

    // Initialize calendar (optional - if you want to preload)
    // generateCalendar(currentMonth, currentYear);
});
</script>
@endpush
@endsection
