<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniHive HR Hub | Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#4f46e5',
                            600: '#4338ca',
                        },
                        secondary: {
                            500: '#10b981',
                            600: '#059669',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col">
    <custom-navbar></custom-navbar>
    
    <div class="flex flex-1">
        <custom-sidebar active="dashboard"></custom-sidebar>
        
        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
                <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                    <i data-feather="moon" class="hidden dark:block text-gray-300"></i>
                    <i data-feather="sun" class="dark:hidden text-yellow-500"></i>
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Total Employees</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">124</h3>
                        </div>
                        <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-500">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">On Leave Today</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">14</h3>
                        </div>
                        <div class="p-3 rounded-full bg-secondary-100 dark:bg-secondary-900 text-secondary-500">
                            <i data-feather="calendar"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">New Hires</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">7</h3>
                        </div>
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-500">
                            <i data-feather="user-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Pending Requests</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">23</h3>
                        </div>
                        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900 text-red-500">
                            <i data-feather="alert-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Recent Activity</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-full mr-3 text-primary-500">
                            <i data-feather="user-plus" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-300"><span class="font-medium text-gray-800 dark:text-white">John Doe</span> joined the faculty</p>
                            <p class="text-sm text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="p-2 bg-secondary-100 dark:bg-secondary-900 rounded-full mr-3 text-secondary-500">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-300"><span class="font-medium text-gray-800 dark:text-white">Jane Smith</span> requested leave</p>
                            <p class="text-sm text-gray-500">4 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-full mr-3 text-purple-500">
                            <i data-feather="edit" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-300"><span class="font-medium text-gray-800 dark:text-white">Admin</span> updated profile settings</p>
                            <p class="text-sm text-gray-500">Yesterday</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="/employees.html" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition flex flex-col items-center">
                        <i data-feather="users" class="text-primary-500 mb-2"></i>
                        <span class="text-gray-800 dark:text-white">Employees</span>
                    </a>
                    <a href="/leave.html" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition flex flex-col items-center">
                        <i data-feather="calendar" class="text-secondary-500 mb-2"></i>
                        <span class="text-gray-800 dark:text-white">Leave</span>
                    </a>
                    <a href="/profile.html" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition flex flex-col items-center">
                        <i data-feather="user" class="text-purple-500 mb-2"></i>
                        <span class="text-gray-800 dark:text-white">Profile</span>
                    </a>
                    <a href="/settings.html" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition flex flex-col items-center">
                        <i data-feather="settings" class="text-yellow-500 mb-2"></i>
                        <span class="text-gray-800 dark:text-white">Settings</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <custom-footer></custom-footer>
    
    <script src="components/navbar.js"></script>
    <script src="components/sidebar.js"></script>
    <script src="components/footer.js"></script>
    <script src="script.js"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>