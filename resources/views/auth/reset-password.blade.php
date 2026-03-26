@extends('layouts.auth')

@section('title', 'Reset Password - DATICAN Conference')

@section('content')
<div class="min-h-screen gradient-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white shadow-xl p-8">
        <!-- Logo -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center mb-6">
                <img class="h-12 w-auto" src="{{ asset('images/logo/datican_logo.png') }}" alt="DATICAN">
                <div class="ml-3">
                    <span class="bg-primary text-white px-3 py-1 text-sm font-semibold">
                        Conference 2026
                    </span>
                </div>
            </a>
            <h2 class="text-3xl font-bold text-gray-900">
                Choose New Password
            </h2>
            <p class="mt-2 text-gray-600">
                Create a new password for your account
            </p>
        </div>
        
        <!-- Messages -->
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <!-- User Info -->
        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-envelope text-green-600 mr-3"></i>
                <div>
                    <p class="text-sm text-green-800">Resetting password for:</p>
                    <p class="font-medium text-green-900">{{ $email }}</p>
                    <p class="text-sm text-green-700">{{ $first_name }}</p>
                </div>
            </div>
        </div>
        
        <!-- Reset Password Form -->
        <form class="mt-8 space-y-6" action="{{ route('password.reset') }}" method="POST">
            @csrf
            
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="first_name" value="{{ $first_name }}">
            
            <div class="space-y-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        New Password *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="pl-10 block w-full px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                               placeholder="Enter new password">
                        <button type="button" onclick="togglePassword('password')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters</p>
                </div>
                
                <!-- Password Strength Meter -->
                <div>
                    <div class="flex items-center space-x-2">
                        <div class="flex-1 h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div id="strength-bar" class="h-full w-0 transition-all duration-300"></div>
                        </div>
                        <span id="strength-text" class="text-xs text-gray-500">Password strength</span>
                    </div>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm New Password *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="pl-10 block w-full px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                               placeholder="Confirm new password">
                        <button type="button" onclick="togglePassword('password_confirmation')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent font-medium transition-colors hover-lift">
                    <i class="fas fa-check-circle mr-2"></i>
                    Reset Password
                </button>
            </div>
            
            <!-- Back to Login Link -->
            <div class="text-center pt-4">
                <p class="text-sm text-gray-600">
                    <a href="{{ route('login') }}" class="font-medium text-accent hover:text-accent-dark">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Login
                    </a>
                </p>
            </div>
        </form>
        
        <!-- Quick Links -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-700 mb-4 text-center">Need Help?</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('home') }}" 
                   class="flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
                <a href="mailto:manager.datican@gmail.com" 
                   class="flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
    }

    // Password strength meter
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        let message = '';
        let color = '';
        let width = 0;

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;

        if (password.length === 0) {
            message = 'Password strength';
            color = 'bg-gray-200';
            width = 0;
        } else if (strength <= 2) {
            message = 'Weak';
            color = 'bg-red-500';
            width = 25;
        } else if (strength === 3) {
            message = 'Fair';
            color = 'bg-yellow-500';
            width = 50;
        } else if (strength === 4) {
            message = 'Good';
            color = 'bg-blue-500';
            width = 75;
        } else if (strength === 5) {
            message = 'Strong';
            color = 'bg-green-500';
            width = 100;
        }

        strengthBar.className = `h-full transition-all duration-300 ${color}`;
        strengthBar.style.width = `${width}%`;
        strengthText.textContent = message;
        
        // Change text color based on strength
        if (strength <= 2) {
            strengthText.className = 'text-xs text-red-600';
        } else if (strength === 3) {
            strengthText.className = 'text-xs text-yellow-600';
        } else if (strength === 4) {
            strengthText.className = 'text-xs text-blue-600';
        } else if (strength === 5) {
            strengthText.className = 'text-xs text-green-600';
        }
    });
</script>

<style>
    .gradient-bg {
        background: linear-gradient(135deg, #2C3E50 0%, #1A252F 100%);
    }
    
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection