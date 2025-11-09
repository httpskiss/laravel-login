<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniHive HR Hub | Settings</title>
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
        <custom-sidebar active="settings"></custom-sidebar>
        
        <main class="flex-1 p-6 ml-0 lg:ml-[250px]">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Settings</h1>
                <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                    <i data-feather="moon" class="hidden dark:block text-gray-300"></i>
                    <i data-feather="sun" class="dark:hidden text-yellow-500"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Settings Sidebar -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Settings Menu</h2>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <a href="#" class="block px-4 py-3 text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900">
                                <div class="flex items-center gap-3">
                                    <i data-feather="user" class="w-5 h-5"></i>
                                    <span>Account</span>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <i data-feather="lock" class="w-5 h-5"></i>
                                    <span>Security</span>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <i data-feather="bell" class="w-5 h-5"></i>
                                    <span>Notifications</span>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <i data-feather="globe" class="w-5 h-5"></i>
                                    <span>Language</span>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <i data-feather="database" class="w-5 h-5"></i>
                                    <span>Data & Privacy</span>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <i data-feather="info" class="w-5 h-5"></i>
                                    <span>About</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Settings Content -->
                <div class="lg:col-span-9">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Account Settings</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account information and preferences</p>
                        </div>
                        <div class="p-6">
                            <form>
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Profile Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="first-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                                            <input type="text" id="first-name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="Admin">
                                        </div>
                                        <div>
                                            <label for="last-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                                            <input type="text" id="last-name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="User">
                                        </div>
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                            <input type="email" id="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="admin@unihive.edu">
                                        </div>
                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                            <input type="text" id="phone" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="(555) 123-4567">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Profile Picture</h3>
                                    <div class="flex items-center gap-6">
                                        <div class="relative">
                                            <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <i data-feather="user" class="w-12 h-12 text-gray-500 dark:text-gray-400"></i>
                                            </div>
                                            <button class="absolute bottom-0 right-0 bg-primary-500 text-white p-2 rounded-full hover:bg-primary-600 transition">
                                                <i data-feather="camera" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                Change Photo
                                            </button>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">JPG, GIF or PNG. Max size of 2MB</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Theme Preferences</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <button type="button" class="p-4 border rounded-lg text-left hover:border-primary-500 dark:hover:border-primary-400">
                                            <div class="flex items-center gap-3">
                                                <i data-feather="sun" class="text-yellow-500"></i>
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-white">Light Mode</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Bright and clean interface</p>
                                                </div>
                                            </div>
                                        </button>
                                        <button type="button" class="p-4 border rounded-lg text-left hover:border-primary-500 dark:hover:border-primary-400">
                                            <div class="flex items-center gap-3">
                                                <i data-feather="moon" class="text-indigo-500"></i>
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-white">Dark Mode</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Easier on the eyes</p>
                                                </div>
                                            </div>
                                        </button>
                                        <button type="button" class="p-4 border rounded-lg text-left hover:border-primary-500 dark:hover:border-primary-400">
                                            <div class="flex items-center gap-3">
                                                <i data-feather="settings" class="text-gray-500"></i>
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-white">System</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Follow device settings</p>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end gap-3">
                                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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