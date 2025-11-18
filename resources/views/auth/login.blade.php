<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiPSU HRMIS</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* --- BiPSU Theme Gradient Background --- */
        .light-gray-bg {
            background: linear-gradient(135deg, #fef5cc 0%, #d4e4ff 50%, #fef0b3 100%);
            /* Vibrant blend of yellow and blue matching BiPSU colors */
        }
        /* ------------------------------------------------- */

        .form-container {
            perspective: 1000px;
            position: relative;
            height: 100%;
        }
        .form-flip {
            transform-style: preserve-3d;
            transition: transform 0.8s;
            position: relative;
            width: 100%;
            min-height: 600px;
        }
        .form-flip.flipped {
            transform: rotateY(180deg);
        }
        .form-front, .form-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            top: 0;
            left: 0;
            overflow-y: auto;
        }
        .form-front {
            z-index: 2;
        }
        .form-back {
            transform: rotateY(180deg);
        }
        .input-container {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .input-field {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .input-field:focus {
            border-color: #1e3a8a; /* Updated to match sidebar blue */
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1); /* Updated to match sidebar blue */
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.3s;
        }
        .password-toggle:hover {
            color: #1e3a8a; /* Updated to match sidebar blue */
        }
        
        /* --- IMPROVED LEFT PANEL STYLES (From previous response) --- */
        .university-bg {
            /* Lighter overlay to show image */
            background-image: linear-gradient(rgba(30, 58, 138, 0.7), rgba(30, 58, 138, 0.8)), url('assets/images/uni_photo.jpg'); 
            background-size: cover;
            background-position: center;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8; /* A standard, lighter Tailwind blue (blue-700/600 equivalent) */
            transform: translateY(-1px); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        
        .feature-divider {
            width: 3rem;
            height: 3px;
            background-color: #FFCC00; /* Secondary brand color (gold) */
            margin: 1rem 0 1.5rem 0;
            border-radius: 9999px;
        }
        /* --------------------------------------------------------- */

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .btn-primary {
            background-color: #1e3a8a; /* Dark blue from sidebar */
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #FFCC00; /* Lighter blue/Gold for hover */
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }
        .divider-text {
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            background-color: white;
            transition: all 0.3s;
        }
        .social-btn:hover {
            background-color: #f9fafb;
        }
        .form-radio {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }
        .form-radio input {
            margin-right: 0.5rem;
        }
        .form-checkbox {
            display: flex;
            align-items: center;
        }
        .form-checkbox input {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center light-gray-bg"> <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="md:flex">
                <div class="hidden md:block md:w-1/2 university-bg p-12 text-white"> 
                    <div class="flex flex-col h-full justify-center">

                        <h1 class="text-5xl font-extrabold mb-3 tracking-tight">BiPSU HRMIS</h1>
                        <div class="feature-divider"></div>

                        <h2 class="text-xl font-light mb-8">Human Resource Management Information System</h2>

                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="fas fa-users text-2xl mr-4 mt-1 text-yellow-300"></i> <div>
                                    <h3 class="text-lg font-semibold">Employee Profiles (PDS)</h3>
                                    <p class="text-sm text-gray-200">Manage comprehensive Personal Data Sheets and records.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-calendar-alt text-2xl mr-4 mt-1 text-yellow-300"></i>
                                <div>
                                    <h3 class="text-lg font-semibold">Attendance & Timekeeping</h3>
                                    <p class="text-sm text-gray-200">Accurate tracking of work hours and duty statuses.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-file-invoice-dollar text-2xl mr-4 mt-1 tezxt-yellow-300"></i>
                                <div>
                                    <h3 class="text-lg font-semibold">Leave & Benefits</h3>
                                    <p class="text-sm text-gray-200">Streamlined application and approval for all leave types.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-auto pt-8 text-center text-sm font-light border-t border-blue-400 opacity-80">
                            "Weaving BiPSU with the world, and the world with BiPSU."
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-1/2 p-8 form-container">
                    <div class="form-flip" id="formFlip">
                        <div class="form-front">
                            <div class="flex flex-col items-center mb-6">
                                <img src="{{ asset('assets/images/one_bipsu.png') }}" alt="University Logo" class="h-15 mb-2"> 
                                <span class="text-sm text-blue-400">Together as One, Powered by BiPSU</span>
                           </div>

                            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Sign In to Your Account</h2>
                            
                            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                                @csrf
                                <div class="input-container">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <div class="relative">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" id="email" name="email" class="input-field" placeholder="@university.edu" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    </div>
                                    @error('email')
                                        <div id="email-error" class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="input-container">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <div class="relative">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" id="password" name="password" class="input-field" placeholder="••••••••" required autocomplete="current-password">
                                        <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('password', this)"></i>
                                    </div>
                                    @error('password')
                                        <div id="password-error" class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="flex items-center justify-between mb-4">
                                    <div class="form-checkbox">
                                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('remember') ? 'checked' : '' }}>
                                        <label for="remember" class="block text-sm text-gray-700">Remember me</label>
                                    </div>
                                    <div class="text-sm">
                                        <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500">Forgot password?</a>
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full btn-primary">
                                    Sign in
                                </button>
                            </form>
                            
                            <div class="divider">
                                <span class="divider-text">Or continue with</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('social.login', 'google') }}" class="social-btn flex items-center justify-center">
                                    <i class="fab fa-google text-red-500 mr-2"></i> Google
                                </a>
                                <a href="{{ route('social.login', 'facebook') }}" class="social-btn flex items-center justify-center">
                                    <i class="fab fa-facebook text-blue-700 mr-2"></i> Facebook
                                </a>
                            </div>
                            
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-600">
                                    Don't have an account? 
                                    <button type="button" onclick="flipForm()" class="font-medium text-blue-600 hover:text-blue-500 ml-1">Register here</button>
                                </p>
                            </div>
                        </div>

                        <div class="form-back absolute top-0 left-0 w-full h-full bg-white p-8">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex flex-col items-center w-full">
                                    <img src="{{ asset('assets/images/one_bipsu.png') }}" alt="University Logo" class="h-15 mb-2"> 
                                    <span class="text-sm text-blue-400">Together as One, Powered by BiPSU</span>
                                </div>
                                <button type="button" onclick="flipForm()" class="text-gray-400 hover:text-gray-600 ml-4">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Create New Account</h2>
                            
                            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="input-container">
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                        <div class="relative">
                                            <i class="fas fa-user input-icon"></i>
                                            <input type="text" id="first_name" name="first_name" class="input-field" placeholder="John" value="{{ old('first_name') }}" required autocomplete="given-name">
                                        </div>
                                        @error('name')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="input-container">
                                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                        <div class="relative">
                                            <i class="fas fa-user input-icon"></i>
                                            <input type="text" id="last_name" name="last_name" class="input-field" placeholder="Doe" value="{{ old('last_name') }}" required autocomplete="family-name">
                                        </div>
                                        @error('last_name')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="input-container">
                                    <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <div class="relative">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" id="registerEmail" name="email" class="input-field" placeholder="you@company.com" value="{{ old('email') }}" required autocomplete="email">
                                    </div>
                                    @error('email')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="input-container">
                                    <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <div class="relative">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" id="registerPassword" name="password" class="input-field" placeholder="••••••••" required autocomplete="new-password">
                                        <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('registerPassword', this)"></i>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
                                    @error('password')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="input-container">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                    <div class="relative">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" placeholder="••••••••" required autocomplete="new-password">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="input-container">
                                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                                        <div class="relative">
                                            <i class="fas fa-id-card input-icon"></i>
                                            <input type="text" id="employee_id" name="employee_id" class="input-field" placeholder="EMP-12345" value="{{ old('employee_id') }}" required>
                                        </div>
                                        @error('employee_id')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="input-container">
                                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                        <div class="relative">
                                            <i class="fas fa-building input-icon"></i>
                                            <select id="department" name="department" class="input-field" required>
                                                <option value="">Select Department</option>
                                                <option value="STCS" {{ old('department') == 'STCS' ? 'selected' : '' }}>STCS</option>
                                                <option value="SOE" {{ old('department') == 'SOE' ? 'selected' : '' }}>SOE</option>
                                                <option value="STED" {{ old('department') == 'STED' ? 'selected' : '' }}>STED</option>
                                                <option value="SNHS" {{ old('department') == 'SNHS' ? 'selected' : '' }}>SNHS</option>
                                                <option value="SCJE" {{ old('department') == 'SCJE' ? 'selected' : '' }}>SCJE</option>
                                                <option value="SME" {{ old('department') == 'SME' ? 'selected' : '' }}>SME</option>
                                                <option value="SAS" {{ old('department') == 'SAS' ? 'selected' : '' }}>SAS</option>
                                            </select>
                                        </div>
                                        @error('department')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="input-container">
                                    <label class="form-checkbox">
                                        <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                                        <span class="text-sm text-gray-700">
                                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                                        </span>
                                    </label>
                                    @error('terms')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="w-full btn-primary">
                                    Create Account
                                </button>
                            </form>
                            
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-600">
                                    Already have an account? 
                                    <button type="button" onclick="flipForm()" class="font-medium text-blue-600 hover:text-blue-500 ml-1">Sign in</button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function flipForm() {
            document.getElementById('formFlip').classList.toggle('flipped');
        }

        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }

        // Auto-flip to register form if there are register errors
        @if($errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('employee_id') || $errors->has('department') || $errors->has('terms'))
            document.addEventListener('DOMContentLoaded', function() {
                // Check if any of the registration errors exist and flip if necessary
                // Note: Your original code checked 'role', which isn't in the registration form fields shown, 
                // I've kept the error fields relevant to the visible form.
                flipForm();
            });
        @endif
    </script>

    @include('components.chatbot')
</body>
</html>