@extends('layouts.app')

@section('title', 'Edit Profile - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Profile</h1>
        <p class="text-gray-600 mb-8">Update your personal information and preferences.</p>
        
        <div class="bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
            </div>
            
            <form method="POST" action="{{ route('profile.update') }}" class="p-6">
                @csrf
                @method('PUT')
                
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
                @endif
                
                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                        <div>
                            <p class="text-red-800 font-medium">Please fix the following errors:</p>
                            <ul class="mt-1 list-disc list-inside text-red-700 text-sm">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name *
                        </label>
                        <input type="text" id="first_name" name="first_name" 
                               value="{{ old('first_name', auth()->user()->first_name) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name *
                        </label>
                        <input type="text" id="last_name" name="last_name" 
                               value="{{ old('last_name', auth()->user()->last_name) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address *
                    </label>
                    <input type="email" id="email" name="email" 
                           value="{{ old('email', auth()->user()->email) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">This will be used for all conference communications.</p>
                </div>
                
                <div class="mb-6">
                    <label for="affiliation" class="block text-sm font-medium text-gray-700 mb-2">
                        Institution/Affiliation *
                    </label>
                    <input type="text" id="affiliation" name="affiliation" 
                           value="{{ old('affiliation', auth()->user()->affiliation) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Your university, organization, or company.</p>
                </div>
                
                <div class="mb-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Biography (Optional)
                    </label>
                    <textarea id="bio" name="bio" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('bio', auth()->user()->bio) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Tell us about your research interests and background.</p>
                </div>
                
                <!-- Conference Registration Notice -->
                @php
                    $registration = \App\Models\ConferenceRegistration::where('email', auth()->user()->email)
                        ->orWhere('user_id', auth()->user()->id)
                        ->first();
                @endphp
                @if($registration)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                        <div>
                            <p class="text-blue-800 font-medium">Conference Registration Linked</p>
                            <p class="text-sm text-blue-600 mt-1">
                                Your profile is linked to your conference registration. 
                                To update your conference registration details (title, phone, etc.), 
                                please contact the conference administration.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="flex justify-between items-center pt-6 border-t">
                    <a href="{{ route('profile') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Account Security -->
        <div class="mt-8 bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Account Security</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">For security reasons, password changes must be done through the password reset process.</p>
                <a href="{{ route('password.request') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-key mr-2"></i> Change Password
                </a>
            </div>
        </div>
        
        <!-- Conference Registration -->
        <div class="mt-8 bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Conference Registration</h2>
            </div>
            <div class="p-6">
                @if($registration)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-800 font-medium">You are registered for DATICAN Conference 2026</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Registration ID: {{ $registration->id }}
                            @if($registration->is_presenting_paper)
                            • You are registered as a presenter
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('conference.registration') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-eye mr-2"></i> View Details
                    </a>
                </div>
                @else
                <div class="text-center py-6">
                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600 mb-4">You haven't registered for the conference yet.</p>
                    <a href="{{ route('conference.registration') }}" 
                       class="inline-flex items-center px-6 py-2 bg-accent text-white rounded-lg hover:bg-red-600">
                        <i class="fas fa-calendar-plus mr-2"></i> Register Now
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection