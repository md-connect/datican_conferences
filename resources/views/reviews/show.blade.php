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
                    <div class="text-3xl font-bold {{ $review->overall_score >= 4 ? 'text-green-600' : ($review->overall_score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $review->overall_score ?? 'N/A' }}/5
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
                            ];
                            echo $recommendationTexts[$review->recommendation] ?? 'No recommendation';
                        @endphp
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-gray-500">Paper</p>
                    <p class="font-medium">{{ $review->paper->title }}</p>
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
                            'accepted' => 'bg-blue-100 text-blue-800',
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
        
        <!-- Review Content -->
        <div class="space-y-8">
            <!-- Summary -->
            @if($review->summary)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary</h3>
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Weaknesses</h3>
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
            @if(($review->reviewer_id === auth()->id() || auth()->user()->is_admin) && $review->comments_chair)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Confidential Comments for Chairs</h3>
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <p class="text-gray-700 whitespace-pre-line">{{ $review->comments_chair }}</p>
                </div>
            </div>
            @endif
            
            <!-- Detailed Scores -->
            @if($review->scores && is_array($review->scores) && count($review->scores) > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Detailed Scoring</h3>
                <div class="space-y-4">
                    @foreach($review->scores as $criterion => $score)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $criterion)) }}</span>
                            <span class="font-bold">{{ $score }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $score >= 4 ? 'bg-green-500' : ($score >= 3 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ ($score / 5) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Actions -->
        <div class="mt-8 pt-8 border-t">
            <div class="flex justify-between">
                <a href="{{ route('reviews.my') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Back to My Reviews
                </a>
                
                <div class="space-x-4">
                    <a href="{{ route('papers.show', $review->paper) }}" 
                       target="_blank"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        View Paper
                    </a>
                    
                    @if(auth()->id() === $review->reviewer_id && $review->status !== 'completed')
                    <a href="{{ route('reviews.edit', $review) }}" 
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Edit Review
                    </a>
                    @endif
                    
                    @if(auth()->user()->is_admin)
                    <a href="{{ route('assignments.index') }}?paper={{ $review->paper_id }}" 
                       class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Assignment Management
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection