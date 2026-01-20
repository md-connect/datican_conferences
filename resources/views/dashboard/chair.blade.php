@extends('layouts.app')

@section('title', 'Chair Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Conference Chair Dashboard</h1>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['papers'] }}</p>
                        <p class="text-sm text-gray-500">Papers Submitted</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['reviewers'] }}</p>
                        <p class="text-sm text-gray-500">Active Reviewers</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_reviews'] }}</p>
                        <p class="text-sm text-gray-500">Pending Reviews</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-percentage text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['acceptance_rate'] }}%</p>
                        <p class="text-sm text-gray-500">Acceptance Rate</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Replace the Quick Actions section with this: -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Chair Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('assignments.index') }}" 
                class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200">
                    <i class="fas fa-user-plus text-3xl text-blue-600 mb-3"></i>
                    <span class="font-medium text-blue-800">Reviewer Assignments</span>
                    <span class="text-sm text-blue-600 mt-1">Manage assignments</span>
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
            <!-- Papers Needing Attention -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Papers Needing Decisions</h2>
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
                        <p>No papers pending decisions.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Recent Submissions -->
            <div class="bg-white rounded-xl shadow">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reviewer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assigned
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Completed
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pending
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avg. Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
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
                                <span class="px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded-full">
                                    0
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($reviewer->avg_review_time)
                                {{ $reviewer->avg_review_time }} days
                                @else
                                N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('users.show', $reviewer) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('chair.reviews', ['reviewer_id' => $reviewer->id]) }}" 
                                    class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-tasks"></i>
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
            
            <!-- Deadlines -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Important Deadlines</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($deadlines as $deadline)
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $deadline->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $deadline->description }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium 
                                    @if($deadline->is_past) text-red-600
                                    @elseif($deadline->is_approaching) text-yellow-600
                                    @else text-green-600
                                    @endif">
                                    {{ $deadline->date->format('M d, Y') }}
                                </div>
                                @if($deadline->is_approaching && !$deadline->is_past)
                                <div class="text-xs text-yellow-600 mt-1">
                                    {{ $deadline->days_left }} days left
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                        <p>No deadlines set.</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t">
                    <a href="{{ route('settings.deadlines') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Manage Deadlines
                    </a>
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