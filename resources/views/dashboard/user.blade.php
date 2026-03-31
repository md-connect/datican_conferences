@extends('layouts.app')

@section('title', 'User Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Welcome, {{ auth()->user()->first_name }}!</h1>
        
        <!-- Registration Status -->
        @if($hasRegistration)
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Conference Registration Complete</h3>
                    <div class="mt-1 text-sm text-green-700">
                        <p>Thank you for registering for DATICAN Conference 2026!</p>
                        @if($conferenceRegistration->is_presenting_paper)
                        <p class="mt-1">You indicated you will present a paper.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Complete Your Conference Registration</h3>
                    <div class="mt-1 text-sm text-blue-700">
                        <p>Register for DATICAN Conference 2026 to submit papers and attend sessions.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('conference.registration') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Register for Conference
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $paperCount }}</p>
                        <p class="text-sm text-gray-500">Submitted Papers</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-calendar-check text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $hasRegistration ? 'Yes' : 'No' }}</p>
                        <p class="text-sm text-gray-500">Conference Registration</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-user-check text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->is_reviewer ? 'Yes' : 'No' }}</p>
                        <p class="text-sm text-gray-500">Reviewer Status</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Paper Submission -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Submit an Abstract</h3>
                <p class="text-gray-600 mb-4">
                    Submit your research to DATICAN Conference 2026. All submissions will undergo peer review.
                </p>
                <a href="{{ route('papers.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i> Submit New Abstract
                </a>
            </div>
            
            <!-- Conference Registration -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Conference Registration</h3>
                <p class="text-gray-600 mb-4">
                    @if($hasRegistration)
                        Update your conference registration details or check your registration status.
                    @else
                        Register for the conference to present papers, attend sessions, and network.
                    @endif
                </p>
                <a href="{{ route('conference.registration') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ $hasRegistration ? 'View Registration' : 'Register Now' }}
                </a>
            </div>
        </div>
        
        <!-- Recent Activity -->
        @if($paperCount > 0)
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Papers</h3>
            @if($paperCount === 0)
            <p class="text-gray-500">You haven't submitted any papers yet.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($user->papers()->latest()->take(3)->get() as $paper)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $paper->anonymous_id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                                {{ $paper->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ 
                                    $paper->status === 'accepted' ? 'bg-green-100 text-green-800' :
                                    ($paper->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                    ($paper->status === 'under_review' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-gray-100 text-gray-800'))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('papers.show', $paper) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('papers.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View all papers →
                </a>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('call-for-papers') }}" 
               class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">Call for Abstracts</h4>
                        <p class="text-sm text-gray-500">Submission guidelines and topics</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('committees') }}" 
               class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">Committees</h4>
                        <p class="text-sm text-gray-500">Program and organizing committees</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('profile') }}" 
               class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">Profile</h4>
                        <p class="text-sm text-gray-500">Manage your account details</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection