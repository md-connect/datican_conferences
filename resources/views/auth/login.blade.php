@extends('layouts.auth')

@section('title', 'Login - DATICAN Conference')

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
                Sign in to Portal
            </h2>
            <p class="mt-2 text-gray-600">
                Improving Medical Diagnostics in Nigeria Using AI and Data Science
            </p>
        </div>
        
        <!-- Messages -->
        @if(session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('status') }}</p>
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
        
        <!-- Login Form -->
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="pl-10 block w-full px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                               placeholder="you@example.com" value="{{ old('email') }}">
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-accent hover:text-red-600">
                            Forgot password?
                        </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="pl-10 block w-full px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                               placeholder="••••••••">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox"
                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                        Remember me on this device
                    </label>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 text-white bg-accent hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent font-medium transition-colors hover-lift">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign in to Portal
                </button>
            </div>
            
            <!-- Registration Link -->
            <div class="text-center pt-4">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-medium text-primary hover:text-secondary">
                        Sign up now
                    </a>
                </p>
            </div>
        </form>
        
        <!-- Quick Links -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-700 mb-4 text-center">Quick Access</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('home') }}" 
                   class="flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
                <a href="{{ route('call-for-papers') }}" 
                   class="flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>
                    Call for Papers
                </a>
            </div>
        </div>
    </div>
</div>

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