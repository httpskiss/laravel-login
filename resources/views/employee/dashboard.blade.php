<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIPSU HRMIS - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .biometric-placeholder {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .attendance-card {
            transition: all 0.3s ease;
        }
        
        .attendance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .status-present {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-absent {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-late {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-leave {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .qr-scanner {
            position: relative;
            width: 300px;
            height: 300px;
            margin: 0 auto;
            border: 3px dashed #3b82f6;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .qr-scanner video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .qr-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.2rem;
        }
        
        /* Custom animation for notifications */
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .pulse-animation {
            animation: pulse 1.5s infinite;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Glass morphism effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Mobile menu button -->
        <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-800 text-white rounded-lg shadow-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-gradient-to-b from-blue-800 to-blue-900 text-white fixed h-full overflow-y-auto shadow-xl">
            <div class="p-4 flex items-center justify-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white p-2 rounded-lg">
                        <img src="https://via.placeholder.com/40" alt="BIPSU Logo" class="h-8 w-auto object-contain">
                    </div>
                    <span class="logo-text text-xl font-bold whitespace-nowrap">eHRMIS</span>
                </div>
            </div>
            
            <nav class="mt-6">
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-home mr-3 text-blue-300"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-users mr-3 text-blue-300"></i>
                        <span class="nav-text">Employees</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-calendar-alt mr-3 text-blue-300"></i>
                        <span class="nav-text">Attendance</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-calendar-minus mr-3 text-blue-300"></i>
                        <span class="nav-text">Leave</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-plane mr-3 text-blue-300"></i>
                        <span class="nav-text">Travel</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-money-bill-wave mr-3 text-blue-300"></i>
                        <span class="nav-text">Payroll</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-chart-line mr-3 text-blue-300"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <i class="fas fa-cog mr-3 text-blue-300"></i>
                        <span class="nav-text">Settings</span>
                    </a>
                </div>
            </nav>
            
            <div class="p-4 mt-auto">
                <button id="toggleSidebar" class="w-full flex items-center justify-center py-2 bg-blue-700 rounded-lg hover:bg-blue-600 transition-all duration-200">
                    <i class="fas fa-chevron-left"></i>
                    <span class="nav-text ml-2">Collapse</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto">
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notification Dropdown -->
                        <div class="relative">
                            <button id="notificationBtn" class="relative p-2 text-gray-600 hover:text-blue-600">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notificationCounter" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">3</span>
                            </button>
                            
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <h3 class="font-medium text-gray-800">Notifications</h3>
                                </div>
                                <div class="max-h-60 overflow-y-auto">
                                    <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-100">
                                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-user-plus text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-800">New employee registered</p>
                                            <p class="text-xs text-gray-500">2 minutes ago</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-100">
                                        <div class="bg-green-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-800">Leave request approved</p>
                                            <p class="text-xs text-gray-500">1 hour ago</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-100">
                                        <div class="bg-yellow-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-800">Late attendance detected</p>
                                            <p class="text-xs text-gray-500">3 hours ago</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="px-4 py-2 border-t border-gray-200 text-center">
                                    <a href="#" class="text-sm text-blue-600 hover:underline">View all notifications</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer">
                                <img src="https://via.placeholder.com/40" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                                <span class="ml-2 text-gray-700">John Doe</span>
                                <i class="fas fa-chevron-down ml-1 text-gray-600 text-xs"></i>
                            </div>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden border border-gray-200">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
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
                <!-- Dashboard Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sidebar state
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            
            // Toggle sidebar function
            const toggleSidebar = () => {
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
                sidebar.classList.toggle('mobile-open');
                
                if (sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('collapsed');
                }
            };
            
            // Initialize sidebar state from localStorage
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
            mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
            
            // Close mobile sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && 
                    !e.target.closest('#sidebar') && 
                    !e.target.closest('#mobileMenuBtn') && 
                    sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                }
            });
            
            // Handle window resize
            const handleResize = () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-open');
                }
            };
            
            window.addEventListener('resize', handleResize);
            
            // Notification dropdown
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationCounter = document.getElementById('notificationCounter');
            
            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('hidden');
                    
                    // Mark notifications as read
                    notificationCounter.classList.add('hidden');
                });
                
                // Close when clicking outside
                document.addEventListener('click', function() {
                    notificationDropdown.classList.add('hidden');
                });
                
                // Prevent dropdown from closing when clicking inside
                notificationDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Profile dropdown
            const profileDropdown = document.querySelector('.dropdown');
            const profileMenu = document.querySelector('.dropdown-menu');
            
            if (profileDropdown && profileMenu) {
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('hidden');
                });
                
                // Close when clicking outside
                document.addEventListener('click', function() {
                    profileMenu.classList.add('hidden');
                });
                
                // Prevent dropdown from closing when clicking inside
                profileMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Show a sample success message (for demo)
            setTimeout(() => {
                document.getElementById('successMessage').classList.remove('hidden');
                
                setTimeout(() => {
                    document.getElementById('successMessage').classList.add('hidden');
                }, 5000);
            }, 1000);
            
            // Initialize charts
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceChart = new Chart(attendanceCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Present',
                        data: [850, 890, 910, 920, 950, 980, 1000],
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }, {
                        label: 'Absent',
                        data: [50, 40, 35, 30, 25, 20, 15],
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }, {
                        label: 'Late',
                        data: [100, 70, 55, 50, 45, 40, 35],
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            const leaveCtx = document.getElementById('leaveChart').getContext('2d');
            const leaveChart = new Chart(leaveCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Vacation', 'Sick', 'Personal', 'Maternity', 'Paternity'],
                    datasets: [{
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ec4899',
                            '#8b5cf6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                    }
                }
            });
            
            // Simulate notification pulse animation
            if (notificationCounter) {
                notificationCounter.classList.remove('hidden');
                notificationCounter.classList.add('pulse-animation');
                
                // Stop animation after 3 pulses
                setTimeout(() => {
                    notificationCounter.classList.remove('pulse-animation');
                }, 4500);
            }
        });
    </script>
</body>
</html>