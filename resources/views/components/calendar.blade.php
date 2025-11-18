<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Attendance Calendar</h1>
                <p class="text-gray-600">Track your daily attendance status</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="legend-dot bg-blue-600"></span>
                    <span class="text-sm">Present</span>
                </div>
                <div class="flex items-center">
                    <span class="legend-dot bg-red-600"></span>
                    <span class="text-sm">Absent</span>
                </div>
                <div class="flex items-center">
                    <span class="legend-dot bg-yellow-600"></span>
                    <span class="text-sm">Late</span>
                </div>
                <div class="flex items-center">
                    <span class="legend-dot bg-purple-600"></span>
                    <span class="text-sm">Leave</span>
                </div>
                <div class="flex items-center">
                    <span class="legend-dot bg-green-600"></span>
                    <span class="text-sm">Holiday</span>
                </div>
            </div>
        </div>

        <!-- Calendar Controls -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <button id="prev-month" class="p-2 rounded-full hover:bg-gray-100">
                        <i class="fas fa-chevron-left text-gray-600"></i>
                    </button>
                    <h2 id="current-month" class="text-xl font-semibold text-gray-800">{{ now()->format('F Y') }}</h2>
                    <button id="next-month" class="p-2 rounded-full hover:bg-gray-100">
                        <i class="fas fa-chevron-right text-gray-600"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="today-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Today
                    </button>
                    <select id="month-select" class="appearance-none bg-gray-100 border-0 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        @foreach(range(0, 11) as $month)
                            <option value="{{ $month }}" {{ $month == now()->month - 1 ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $month + 1)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select id="year-select" class="appearance-none bg-gray-100 border-0 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        @foreach(range(now()->year - 2, now()->year + 2) as $year)
                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 gap-px bg-gray-200">
                <div class="bg-gray-100 py-2 text-center text-sm font-medium text-gray-500">Sun</div>
                <div class="bg-gray-100 py-2 text-center text-sm font-medium text-gray-500">Mon</div>
                <div class="bg-gray-100 py-2 text-center text-sm font-medium text-gray-500">Tue</div>
                <div class="bg-gray-100 py-2 text-center text-sm font-medium text-gray-500">Wed</div>
                <div class="bg-gray-100 py-2 text-center text-sm font-medium text-gray-500">Thu</div>
                <div class="bg-gray-100 py-2 text-center text-sm font-medium text-gray-500">Fri</div>
                <div class="bg-gray-100 py-2 text-center text-sm font-medium text-gray-500">Sat</div>
            </div>
            
            <!-- Calendar Days -->
            <div id="calendar-days" class="grid grid-cols-7 gap-px bg-gray-200">
                <!-- Days will be populated by JavaScript -->
            </div>
        </div>

        <!-- Monthly Summary -->
        <div class="mt-8 bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Your Monthly Summary</h2>
                    <p id="summary-month" class="text-sm text-gray-500">Attendance overview for {{ now()->format('F Y') }}</p>
                </div>
                <div>
                    <select id="summary-month-select" class="appearance-none bg-gray-100 border-0 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        @foreach(range(0, 11) as $month)
                            <option value="{{ $month }}" {{ $month == now()->month - 1 ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $month + 1)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select id="summary-year-select" class="appearance-none bg-gray-100 border-0 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm ml-2">
                        @foreach(range(now()->year - 2, now()->year + 2) as $year)
                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Present Days</p>
                    <p id="present-days" class="text-2xl font-bold text-blue-600">0</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Late Days</p>
                    <p id="late-days" class="text-2xl font-bold text-yellow-600">0</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Absent Days</p>
                    <p id="absent-days" class="text-2xl font-bold text-red-600">0</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Leave Days</p>
                    <p id="leave-days" class="text-2xl font-bold text-purple-600">0</p>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Total Working Days: <span id="total-days">0</span></span>
                    <span class="text-sm font-medium text-gray-700"><span id="attendance-percent">0</span>% Attendance</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="attendance-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Current date
            const today = new Date();
            let currentMonth = today.getMonth();
            let currentYear = today.getFullYear();
            
            // Initialize calendar
            renderCalendar(currentMonth, currentYear);
            fetchAttendanceData(currentMonth, currentYear);

            // Event listeners
            document.getElementById('prev-month').addEventListener('click', function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar(currentMonth, currentYear);
                fetchAttendanceData(currentMonth, currentYear);
                updateSelects(currentMonth, currentYear);
            });

            document.getElementById('next-month').addEventListener('click', function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar(currentMonth, currentYear);
                fetchAttendanceData(currentMonth, currentYear);
                updateSelects(currentMonth, currentYear);
            });

            document.getElementById('today-btn').addEventListener('click', function() {
                currentMonth = today.getMonth();
                currentYear = today.getFullYear();
                renderCalendar(currentMonth, currentYear);
                fetchAttendanceData(currentMonth, currentYear);
                updateSelects(currentMonth, currentYear);
            });

            document.getElementById('month-select').addEventListener('change', function() {
                currentMonth = parseInt(this.value);
                renderCalendar(currentMonth, currentYear);
                fetchAttendanceData(currentMonth, currentYear);
            });

            document.getElementById('year-select').addEventListener('change', function() {
                currentYear = parseInt(this.value);
                renderCalendar(currentMonth, currentYear);
                fetchAttendanceData(currentMonth, currentYear);
            });

            document.getElementById('summary-month-select').addEventListener('change', function() {
                const month = parseInt(this.value);
                const year = parseInt(document.getElementById('summary-year-select').value);
                fetchAttendanceData(month, year);
            });

            document.getElementById('summary-year-select').addEventListener('change', function() {
                const month = parseInt(document.getElementById('summary-month-select').value);
                const year = parseInt(this.value);
                fetchAttendanceData(month, year);
            });

            // Fetch attendance data from the server
            function fetchAttendanceData(month, year) {
                fetch(`/api/attendance/calendar-data?month=${month + 1}&year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        renderCalendar(month, year, data.attendance);
                        updateSummary(data.summary);
                    })
                    .catch(error => {
                        console.error('Error fetching attendance data:', error);
                    });
            }

            // Render calendar function
            function renderCalendar(month, year, attendanceData = {}) {
                const calendarDays = document.getElementById('calendar-days');
                calendarDays.innerHTML = '';
                
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = firstDay.getDay();
                
                // Previous month's days
                const prevMonthLastDay = new Date(year, month, 0).getDate();
                for (let i = 0; i < startingDay; i++) {
                    const day = document.createElement('div');
                    day.className = 'calendar-day bg-white p-2 h-24 overflow-y-auto';
                    const dayNumber = prevMonthLastDay - startingDay + i + 1;
                    day.innerHTML = `<span class="text-gray-400">${dayNumber}</span>`;
                    calendarDays.appendChild(day);
                }
                
                // Current month's days
                for (let i = 1; i <= daysInMonth; i++) {
                    const day = document.createElement('div');
                    day.className = 'calendar-day bg-white p-2 h-24 overflow-y-auto';
                    
                    const dateKey = `${year}-${month + 1}-${i}`;
                    const status = attendanceData[dateKey]?.status || '';
                    const dayOfWeek = new Date(year, month, i).getDay();
                    
                    // Add classes based on status
                    if (status === 'Present') day.classList.add('present');
                    else if (status === 'Absent') day.classList.add('absent');
                    else if (status === 'Late') day.classList.add('late');
                    else if (status === 'On_Leave') day.classList.add('leave');
                    else if (status === 'Holiday') day.classList.add('holiday');
                    else if (dayOfWeek === 0 || dayOfWeek === 6) day.classList.add('weekend');
                    
                    // Add today's date highlight
                    if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                        day.classList.add('ring-2', 'ring-blue-500');
                    }
                    
                    // Future dates styling
                    const isFuture = (year > today.getFullYear()) || 
                                    (year === today.getFullYear() && month > today.getMonth()) || 
                                    (year === today.getFullYear() && month === today.getMonth() && i > today.getDate());
                    
                    if (isFuture) {
                        day.classList.add('future');
                    }
                    
                    day.innerHTML = `
                        <div class="flex justify-between items-start">
                            <span class="font-medium">${i}</span>
                            ${status ? `<span class="text-xs px-1 py-0.5 rounded ${getStatusBadgeClass(status)}">${status.replace('_', ' ')}</span>` : ''}
                        </div>
                        ${status === 'Present' && attendanceData[dateKey]?.time_in ? `<div class="text-xs mt-1"><i class="fas fa-check-circle mr-1"></i> ${formatTime(attendanceData[dateKey].time_in)}</div>` : ''}
                        ${status === 'Late' && attendanceData[dateKey]?.time_in ? `<div class="text-xs mt-1"><i class="fas fa-clock mr-1"></i> ${formatTime(attendanceData[dateKey].time_in)}</div>` : ''}
                        ${status === 'On_Leave' ? '<div class="text-xs mt-1"><i class="fas fa-umbrella-beach mr-1"></i> On Leave</div>' : ''}
                        ${status === 'Holiday' ? '<div class="text-xs mt-1"><i class="fas fa-gift mr-1"></i> Public Holiday</div>' : ''}
                    `;
                    
                    calendarDays.appendChild(day);
                }
                
                // Next month's days
                const daysToAdd = 42 - (daysInMonth + startingDay); // 6 rows x 7 days
                for (let i = 1; i <= daysToAdd; i++) {
                    const day = document.createElement('div');
                    day.className = 'calendar-day bg-white p-2 h-24 overflow-y-auto';
                    day.innerHTML = `<span class="text-gray-400">${i}</span>`;
                    calendarDays.appendChild(day);
                }
                
                // Update month/year display
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                document.getElementById('current-month').textContent = `${monthNames[month]} ${year}`;
                document.getElementById('summary-month').textContent = `Attendance overview for ${monthNames[month]} ${year}`;
            }
            
            function getStatusBadgeClass(status) {
                switch(status) {
                    case 'Present': return 'bg-blue-100 text-blue-800';
                    case 'Absent': return 'bg-red-100 text-red-800';
                    case 'Late': return 'bg-yellow-100 text-yellow-800';
                    case 'On_Leave': return 'bg-purple-100 text-purple-800';
                    case 'Holiday': return 'bg-green-100 text-green-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            }
            
            function formatTime(timeString) {
                if (!timeString) return '';
                const [hours, minutes] = timeString.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                return `${displayHour}:${minutes} ${ampm}`;
            }
            
            function updateSummary(data) {
                document.getElementById('present-days').textContent = data.present_days || 0;
                document.getElementById('late-days').textContent = data.late_days || 0;
                document.getElementById('absent-days').textContent = data.absent_days || 0;
                document.getElementById('leave-days').textContent = data.on_leave_days || 0;
                document.getElementById('total-days').textContent = data.total_working_days || 0;
                
                const attendancePercent = data.total_working_days > 0 
                    ? Math.round(((data.present_days + data.late_days) / data.total_working_days) * 100)
                    : 0;
                
                document.getElementById('attendance-percent').textContent = attendancePercent;
                document.getElementById('attendance-bar').style.width = `${attendancePercent}%`;
            }
            
            function updateSelects(month, year) {
                document.getElementById('month-select').value = month;
                document.getElementById('year-select').value = year;
                document.getElementById('summary-month-select').value = month;
                document.getElementById('summary-year-select').value = year;
            }
        });
    </script>
</body>
</html>