@extends('layouts.app')

@section('title', 'Analytics Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Analytics Dashboard</h1>
        <p class="text-gray-600 mb-8">Track submission and review performance metrics.</p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        @php
                            $totalPapers = \App\Models\Paper::where('conference_year', date('Y'))->count();
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">{{ $totalPapers }}</p>
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
                        @php
                            $completedReviews = \App\Models\ReviewAssignment::whereHas('paper', function($q) {
                                $q->where('conference_year', date('Y'));
                            })->where('status', 'completed')->count();
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">{{ $completedReviews }}</p>
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
                        @php
                            $activeReviewers = \App\Models\User::where('is_reviewer', true)
                                ->whereHas('reviewAssignments', function($q) {
                                    $q->whereHas('paper', function($q2) {
                                        $q2->where('conference_year', date('Y'));
                                    });
                                })->count();
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">{{ $activeReviewers }}</p>
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
                            $totalDecisions = \App\Models\Paper::where('conference_year', date('Y'))
                                ->whereIn('status', ['accepted', 'rejected'])->count();
                            $accepted = \App\Models\Paper::where('conference_year', date('Y'))
                                ->where('status', 'accepted')->count();
                            $rate = $totalDecisions > 0 ? round(($accepted / $totalDecisions) * 100, 1) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">{{ $rate }}%</p>
                        <p class="text-sm text-gray-500">Acceptance Rate</p>
                    </div>
                </div>
            </div>
            
            <!-- BOTH REVIEWS DONE STAT - HIGHLIGHTED -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-20 text-white mr-4">
                        <i class="fas fa-check-double text-xl"></i>
                    </div>
                    <div>
                        @php
                            $bothReviewsDone = \App\Models\Paper::where('conference_year', date('Y'))
                                ->where('status', 'under_review')
                                ->withCount(['reviewAssignments as completed_assignments' => function($query) {
                                    $query->where('status', 'completed');
                                }])
                                ->having('completed_assignments', '>=', 2)
                                ->count();
                        @endphp
                        <p class="text-2xl font-bold text-white">{{ $bothReviewsDone }}</p>
                        <p class="text-sm text-white text-opacity-90">Both Reviews Done</p>
                        <p class="text-xs text-white text-opacity-75 mt-1">≥2 completed reviews</p>
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
                            'submitted' => ['color' => 'bg-blue-500', 'label' => 'Submitted'],
                            'under_review' => ['color' => 'bg-yellow-500', 'label' => 'Under Review'],
                            'accepted' => ['color' => 'bg-green-500', 'label' => 'Accepted'],
                            'rejected' => ['color' => 'bg-red-500', 'label' => 'Rejected'],
                            'needs_revision' => ['color' => 'bg-orange-500', 'label' => 'Needs Revision'],
                        ];
                        
                        $statusCounts = [];
                        $total = 0;
                        foreach($statuses as $key => $status) {
                            $count = \App\Models\Paper::where('conference_year', date('Y'))
                                ->where('status', $key)->count();
                            $statusCounts[$key] = $count;
                            $total += $count;
                        }
                    @endphp
                    
                    @foreach($statuses as $key => $status)
                    @if($statusCounts[$key] > 0 || $total > 0)
                    <div>
                        <div class="flex justify-between mb-1">
                            <div class="flex items-center">
                                <div class="w-3 h-3 {{ $status['color'] }} rounded-full mr-2"></div>
                                <span class="text-sm font-medium text-gray-700">{{ $status['label'] }}</span>
                            </div>
                            <div class="text-sm text-gray-700">
                                {{ $statusCounts[$key] }} ({{ $total > 0 ? round(($statusCounts[$key] / $total) * 100, 1) : 0 }}%)
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $status['color'] }}" 
                                 style="width: {{ $total > 0 ? ($statusCounts[$key] / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                
                <!-- Both Reviews Done Detail -->
                @php
                    $papersUnderReview = \App\Models\Paper::where('conference_year', date('Y'))
                        ->where('status', 'under_review')
                        ->withCount(['reviewAssignments as completed_assignments' => function($q) {
                            $q->where('status', 'completed');
                        }])
                        ->get();
                    
                    $bothReviews = $papersUnderReview->filter(fn($p) => $p->completed_assignments >= 2)->count();
                    $oneReview = $papersUnderReview->filter(fn($p) => $p->completed_assignments == 1)->count();
                    $noReviews = $papersUnderReview->filter(fn($p) => $p->completed_assignments == 0)->count();
                    $totalUnderReview = $papersUnderReview->count();
                @endphp
                
                <div class="mt-4 pt-4 border-t">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Peer Review Progress (Under Review Papers)</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-2 bg-green-50 rounded-lg">
                            <p class="text-lg font-bold text-green-600">{{ $bothReviews }}</p>
                            <p class="text-xs text-green-600">Both Done (≥2)</p>
                        </div>
                        <div class="text-center p-2 bg-yellow-50 rounded-lg">
                            <p class="text-lg font-bold text-yellow-600">{{ $oneReview }}</p>
                            <p class="text-xs text-yellow-600">One Review</p>
                        </div>
                        <div class="text-center p-2 bg-red-50 rounded-lg">
                            <p class="text-lg font-bold text-red-600">{{ $noReviews }}</p>
                            <p class="text-xs text-red-600">No Reviews</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 text-center mt-2">Total under review: {{ $totalUnderReview }}</p>
                </div>
            </div>
            
            <!-- Review Assignment Status -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Review Assignment Status</h2>
                <div class="space-y-4">
                    @php
                        $reviewStatuses = [
                            'pending' => ['color' => 'bg-yellow-500', 'count' => \App\Models\ReviewAssignment::whereHas('paper', function($q) {
                                $q->where('conference_year', date('Y'));
                            })->where('status', 'pending')->count()],
                            'in_progress' => ['color' => 'bg-blue-500', 'count' => \App\Models\ReviewAssignment::whereHas('paper', function($q) {
                                $q->where('conference_year', date('Y'));
                            })->whereIn('status', ['accepted', 'in_progress'])->count()],
                            'completed' => ['color' => 'bg-green-500', 'count' => \App\Models\ReviewAssignment::whereHas('paper', function($q) {
                                $q->where('conference_year', date('Y'));
                            })->where('status', 'completed')->count()],
                            'declined' => ['color' => 'bg-red-500', 'count' => \App\Models\ReviewAssignment::whereHas('paper', function($q) {
                                $q->where('conference_year', date('Y'));
                            })->where('status', 'declined')->count()],
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
                                    $papersWithReviews = \App\Models\Paper::where('conference_year', date('Y'))
                                        ->has('reviewAssignments')->count();
                                    $totalReviews = \App\Models\ReviewAssignment::whereHas('paper', function($q) {
                                        $q->where('conference_year', date('Y'));
                                    })->count();
                                    $avg = $papersWithReviews > 0 ? round($totalReviews / $papersWithReviews, 1) : 0;
                                @endphp
                                {{ $avg }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Average Score</p>
                            <p class="text-lg font-bold text-gray-900">
                                @php
                                    $completedReviewsList = \App\Models\ReviewAssignment::whereHas('paper', function($q) {
                                        $q->where('conference_year', date('Y'));
                                    })->where('status', 'completed')->get();
                                    
                                    $totalScore = 0;
                                    foreach($completedReviewsList as $review) {
                                        $totalScore += ($review->criteria_relevance ?? 0) + 
                                                      ($review->criteria_originality ?? 0) + 
                                                      ($review->criteria_quality ?? 0) + 
                                                      ($review->criteria_impact ?? 0) + 
                                                      ($review->criteria_clarity ?? 0) + 
                                                      ($review->criteria_contribution ?? 0);
                                    }
                                    $avgScore = $completedReviewsList->count() > 0 ? round($totalScore / $completedReviewsList->count(), 1) : 0;
                                @endphp
                                {{ $avgScore }}/100
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ready for Decision Section (Papers needing decisions) -->
        @php
            $readyForDecision = \App\Models\Paper::where('conference_year', date('Y'))
                ->where('status', 'under_review')
                ->withCount(['reviewAssignments as total_assignments'])
                ->withCount(['reviewAssignments as completed_assignments' => function($query) {
                    $query->where('status', 'completed');
                }])
                ->having('total_assignments', '>', 0)
                ->having('completed_assignments', '>=', 2)
                ->with(['authors', 'reviewAssignments' => function($q) {
                    $q->where('status', 'completed');
                }])
                ->get();
        @endphp
        
        @if($readyForDecision->count() > 0)
        <div class="bg-white rounded-xl shadow mb-8">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Ready for Decision</h2>
                    <p class="text-sm text-gray-500 mt-1">Papers with at least 2 completed reviews awaiting chair decision</p>
                </div>
                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">{{ $readyForDecision->count() }}</span>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($readyForDecision as $paper)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="font-medium text-gray-900">{{ $paper->anonymous_id }}</h3>
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                    {{ $paper->completed_assignments }}/{{ $paper->total_assignments }} reviews
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 truncate mt-1">{{ Str::limit($paper->title, 80) }}</p>
                            <div class="flex items-center space-x-3 mt-2">
                                @php
                                    $totalScore = 0;
                                    foreach($paper->reviewAssignments as $review) {
                                        $totalScore += ($review->criteria_relevance ?? 0) + 
                                                      ($review->criteria_originality ?? 0) + 
                                                      ($review->criteria_quality ?? 0) + 
                                                      ($review->criteria_impact ?? 0) + 
                                                      ($review->criteria_clarity ?? 0) + 
                                                      ($review->criteria_contribution ?? 0);
                                    }
                                    $avgScore = $paper->reviewAssignments->count() > 0 ? round($totalScore / $paper->reviewAssignments->count(), 1) : 0;
                                @endphp
                                @if($avgScore > 0)
                                <span class="text-sm font-medium {{ $avgScore >= 70 ? 'text-green-600' : ($avgScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    Avg Score: {{ $avgScore }}/100
                                </span>
                                @endif
                                <span class="text-xs text-gray-400">
                                    Submitted: {{ $paper->submitted_at?->format('M d, Y') ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('chair.papers.decision.form', $paper) }}" 
                               class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Make Decision
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t">
                <a href="{{ route('chair.papers') }}?status=under_review" class="text-sm text-blue-600 hover:text-blue-800">
                    View all papers needing decisions →
                </a>
            </div>
        </div>
        @endif
        
        <!-- Recent Activity & Top Reviewers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Activity</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @php
                        $recentPapers = \App\Models\Paper::where('conference_year', date('Y'))
                            ->with('authors')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($recentPapers as $paper)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-medium text-gray-900">{{ $paper->anonymous_id }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($paper->status == 'submitted') bg-blue-100 text-blue-800
                                        @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                                        @elseif($paper->status == 'accepted') bg-green-100 text-green-800
                                        @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                        @elseif($paper->status == 'needs_revision') bg-orange-100 text-orange-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 truncate mt-1">{{ Str::limit($paper->title, 60) }}</p>
                                @if($paper->authors->count() > 0)
                                <p class="text-xs text-gray-500 mt-1">
                                    Authors: {{ $paper->authors->map(function($author) {
                                        return trim($author->first_name . ' ' . $author->last_name);
                                    })->filter()->join(', ') }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">{{ $paper->created_at->diffForHumans() }}</span>
                                @php
                                    $reviewCount = $paper->reviewAssignments->where('status', 'completed')->count();
                                @endphp
                                @if($reviewCount > 0)
                                <div class="text-xs text-green-600 mt-1">{{ $reviewCount }} review(s)</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-file-alt text-3xl mb-2 text-gray-300"></i>
                        <p>No recent papers</p>
                    </div>
                    @endforelse
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
                                $query->where('status', 'completed')
                                    ->whereHas('paper', function($q) {
                                        $q->where('conference_year', date('Y'));
                                    });
                            }])
                            ->having('completed_reviews_count', '>', 0)
                            ->orderBy('completed_reviews_count', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @forelse($topReviewers as $reviewer)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium text-sm">
                                        {{ strtoupper(substr($reviewer->first_name ?? 'U', 0, 1) . substr($reviewer->last_name ?? 'N', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ trim(($reviewer->first_name ?? '') . ' ' . ($reviewer->last_name ?? '')) }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $reviewer->email }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">{{ $reviewer->completed_reviews_count }}</div>
                                <div class="text-xs text-gray-500">reviews</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-users text-3xl mb-2 text-gray-300"></i>
                        <p>No reviewers yet</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t">
                    <a href="{{ route('chair.reviewers') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        View all reviewer statistics →
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="mt-8 bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Export Data</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('analytics.export', 'papers') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200 transition">
                    <i class="fas fa-file-alt text-3xl text-blue-600 mb-3"></i>
                    <span class="font-medium text-blue-800">Export Papers</span>
                    <span class="text-sm text-blue-600 mt-1">All paper submissions</span>
                </a>
                
                <a href="{{ route('analytics.export', 'reviews') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200 transition">
                    <i class="fas fa-clipboard-check text-3xl text-green-600 mb-3"></i>
                    <span class="font-medium text-green-800">Export Reviews</span>
                    <span class="text-sm text-green-600 mt-1">All completed reviews</span>
                </a>
                
                <a href="{{ route('analytics.export', 'authors') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 border border-purple-200 transition">
                    <i class="fas fa-user-edit text-3xl text-purple-600 mb-3"></i>
                    <span class="font-medium text-purple-800">Export Authors</span>
                    <span class="text-sm text-purple-600 mt-1">All authors</span>
                </a>
                
                <a href="{{ route('analytics.export', 'statistics') }}" 
                   class="flex flex-col items-center justify-center p-6 bg-indigo-50 rounded-lg hover:bg-indigo-100 border border-indigo-200 transition">
                    <i class="fas fa-chart-line text-3xl text-indigo-600 mb-3"></i>
                    <span class="font-medium text-indigo-800">Export Statistics</span>
                    <span class="text-sm text-indigo-600 mt-1">Summary report</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection