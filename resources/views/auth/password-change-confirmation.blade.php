@extends('layouts.auth')

@section('title', 'Password Changed - DATICAN Conference')

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
        </div>
        
        <!-- Success Icon -->
        <div class="flex justify-center">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-5xl text-green-600"></i>
            </div>
        </div>
        
        <!-- Success Message -->
        <div class="text-center space-y-4">
            <h2 class="text-3xl font-bold text-gray-900">
                Password Changed!
            </h2>
            <p class="text-gray-600">
                Your password has been successfully updated. You can now log in with your new password.
            </p>
        </div>
        
        <!-- User Info (Optional - shows which account was updated) -->
        @if(isset($email))
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-user-circle text-gray-400 text-xl mr-3"></i>
                <div>
                    <p class="text-sm text-gray-600">Account updated for:</p>
                    <p class="font-medium text-gray-800">{{ $email }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Action Buttons -->
        <div class="space-y-4">
            <a href="{{ route('login') }}" 
               class="w-full flex justify-center items-center py-3 px-4 text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium transition-colors hover-lift">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login to Your Account
            </a>
            
            <a href="{{ route('home') }}" 
               class="w-full flex justify-center items-center py-3 px-4 text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 font-medium transition-colors">
                <i class="fas fa-home mr-2"></i>
                Return to Homepage
            </a>
        </div>
        
        
        <!-- Quick Links -->
        <div class="mt-6 pt-6 border-t border-gray-200">
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