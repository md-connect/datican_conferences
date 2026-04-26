@extends('layouts.app')

@section('title', 'Admin Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
        <p class="text-gray-600 mb-8">Full administrative access to conference registration and paper management systems.</p>
        
        <!-- Welcome Message -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow p-6 mb-8">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-white shadow mr-4">
                    <i class="fas fa-user-shield text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Welcome, {{ auth()->user()->first_name }}!</h2>
                    <p class="text-gray-600 mt-1">You have full administrative access to both the conference registration system and paper management system.</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats - Combined -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Conference Stats -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Conference Registrations</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format(\App\Models\ConferenceRegistration::count()) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total registrations</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <a href="{{ route('admin.registrations') }}" class="mt-4 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <!-- Paper Stats -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Papers Submitted</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format(\App\Models\Paper::count()) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total papers</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-file-alt text-green-600 text-2xl"></i>
                    </div>
                </div>
                <a href="{{ route('papers.index') }}" class="mt-4 inline-flex items-center text-sm text-green-600 hover:text-green-800">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <!-- Reviews Completed -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Reviews Completed</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format(\App\Models\ReviewAssignment::where('status', 'completed')->count()) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Completed reviews</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-clipboard-check text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <a href="{{ route('analytics.index') }}" class="mt-4 inline-flex items-center text-sm text-purple-600 hover:text-purple-800">
                    Analytics <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <!-- Both Reviews Done -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Both Reviews Done</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">
                            @php
                                $bothReviewsDone = \App\Models\Paper::whereHas('reviewAssignments', function($q) {
                                    $q->where('status', 'completed');
                                }, '>=', 2)->count();
                            @endphp
                            {{ $bothReviewsDone }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Papers with 2+ reviews</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-users text-indigo-600 text-2xl"></i>
                    </div>
                </div>
                <a href="{{ route('chair.papers') }}?status=reviewed" class="mt-4 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <!-- User Stats -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format(\App\Models\User::count()) }}</p>
                        <p class="text-xs text-gray-500 mt-1">System users</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-red-600 text-2xl"></i>
                    </div>
                </div>
                <a href="{{ route('users.index') }}" class="mt-4 inline-flex items-center text-sm text-red-600 hover:text-red-800">
                    Manage users <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <!-- Decision Stats Row - NEW -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @php
                $acceptedPapers = \App\Models\Paper::whereIn('decision', ['accept', 'accept_with_minor_revision', 'accept_with_major_revision'])->count();
                $rejectedPapers = \App\Models\Paper::where('decision', 'reject')->count();
                $needingDecision = \App\Models\Paper::where('status', 'under_review')
                    ->whereHas('reviewAssignments', function($q) {
                        $q->where('status', 'completed');
                    }, '>=', 2)
                    ->whereNull('decision')
                    ->count();
            @endphp
            
            <!-- Accepted Papers -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Accepted Papers</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $acceptedPapers }}</p>
                        <p class="text-xs text-gray-500 mt-1">Including revisions</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
                
            </div>
            
            <!-- Rejected Papers -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Rejected Papers</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $rejectedPapers }}</p>
                        <p class="text-xs text-gray-500 mt-1">Not accepted</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>
               
            </div>
            
            <!-- Needing Decision -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Needing Decision</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $needingDecision }}</p>
                        <p class="text-xs text-gray-500 mt-1">Ready for chair decision</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-gavel text-orange-600 text-2xl"></i>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('assignments.index') }}" 
               class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-tasks text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Paper Assignments</p>
                        <p class="text-sm text-gray-500">Assign 2 reviewers per paper</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('admin.conference.dashboard') }}" 
               class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Conference Registrations</p>
                        <p class="text-sm text-gray-500">View conference attendees</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('analytics.index') }}" 
               class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-chart-bar text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Analytics</p>
                        <p class="text-sm text-gray-500">View system statistics</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('chair.reviewers') }}" 
               class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-users-cog text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Manage Reviewers</p>
                        <p class="text-sm text-gray-500">Reviewer performance</p>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Two Column Layout: Conference & Paper Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Conference Management -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Conference Management</h3>
                    <p class="text-sm text-gray-500 mt-1">Manage conference registrations and attendees</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="{{ route('admin.registrations') }}" 
                           class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <i class="fas fa-users text-blue-600 mr-3"></i>
                                <div>
                                    <span class="font-medium text-gray-700">View All Registrations</span>
                                    <p class="text-sm text-gray-500">All conference attendees</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        
                        <a href="{{ route('admin.export.registrations') }}" 
                           class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <i class="fas fa-file-export text-green-600 mr-3"></i>
                                <div>
                                    <span class="font-medium text-gray-700">Export Registration Data</span>
                                    <p class="text-sm text-gray-500">Download as Excel/CSV</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        
                        <a href="{{ route('conference.registration.stats') }}" 
                           class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <i class="fas fa-chart-pie text-purple-600 mr-3"></i>
                                <div>
                                    <span class="font-medium text-gray-700">View Registration Statistics</span>
                                    <p class="text-sm text-gray-500">Analytics and insights</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    </div>
                    
                    <!-- Quick Conference Stats -->
                    @php
                        $conferenceStats = [
                            'total_presenters' => \App\Models\ConferenceRegistration::where('is_presenting_paper', true)->count(),
                            'total_datican_members' => \App\Models\ConferenceRegistration::where('is_datican_member', true)->count(),
                        ];
                    @endphp
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-700 mb-3">Quick Stats</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <p class="text-xl font-bold text-blue-700">{{ $conferenceStats['total_presenters'] }}</p>
                                <p class="text-xs text-blue-600">Paper Presenters</p>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <p class="text-xl font-bold text-purple-700">{{ $conferenceStats['total_datican_members'] }}</p>
                                <p class="text-xs text-purple-600">DATICAN Members</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Paper Management -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Paper Management</h3>
                    <p class="text-sm text-gray-500 mt-1">Manage paper submissions and peer reviews (2 reviewers per paper)</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="{{ route('assignments.index') }}" 
                           class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <i class="fas fa-tasks text-blue-600 mr-3"></i>
                                <div>
                                    <span class="font-medium text-gray-700">Reviewer Assignments</span>
                                    <p class="text-sm text-gray-500">Assign 2 reviewers per paper</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        
                        <a href="{{ route('users.index') }}" 
                           class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <i class="fas fa-users-cog text-green-600 mr-3"></i>
                                <div>
                                    <span class="font-medium text-gray-700">User Management</span>
                                    <p class="text-sm text-gray-500">Manage system users</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        
                        <a href="{{ route('papers.index') }}" 
                           class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-purple-600 mr-3"></i>
                                <div>
                                    <span class="font-medium text-gray-700">View All Papers</span>
                                    <p class="text-sm text-gray-500">All paper submissions</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    </div>
                    
                    <!-- Quick Paper Stats -->
                    @php
                        $paperStats = [
                            'submitted' => \App\Models\Paper::where('status', 'submitted')->count(),
                            'under_review' => \App\Models\Paper::where('status', 'under_review')->count(),
                            'accepted' => \App\Models\Paper::where('status', 'accepted')->count(),
                            'rejected' => \App\Models\Paper::where('status', 'rejected')->count(),
                            'needs_revision' => \App\Models\Paper::where('status', 'needs_revision')->count(),
                        ];
                    @endphp
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-700 mb-3">Paper Status</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <p class="text-xl font-bold text-blue-700">{{ $paperStats['submitted'] }}</p>
                                <p class="text-xs text-blue-600">Submitted</p>
                            </div>
                            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                <p class="text-xl font-bold text-yellow-700">{{ $paperStats['under_review'] }}</p>
                                <p class="text-xs text-yellow-600">Under Review</p>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <p class="text-xl font-bold text-green-700">{{ $paperStats['accepted'] }}</p>
                                <p class="text-xs text-green-600">Accepted</p>
                            </div>
                            <div class="text-center p-3 bg-red-50 rounded-lg">
                                <p class="text-xl font-bold text-red-700">{{ $paperStats['rejected'] }}</p>
                                <p class="text-xs text-red-600">Rejected</p>
                            </div>
                            <div class="text-center p-3 bg-orange-50 rounded-lg">
                                <p class="text-xl font-bold text-orange-700">{{ $paperStats['needs_revision'] }}</p>
                                <p class="text-xs text-orange-600">Needs Revision</p>
                            </div>
                            <div class="text-center p-3 bg-indigo-50 rounded-lg">
                                <p class="text-xl font-bold text-indigo-700">{{ $bothReviewsDone }}</p>
                                <p class="text-xs text-indigo-600">Both Reviews Done</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Recent Registrations -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Conference Registrations</h3>
                        <a href="{{ route('admin.registrations') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            View all <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institution</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $recentRegistrations = \App\Models\ConferenceRegistration::latest()->take(5)->get();
                            @endphp
                            @forelse($recentRegistrations as $registration)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-medium text-sm">
                                                {{ substr($registration->firstname, 0, 1) }}{{ substr($registration->lastname, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $registration->firstname }} {{ $registration->lastname }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $registration->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ Str::limit($registration->institution, 25) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @if($registration->is_presenting_paper)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-file-alt mr-1"></i> Presenter
                                        </span>
                                        @endif
                                        @if($registration->is_datican_member)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-user-check mr-1"></i> DATICAN
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $registration->created_at->format('M d') }}
                                    <div class="text-xs text-gray-400">{{ $registration->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-users-slash text-3xl mb-3 text-gray-300"></i>
                                    <p>No registrations yet</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Recent Papers with Review Status -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Paper Submissions</h3>
                        <a href="{{ route('papers.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            View all <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviews</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $recentPapers = \App\Models\Paper::with(['authors', 'reviewAssignments'])->latest()->take(5)->get();
                            @endphp
                            @forelse($recentPapers as $paper)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $paper->anonymous_id }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if($paper->authors->count() > 0)
                                            {{ $paper->authors->first()->first_name }} {{ $paper->authors->first()->last_name }}
                                            @if($paper->authors->count() > 1)
                                                +{{ $paper->authors->count() - 1 }} more
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($paper->title, 40) }}</div>
                                    <div class="text-xs text-gray-500">{{ $paper->topic_area }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $completedReviews = $paper->reviewAssignments->where('status', 'completed')->count();
                                        $totalNeeded = 2;
                                        $progressColor = $completedReviews == 0 ? 'bg-red-100 text-red-800' : 
                                                       ($completedReviews == 1 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                    @endphp
                                    <div class="text-center">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $progressColor }}">
                                            {{ $completedReviews }}/{{ $totalNeeded }}
                                        </span>
                                        @if($completedReviews == 2)
                                        <div class="text-xs text-green-600 mt-1">Both done</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($paper->status == 'submitted') bg-blue-100 text-blue-800
                                        @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                                        @elseif($paper->status == 'accepted') bg-green-100 text-green-800
                                        @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                        @elseif($paper->status == 'needs_revision') bg-orange-100 text-orange-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $paper->created_at->format('M d') }}
                                    <div class="text-xs text-gray-400">{{ $paper->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-file-alt text-3xl mb-3 text-gray-300"></i>
                                    <p>No papers submitted yet</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Export Data</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.export.registrations') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200">
                    <i class="fas fa-users text-3xl text-blue-600 mb-3"></i>
                    <span class="font-medium text-blue-800">Export Registrations</span>
                    <span class="text-sm text-blue-600 mt-1">Conference attendees data</span>
                </a>
                
                <a href="{{ route('analytics.export', 'papers') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200">
                    <i class="fas fa-file-alt text-3xl text-green-600 mb-3"></i>
                    <span class="font-medium text-green-800">Export Papers</span>
                    <span class="text-sm text-green-600 mt-1">All paper submissions</span>
                </a>
                
                <a href="{{ route('analytics.export', 'reviews') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 border border-purple-200">
                    <i class="fas fa-clipboard-check text-3xl text-purple-600 mb-3"></i>
                    <span class="font-medium text-purple-800">Export Reviews</span>
                    <span class="text-sm text-purple-600 mt-1">All completed reviews</span>
                </a>
                
                <a href="{{ route('chair.export.reviews') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-indigo-50 rounded-lg hover:bg-indigo-100 border border-indigo-200">
                    <i class="fas fa-chart-line text-3xl text-indigo-600 mb-3"></i>
                    <span class="font-medium text-indigo-800">Export Peer Review</span>
                    <span class="text-sm text-indigo-600 mt-1">Reviewer comparison data</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh script -->
@section('scripts')
@if(auth()->user()->is_admin)
<script>
    // Auto-refresh dashboard every 60 seconds for admins
    setInterval(() => {
        window.location.reload();
    }, 60000);
</script>
@endif
@endsection
@endsection