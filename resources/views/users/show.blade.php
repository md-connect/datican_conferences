@extends('layouts.app')

@section('title', $user->name . ' - User Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('users.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-2">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Users
                </a>
                <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
            </div>
            <div class="flex space-x-3">
                <a href="mailto:{{ $user->email }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-envelope mr-2"></i> Send Email
                </a>
            </div>
        </div>
        
        <!-- User Info Card -->
        <div class="bg-white rounded-xl shadow mb-8">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
            </div>
            <div class="p-6">
                <div class="flex items-start space-x-6">
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600">
                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</h3>
                                <p class="text-gray-600">{{ $user->affiliation }}</p>
                                
                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                        <span class="text-gray-700">{{ $user->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar text-gray-400 w-5 mr-3"></i>
                                        <span class="text-gray-700">
                                            Joined {{ $user->created_at->format('F d, Y') }}
                                            <span class="text-gray-500 text-sm">({{ $user->created_at->diffForHumans() }})</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Roles & Permissions</h4>
                                <div class="flex flex-wrap gap-2">
                                    @if($user->is_admin)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-user-shield mr-1"></i> Administrator
                                    </span>
                                    @endif
                                    @if($user->is_reviewer)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-clipboard-check mr-1"></i> Reviewer
                                    </span>
                                    @endif
                                    @if($user->papers()->exists())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-file-alt mr-1"></i> Author
                                    </span>
                                    @endif
                                </div>
                                
                                <!-- Conference Registration Status -->
                                @php
                                    $registration = \App\Models\ConferenceRegistration::where('email', $user->email)
                                        ->orWhere('user_id', $user->id)
                                        ->first();
                                @endphp
                                <div class="mt-4">
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Conference</h4>
                                    @if($registration)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-calendar-check mr-1"></i> Registered for Conference
                                    </span>
                                    @if($registration->is_presenting_paper)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 ml-2">
                                        <i class="fas fa-file-alt mr-1"></i> Presenting Paper
                                    </span>
                                    @endif
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-calendar-times mr-1"></i> Not Registered
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Submitted Papers -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Submitted Papers</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($user->papers()->latest()->take(5)->get() as $paper)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900 truncate">{{ Str::limit($paper->title, 50) }}</h3>
                                <div class="flex items-center space-x-2 mt-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($paper->status == 'submitted') bg-blue-100 text-blue-800
                                        @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                                        @elseif($paper->status == 'accepted') bg-green-100 text-green-800
                                        @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $paper->anonymous_id }}</span>
                                </div>
                            </div>
                            <a href="{{ route('papers.show', $paper) }}" 
                               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                View
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                        <p>No papers submitted.</p>
                    </div>
                    @endforelse
                </div>
                @if($user->papers()->count() > 5)
                <div class="px-6 py-4 border-t text-center">
                    <a href="{{ route('papers.index') }}?author={{ $user->id }}" class="text-blue-600 hover:text-blue-800">
                        View all {{ $user->papers()->count() }} papers →
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Review Assignments -->
            @if($user->is_reviewer)
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Review Assignments</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @php
                        $assignments = $user->reviewAssignments()->with('paper')->latest()->take(5)->get();
                    @endphp
                    @forelse($assignments as $assignment)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $assignment->paper->anonymous_id }}</h3>
                                <div class="flex items-center space-x-2 mt-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($assignment->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($assignment->status == 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($assignment->status == 'completed') bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Due: {{ $assignment->deadline->format('M d') }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $assignment->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-clipboard-check text-4xl mb-4"></i>
                        <p>No review assignments.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
        
        <!-- Statistics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-file-alt text-3xl text-blue-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->papers()->count() }}</p>
                    <p class="text-sm text-gray-500">Papers Submitted</p>
                </div>
            </div>
            
            @if($user->is_reviewer)
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-clipboard-check text-3xl text-green-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->reviewAssignments()->count() }}</p>
                    <p class="text-sm text-gray-500">Reviews Assigned</p>
                </div>
            </div>
            @endif
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-calendar-check text-3xl text-purple-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $registration ? 'Yes' : 'No' }}
                    </p>
                    <p class="text-sm text-gray-500">Conference Registered</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection