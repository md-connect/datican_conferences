@extends('layouts.auth')

@section('title', 'Forgot Password - DATICAN Conference')

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
                Forgot Password?
            </h2>
            <p class="mt-2 text-gray-600">
                Enter your email and first name to reset your password
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
        
        <!-- Forgot Password Form -->
        <form class="mt-8 space-y-6" action="{{ route('password.validate') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address *
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
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input id="first_name" name="first_name" type="text" required
                               class="pl-10 block w-full px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                               placeholder="Enter your first name" value="{{ old('first_name') }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Enter the first name you used during registration</p>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent font-medium transition-colors hover-lift">
                    <i class="fas fa-check-circle mr-2"></i>
                    Verify & Continue
                </button>
            </div>
            
            <!-- Back to Login Link -->
            <div class="text-center pt-4">
                <p class="text-sm text-gray-600">
                    Remember your password? 
                    <a href="{{ route('login') }}" class="font-medium text-accent hover:text-accent-dark">
                        Back to Login
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