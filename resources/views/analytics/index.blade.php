@extends('layouts.app')

@section('title', 'Analytics Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Analytics Dashboard</h1>
        <p class="text-gray-600 mb-8">Track submission and review performance metrics.</p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Paper::count() }}</p>
                        <p class="text-sm text-gray-500">Total Papers</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\ReviewAssignment::count() }}</p>
                        <p class="text-sm text-gray-500">Reviews Completed</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('is_reviewer', true)->count() }}</p>
                        <p class="text-sm text-gray-500">Active Reviewers</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-percentage text-xl"></i>
                    </div>
                    <div>
                        @php
                            $accepted = \App\Models\Paper::where('status', 'accepted')->count();
                            $reviewed = \App\Models\Paper::where('status', 'accepted')->orWhere('status', 'rejected')->count();
                            $rate = $reviewed > 0 ? round(($accepted / $reviewed) * 100, 1) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">{{ $rate }}%</p>
                        <p class="text-sm text-gray-500">Acceptance Rate</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Paper Status Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Paper Status Chart -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Paper Status Distribution</h2>
                <div class="space-y-4">
                    @php
                        $statuses = [
                            'draft' => ['color' => 'bg-gray-500', 'count' => \App\Models\Paper::where('status', 'draft')->count()],
                            'submitted' => ['color' => 'bg-blue-500', 'count' => \App\Models\Paper::where('status', 'submitted')->count()],
                            'under_review' => ['color' => 'bg-yellow-500', 'count' => \App\Models\Paper::where('status', 'under_review')->count()],
                            'accepted' => ['color' => 'bg-green-500', 'count' => \App\Models\Paper::where('status', 'accepted')->count()],
                            'rejected' => ['color' => 'bg-red-500', 'count' => \App\Models\Paper::where('status', 'rejected')->count()],
                            'camera_ready' => ['color' => 'bg-purple-500', 'count' => \App\Models\Paper::where('status', 'camera_ready')->count()],
                        ];
                        $total = array_sum(array_column($statuses, 'count'));
                    @endphp
                    
                    @foreach($statuses as $status => $data)
                    @if($data['count'] > 0)
                    <div>
                        <div class="flex justify-between mb-1">
                            <div class="flex items-center">
                                <div class="w-3 h-3 {{ $data['color'] }} rounded-full mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                            </div>
                            <div class="text-sm text-gray-700">
                                {{ $data['count'] }} ({{ $total > 0 ? round(($data['count'] / $total) * 100, 1) : 0 }}%)
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $data['color'] }}" 
                                 style="width: {{ $total > 0 ? ($data['count'] / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            
            <!-- Review Status Chart -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Review Assignment Status</h2>
                <div class="space-y-4">
                    @php
                        $reviewStatuses = [
                            'pending' => ['color' => 'bg-yellow-500', 'count' => \App\Models\ReviewAssignment::where('status', 'pending')->count()],
                            'in_progress' => ['color' => 'bg-blue-500', 'count' => \App\Models\ReviewAssignment::where('status', 'in_progress')->count()],
                            'completed' => ['color' => 'bg-green-500', 'count' => \App\Models\ReviewAssignment::where('status', 'completed')->count()],
                            'declined' => ['color' => 'bg-red-500', 'count' => \App\Models\ReviewAssignment::where('status', 'declined')->count()],
                        ];
                        $reviewTotal = array_sum(array_column($reviewStatuses, 'count'));
                    @endphp
                    
                    @foreach($reviewStatuses as $status => $data)
                    <div>
                        <div class="flex justify-between mb-1">
                            <div class="flex items-center">
                                <div class="w-3 h-3 {{ $data['color'] }} rounded-full mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                            </div>
                            <div class="text-sm text-gray-700">
                                {{ $data['count'] }} ({{ $reviewTotal > 0 ? round(($data['count'] / $reviewTotal) * 100, 1) : 0 }}%)
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $data['color'] }}" 
                                 style="width: {{ $reviewTotal > 0 ? ($data['count'] / $reviewTotal) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-6 pt-6 border-t">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Average Reviews per Paper</p>
                            <p class="text-lg font-bold text-gray-900">
                                @php
                                    $papersWithReviews = \App\Models\Paper::has('reviews')->count();
                                    $totalReviews = \App\Models\ReviewAssignment::count();
                                    $avg = $papersWithReviews > 0 ? round($totalReviews / $papersWithReviews, 1) : 0;
                                @endphp
                                {{ $avg }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Average Score</p>
                            <p class="text-lg font-bold text-gray-900">
                                @php
                                    $avgScore = \App\Models\ReviewAssignment::avg('overall_score') ?? 0;
                                @endphp
                                {{ round($avgScore, 1) }}/5
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity & Top Reviewers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Activity</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @php
                        // FIXED: Changed from 'user' to 'authors' relationship
                        $recentPapers = \App\Models\Paper::with('authors')->latest()->take(5)->get();
                    @endphp
                    @foreach($recentPapers as $paper)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $paper->anonymous_id }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ Str::limit($paper->title, 50) }}</p>
                                <div class="flex items-center space-x-2 mt-2">
                                    @if($paper->authors->count() > 0)
                                        <span class="text-sm text-gray-500">
                                            Authors: {{ $paper->authors->map(function($author) {
                                                return trim($author->first_name . ' ' . $author->last_name);
                                            })->filter()->join(', ') }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">No authors assigned</span>
                                    @endif
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($paper->status == 'submitted') bg-blue-100 text-blue-800
                                        @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                                        @elseif($paper->status == 'accepted') bg-green-100 text-green-800
                                        @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">{{ $paper->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Top Reviewers -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Top Reviewers</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @php
                        $topReviewers = \App\Models\User::where('is_reviewer', true)
                            ->withCount(['reviewAssignments as completed_reviews_count' => function($query) {
                                $query->where('status', 'completed');
                            }])
                            ->orderBy('completed_reviews_count', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @foreach($topReviewers as $reviewer)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium">
                                        {{ strtoupper(substr($reviewer->first_name, 0, 1) . substr($reviewer->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ trim($reviewer->first_name . ' ' . $reviewer->last_name) }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $reviewer->affiliation ?? 'No affiliation' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">{{ $reviewer->completed_reviews_count }}</div>
                                <div class="text-xs text-gray-500">reviews</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t">
                    <a href="{{ route('analytics.reviewers') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        View all reviewer statistics →
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="mt-8 bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Export Data</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('analytics.export', 'papers') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200">
                    <i class="fas fa-file-alt text-3xl text-blue-600 mb-3"></i>
                    <span class="font-medium text-blue-800">Export Papers</span>
                    <span class="text-sm text-blue-600 mt-1">All paper submissions</span>
                </a>
                
                <a href="{{ route('analytics.export', 'reviews') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200">
                    <i class="fas fa-clipboard-check text-3xl text-green-600 mb-3"></i>
                    <span class="font-medium text-green-800">Export Reviews</span>
                    <span class="text-sm text-green-600 mt-1">All completed reviews</span>
                </a>
                
                <a href="{{ route('analytics.export', 'users') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 border border-purple-200">
                    <i class="fas fa-users text-3xl text-purple-600 mb-3"></i>
                    <span class="font-medium text-purple-800">Export Users</span>
                    <span class="text-sm text-purple-600 mt-1">All system users</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection