@extends('layouts.app')

@section('title', 'Review - ' . $review->paper->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Review Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mb-2">
                        {{ $review->paper->anonymous_id }}
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900">Review Details</h1>
                    <p class="text-gray-600">Submitted by {{ $review->reviewer->full_name ?? 'Unknown Reviewer' }}</p>
                </div>
                <div class="text-right">
                    @php
                        $totalScore = ($review->criteria_relevance ?? 0) + 
                                      ($review->criteria_originality ?? 0) + 
                                      ($review->criteria_quality ?? 0) + 
                                      ($review->criteria_impact ?? 0) + 
                                      ($review->criteria_clarity ?? 0) + 
                                      ($review->criteria_contribution ?? 0);
                    @endphp
                    <div class="text-3xl font-bold 
                        {{ $totalScore >= 80 ? 'text-green-600' : ($totalScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $totalScore }}/100
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        @php
                            $recommendationTexts = [
                                'strong_accept' => 'Strong Accept',
                                'accept' => 'Accept',
                                'weak_accept' => 'Weak Accept',
                                'borderline' => 'Borderline',
                                'weak_reject' => 'Weak Reject',
                                'reject' => 'Reject',
                                'strong_reject' => 'Strong Reject',
                                'minor_revisions' => 'Minor Revisions Required',
                                'major_revisions' => 'Major Revisions Required',
                            ];
                            echo $recommendationTexts[$review->recommendation] ?? 'No recommendation';
                        @endphp
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Paper</p>
                    <p class="font-medium">{{ Str::limit($review->paper->title, 40) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Submitted</p>
                    <p class="font-medium">{{ $review->submitted_at ? $review->submitted_at->format('F d, Y H:i') : 'Not submitted' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Reviewer Confidence</p>
                    <p class="font-medium">{{ $review->confidence ? ucfirst($review->confidence) : 'Not specified' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'under_review' => 'bg-blue-100 text-blue-800',
                            'in_progress' => 'bg-indigo-100 text-indigo-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'declined' => 'bg-red-100 text-red-800',
                        ];
                        $colorClass = $statusColors[$review->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                        {{ ucfirst(str_replace('_', ' ', $review->status)) }}
                    </span>
                </div>
            </div>
            
            @if($review->deadline)
            <div class="mt-4 pt-4 border-t">
                <div class="flex items-center">
                    <p class="text-gray-500 mr-4">Deadline:</p>
                    <p class="font-medium {{ $review->deadline < now() && $review->status !== 'completed' ? 'text-red-600' : '' }}">
                        {{ $review->deadline->format('F d, Y') }}
                        @if($review->deadline < now() && $review->status !== 'completed')
                            <span class="text-sm">(Overdue by {{ $review->deadline->diffForHumans(null, false, false, 2) }})</span>
                        @endif
                    </p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Scoring Criteria Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Scoring Criteria (100 Points Total)</h3>
            
            <div class="space-y-6">
                <!-- Relevance to Conference Theme (20) -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <span class="font-medium text-gray-800">1. Relevance to Conference Theme</span>
                            <span class="text-sm text-gray-500 ml-2">(20 points max)</span>
                        </div>
                        <span class="text-lg font-bold 
                            {{ ($review->criteria_relevance ?? 0) >= 17 ? 'text-green-600' : 
                               (($review->criteria_relevance ?? 0) >= 11 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $review->criteria_relevance ?? 'N/A' }}/20
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            {{ ($review->criteria_relevance ?? 0) >= 17 ? 'bg-green-600' : 
                               (($review->criteria_relevance ?? 0) >= 11 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                            style="width: {{ (($review->criteria_relevance ?? 0) / 20) * 100 }}%">
                        </div>
                    </div>
                    @if($review->criteria_relevance)
                    <p class="text-xs text-gray-500 mt-1">
                        @if($review->criteria_relevance >= 17) Uses medical images and data science
                        @elseif($review->criteria_relevance >= 11) Non-images but medical and data science
                        @elseif($review->criteria_relevance >= 6) Medical but no data science OR data science without medicine
                        @else Not relevant
                        @endif
                    </p>
                    @endif
                </div>
                
                <!-- Originality & Innovation (20) -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <span class="font-medium text-gray-800">2. Originality & Innovation</span>
                            <span class="text-sm text-gray-500 ml-2">(20 points max)</span>
                        </div>
                        <span class="text-lg font-bold 
                            {{ ($review->criteria_originality ?? 0) >= 16 ? 'text-green-600' : 
                               (($review->criteria_originality ?? 0) >= 11 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $review->criteria_originality ?? 'N/A' }}/20
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            {{ ($review->criteria_originality ?? 0) >= 16 ? 'bg-green-600' : 
                               (($review->criteria_originality ?? 0) >= 11 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                            style="width: {{ (($review->criteria_originality ?? 0) / 20) * 100 }}%">
                        </div>
                    </div>
                    @if($review->criteria_originality)
                    <p class="text-xs text-gray-500 mt-1">
                        @if($review->criteria_originality >= 16) Highly original
                        @elseif($review->criteria_originality >= 11) Some originality
                        @elseif($review->criteria_originality >= 6) Limited originality
                        @else No originality
                        @endif
                    </p>
                    @endif
                </div>
                
                <!-- Technical/Academic Quality (15) -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <span class="font-medium text-gray-800">3. Technical/Academic Quality</span>
                            <span class="text-sm text-gray-500 ml-2">(15 points max)</span>
                        </div>
                        <span class="text-lg font-bold 
                            {{ ($review->criteria_quality ?? 0) >= 12 ? 'text-green-600' : 
                               (($review->criteria_quality ?? 0) >= 8 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $review->criteria_quality ?? 'N/A' }}/15
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            {{ ($review->criteria_quality ?? 0) >= 12 ? 'bg-green-600' : 
                               (($review->criteria_quality ?? 0) >= 8 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                            style="width: {{ (($review->criteria_quality ?? 0) / 15) * 100 }}%">
                        </div>
                    </div>
                    @if($review->criteria_quality)
                    <p class="text-xs text-gray-500 mt-1">
                        @if($review->criteria_quality >= 12) Excellent rigor and depth
                        @elseif($review->criteria_quality >= 8) Good quality
                        @elseif($review->criteria_quality >= 4) Fair quality
                        @else Weak or flawed
                        @endif
                    </p>
                    @endif
                </div>
                
                <!-- Practical Impact & Applicability (15) -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <span class="font-medium text-gray-800">4. Practical Impact & Applicability</span>
                            <span class="text-sm text-gray-500 ml-2">(15 points max)</span>
                        </div>
                        <span class="text-lg font-bold 
                            {{ ($review->criteria_impact ?? 0) >= 12 ? 'text-green-600' : 
                               (($review->criteria_impact ?? 0) >= 8 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $review->criteria_impact ?? 'N/A' }}/15
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            {{ ($review->criteria_impact ?? 0) >= 12 ? 'bg-green-600' : 
                               (($review->criteria_impact ?? 0) >= 8 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                            style="width: {{ (($review->criteria_impact ?? 0) / 15) * 100 }}%">
                        </div>
                    </div>
                    @if($review->criteria_impact)
                    <p class="text-xs text-gray-500 mt-1">
                        @if($review->criteria_impact >= 12) Highly impactful with great adoption potential
                        @elseif($review->criteria_impact >= 8) Moderately useful
                        @elseif($review->criteria_impact >= 4) Limited impact
                        @else No clear application
                        @endif
                    </p>
                    @endif
                </div>
                
                <!-- Clarity & Organization (15) -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <span class="font-medium text-gray-800">5. Clarity & Organization</span>
                            <span class="text-sm text-gray-500 ml-2">(15 points max)</span>
                        </div>
                        <span class="text-lg font-bold 
                            {{ ($review->criteria_clarity ?? 0) >= 12 ? 'text-green-600' : 
                               (($review->criteria_clarity ?? 0) >= 8 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $review->criteria_clarity ?? 'N/A' }}/15
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            {{ ($review->criteria_clarity ?? 0) >= 12 ? 'bg-green-600' : 
                               (($review->criteria_clarity ?? 0) >= 8 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                            style="width: {{ (($review->criteria_clarity ?? 0) / 15) * 100 }}%">
                        </div>
                    </div>
                    @if($review->criteria_clarity)
                    <p class="text-xs text-gray-500 mt-1">
                        @if($review->criteria_clarity >= 12) Very clear and well-structured
                        @elseif($review->criteria_clarity >= 8) Generally clear
                        @elseif($review->criteria_clarity >= 5) Somewhat unclear
                        @else Poorly organized
                        @endif
                    </p>
                    @endif
                </div>
                
                <!-- Contribution to Knowledge (15) -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <span class="font-medium text-gray-800">6. Contribution to Knowledge</span>
                            <span class="text-sm text-gray-500 ml-2">(15 points max)</span>
                        </div>
                        <span class="text-lg font-bold 
                            {{ ($review->criteria_contribution ?? 0) >= 12 ? 'text-green-600' : 
                               (($review->criteria_contribution ?? 0) >= 8 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $review->criteria_contribution ?? 'N/A' }}/15
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            {{ ($review->criteria_contribution ?? 0) >= 12 ? 'bg-green-600' : 
                               (($review->criteria_contribution ?? 0) >= 8 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                            style="width: {{ (($review->criteria_contribution ?? 0) / 15) * 100 }}%">
                        </div>
                    </div>
                    @if($review->criteria_contribution)
                    <p class="text-xs text-gray-500 mt-1">
                        @if($review->criteria_contribution >= 12) Excellent contribution
                        @elseif($review->criteria_contribution >= 8) Moderate contribution
                        @elseif($review->criteria_contribution >= 5) Fair contribution
                        @else Very weak contribution
                        @endif
                    </p>
                    @endif
                </div>
            </div>
            
            <!-- Total Score Summary -->
            <div class="mt-8 p-4 bg-gray-100 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="font-semibold text-gray-800">Total Score</span>
                        <p class="text-xs text-gray-500">Sum of all criteria</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-bold 
                            {{ $totalScore >= 80 ? 'text-green-600' : ($totalScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $totalScore }}
                        </span>
                        <span class="text-gray-500">/ 100</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="h-2 rounded-full 
                        {{ $totalScore >= 80 ? 'bg-green-600' : ($totalScore >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                        style="width: {{ $totalScore }}%">
                    </div>
                </div>
                <div class="mt-2 text-center">
                    <span class="text-sm 
                        {{ $totalScore >= 80 ? 'text-green-600' : ($totalScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                        @if($totalScore >= 90) Excellent
                        @elseif($totalScore >= 80) Very Good
                        @elseif($totalScore >= 70) Good
                        @elseif($totalScore >= 60) Satisfactory
                        @elseif($totalScore >= 50) Below Average
                        @else Poor
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Review Content -->
        <div class="space-y-8">
            <!-- Summary -->
            @if($review->summary)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary of Contributions</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $review->summary }}</p>
            </div>
            @endif
            
            <!-- Strengths -->
            @if($review->strengths)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Strengths</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $review->strengths }}</p>
            </div>
            @endif
            
            <!-- Weaknesses -->
            @if($review->weaknesses)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Weaknesses & Limitations</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $review->weaknesses }}</p>
            </div>
            @endif
            
            <!-- Suggestions -->
            @if($review->suggestions)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Suggestions for Improvement</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $review->suggestions }}</p>
            </div>
            @endif
            
            <!-- Revision Suggestions -->
            @if($review->revision_suggestions)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Revision Suggestions</h3>
                <div class="bg-yellow-50 rounded-lg p-6 border-l-4 border-yellow-400">
                    <p class="text-gray-700 whitespace-pre-line">{{ $review->revision_suggestions }}</p>
                </div>
            </div>
            @endif
            
            <!-- Comments for Authors -->
            @if($review->comments_author)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Comments for Authors</h3>
                <div class="bg-blue-50 rounded-lg p-6">
                    <p class="text-gray-700 whitespace-pre-line">{{ $review->comments_author }}</p>
                </div>
            </div>
            @endif
            
            <!-- Comments for Chairs (if visible) -->
            @if(($review->reviewer_id === auth()->id() || auth()->user()->is_admin || auth()->user()->is_chair) && $review->comments_chair)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Confidential Comments for Chairs</h3>
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <p class="text-gray-700 whitespace-pre-line">{{ $review->comments_chair }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Actions -->
        <div class="mt-8 pt-8 border-t">
            <div class="flex flex-wrap justify-between gap-4">
                <a href="{{ route('reviews.my') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Back to My Reviews
                </a>
                
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('papers.show', $review->paper) }}" 
                       target="_blank"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-external-link-alt mr-2"></i> View Paper
                    </a>
                    
                    @if(auth()->id() === $review->reviewer_id && $review->status !== 'completed')
                    <a href="{{ route('reviews.edit', $review) }}" 
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i> Edit Review
                    </a>
                    @endif
                    
                    @if(auth()->user()->is_admin || auth()->user()->is_chair)
                    <a href="{{ route('assignments.index') }}?paper={{ $review->paper_id }}" 
                       class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-tasks mr-2"></i> Assignment Management
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection