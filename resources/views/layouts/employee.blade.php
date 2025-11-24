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
            z-index: 40;
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
                width: 250px;
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
        }
        
        /* Logo Styling */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem 0.5rem;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }
        
        .sidebar-logo {
            transition: all 0.3s ease;
            max-width: 100%;
            height: auto;
        }
        
        .sidebar.collapsed .logo-container {
            padding: 1rem 0.25rem;
        }
        
        .sidebar.collapsed .sidebar-logo {
            height: 40px;
            width: auto;
        }
        
        /* Navigation Items */
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin: 0.25rem 0.5rem;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            color: white;
            text-decoration: none;
        }
        
        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-item.active {
            background-color: rgb(255, 255, 255);
            color: #1e40af;
        }
        
        .nav-icon {
            min-width: 1.5rem;
            text-align: center;
            margin-right: 0.75rem;
        }
        
        .sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        /* Collapse Button */
        .collapse-btn {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0.75rem 1rem;
            margin: 1rem 0.5rem;
            border-radius: 0.75rem;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            min-height: 44px;
            width: calc(100% - 1rem);
            box-sizing: border-box;
            cursor: pointer;
        }

        .collapse-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .collapse-btn:active {
            transform: translateY(0);
        }

        .sidebar.collapsed .collapse-btn {
            justify-content: center;
            padding: 0.75rem;
            border-radius: 0.5rem;
            width: calc(100% - 1rem);
        }

        .collapse-btn i {
            transition: transform 0.3s ease;
            font-size: 0.9rem;
        }

        .sidebar.collapsed .collapse-btn i {
            transform: rotate(180deg);
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 45;
        }
        
        @media (max-width: 768px) {
            .sidebar-overlay.mobile-open {
                display: block;
            }
        }

        /* Profile Dropdown Styles */
        .dropdown-menu {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
        }

        .dropdown-menu a {
            transition: all 0.2s ease;
        }

        .dropdown-menu a:hover {
            background-color: #eff6ff;
            color: #1e40af;
        }

        .dropdown-menu form button:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }

        /* Custom styles for employee features */
        .attendance-card {
            transition: all 0.3s ease;
        }

        .leave-balance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Mobile menu button (hidden on desktop) -->
        <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-800 text-white rounded-lg shadow-md">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Mobile overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-blue-950 text-white fixed h-full overflow-y-auto">
            <!-- Logo Section - Centered -->
            <div class="logo-container">
                <img src="{{ asset('assets/images/one_bipsu.png') }}" alt="BIPSU Logo" 
                    class="sidebar-logo h-12 w-auto object-contain">
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-2">
                <a href="{{ route('employees.dashboard') }}" class="nav-item {{ request()->routeIs('employees.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="{{ route('employees.attendance') }}" class="nav-item {{ request()->routeIs('employees.attendance*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt nav-icon"></i>
                    <span class="nav-text">Attendance</span>
                </a>
                <a href="{{ route('employees.leaves') }}" class="nav-item {{ request()->routeIs('employees.leaves*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-minus nav-icon"></i>
                    <span class="nav-text">Leave</span>
                </a>
                <a href="{{ route('employees.travel') }}" class="nav-item {{ request()->routeIs('employees.travel*') ? 'active' : '' }}">
                    <i class="fas fa-plane nav-icon"></i>
                    <span class="nav-text">Travel</span>
                </a>
                <a href="{{ route('employees.pds') }}" class="nav-item {{ request()->routeIs('employees.pds') ? 'active' : '' }}">
                    <i class="fas fa-id-card nav-icon"></i>
                    <span class="nav-text">PDS</span>
                </a>
                <a href="{{ route('employees.saln') }}" class="nav-item {{ request()->routeIs('employees.saln') ? 'active' : '' }}">
                    <i class="fas fa-file-contract nav-icon"></i>
                    <span class="nav-text">SALN</span>
                </a>
                <a href="{{ route('employees.payroll') }}" class="nav-item {{ request()->routeIs('employees.payroll') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave nav-icon"></i>
                    <span class="nav-text">Payroll</span>
                </a>
                <a href="{{ route('employees.reports') }}" class="nav-item {{ request()->routeIs('employees.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <a href="{{ route('employees.settings') }}" class="nav-item {{ request()->routeIs('employees.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </nav>
            
            <!-- Collapse Button -->
            <div class="mt-auto border-t border-gray-700 pt-2">
                <button id="toggleSidebar" class="collapse-btn w-full">
                    <i class="fas fa-chevron-left nav-icon"></i>
                    <span class="nav-text ml-3">Collapse</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto">
            <header class="bg-blue-950 shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-2xl font-semibold text-white">@yield('title')</h1>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Profile Dropdown -->
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer text-white hover:text-blue-200 transition duration-150">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="w-8 h-8 rounded-full border-2 border-white">
                                <span class="ml-2 font-medium">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </span>
                                <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"></i>
                            </div>
                            <div class="dropdown-menu absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-2 z-50 hidden border border-gray-200">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->role }}</p>
                                </div>
                                
                                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition duration-150">
                                    <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                    My Profile
                                </a>
                                <a href="{{ route('profile.settings') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition duration-150">
                                    <i class="fas fa-cog mr-3 text-gray-400"></i>
                                    Profile Settings
                                </a>
                                
                                <div class="border-t border-gray-100 my-1"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition duration-150">
                                        <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <div>
                                <p class="text-green-700 font-medium">Success</p>
                                <p class="text-green-600 text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Error Message -->
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <div>
                                <p class="text-red-700 font-medium">Error</p>
                                <p class="text-red-600 text-sm">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                            <div>
                                <p class="text-red-700 font-medium">Please fix the following errors:</p>
                                <ul class="text-red-600 text-sm list-disc list-inside mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle profile dropdown toggle with animation
            const dropdown = document.querySelector('.dropdown');
            if (dropdown) {
                const dropdownToggle = dropdown.querySelector('.flex.items-center');
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                const chevron = dropdown.querySelector('.fa-chevron-down');

                dropdownToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                    chevron.classList.toggle('rotate-180');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    }
                });

                // Prevent dropdown from closing when clicking inside it
                dropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

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
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('mobile-open');
                
                // On mobile, we don't want the collapsed state when opening
                if (sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('collapsed');
                }
            };
            
            // Close mobile sidebar when clicking overlay
            const closeMobileSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('mobile-open');
            };
            
            // Initialize sidebar state from localStorage
            const initializeSidebar = () => {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = document.getElementById('toggleSidebar');
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                const overlay = document.getElementById('sidebarOverlay');
                
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
                
                if (overlay) {
                    overlay.addEventListener('click', closeMobileSidebar);
                }
            };
            
            // Handle window resize
            const handleResize = () => {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                if (window.innerWidth > 768) {
                    // Desktop - remove mobile-open class if it exists
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('mobile-open');
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

        // Enhanced toast function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white font-medium flex items-center space-x-3 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } z-50 transform transition-all duration-300 translate-x-full`;
            
            const icon = type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
            toast.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 10);
            
            // Animate out and remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
    @endpush
    @stack('scripts')
</body>
</html>