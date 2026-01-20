@extends('layouts.app')

@section('title', 'Reviewer Analytics - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reviewer Performance</h1>
                <p class="text-gray-600 mt-1">Track and analyze reviewer performance metrics</p>
            </div>
            <a href="{{ route('analytics.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Back to Analytics
            </a>
        </div>
        
        <!-- Reviewer Performance Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden mb-8">
            <div class="px-6 py-4 border-b bg-gray-50">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Reviewer Performance Metrics</h2>
                    <span class="text-sm text-gray-500">Sorted by completed reviews</span>
                </div>
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
                                In Progress
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pending
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avg. Score
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avg. Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Completion Rate
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $reviewers = \App\Models\User::where('is_reviewer', true)
                                ->withCount(['reviewAssignments as assigned_count' => function($query) {
                                    $query->where('status', '!=', 'declined');
                                }])
                                ->withCount(['reviewAssignments as completed_count' => function($query) {
                                    $query->where('status', 'completed');
                                }])
                                ->withCount(['reviewAssignments as in_progress_count' => function($query) {
                                    $query->where('status', 'in_progress');
                                }])
                                ->withCount(['reviewAssignments as pending_count' => function($query) {
                                    $query->where('status', 'pending');
                                }])
                                ->orderBy('completed_count', 'desc')
                                ->get();
                        @endphp
                        
                        @foreach($reviewers as $reviewer)
                        @php
                            $completionRate = $reviewer->assigned_count > 0 
                                ? round(($reviewer->completed_count / $reviewer->assigned_count) * 100, 1) 
                                : 0;
                            
                            // Get average score for completed reviews
                            $avgScore = \App\Models\Review::where('reviewer_id', $reviewer->id)->avg('score');
                            $avgScore = $avgScore ? round($avgScore, 1) : 'N/A';
                            
                            // Calculate average review time (in days)
                            $avgTime = \App\Models\Review::where('reviewer_id', $reviewer->id)
                                ->selectRaw('AVG(DATEDIFF(completed_at, assigned_at)) as avg_days')
                                ->first();
                            $avgTimeDays = $avgTime->avg_days ? round($avgTime->avg_days, 1) : 'N/A';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">
                                            {{ strtoupper(substr($reviewer->first_name, 0, 1) . substr($reviewer->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $reviewer->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $reviewer->affiliation }}</div>
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
                                <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full">
                                    {{ $reviewer->in_progress_count }}
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">
                                    @if($avgScore !== 'N/A')
                                    <span class="{{ $avgScore >= 3.5 ? 'text-green-600' : ($avgScore >= 2.5 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $avgScore }}/5
                                    </span>
                                    @else
                                    N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($avgTimeDays !== 'N/A')
                                {{ $avgTimeDays }} days
                                @else
                                N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="h-2 rounded-full 
                                            @if($completionRate >= 80) bg-green-500
                                            @elseif($completionRate >= 60) bg-yellow-500
                                            @else bg-red-500
                                            @endif" 
                                             style="width: {{ min($completionRate, 100) }}%">
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $completionRate }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-tachometer-alt text-3xl text-blue-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
                            $avgCompletion = $reviewers->avg('completed_count');
                        @endphp
                        {{ round($avgCompletion, 1) }}
                    </p>
                    <p class="text-sm text-gray-500">Average Reviews per Reviewer</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-clock text-3xl text-green-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
                            // Calculate overall average review time
                            $totalReviews = \App\Models\Review::count();
                            $totalTime = \App\Models\Review::selectRaw('SUM(DATEDIFF(completed_at, assigned_at)) as total_days')
                                ->first();
                            $overallAvgTime = $totalReviews > 0 ? round(($totalTime->total_days ?? 0) / $totalReviews, 1) : 'N/A';
                        @endphp
                        {{ $overallAvgTime !== 'N/A' ? $overallAvgTime . ' days' : 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-500">Average Review Time</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-check-circle text-3xl text-purple-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
                            $totalAssigned = $reviewers->sum('assigned_count');
                            $totalCompleted = $reviewers->sum('completed_count');
                            $overallCompletionRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 1) : 0;
                        @endphp
                        {{ $overallCompletionRate }}%
                    </p>
                    <p class="text-sm text-gray-500">Overall Completion Rate</p>
                </div>
            </div>
        </div>
        
        <!-- Review Distribution -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Review Distribution</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Review Load Distribution</h3>
                    @php
                        $loadCategories = [
                            '0 reviews' => $reviewers->where('assigned_count', 0)->count(),
                            '1-3 reviews' => $reviewers->whereBetween('assigned_count', [1, 3])->count(),
                            '4-6 reviews' => $reviewers->whereBetween('assigned_count', [4, 6])->count(),
                            '7+ reviews' => $reviewers->where('assigned_count', '>=', 7)->count(),
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($loadCategories as $category => $count)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-700">{{ $category }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $count }} reviewers</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-blue-500" 
                                     style="width: {{ $reviewers->count() > 0 ? ($count / $reviewers->count()) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Performance Categories</h3>
                    @php
                        $performanceCategories = [
                            'High Performers (>80%)' => $reviewers->where(function($reviewer) {
                                $completion = $reviewer->assigned_count > 0 
                                    ? ($reviewer->completed_count / $reviewer->assigned_count) * 100 
                                    : 0;
                                return $completion >= 80;
                            })->count(),
                            'Medium Performers (60-79%)' => $reviewers->where(function($reviewer) {
                                $completion = $reviewer->assigned_count > 0 
                                    ? ($reviewer->completed_count / $reviewer->assigned_count) * 100 
                                    : 0;
                                return $completion >= 60 && $completion < 80;
                            })->count(),
                            'Low Performers (<60%)' => $reviewers->where(function($reviewer) {
                                $completion = $reviewer->assigned_count > 0 
                                    ? ($reviewer->completed_count / $reviewer->assigned_count) * 100 
                                    : 0;
                                return $completion < 60;
                            })->count(),
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($performanceCategories as $category => $count)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-700">{{ $category }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $count }} reviewers</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full 
                                    @if(str_contains($category, 'High')) bg-green-500
                                    @elseif(str_contains($category, 'Medium')) bg-yellow-500
                                    @else bg-red-500
                                    @endif" 
                                     style="width: {{ $reviewers->count() > 0 ? ($count / $reviewers->count()) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection