@extends('layouts.app')

@section('title', 'Chair Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Conference Chair Dashboard</h1>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <!-- Conference Registrations -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['conference_registrations'] }}</p>
                        <p class="text-sm text-gray-500">Registrations</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('chair.registrations') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Papers Submitted -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['papers'] }}</p>
                        <p class="text-sm text-gray-500">Papers</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('chair.papers') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Reviews Completed -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['reviews_completed'] }}</p>
                        <p class="text-sm text-gray-500">Reviews Done</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('chair.reviews') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Peer Reviews -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['papers_with_both_reviews'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Both Reviews Done</p>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xs text-gray-500">Out of {{ $stats['papers_under_review'] ?? 0 }} under review</span>
                </div>
            </div>
            
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                        <p class="text-sm text-gray-500">System Users</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('users.index') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Export Actions -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Export Data</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('chair.export.registrations') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200 transition-all duration-300">
                    <i class="fas fa-users text-3xl text-blue-600 mb-3"></i>
                    <span class="font-medium text-blue-800">Export Registrations</span>
                    <span class="text-sm text-blue-600 mt-1">Download as CSV</span>
                </a>
                
                <a href="{{ route('analytics.export', 'papers') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200 transition-all duration-300">
                    <i class="fas fa-file-alt text-3xl text-green-600 mb-3"></i>
                    <span class="font-medium text-green-800">Export Papers</span>
                    <span class="text-sm text-green-600 mt-1">Download as CSV</span>
                </a>
                
                <a href="{{ route('chair.export.reviews') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 border border-purple-200 transition-all duration-300">
                    <i class="fas fa-clipboard-list text-3xl text-purple-600 mb-3"></i>
                    <span class="font-medium text-purple-800">Export Reviews</span>
                    <span class="text-sm text-purple-600 mt-1">Download as CSV</span>
                </a>
                
                <a href="{{ route('chair.export.peer', 'reviews') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-indigo-50 rounded-lg hover:bg-indigo-100 border border-indigo-200 transition-all duration-300">
                    <i class="fas fa-chart-line text-3xl text-indigo-600 mb-3"></i>
                    <span class="font-medium text-indigo-800">Export Peer Review</span>
                    <span class="text-sm text-indigo-600 mt-1">Both reviewers comparison</span>
                </a>
            </div>
        </div>
        
        <!-- Chair Actions -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Chair Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('assignments.index') }}" 
                class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200">
                    <i class="fas fa-user-plus text-3xl text-blue-600 mb-3"></i>
                    <span class="font-medium text-blue-800">Reviewer Assignments</span>
                    <span class="text-sm text-blue-600 mt-1">Assign 2 reviewers per paper</span>
                </a>
                
                <a href="{{ route('analytics.index') }}" 
                class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200">
                    <i class="fas fa-chart-bar text-3xl text-green-600 mb-3"></i>
                    <span class="font-medium text-green-800">Analytics</span>
                    <span class="text-sm text-green-600 mt-1">View statistics</span>
                </a>
                
                <a href="{{ route('chair.reviewers') }}" 
                class="flex flex-col items-center justify-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 border border-purple-200">
                    <i class="fas fa-users-cog text-3xl text-purple-600 mb-3"></i>
                    <span class="font-medium text-purple-800">Manage Reviewers</span>
                    <span class="text-sm text-purple-600 mt-1">View/Edit reviewers</span>
                </a>
                
                <a href="{{ route('chair.papers') }}" 
                class="flex flex-col items-center justify-center p-6 bg-yellow-50 rounded-lg hover:bg-yellow-100 border border-yellow-200">
                    <i class="fas fa-file-invoice text-3xl text-yellow-600 mb-3"></i>
                    <span class="font-medium text-yellow-800">All Papers</span>
                    <span class="text-sm text-yellow-600 mt-1">View all submissions</span>
                </a>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Papers Needing Decisions (both reviews completed) -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Ready for Decision</h2>
                    <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">{{ $pendingDecisions->count() }}</span>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($pendingDecisions as $paper)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $paper->anonymous_id }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ Str::limit($paper->title, 60) }}</p>
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="text-sm text-gray-500">
                                        {{ $paper->completed_assignments_count }}/{{ $paper->total_assignments }} reviews completed
                                    </span>
                                    @if($paper->average_score > 0)
                                    <span class="text-sm font-medium {{ $paper->average_score >= 3.5 ? 'text-green-600' : 'text-red-600' }}">
                                        Avg: {{ number_format($paper->average_score, 1) }}/5
                                    </span>
                                    @endif
                                    @if($paper->completed_assignments_count >= 2)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                        Both reviews done
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('chair.papers.decision.form', $paper) }}" 
                                   class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                    Decide
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-4"></i>
                        <p>No papers ready for decision.</p>
                        <p class="text-sm mt-1">Papers need both reviews completed.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Papers Needing Reviewers -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Papers Needing Reviewers</h2>
                    <span class="text-sm text-gray-500">Need 2 reviewers per paper</span>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($papersNeedingReviewers ?? [] as $paper)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $paper->anonymous_id }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ Str::limit($paper->title, 60) }}</p>
                                <div class="flex items-center space-x-3 mt-2">
                                    @php
                                        $activeReviews = $paper->reviewAssignments->whereIn('status', ['pending', 'under_review', 'in_progress'])->count();
                                    @endphp
                                    <span class="text-sm text-gray-500">
                                        {{ $activeReviews }}/2 reviewers assigned
                                    </span>
                                    @if($activeReviews == 0)
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                        No reviewers
                                    </span>
                                    @elseif($activeReviews == 1)
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                        Needs 1 more
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('assignments.assign', $paper) }}" 
                                   class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200">
                                    Assign
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-4"></i>
                        <p>All papers have assigned reviewers.</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t">
                    <a href="{{ route('assignments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Manage all assignments →
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Submissions -->
        <div class="mt-8 bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Recent Submissions</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentSubmissions as $paper)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $paper->anonymous_id }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ Str::limit($paper->title, 60) }}</p>
                            <div class="flex items-center space-x-3 mt-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($paper->status == 'submitted') bg-blue-100 text-blue-800
                                    @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                                    @elseif($paper->status == 'reviewed') bg-green-100 text-green-800
                                    @elseif($paper->status == 'accepted') bg-emerald-100 text-emerald-800
                                    @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $paper->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('papers.show', $paper) }}" 
                               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                View
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                    <p>No recent submissions.</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Reviewer Performance -->
        <div class="mt-8 bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Reviewer Performance</h2>
                <a href="{{ route('chair.reviewers') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reviewerPerformance as $reviewer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">
                                            {{ strtoupper(substr($reviewer->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $reviewer->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $reviewer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                                    {{ $reviewer->assigned_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">
                                    {{ $reviewer->completed_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reviewer->pending_count > 0)
                                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">
                                    {{ $reviewer->pending_count }}
                                </span>
                                @else
                                <span class="px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded-full">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($reviewer->avg_score)
                                {{ number_format($reviewer->avg_score, 1) }}/100
                                @else
                                N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('chair.reviews', ['reviewer_id' => $reviewer->id]) }}" 
                                    class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p>No reviewer data available.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Topics Distribution -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Papers by Topic</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($topicsDistribution as $topic)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $topic->name }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $topic->papers_count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     style="width: {{ ($topic->papers_count / max($stats['papers'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>No topic data available.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Peer Review Progress -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Peer Review Progress</h2>
                </div>
                <div class="p-6">
                    @php
                        $totalUnderReview = $stats['papers_under_review'] ?? 0;
                        $bothReviewsDone = $stats['papers_with_both_reviews'] ?? 0;
                        $oneReviewDone = $stats['papers_with_one_review'] ?? 0;
                        $noReviews = $stats['papers_with_no_reviews'] ?? 0;
                    @endphp
                    
                    @if($totalUnderReview > 0)
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-700">Both Reviews Completed</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bothReviewsDone }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" 
                                     style="width: {{ ($bothReviewsDone / $totalUnderReview) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-700">One Review Completed</span>
                                <span class="text-sm font-medium text-gray-900">{{ $oneReviewDone }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" 
                                     style="width: {{ ($oneReviewDone / $totalUnderReview) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-700">No Reviews Yet</span>
                                <span class="text-sm font-medium text-gray-900">{{ $noReviews }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" 
                                     style="width: {{ ($noReviews / $totalUnderReview) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">Total papers under review: <strong>{{ $totalUnderReview }}</strong></p>
                        <p class="text-xs text-gray-500 mt-1">Each paper requires 2 completed reviews</p>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No papers currently under review.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome for icons -->
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
@endsection