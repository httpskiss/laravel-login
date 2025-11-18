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
                <a href="{{ route('employees.dashboard') }}" class="nav-item">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="{{ route('employees.attendance') }}" class="nav-item">
                    <i class="fas fa-calendar-alt nav-icon"></i>
                    <span class="nav-text">Attendance</span>
                </a>
                <a href="{{ route('employees.leaves') }}" class="nav-item">
                    <i class="fas fa-calendar-minus nav-icon"></i>
                    <span class="nav-text">Leave</span>
                </a>
                <a href="{{ route('employees.travel') }}" class="nav-item">
                    <i class="fas fa-plane nav-icon"></i>
                    <span class="nav-text">Travel</span>
                </a>
                <a href="{{ route('employees.pds') }}" class="nav-item">
                    <i class="fas fa-id-card nav-icon"></i>
                    <span class="nav-text">PDS</span>
                </a>
                <a href="{{ route('employees.complaints.index') }}" class="nav-item">
                    <i class="fas fa-exclamation-circle nav-icon"></i>
                    <span class="nav-text">HR Complaints</span>
                </a>
                <a href="{{ route('employees.saln') }}" class="nav-item">
                    <i class="fas fa-file-contract nav-icon"></i>
                    <span class="nav-text">SALN</span>
                </a>
                <a href="{{ route('employees.payroll') }}" class="nav-item">
                    <i class="fas fa-money-bill-wave nav-icon"></i>
                    <span class="nav-text">Payroll</span>
                </a>
                <a href="{{ route('employees.reports') }}" class="nav-item">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <a href="{{ route('employees.settings') }}" class="nav-item">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </nav>
            
            <!-- Collapse Button - Updated with new styling -->
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
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="w-8 h-8 rounded-full">
                                <span class="ml-2 text-white">
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