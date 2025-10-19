<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIPSU HRMIS - Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
            width: 250px;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .logo-text {
            display: none;
        }
        
        .main-content {
            transition: all 0.3s ease;
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        .sidebar.collapsed ~ .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar.collapsed ~ .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }
        
        /* Prevent logo from squishing */
        .sidebar-logo {
            min-width: 40px;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-logo {
            min-width: 40px;
            padding: 0.5rem;
        }
        
        .sidebar-logo-container {
            width: 180px;
            overflow: hidden;
        }
        
        .sidebar.collapsed .sidebar-logo-container {
            width: 40px;
        }
        
        /* Ensure smooth transition */
        .sidebar-logo-container img {
            transition: all 0.3s ease;
        }
        
        /* Adjust logo size in collapsed state */
        .sidebar.collapsed .sidebar-logo-container img {
            height: 36px;
            width: auto;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sidebar-logo-container {
                width: 180px;
            }
            
            .sidebar.mobile-open .sidebar-logo-container {
                width: 180px;
            }
        }
        
        /* Custom styles for attendance module */
        .biometric-container {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .fingerprint-scan {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }
            50% {
                transform: scale(1.05);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 0.7;
            }
        }
        
        .attendance-status {
            transition: all 0.3s ease;
        }
        
        .time-in-out-btn {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .time-in-out-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .attendance-record:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Mobile menu button (hidden on desktop) -->
        <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-800 text-white rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        <div id="sidebar" class="sidebar bg-blue-800 text-white fixed h-full overflow-y-auto">
            <div class="p-4 flex items-center">
                <div class="flex items-center space-x-3">
                    <img src="assets/images/uni_logo.png" alt="BIPSU Logo"
                         class="h-12 w-auto object-contain max-w-full transition-all duration-300">
                    <span class="logo-text text-xl font-bold whitespace-nowrap">eHRMIS</span>
                </div>
            </div>
            <nav class="mt-6">
                <div class="px-4 py-2">
                    <a href="dashboard.html" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="employees.html" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-users mr-3"></i>
                        <span class="nav-text">Employees</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="attendance.html" class="flex items-center px-4 py-3 rounded-lg bg-blue-900">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        <span class="nav-text">Attendance</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="payroll.html" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-money-bill-wave mr-3"></i>
                        <span class="nav-text">Payroll</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="leave.html" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-calendar-minus mr-3"></i>
                        <span class="nav-text">Leave</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="travel.html" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plane mr-3"></i>
                        <span class="nav-text">Travel</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="reports.html" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-chart-line mr-3"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="settings.html" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-cog mr-3"></i>
                        <span class="nav-text">Settings</span>
                    </a>
                </div>
            </nav>
            <div class="p-4">
                <button id="toggleSidebar" class="w-full flex items-center justify-center py-2 bg-blue-700 rounded-lg">
                    <i class="fas fa-chevron-left"></i>
                    <span class="nav-text ml-2">Collapse</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-2xl font-semibold text-gray-800">Attendance Management</h1>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="p-2 text-gray-600 hover:text-blue-600 relative">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="notification-counter absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">3</span>
                            </button>
                        </div>
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile" class="w-8 h-8 rounded-full">
                                <span class="ml-2 text-gray-700">
                                    John Doe
                                </span>
                                <i class="fas fa-chevron-down ml-1 text-gray-600 text-xs"></i>
                            </div>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <a href="profile.html" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="settings.html" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                <div class="border-t border-gray-200"></div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-6">
                <!-- Success/Error Messages -->
                <div id="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 hidden" role="alert">
                    <p id="successMessageText"></p>
                </div>
                <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 hidden" role="alert">
                    <p id="errorMessageText"></p>
                </div>

                <!-- Attendance Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Today's Date</p>
                                <h3 class="text-2xl font-bold" id="currentDate">Loading...</h3>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-calendar-day text-blue-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Current Time</p>
                                <h3 class="text-2xl font-bold" id="currentTime">Loading...</h3>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-clock text-green-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Today's Status</p>
                                <h3 class="text-2xl font-bold" id="attendanceStatus">-</h3>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-user-check text-yellow-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Monthly Attendance</p>
                                <h3 class="text-2xl font-bold">18/22</h3>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-calendar-check text-purple-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biometric and Manual Attendance Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Biometric Attendance -->
                    <div class="lg:col-span-1">
                        <div class="biometric-container text-white rounded-lg shadow p-6 h-full">
                            <h2 class="text-xl font-bold mb-4">Biometric Attendance</h2>
                            <div class="flex flex-col items-center justify-center py-8">
                                <div class="relative mb-6">
                                    <div class="fingerprint-scan bg-blue-600 rounded-full p-8">
                                        <i class="fas fa-fingerprint text-5xl"></i>
                                    </div>
                                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-white text-blue-800 px-3 py-1 rounded-full text-xs font-semibold shadow">
                                        Ready to Scan
                                    </div>
                                </div>
                                <p class="text-center mb-6">Place your finger on the scanner to record your attendance</p>
                                <button id="scanFingerprintBtn" class="bg-white text-blue-800 font-semibold py-2 px-6 rounded-full hover:bg-blue-100 transition-colors">
                                    Start Scanning
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Attendance -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6 h-full">
                            <h2 class="text-xl font-bold mb-4">Manual Attendance</h2>
                            <div class="flex flex-col md:flex-row gap-4 mb-6">
                                <div class="flex-1">
                                    <label for="attendanceType" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select id="attendanceType" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="time_in">Time In</option>
                                        <option value="time_out">Time Out</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label for="attendanceDate" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" id="attendanceDate" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="flex-1">
                                    <label for="attendanceTime" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                    <input type="time" id="attendanceTime" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-1">
                                    <label for="attendanceRemarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                    <textarea id="attendanceRemarks" rows="2" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Optional remarks"></textarea>
                                </div>
                                <div class="flex items-end">
                                    <button id="submitManualAttendanceBtn" class="bg-blue-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-blue-700 transition-colors h-12">
                                        Submit Attendance
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <button id="timeInBtn" class="time-in-out-btn bg-green-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-green-700 flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i> Time In
                    </button>
                    <button id="timeOutBtn" class="time-in-out-btn bg-red-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-red-700 flex items-center justify-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                    </button>
                    <button id="breakStartBtn" class="time-in-out-btn bg-yellow-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-yellow-700 flex items-center justify-center">
                        <i class="fas fa-coffee mr-2"></i> Start Break
                    </button>
                    <button id="breakEndBtn" class="time-in-out-btn bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-purple-700 flex items-center justify-center">
                        <i class="fas fa-coffee mr-2"></i> End Break
                    </button>
                </div>

                <!-- Attendance Records -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-bold">Recent Attendance Records</h2>
                        <div class="flex space-x-2">
                            <select id="recordFilter" class="border border-gray-300 rounded-md py-1 px-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="all">All Records</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                            <button class="bg-blue-600 text-white text-sm font-semibold py-1 px-3 rounded-md hover:bg-blue-700">
                                <i class="fas fa-download mr-1"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceRecords" class="bg-white divide-y divide-gray-200">
                                <!-- Records will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            Showing <span id="startRecord">1</span> to <span id="endRecord">10</span> of <span id="totalRecords">24</span> records
                        </div>
                        <div class="flex space-x-2">
                            <button id="prevPageBtn" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50" disabled>
                                Previous
                            </button>
                            <button id="nextPageBtn" class="px-3 py-1 border border-gray-300 rounded-md text-sm">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle profile dropdown toggle
            const dropdown = document.querySelector('.dropdown');
            if (dropdown) {
                dropdown.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown-menu')) return;
                    this.querySelector('.dropdown-menu').classList.toggle('hidden');
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    const openDropdown = document.querySelector('.dropdown-menu:not(.hidden)');
                    if (openDropdown) openDropdown.classList.add('hidden');
                }
            });

            // Toggle Sidebar Functionality
            const toggleSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = document.getElementById('toggleSidebar');

                if (!sidebar || !toggleBtn) return;

                sidebar.classList.toggle('collapsed');

                const icon = toggleBtn.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                    toggleBtn.querySelector('.nav-text').textContent = 'Expand';
                } else {
                    icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
                    toggleBtn.querySelector('.nav-text').textContent = 'Collapse';
                }

                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            };

            // Mobile sidebar toggle
            const toggleMobileSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('mobile-open');

                // On mobile, we don't want the collapsed state when opening
                if (sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('collapsed');
                }
            };

            // Initialize sidebar state from localStorage
            const initializeSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = document.getElementById('toggleSidebar');
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');

                if (!sidebar || !toggleBtn) return;

                // Only apply collapsed state on desktop
                if (window.innerWidth > 768) {
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        const icon = toggleBtn.querySelector('i');
                        icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                        toggleBtn.querySelector('.nav-text').textContent = 'Expand';
                    }
                }

                // Set up event listeners
                toggleBtn.addEventListener('click', toggleSidebar);

                if (mobileMenuBtn) {
                    mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
                }

                // Close mobile sidebar when clicking outside
                document.addEventListener('click', function(e) {
                    const sidebar = document.getElementById('sidebar');
                    const mobileMenuBtn = document.getElementById('mobileMenuBtn');

                    if (window.innerWidth <= 768 && 
                        !e.target.closest('#sidebar') && 
                        !e.target.closest('#mobileMenuBtn') &&
                        sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    }
                });
            };

            // Handle window resize
            const handleResize = () => {
                const sidebar = document.getElementById('sidebar');

                if (window.innerWidth > 768) {
                    // Desktop - remove mobile-open class if it exists
                    sidebar.classList.remove('mobile-open');
                } else {
                    // Mobile - ensure sidebar is hidden by default
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('collapsed');
                    }
                }
            };

            // Initialize on page load
            initializeSidebar();

            // Add resize event listener
            window.addEventListener('resize', handleResize);

            // Update current date and time
            function updateDateTime() {
                const now = new Date();
                const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
                
                document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', dateOptions);
                document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', timeOptions);
            }

            // Update immediately and then every second
            updateDateTime();
            setInterval(updateDateTime, 1000);

            // Sample attendance data
            const attendanceData = [
                { date: '2023-06-01', timeIn: '08:00 AM', timeOut: '05:00 PM', status: 'Present', hours: '9.0', remarks: '' },
                { date: '2023-06-02', timeIn: '08:15 AM', timeOut: '05:30 PM', status: 'Present', hours: '9.25', remarks: 'Late arrival' },
                { date: '2023-06-03', timeIn: '08:05 AM', timeOut: '04:45 PM', status: 'Present', hours: '8.67', remarks: 'Early departure' },
                { date: '2023-06-04', timeIn: '-', timeOut: '-', status: 'Weekend', hours: '0.0', remarks: '' },
                { date: '2023-06-05', timeIn: '08:00 AM', timeOut: '05:00 PM', status: 'Present', hours: '9.0', remarks: '' },
                { date: '2023-06-06', timeIn: '08:00 AM', timeOut: '05:00 PM', status: 'Present', hours: '9.0', remarks: '' },
                { date: '2023-06-07', timeIn: '08:00 AM', timeOut: '05:00 PM', status: 'Present', hours: '9.0', remarks: '' },
                { date: '2023-06-08', timeIn: '08:00 AM', timeOut: '05:00 PM', status: 'Present', hours: '9.0', remarks: '' },
                { date: '2023-06-09', timeIn: '08:00 AM', timeOut: '05:00 PM', status: 'Present', hours: '9.0', remarks: '' },
                { date: '2023-06-10', timeIn: '-', timeOut: '-', status: 'Weekend', hours: '0.0', remarks: '' },
            ];

            // Load attendance records
            function loadAttendanceRecords(filter = 'all') {
                const recordsContainer = document.getElementById('attendanceRecords');
                recordsContainer.innerHTML = '';

                let filteredData = [...attendanceData];
                const today = new Date();
                
                if (filter === 'today') {
                    const todayStr = today.toISOString().split('T')[0];
                    filteredData = attendanceData.filter(record => record.date === todayStr);
                } else if (filter === 'week') {
                    const oneWeekAgo = new Date(today);
                    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
                    filteredData = attendanceData.filter(record => {
                        const recordDate = new Date(record.date);
                        return recordDate >= oneWeekAgo && recordDate <= today;
                    });
                } else if (filter === 'month') {
                    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    filteredData = attendanceData.filter(record => {
                        const recordDate = new Date(record.date);
                        return recordDate >= firstDayOfMonth && recordDate <= today;
                    });
                }

                filteredData.forEach(record => {
                    const row = document.createElement('tr');
                    row.className = 'attendance-record hover:bg-gray-50 transition-colors';
                    
                    // Determine status color
                    let statusColor = 'text-gray-600';
                    if (record.status === 'Present') statusColor = 'text-green-600';
                    else if (record.status === 'Late') statusColor = 'text-yellow-600';
                    else if (record.status === 'Absent') statusColor = 'text-red-600';
                    
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${record.date}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${record.timeIn}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${record.timeOut}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm ${statusColor} font-semibold">${record.status}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${record.hours}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${record.remarks}</td>
                    `;
                    
                    recordsContainer.appendChild(row);
                });

                // Update pagination info
                document.getElementById('startRecord').textContent = '1';
                document.getElementById('endRecord').textContent = filteredData.length;
                document.getElementById('totalRecords').textContent = filteredData.length;
            }

            // Initialize attendance records
            loadAttendanceRecords();

            // Filter change handler
            document.getElementById('recordFilter').addEventListener('change', function() {
                loadAttendanceRecords(this.value);
            });

            // Biometric scan simulation
            document.getElementById('scanFingerprintBtn').addEventListener('click', function() {
                const fingerprintScan = document.querySelector('.fingerprint-scan');
                const statusText = document.querySelector('.fingerprint-scan + div');
                
                // Show scanning state
                this.disabled = true;
                this.textContent = 'Scanning...';
                fingerprintScan.classList.add('animate-pulse');
                statusText.textContent = 'Scanning...';
                statusText.classList.remove('bg-white', 'text-blue-800');
                statusText.classList.add('bg-blue-200', 'text-blue-900');
                
                // Simulate scan after 3 seconds
                setTimeout(() => {
                    fingerprintScan.classList.remove('animate-pulse');
                    this.disabled = false;
                    this.textContent = 'Scan Complete';
                    
                    // Random success/failure
                    if (Math.random() > 0.2) { // 80% success rate
                        statusText.textContent = 'Scan Successful';
                        statusText.classList.remove('bg-blue-200', 'text-blue-900');
                        statusText.classList.add('bg-green-100', 'text-green-800');
                        
                        // Update attendance status
                        document.getElementById('attendanceStatus').textContent = 'Time In: ' + new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        // Show success message
                        showMessage('Fingerprint scanned successfully. Attendance recorded.', 'success');
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            this.textContent = 'Start Scanning';
                            statusText.textContent = 'Ready to Scan';
                            statusText.classList.remove('bg-green-100', 'text-green-800');
                            statusText.classList.add('bg-white', 'text-blue-800');
                        }, 2000);
                    } else {
                        statusText.textContent = 'Scan Failed';
                        statusText.classList.remove('bg-blue-200', 'text-blue-900');
                        statusText.classList.add('bg-red-100', 'text-red-800');
                        this.textContent = 'Try Again';
                        
                        // Show error message
                        showMessage('Fingerprint scan failed. Please try again.', 'error');
                    }
                }, 3000);
            });

            // Quick action buttons
            document.getElementById('timeInBtn').addEventListener('click', function() {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                document.getElementById('attendanceStatus').textContent = 'Time In: ' + timeString;
                showMessage('Time In recorded at ' + timeString, 'success');
            });

            document.getElementById('timeOutBtn').addEventListener('click', function() {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                document.getElementById('attendanceStatus').textContent = 'Time Out: ' + timeString;
                showMessage('Time Out recorded at ' + timeString, 'success');
            });

            document.getElementById('breakStartBtn').addEventListener('click', function() {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                showMessage('Break started at ' + timeString, 'info');
            });

            document.getElementById('breakEndBtn').addEventListener('click', function() {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                showMessage('Break ended at ' + timeString, 'info');
            });

            // Manual attendance submission
            document.getElementById('submitManualAttendanceBtn').addEventListener('click', function() {
                const type = document.getElementById('attendanceType').value;
                const date = document.getElementById('attendanceDate').value;
                const time = document.getElementById('attendanceTime').value;
                const remarks = document.getElementById('attendanceRemarks').value;
                
                if (!date || !time) {
                    showMessage('Please select both date and time', 'error');
                    return;
                }
                
                const action = type === 'time_in' ? 'Time In' : 'Time Out';
                const timeString = new Date(`${date}T${time}`).toLocaleString([], {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit'});
                
                if (type === 'time_in') {
                    document.getElementById('attendanceStatus').textContent = 'Time In: ' + timeString.split(', ')[1];
                } else {
                    document.getElementById('attendanceStatus').textContent = 'Time Out: ' + timeString.split(', ')[1];
                }
                
                showMessage(`Manual ${action} recorded for ${timeString}`, 'success');
                
                // Reset form
                document.getElementById('attendanceRemarks').value = '';
            });

            // Set default date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('attendanceDate').value = today;
            
            // Set default time to now
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('attendanceTime').value = `${hours}:${minutes}`;

            // Show message function
            function showMessage(message, type) {
                const successDiv = document.getElementById('successMessage');
                const errorDiv = document.getElementById('errorMessage');
                
                if (type === 'success') {
                    document.getElementById('successMessageText').textContent = message;
                    successDiv.classList.remove('hidden');
                    errorDiv.classList.add('hidden');
                    
                    // Hide after 5 seconds
                    setTimeout(() => {
                        successDiv.classList.add('hidden');
                    }, 5000);
                } else {
                    document.getElementById('errorMessageText').textContent = message;
                    errorDiv.classList.remove('hidden');
                    successDiv.classList.add('hidden');
                    
                    // Hide after 5 seconds
                    setTimeout(() => {
                        errorDiv.classList.add('hidden');
                    }, 5000);
                }
            }
        });
    </script>
</body>
</html>