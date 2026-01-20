@extends('layouts.app')

@section('title', 'My Profile - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">My Profile</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-blue-600">
                                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="text-2xl font-semibold text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                                <p class="text-gray-600 mt-1">{{ auth()->user()->affiliation }}</p>
                                
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Contact Information</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                                <span class="text-gray-700">{{ auth()->user()->email }}</span>
                                            </div>
                                            @if(auth()->user()->phone)
                                            <div class="flex items-center">
                                                <i class="fas fa-phone text-gray-400 w-5 mr-3"></i>
                                                <span class="text-gray-700">{{ auth()->user()->phone }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Account Information</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar text-gray-400 w-5 mr-3"></i>
                                                <span class="text-gray-700">
                                                    Member since {{ auth()->user()->created_at->format('F d, Y') }}
                                                </span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-gray-400 w-5 mr-3"></i>
                                                <span class="text-gray-700">
                                                    Last updated {{ auth()->user()->updated_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conference Registration -->
                        @php
                            $registration = \App\Models\ConferenceRegistration::where('email', auth()->user()->email)
                                ->orWhere('user_id', auth()->user()->id)
                                ->first();
                        @endphp
                        <div class="mt-8 pt-6 border-t">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Conference Registration</h4>
                            @if($registration)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-green-800">Registered for DATICAN Conference 2026</p>
                                        <p class="text-sm text-green-600 mt-1">
                                            Registration ID: {{ $registration->id }}
                                            @if($registration->is_presenting_paper)
                                            • You are registered as a presenter
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('conference.registration') }}" 
                                       class="inline-flex items-center text-sm text-green-700 hover:text-green-900">
                                        <i class="fas fa-eye mr-1"></i> View registration details
                                    </a>
                                </div>
                            </div>
                            @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-yellow-600 text-xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-yellow-800">Not Registered for Conference</p>
                                        <p class="text-sm text-yellow-600 mt-1">Register to secure your spot for May 2026</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('conference.registration') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                        <i class="fas fa-calendar-plus mr-2"></i> Register Now
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t bg-gray-50">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-edit mr-2"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Papers Submitted</span>
                            <span class="font-semibold text-gray-900">{{ auth()->user()->papers()->count() }}</span>
                        </div>
                        @if(auth()->user()->is_reviewer)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Reviews Assigned</span>
                            <span class="font-semibold text-gray-900">{{ auth()->user()->reviewAssignments()->count() }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Account Status</span>
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Active</span>
                        </div>
                        @if(auth()->user()->is_admin)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Role</span>
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Administrator</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        @php
                            $recentPaper = auth()->user()->papers()->latest()->first();
                        @endphp
                        @if($recentPaper)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-alt text-blue-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Paper submitted</p>
                                <p class="text-xs text-gray-500 truncate">{{ Str::limit($recentPaper->title, 40) }}</p>
                                <p class="text-xs text-gray-400">{{ $recentPaper->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @php
                            $recentReview = auth()->user()->reviewAssignments()->latest()->first();
                        @endphp
                        @if($recentReview && auth()->user()->is_reviewer)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clipboard-check text-green-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Review assigned</p>
                                <p class="text-xs text-gray-500">Paper {{ $recentReview->paper->anonymous_id }}</p>
                                <p class="text-xs text-gray-400">{{ $recentReview->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($registration)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-check text-purple-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Conference registration</p>
                                <p class="text-xs text-gray-500">Completed successfully</p>
                                <p class="text-xs text-gray-400">{{ $registration->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Account Actions -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                            <i class="fas fa-tachometer-alt text-gray-400 mr-3"></i>
                            <span>Go to Dashboard</span>
                        </a>
                        <a href="{{ route('papers.index') }}" 
                           class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                            <i class="fas fa-file-alt text-gray-400 mr-3"></i>
                            <span>My Papers</span>
                        </a>
                        @if(auth()->user()->is_reviewer)
                        <a href="{{ route('reviews.my') }}" 
                           class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                            <i class="fas fa-clipboard-check text-gray-400 mr-3"></i>
                            <span>My Reviews</span>
                        </a>
                        @endif
                        <a href="{{ route('conference.registration') }}" 
                           class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                            <i class="fas fa-calendar-plus text-gray-400 mr-3"></i>
                            <span>Conference Registration</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection