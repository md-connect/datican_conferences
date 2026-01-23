@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Change Password</h1>
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <!-- Current Password -->
                <div class="mb-4">
                    <label for="current_password" class="block text-gray-700 text-sm font-medium mb-2">
                        Current Password
                    </label>
                    <input type="password" 
                           name="current_password" 
                           id="current_password"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- New Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">
                        New Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           required
                           minlength="8"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">
                        Confirm New Password
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center justify-between">
                    <a href="{{ route('profile') }}" 
                       class="px-4 py-2 text-gray-700 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection