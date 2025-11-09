<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniHive HR Hub | Leave Management</title>
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
        <custom-sidebar active="leave"></custom-sidebar>
        
        <main class="flex-1 p-6 ml-0 lg:ml-[250px]">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Leave Management</h1>
                <div class="flex gap-2">
                    <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                        <i data-feather="moon" class="hidden dark:block text-gray-300"></i>
                        <i data-feather="sun" class="dark:hidden text-yellow-500"></i>
                    </button>
                    <button class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i data-feather="plus"></i>
                        <span>Request Leave</span>
                    </button>
                </div>
            </div>

            <!-- Leave Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Total Leave Days</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">30</h3>
                        </div>
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-500">
                            <i data-feather="calendar"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Used</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">12</h3>
                        </div>
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-500">
                            <i data-feather="check-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Remaining</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">18</h3>
                        </div>
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-500">
                            <i data-feather="clock"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Pending</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">3</h3>
                        </div>
                        <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900 text-orange-500">
                            <i data-feather="alert-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px">
                        <button class="px-4 py-3 text-center border-b-2 font-medium text-sm border-primary-500 text-primary-600 dark:text-primary-400">
                            All Requests
                        </button>
                        <button class="px-4 py-3 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            Pending
                        </button>
                        <button class="px-4 py-3 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            Approved
                        </button>
                        <button class="px-4 py-3 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            Rejected
                        </button>
                    </nav>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Leave Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dates</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <i data-feather="user" class="text-gray-500 dark:text-gray-300"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">John Doe</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Computer Science</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Annual Leave</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Jun 10 - Jun 15, 2023</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">5 days</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Approved</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex gap-2">
                                        <button class="text-blue-500 hover:text-blue-700">
                                            <i data-feather="eye"></i>
                                        </button>
                                        <button class="text-yellow-500 hover:text-yellow-700">
                                            <i data-feather="edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- More leave request rows would go here -->
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-600">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50">
                            Previous
                        </a>
                        <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">24</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <span class="sr-only">Previous</span>
                                    <i data-feather="chevron-left"></i>
                                </a>
                                <a href="#" aria-current="page" class="z-10 bg-primary-50 dark:bg-primary-900 border-primary-500 dark:border-primary-700 text-primary-600 dark:text-primary-300 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    1
                                </a>
                                <a href="#" class="bg-white dark:bg-gray-800 border-gray-300 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    2
                                </a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <span class="sr-only">Next</span>
                                    <i data-feather="chevron-right"></i>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Calendar -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Leave Calendar</h2>
                    <div class="flex items-center gap-2">
                        <button class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                            <i data-feather="chevron-left"></i>
                        </button>
                        <span class="font-medium text-gray-800 dark:text-white">June 2023</span>
                        <button class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                            <i data-feather="chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-2 mb-2">
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Sun</div>
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Mon</div>
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Tue</div>
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Wed</div>
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Thu</div>
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Fri</div>
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Sat</div>
                </div>
                <div class="grid grid-cols-7 gap-2">
                    <!-- Calendar days would go here -->
                    <!-- This is a simplified representation -->
                    <div class="h-16 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500">28</div>
                    <!-- More days... -->
                    <div class="h-16 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">1</div>
                    <!-- Highlighted leave days -->
                    <div class="h-16 rounded-lg bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-800 flex items-center justify-center relative">
                        <span>10</span>
                        <div class="absolute bottom-1 text-xs px-1 rounded bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200">J. Doe</div>
                    </div>
                    <!-- More calendar days... -->
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