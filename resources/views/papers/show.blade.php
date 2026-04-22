@extends('layouts.app')

@section('title', 'Paper Details - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Paper Info -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mb-2">
                        {{ $paper->anonymous_id }}
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $paper->title }}</h1>
                </div>
                <div>
                    @php
                        $canAccess = auth()->user()->is_admin || auth()->user()->is_chair || 
                                    auth()->user()->is_reviewer || $paper->authors()->where('users.id', auth()->id())->exists();
                        $hasFile = !empty($paper->file_path);
                        $isAbstractOnly = $paper->submission_type === 'abstract_only';
                        $isAuthor = $paper->authors()->where('users.id', auth()->id())->exists();
                    @endphp
                    
                    @if($canAccess)
                        @if($hasFile)
                        <a href="{{ route('papers.download', $paper) }}" 
                        class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-medium">
                            <i class="fas fa-download mr-2"></i>Download
                        </a>
                        @elseif($isAbstractOnly && $isAuthor)
                        <a href="{{ route('papers.submit-full-form', $paper) }}" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-upload mr-2"></i>Upload Paper
                        </a>
                        @elseif($isAbstractOnly)
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg font-medium inline-flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Abstract Submission
                        </span>
                        @else
                        <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg font-medium inline-flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>File Not Available
                        </span>
                        @endif
                    @endif
                </div>
            </div>
            
            <!-- Paper Status Badge -->
            <div class="mb-6">
                @php
                    $statusColors = [
                        'accepted' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'under_review' => 'bg-yellow-100 text-yellow-800',
                        'submitted' => 'bg-blue-100 text-blue-800',
                        'camera_ready' => 'bg-purple-100 text-purple-800',
                        'needs_revision' => 'bg-yellow-100 text-yellow-800',
                        'reviewed' => 'bg-indigo-100 text-indigo-800',
                    ];
                    $statusDisplay = match($paper->status) {
                        'needs_revision' => 'Needs Revision',
                        default => ucfirst(str_replace('_', ' ', $paper->status))
                    };
                @endphp
                <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$paper->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusDisplay }}
                </span>
            </div>
            
            <div class="space-y-4">
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Abstract</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700">{{ $paper->abstract }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Topic Area</p>
                        <p class="font-medium">{{ $paper->topic_area }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Keywords</p>
                        <p class="font-medium">{{ $paper->keywords }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Submission Type</p>
                        <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $paper->submission_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Conference Year</p>
                        <p class="font-medium">{{ $paper->conference_year }}</p>
                    </div>
                </div>
                
                <!-- Authors Section -->
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Authors</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($paper->authors as $author)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">
                            {{ $author->first_name }} {{ $author->last_name }}
                            @if($author->pivot->is_corresponding)
                            <span class="text-blue-600 ml-1" title="Corresponding Author">*</span>
                            @endif
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section (Only for authors, chairs, and admins) -->
        @if($paper->authors()->where('users.id', auth()->id())->exists())
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Peer Reviews Summary</h2>
                <p class="text-sm text-gray-600 mb-6">Each paper is reviewed by at least 2 reviewers (Total: 100 points)</p>
                
                @php
                    $completedReviews = $paper->reviewAssignments->where('status', 'completed');
                    $reviewCount = $completedReviews->count();
                @endphp
                
                @if($reviewCount > 0)
                    <!-- Overall Average Score -->
                    @php
                        $totalScores = [];
                        foreach($completedReviews as $review) {
                            $totalScores[] = ($review->criteria_relevance ?? 0) + 
                                            ($review->criteria_originality ?? 0) + 
                                            ($review->criteria_quality ?? 0) + 
                                            ($review->criteria_impact ?? 0) + 
                                            ($review->criteria_clarity ?? 0) + 
                                            ($review->criteria_contribution ?? 0);
                        }
                        $avgTotalScore = !empty($totalScores) ? round(array_sum($totalScores) / count($totalScores), 1) : 0;
                    @endphp
                    
                    <div class="mb-6 p-4 bg-gray-100 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-gray-800">Average Score</span>
                                <p class="text-sm text-gray-500">Based on {{ $reviewCount }} review(s)</p>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-bold 
                                    {{ $avgTotalScore >= 80 ? 'text-green-600' : ($avgTotalScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $avgTotalScore }}
                                </span>
                                <span class="text-gray-500">/ 100</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="h-2 rounded-full 
                                {{ $avgTotalScore >= 80 ? 'bg-green-600' : ($avgTotalScore >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                style="width: {{ min($avgTotalScore, 100) }}%">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Individual Reviews - Two Columns -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($completedReviews as $index => $review)
                        <div class="border rounded-lg p-4 {{ $index == 0 ? 'border-blue-200 bg-blue-50' : 'border-purple-200 bg-purple-50' }}">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                                        {{ $index == 0 ? 'bg-blue-200 text-blue-800' : 'bg-purple-200 text-purple-800' }}">
                                        Reviewer {{ $index + 1 }}
                                    </span>
                                    <!-- <p class="text-sm text-gray-600 mt-2">{{ $review->reviewer->full_name }}</p> -->
                                </div>
                                @php
                                    $reviewTotal = ($review->criteria_relevance ?? 0) + 
                                                  ($review->criteria_originality ?? 0) + 
                                                  ($review->criteria_quality ?? 0) + 
                                                  ($review->criteria_impact ?? 0) + 
                                                  ($review->criteria_clarity ?? 0) + 
                                                  ($review->criteria_contribution ?? 0);
                                    $reviewerColor = $reviewTotal >= 80 ? 'text-green-600' : ($reviewTotal >= 60 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <div class="text-right">
                                    <span class="text-2xl font-bold {{ $reviewerColor }}">{{ $reviewTotal }}</span>
                                    <span class="text-sm text-gray-500">/100</span>
                                </div>
                            </div>
                            
                            <!-- Score Breakdown -->
                            <div class="space-y-2 mb-4">
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Relevance:</span>
                                        <span class="font-medium">{{ $review->criteria_relevance ?? 0 }}/20</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Originality:</span>
                                        <span class="font-medium">{{ $review->criteria_originality ?? 0 }}/20</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Quality:</span>
                                        <span class="font-medium">{{ $review->criteria_quality ?? 0 }}/15</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Impact:</span>
                                        <span class="font-medium">{{ $review->criteria_impact ?? 0 }}/15</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Clarity:</span>
                                        <span class="font-medium">{{ $review->criteria_clarity ?? 0 }}/15</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Contribution:</span>
                                        <span class="font-medium">{{ $review->criteria_contribution ?? 0 }}/15</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recommendation -->
                            <!-- @if($review->recommendation)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm font-medium text-gray-700">Reviewer Recommendation:</p>
                                <p class="text-sm {{ 
                                    $review->recommendation == 'accept_without_revision' ? 'text-green-600' : 
                                    ($review->recommendation == 'accept_with_minor_revision' ? 'text-yellow-600' : 
                                    ($review->recommendation == 'accept_with_major_revision' ? 'text-orange-600' : 'text-red-600')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $review->recommendation)) }}
                                </p>
                            </div>
                            @endif -->
                            <!-- Strengths -->
                            <!-- @if($review->strengths)
                            <div class="mt-3">
                                <p class="text-sm font-medium text-gray-700">Strengths:</p>
                                <p class="text-sm text-gray-600">{{ Str::limit($review->strengths, 150) }}</p>
                            </div>
                            @endif -->
                            
                            <!-- Weaknesses -->
                            <!-- @if($review->weaknesses)
                            <div class="mt-3">
                                <p class="text-sm font-medium text-gray-700">Weaknesses:</p>
                                <p class="text-sm text-gray-600">{{ Str::limit($review->weaknesses, 150) }}</p>
                            </div>
                            @endif -->
                            
                            <!-- Comments to Authors -->
                            <!-- @if($review->comments_author)
                            <div class="mt-3">
                                <p class="text-sm font-medium text-gray-700">Comments to Authors:</p>
                                <div class="bg-white rounded p-2 mt-1">
                                    <p class="text-sm text-gray-600">{{ Str::limit($review->comments_author, 200) }}</p>
                                </div>
                            </div>
                            @endif -->
                            
                            @if(auth()->user()->is_admin || auth()->user()->is_chair)
                                <div class="mt-3">
                                    <a href="{{ route('reviews.show', $review) }}" 
                                    class="text-sm text-primary-600 hover:text-primary-800">
                                        View Full Review <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    
                    <!-- Review Summary Stats -->
                    <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Number of Reviews</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $reviewCount }}/{{ $totalAssignments }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Average Total Score</p>
                                <p class="text-2xl font-bold {{ $avgTotalScore >= 80 ? 'text-green-600' : ($avgTotalScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $avgTotalScore }}%
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Reviewer Agreement</p>
                                @php
                                    $scores = [];
                                    foreach($completedReviews as $review) {
                                        $scores[] = ($review->criteria_relevance ?? 0) + 
                                                     ($review->criteria_originality ?? 0) + 
                                                     ($review->criteria_quality ?? 0) + 
                                                     ($review->criteria_impact ?? 0) + 
                                                     ($review->criteria_clarity ?? 0) + 
                                                     ($review->criteria_contribution ?? 0);
                                    }
                                    $agreement = count($scores) == 2 ? abs($scores[0] - $scores[1]) : 0;
                                    $agreementStatus = $agreement <= 10 ? 'High' : ($agreement <= 20 ? 'Moderate' : 'Low');
                                @endphp
                                <p class="text-2xl font-bold {{ $agreement <= 10 ? 'text-green-600' : ($agreement <= 20 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $agreementStatus }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Review Status</p>
                                <p class="text-2xl font-bold text-purple-600">
                                    {{ $reviewCount >= 2 ? 'Complete' : 'In Progress' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Chair Decision -->
                        @if($review->chair_decision)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <h2 class="text-sm font-medium text-gray-700">Final Decision:</h2>
                            <p class="text-sm font-semibold {{ 
                                $review->chair_decision == 'accept' ? 'text-green-600' : 
                                ($review->chair_decision == 'accept_with_minor_revision' ? 'text-yellow-600' : 
                                ($review->chair_decision == 'accept_with_major_revision' ? 'text-orange-600' : 'text-red-600')) 
                            }}">
                                @php
                                    $decisionDisplay = match($review->chair_decision) {
                                        'accept' => 'Accepted',
                                        'accept_with_minor_revision' => 'Accepted with Minor Revision',
                                        'accept_with_major_revision' => 'Accepted with Major Revision',
                                        'reject' => 'Rejected',
                                        default => ucfirst(str_replace('_', ' ', $review->chair_decision ?? ''))
                                    };
                                @endphp
                                {{ $decisionDisplay }}
                            </p>
                            @if($review->chair_decision_notes)
                            <div class="mt-2">
                                <p class="text-sm font-medium text-gray-700"><strong>Comments:</strong></p>
                                <p class="text-sm text-gray-600">{{ $review->chair_decision_notes }}</p>
                            </div>
                            @endif
                        </div>

                        @else

                        <h3 class="text-sm text-red-600">No final decision has been made yet. Kindly check back later. A notification will be sent to your email once a decision is made.</h3>
                        @endif
                    
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                        <p>No reviews have been completed yet.</p>
                        @if($paper->status == 'under_review')
                        <p class="text-sm mt-2">Your paper is currently under review. Reviews will appear here once completed.</p>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- Revision Deadline Notice -->
        @if(in_array($paper->status, ['needs_revision', 'accept_with_minor_revision', 'accept_with_major_revision']) && $paper->revision_deadline)
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-calendar-exclamation text-yellow-600 mr-3"></i>
                <div>
                    <p class="font-medium text-yellow-800">Revision Required</p>
                    <p class="text-sm text-yellow-700">
                        Authors must submit revised version by: 
                        <span class="font-bold">{{ \Carbon\Carbon::parse($paper->revision_deadline)->format('F d, Y') }}</span>
                    </p>
                    @if($paper->revision_notes)
                    <p class="text-sm text-yellow-700 mt-2">
                        <strong>Revision Instructions:</strong> {{ $paper->revision_notes }}
                    </p>
                    @endif
                    @if(\Carbon\Carbon::parse($paper->revision_deadline)->isPast())
                    <p class="text-sm text-red-600 mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Revision deadline has passed
                    </p>
                    @else
                    <p class="text-sm text-green-600 mt-1">
                        {{ \Carbon\Carbon::parse($paper->revision_deadline)->diffForHumans() }} remaining
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Admin Actions (Only for admins and chairs) -->
        <!-- @if(auth()->user()->is_admin || auth()->user()->is_chair)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Admin Actions</h2>
                
                <div class="space-y-4">
                    <form action="{{ route('papers.update-status', $paper) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="draft" {{ $paper->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ $paper->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ $paper->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="reviewed" {{ $paper->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                    <option value="accepted" {{ $paper->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="accept_with_minor_revision" {{ $paper->decision == 'accept_with_minor_revision' ? 'selected' : '' }}>Minor Revision</option>
                                    <option value="accept_with_major_revision" {{ $paper->decision == 'accept_with_major_revision' ? 'selected' : '' }}>Major Revision</option>
                                    <option value="rejected" {{ $paper->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="camera_ready" {{ $paper->status == 'camera_ready' ? 'selected' : '' }}>Camera Ready</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Decision</label>
                                <select name="decision" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">No Decision</option>
                                    <option value="accept" {{ $paper->decision == 'accept' ? 'selected' : '' }}>Accept</option>
                                    <option value="accept_with_minor_revision" {{ $paper->decision == 'accept_with_minor_revision' ? 'selected' : '' }}>Accept with Minor Revision</option>
                                    <option value="accept_with_major_revision" {{ $paper->decision == 'accept_with_major_revision' ? 'selected' : '' }}>Accept with Major Revision</option>
                                    <option value="reject" {{ $paper->decision == 'reject' ? 'selected' : '' }}>Reject</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Decision Notes</label>
                            <textarea name="decision_notes" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ $paper->decision_notes }}</textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary">
                                Update Status
                            </button>
                        </div>
                    </form>
                    
                    @if($paper->reviewAssignments->count() < 2 && in_array($paper->status, ['submitted', 'under_review']))
                    <div class="pt-4 border-t">
                        <a href="{{ route('assignments.assign', $paper) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                            <i class="fas fa-user-plus mr-2"></i>Assign Reviewers
                        </a>
                        <p class="text-xs text-gray-500 mt-2">This paper needs {{ 2 - $paper->reviewAssignments->count() }} more reviewer(s)</p>
                    </div>
                    @endif
                </div>
            </div>
        @endif -->
    </div>
</div>

<script>
    // Calculate and update total score in real-time
    function updateTotalScore() {
        const relevance = parseInt(document.querySelector('input[name="criteria_relevance"]')?.value) || 0;
        const originality = parseInt(document.querySelector('input[name="criteria_originality"]')?.value) || 0;
        const quality = parseInt(document.querySelector('input[name="criteria_quality"]')?.value) || 0;
        const impact = parseInt(document.querySelector('input[name="criteria_impact"]')?.value) || 0;
        const clarity = parseInt(document.querySelector('input[name="criteria_clarity"]')?.value) || 0;
        const contribution = parseInt(document.querySelector('input[name="criteria_contribution"]')?.value) || 0;
        
        const total = relevance + originality + quality + impact + clarity + contribution;
        
        const displayElement = document.getElementById('totalScoreDisplay');
        const progressElement = document.getElementById('scoreProgress');
        
        if (displayElement) {
            displayElement.textContent = total;
        }
        
        if (progressElement) {
            const percentage = (total / 100) * 100;
            progressElement.style.width = percentage + '%';
            
            // Change color based on score
            if (total >= 80) {
                progressElement.classList.remove('bg-yellow-500', 'bg-red-500');
                progressElement.classList.add('bg-green-600');
            } else if (total >= 60) {
                progressElement.classList.remove('bg-green-600', 'bg-red-500');
                progressElement.classList.add('bg-yellow-500');
            } else {
                progressElement.classList.remove('bg-green-600', 'bg-yellow-500');
                progressElement.classList.add('bg-red-500');
            }
        }
    }
    
    // Add event listeners to all criteria inputs
    document.addEventListener('DOMContentLoaded', function() {
        const criteriaInputs = document.querySelectorAll('input[name^="criteria_"]');
        criteriaInputs.forEach(input => {
            input.addEventListener('input', updateTotalScore);
            input.addEventListener('change', updateTotalScore);
        });
        updateTotalScore();
    });
</script>
@endsection