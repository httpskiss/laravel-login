<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiPSU HRMIS - @yield('title')</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
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
            min-width: 40px; /* Same width as collapsed sidebar minus padding */
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-logo {
            min-width: 40px;
            padding: 0.5rem;
        }

            .sidebar-logo-container {
            width: 180px; /* Width when sidebar is expanded */
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-logo-container {
            width: 40px; /* Width when sidebar is collapsed */
        }

        /* Ensure smooth transition */
        .sidebar-logo-container img {
            transition: all 0.3s ease;
        }

        /* Adjust logo size in collapsed state */
        .sidebar.collapsed .sidebar-logo-container img {
            height: 36px; /* Slightly smaller in collapsed state */
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


    /* Custom styles for attendance system */
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
                    <img src="{{ asset('assets/images/uni_logo.png') }}" alt="BIPSU Logo" 
                        class="h-12 w-auto object-contain max-w-full transition-all duration-300">
                    <span class="logo-text text-xl font-bold whitespace-nowrap">eHRMIS</span>
                </div>
            </div>
            <nav class="mt-6">
                <div class="px-4 py-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-home mr-3"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
                 <div class="px-4 py-2">
                    <a href="{{ route('admin.employees') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-users mr-3"></i>
                        <span class="nav-text">Employees</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.attendance') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        <span class="nav-text">Attendance</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.leaves') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-calendar-minus mr-3"></i>
                        <span class="nav-text">Leave</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.travel') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plane mr-3"></i>
                        <span class="nav-text">Travel</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.payroll') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-money-bill-wave mr-3"></i>
                        <span class="nav-text">Payroll</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.pds') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-id-card mr-3"></i>
                        <span class="nav-text">PDS</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.saln') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-file-contract mr-3"></i>
                        <span class="nav-text">SALN</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.reports') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-chart-line mr-3"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">
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
                    <h1 class="text-2xl font-semibold text-gray-800">@yield('title')</h1>
                    
                    <div class="flex items-center space-x-4">
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="w-8 h-8 rounded-full">
                                <span class="ml-2 text-gray-700">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </span>
                                <i class="fas fa-chevron-down ml-1 text-gray-600 text-xs"></i>
                            </div>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">Profile</a>
                                <a href="{{ route('profile.settings') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">Settings</a>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    @push('scripts')
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
        });

        // Poll for new notifications every 60 seconds (fallback if Pusher not available)
        setInterval(() => {
            fetch('/notifications/count')
                .then(response => response.json())
                .then(data => {
                    updateNotificationCount(data.count);
                });
        }, 60000);
        
        function updateNotificationCount(count) {
            const counter = document.querySelector('.notification-counter');
            if (counter) {
                if (count > 0) {
                    counter.textContent = count;
                    counter.classList.remove('hidden');
                } else {
                    counter.classList.add('hidden');
                }
            }
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white ${
                type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } z-50`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
       
    </script>
    @endpush
    @stack('scripts')
</body>
</html>
