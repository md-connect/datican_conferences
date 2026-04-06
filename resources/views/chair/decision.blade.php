@extends('layouts.app')

@section('title', 'Paper Decision - ' . $paper->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Make Paper Decision</h1>
                <p class="text-gray-600 mt-2">Final decision for paper: {{ $paper->anonymous_id }}</p>
            </div>
            <a href="{{ route('chair.papers') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Papers
            </a>
        </div>
        
        <!-- Paper Information -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Paper Details</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Paper ID</p>
                            <p class="font-medium">{{ $paper->anonymous_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Title</p>
                            <p class="font-medium">{{ $paper->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Authors</p>
                            <p class="font-medium">{{ $paper->authors->pluck('full_name')->join(', ') }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Submission Details</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Topic Area</p>
                            <p class="font-medium">{{ $paper->topic_area }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Submission Type</p>
                            <p class="font-medium">{{ ucfirst($paper->submission_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Submitted</p>
                            <p class="font-medium">{{ $paper->submitted_at?->format('F d, Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Current Status</p>
                            <span class="px-3 py-1 text-xs font-medium rounded-full 
                                @if($paper->status == 'submitted') bg-blue-100 text-blue-800
                                @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                                @elseif($paper->status == 'reviewed') bg-purple-100 text-purple-800
                                @elseif($paper->status == 'accepted') bg-green-100 text-green-800
                                @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                @elseif($paper->status == 'needs_revision') bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                            </span>
                        </div>
                    </div>
                     
                </div>
            </div>
             <div>
                <p class="text-sm text-gray-600">Abstract</p>
                <p class="font-medium">{{ $paper->abstract }}</p>
            </div>
        </div>
         
        <!-- Reviews Summary with Scoring Criteria -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Peer Reviews Summary</h3>
            <p class="text-sm text-gray-600 mb-6">Both reviewers have evaluated the paper based on 6 criteria (Total: 100 points)</p>
            
            @php
                $completedReviews = $paper->reviewAssignments->where('status', 'completed');
                $reviewCount = $completedReviews->count();
            @endphp
            
            @if($reviewCount > 0)
                <!-- Reviewers Comparison Table -->
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criteria</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Max</th>
                                @foreach($completedReviews as $review)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reviewer {{ $loop->iteration }}
                                </th>
                                @endforeach
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Average</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $criteria = [
                                    'relevance' => ['label' => 'Relevance to Conference Theme', 'max' => 20],
                                    'originality' => ['label' => 'Originality & Innovation', 'max' => 20],
                                    'quality' => ['label' => 'Technical/Academic Quality', 'max' => 15],
                                    'impact' => ['label' => 'Practical Impact & Applicability', 'max' => 15],
                                    'clarity' => ['label' => 'Clarity & Organization', 'max' => 15],
                                    'contribution' => ['label' => 'Contribution to Knowledge', 'max' => 15],
                                ];
                            @endphp
                            
                            @foreach($criteria as $key => $criterion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $criterion['label'] }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-500">
                                    {{ $criterion['max'] }}
                                </td>
                                @foreach($completedReviews as $review)
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $score = $review->{'criteria_' . $key};
                                        $percentage = $score ? round(($score / $criterion['max']) * 100) : 0;
                                        $color = $percentage >= 80 ? 'text-green-600' : ($percentage >= 60 ? 'text-yellow-600' : 'text-red-600');
                                    @endphp
                                    <span class="text-sm font-semibold {{ $color }}">
                                        {{ $score ?? 'N/A' }}
                                    </span>
                                    <span class="text-xs text-gray-400">/{{ $criterion['max'] }}</span>
                                </td>
                                @endforeach
                                <td class="px-4 py-3 text-center bg-gray-50">
                                    @php
                                        $scores = [];
                                        foreach($completedReviews as $review) {
                                            if($review->{'criteria_' . $key}) {
                                                $scores[] = $review->{'criteria_' . $key};
                                            }
                                        }
                                        $avgScore = !empty($scores) ? round(array_sum($scores) / count($scores), 1) : 0;
                                        $avgPercentage = $avgScore ? round(($avgScore / $criterion['max']) * 100) : 0;
                                        $avgColor = $avgPercentage >= 80 ? 'text-green-600' : ($avgPercentage >= 60 ? 'text-yellow-600' : 'text-red-600');
                                    @endphp
                                    <span class="text-sm font-bold {{ $avgColor }}">{{ $avgScore }}</span>
                                    <span class="text-xs text-gray-400">/{{ $criterion['max'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                            
                            <!-- Total Row -->
                            <tr class="bg-gray-100 font-semibold">
                                <td class="px-4 py-3 text-sm font-bold text-gray-900">TOTAL SCORE</td>
                                <td class="px-4 py-3 text-center text-sm font-bold">100</td>
                                @foreach($completedReviews as $review)
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $reviewTotal = ($review->criteria_relevance ?? 0) + 
                                                       ($review->criteria_originality ?? 0) + 
                                                       ($review->criteria_quality ?? 0) + 
                                                       ($review->criteria_impact ?? 0) + 
                                                       ($review->criteria_clarity ?? 0) + 
                                                       ($review->criteria_contribution ?? 0);
                                        $totalColor = $reviewTotal >= 80 ? 'text-green-600' : ($reviewTotal >= 60 ? 'text-yellow-600' : 'text-red-600');
                                    @endphp
                                    <span class="text-lg font-bold {{ $totalColor }}">{{ $reviewTotal }}</span>
                                    <span class="text-xs text-gray-400">/100</span>
                                </td>
                                @endforeach
                                <td class="px-4 py-3 text-center bg-gray-100">
                                    @php
                                        $allTotals = [];
                                        foreach($completedReviews as $review) {
                                            $allTotals[] = ($review->criteria_relevance ?? 0) + 
                                                          ($review->criteria_originality ?? 0) + 
                                                          ($review->criteria_quality ?? 0) + 
                                                          ($review->criteria_impact ?? 0) + 
                                                          ($review->criteria_clarity ?? 0) + 
                                                          ($review->criteria_contribution ?? 0);
                                        }
                                        $overallAvg = !empty($allTotals) ? round(array_sum($allTotals) / count($allTotals), 1) : 0;
                                        $overallColor = $overallAvg >= 80 ? 'text-green-600' : ($overallAvg >= 60 ? 'text-yellow-600' : 'text-red-600');
                                    @endphp
                                    <span class="text-lg font-bold {{ $overallColor }}">{{ $overallAvg }}</span>
                                    <span class="text-xs text-gray-400">/100</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Individual Review Details -->
                <div class="mt-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">Individual Review Details</h4>
                    
                    @php
                        $reviewers = $completedReviews->values(); // Reset indices
                        $reviewer1 = $reviewers->get(0);
                        $reviewer2 = $reviewers->get(1);
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Reviewer 1 - Left Column -->
                        <div class="border rounded-lg p-5 border-blue-200 bg-blue-50">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-blue-200 text-blue-800">
                                        Reviewer 1
                                    </span>
                                    @if($reviewer1)
                                    <p class="text-sm text-gray-600 mt-2">{{ $reviewer1->reviewer->full_name }}</p>
                                    @endif
                                </div>
                                @if($reviewer1)
                                @php
                                    $reviewerTotal = ($reviewer1->criteria_relevance ?? 0) + 
                                                    ($reviewer1->criteria_originality ?? 0) + 
                                                    ($reviewer1->criteria_quality ?? 0) + 
                                                    ($reviewer1->criteria_impact ?? 0) + 
                                                    ($reviewer1->criteria_clarity ?? 0) + 
                                                    ($reviewer1->criteria_contribution ?? 0);
                                    $reviewerColor = $reviewerTotal >= 80 ? 'text-green-600' : ($reviewerTotal >= 60 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <div class="text-right">
                                    <span class="text-2xl font-bold {{ $reviewerColor }}">{{ $reviewerTotal }}</span>
                                    <span class="text-sm text-gray-500">/100</span>
                                </div>
                                @endif
                            </div>
                            
                            @if($reviewer1)
                                <!-- Score Breakdown -->
                                <div class="space-y-2 mb-4">
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Relevance:</span>
                                            <span class="font-medium">{{ $reviewer1->criteria_relevance ?? 0 }}/20</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Originality:</span>
                                            <span class="font-medium">{{ $reviewer1->criteria_originality ?? 0 }}/20</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Quality:</span>
                                            <span class="font-medium">{{ $reviewer1->criteria_quality ?? 0 }}/15</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Impact:</span>
                                            <span class="font-medium">{{ $reviewer1->criteria_impact ?? 0 }}/15</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Clarity:</span>
                                            <span class="font-medium">{{ $reviewer1->criteria_clarity ?? 0 }}/15</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Contribution:</span>
                                            <span class="font-medium">{{ $reviewer1->criteria_contribution ?? 0 }}/15</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Recommendation -->
                                @if($reviewer1->recommendation)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-sm font-medium text-gray-700">Reviewer Recommendation:</p>
                                    <p class="text-sm {{ 
                                        $reviewer1->recommendation == 'accept_without_revision' ? 'text-green-600' : 
                                        ($reviewer1->recommendation == 'accept_with_minor_revision' ? 'text-yellow-600' : 
                                        ($reviewer1->recommendation == 'accept_with_major_revision' ? 'text-orange-600' : 'text-red-600')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $reviewer1->recommendation)) }}
                                    </p>
                                </div>
                                @endif
                                
                                <!-- Strengths -->
                                @if($reviewer1->strengths)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-sm font-medium text-gray-700">Strengths:</p>
                                    <p class="text-sm text-gray-600">{{ $reviewer1->strengths }}</p>
                                </div>
                                @endif
                                
                                <!-- Weaknesses -->
                                @if($reviewer1->weaknesses)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Weaknesses & Limitations:</p>
                                    <p class="text-sm text-gray-600">{{ $reviewer1->weaknesses }}</p>
                                </div>
                                @endif
                                
                                <!-- Suggestions -->
                                @if($reviewer1->suggestions)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Suggestions for Improvement:</p>
                                    <p class="text-sm text-gray-600">{{ $reviewer1->suggestions }}</p>
                                </div>
                                @endif
                                
                                <!-- Revision Suggestions -->
                                @if($reviewer1->revision_suggestions)
                                <div class="mt-3 p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                                    <p class="text-sm font-medium text-yellow-800">Revision Suggestions:</p>
                                    <p class="text-sm text-yellow-700">{{ $reviewer1->revision_suggestions }}</p>
                                </div>
                                @endif
                                
                                <!-- Comments to Authors -->
                                @if($reviewer1->comments_author)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Comments to Authors:</p>
                                    <p class="text-sm text-gray-600 line-clamp-3">{{ Str::limit($reviewer1->comments_author, 200) }}</p>
                                </div>
                                @endif
                                
                                <!-- Confidential Comments for Chairs -->
                                @if($reviewer1->comments_chair)
                                <div class="mt-3 p-3 bg-red-50 rounded-lg border-l-4 border-red-400">
                                    <p class="text-sm font-medium text-red-800">Confidential Comments for Chairs:</p>
                                    <p class="text-sm text-red-700">{{ $reviewer1->comments_chair }}</p>
                                </div>
                                @endif
                                
                                <div class="mt-3">
                                    <a href="{{ route('reviews.show', $reviewer1) }}" 
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                        View Full Review <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-user-slash text-4xl mb-2"></i>
                                    <p>No review from Reviewer 1 yet</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Reviewer 2 - Right Column -->
                        <div class="border rounded-lg p-5 border-purple-200 bg-purple-50">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-purple-200 text-purple-800">
                                        Reviewer 2
                                    </span>
                                    @if($reviewer2)
                                    <p class="text-sm text-gray-600 mt-2">{{ $reviewer2->reviewer->full_name }}</p>
                                    @endif
                                </div>
                                @if($reviewer2)
                                @php
                                    $reviewerTotal = ($reviewer2->criteria_relevance ?? 0) + 
                                                    ($reviewer2->criteria_originality ?? 0) + 
                                                    ($reviewer2->criteria_quality ?? 0) + 
                                                    ($reviewer2->criteria_impact ?? 0) + 
                                                    ($reviewer2->criteria_clarity ?? 0) + 
                                                    ($reviewer2->criteria_contribution ?? 0);
                                    $reviewerColor = $reviewerTotal >= 80 ? 'text-green-600' : ($reviewerTotal >= 60 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <div class="text-right">
                                    <span class="text-2xl font-bold {{ $reviewerColor }}">{{ $reviewerTotal }}</span>
                                    <span class="text-sm text-gray-500">/100</span>
                                </div>
                                @endif
                            </div>
                            
                            @if($reviewer2)
                                <!-- Score Breakdown -->
                                <div class="space-y-2 mb-4">
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Relevance:</span>
                                            <span class="font-medium">{{ $reviewer2->criteria_relevance ?? 0 }}/20</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Originality:</span>
                                            <span class="font-medium">{{ $reviewer2->criteria_originality ?? 0 }}/20</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Quality:</span>
                                            <span class="font-medium">{{ $reviewer2->criteria_quality ?? 0 }}/15</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Impact:</span>
                                            <span class="font-medium">{{ $reviewer2->criteria_impact ?? 0 }}/15</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Clarity:</span>
                                            <span class="font-medium">{{ $reviewer2->criteria_clarity ?? 0 }}/15</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Contribution:</span>
                                            <span class="font-medium">{{ $reviewer2->criteria_contribution ?? 0 }}/15</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Recommendation -->
                                @if($reviewer2->recommendation)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-sm font-medium text-gray-700">Reviewer Recommendation:</p>
                                    <p class="text-sm {{ 
                                        $reviewer2->recommendation == 'accept_without_revision' ? 'text-green-600' : 
                                        ($reviewer2->recommendation == 'accept_with_minor_revision' ? 'text-yellow-600' : 
                                        ($reviewer2->recommendation == 'accept_with_major_revision' ? 'text-orange-600' : 'text-red-600')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $reviewer2->recommendation)) }}
                                    </p>
                                </div>
                                @endif
                                
                                <!-- Strengths -->
                                @if($reviewer2->strengths)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-sm font-medium text-gray-700">Strengths:</p>
                                    <p class="text-sm text-gray-600">{{ $reviewer2->strengths }}</p>
                                </div>
                                @endif
                                
                                <!-- Weaknesses -->
                                @if($reviewer2->weaknesses)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Weaknesses & Limitations:</p>
                                    <p class="text-sm text-gray-600">{{ $reviewer2->weaknesses }}</p>
                                </div>
                                @endif
                                
                                <!-- Suggestions -->
                                @if($reviewer2->suggestions)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Suggestions for Improvement:</p>
                                    <p class="text-sm text-gray-600">{{ $reviewer2->suggestions }}</p>
                                </div>
                                @endif
                                
                                <!-- Revision Suggestions -->
                                @if($reviewer2->revision_suggestions)
                                <div class="mt-3 p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                                    <p class="text-sm font-medium text-yellow-800">Revision Suggestions:</p>
                                    <p class="text-sm text-yellow-700">{{ $reviewer2->revision_suggestions }}</p>
                                </div>
                                @endif
                                
                                <!-- Comments to Authors -->
                                @if($reviewer2->comments_author)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Comments to Authors:</p>
                                    <p class="text-sm text-gray-600 line-clamp-3">{{ Str::limit($reviewer2->comments_author, 200) }}</p>
                                </div>
                                @endif
                                
                                <!-- Confidential Comments for Chairs -->
                                @if($reviewer2->comments_chair)
                                <div class="mt-3 p-3 bg-red-50 rounded-lg border-l-4 border-red-400">
                                    <p class="text-sm font-medium text-red-800">Confidential Comments for Chairs:</p>
                                    <p class="text-sm text-red-700">{{ $reviewer2->comments_chair }}</p>
                                </div>
                                @endif
                                
                                <div class="mt-3">
                                    <a href="{{ route('reviews.show', $reviewer2) }}" 
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                        View Full Review <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-user-slash text-4xl mb-2"></i>
                                    <p>No review from Reviewer 2 yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Overall Summary -->
                <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Number of Reviews</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $reviewCount }}/2</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Average Total Score</p>
                            <p class="text-2xl font-bold {{ $overallAvg >= 80 ? 'text-green-600' : ($overallAvg >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $overallAvg }}%
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
                            <p class="text-xs text-gray-500">Difference: {{ $agreement }} pts</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Review Status</p>
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $reviewCount == 2 ? 'Complete' : 'In Progress' }}
                            </p>
                        </div>
                    </div>
                </div>
                
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <p>No completed reviews available for decision.</p>
                <p class="text-sm mt-2">Wait for both reviewers to complete their reviews.</p>
            </div>
            @endif
        </div>

        <!-- Show if decision already exists -->
        @if($paper->decision)
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-3xl text-green-600 mr-4"></i>
                <div>
                    <h3 class="text-lg font-semibold text-green-800">Decision Already Made</h3>
                    <div class="mt-2">
                        <p class="text-green-700">
                            <strong>Decision:</strong> 
                            <span class="px-3 py-1 ml-2 text-sm font-medium rounded-full 
                                @if($paper->decision == 'accept') bg-green-100 text-green-800
                                @elseif($paper->decision == 'accept_with_minor_revision') bg-yellow-100 text-yellow-800
                                @elseif($paper->decision == 'accept_with_major_revision') bg-orange-100 text-orange-800
                                @elseif($paper->decision == 'reject') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $paper->decision)) }}
                            </span>
                        </p>
                        @if($paper->decision_notes)
                        <p class="text-green-700 mt-2">
                            <strong>Notes:</strong> {{ $paper->decision_notes }}
                        </p>
                        @endif
                        <p class="text-green-700 mt-2">
                            <strong>Decision Date:</strong> {{ $paper->decision_made_at->format('F d, Y H:i') }}
                        </p>
                        <p class="text-green-700 mt-2">
                            <strong>Status:</strong> 
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($paper->status == 'accepted') bg-emerald-100 text-emerald-800
                                @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                @elseif($paper->status == 'needs_revision') bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-green-200">
                <a href="{{ route('chair.papers') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Papers List
                </a>
            </div>
        </div>
        @else
        
        <!-- Decision Form -->
        @php
            $totalAssignments = $paper->reviewAssignments->where('status', '!=', 'declined')->count();
            $completedAssignments = $paper->reviewAssignments->where('status', 'completed')->count();
            $allReviewsCompleted = ($totalAssignments > 0) && ($completedAssignments === $totalAssignments);
            $hasBothReviews = ($completedAssignments >= 2);
        @endphp

        @if($allReviewsCompleted && $hasBothReviews)
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Make Decision</h3>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h3 class="text-red-800 font-medium mb-2">Form Errors:</h3>
                    <ul class="list-disc list-inside text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('chair.papers.decision', $paper) }}" id="decisionForm">
                @csrf
                
                <div class="space-y-6">
                    <!-- Decision Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Decision *</label>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <label class="flex flex-col items-center p-4 border-2 border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                                <input type="radio" name="decision" value="accept" class="mb-3" required>
                                <i class="fas fa-check-circle text-3xl text-green-600 mb-2"></i>
                                <span class="font-medium text-green-700">Accept</span>
                                <span class="text-xs text-gray-600 text-center mt-1">Paper meets standards</span>
                            </label>
                            
                            <label class="flex flex-col items-center p-4 border-2 border-yellow-200 rounded-lg hover:bg-yellow-50 cursor-pointer">
                                <input type="radio" name="decision" value="accept_with_minor_revision" class="mb-3" required>
                                <i class="fas fa-edit text-3xl text-yellow-600 mb-2"></i>
                                <span class="font-medium text-yellow-700 text-center">Accept with Minor Revision</span>
                                <span class="text-xs text-gray-600 text-center mt-1">Small changes required</span>
                            </label>
                            
                            <label class="flex flex-col items-center p-4 border-2 border-orange-200 rounded-lg hover:bg-orange-50 cursor-pointer">
                                <input type="radio" name="decision" value="accept_with_major_revision" class="mb-3" required>
                                <i class="fas fa-redo-alt text-3xl text-orange-600 mb-2"></i>
                                <span class="font-medium text-orange-700 text-center">Accept with Major Revision</span>
                                <span class="text-xs text-gray-600 text-center mt-1">Significant changes required</span>
                            </label>
                            
                            <label class="flex flex-col items-center p-4 border-2 border-red-200 rounded-lg hover:bg-red-50 cursor-pointer">
                                <input type="radio" name="decision" value="reject" class="mb-3" required>
                                <i class="fas fa-times-circle text-3xl text-red-600 mb-2"></i>
                                <span class="font-medium text-red-700">Reject</span>
                                <span class="text-xs text-gray-600 text-center mt-1">Does not meet standards</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Revision Deadline (Conditional) -->
                    <div id="revisionDeadlineContainer" class="hidden" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Revision Deadline <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                            name="revision_deadline" 
                            id="revisionDeadlineInput"
                            class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            value="{{ old('revision_deadline') }}">
                        <p class="text-sm text-gray-500 mt-1">Date by which authors must submit revised version</p>
                        @error('revision_deadline')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Decision Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Decision Notes
                        </label>
                        <textarea name="decision_notes" 
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Additional notes for authors...">{{ old('decision_notes') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">These notes will be shared with the authors</p>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t">
                        <button type="submit" 
                                class="px-8 py-3 bg-primary text-white rounded-lg hover:bg-primary-700 font-medium">
                            <i class="fas fa-gavel mr-2"></i> Submit Decision
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <i class="fas fa-clock text-3xl text-yellow-600 mb-4"></i>
            <h3 class="text-lg font-medium text-yellow-800 mb-2">Waiting for Reviews</h3>
            <p class="text-yellow-700 mb-4">
                This paper has {{ $completedAssignments }}/2 completed reviews.
                <br>
                Wait for both reviewers to complete their reviews before making a decision.
            </p>
            
            @if($totalAssignments === 0)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                <span class="text-red-700">No reviewers have been assigned to this paper.</span>
                <div class="mt-3">
                    <a href="{{ route('assignments.assign', $paper) }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-user-plus mr-2"></i> Assign Reviewers
                    </a>
                </div>
            </div>
            @endif
        </div>
        @endif
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Decision form script loaded');
    
    const decisionRadios = document.querySelectorAll('input[name="decision"]');
    const revisionDeadlineContainer = document.getElementById('revisionDeadlineContainer');
    const revisionDeadlineInput = document.getElementById('revisionDeadlineInput');
    const form = document.getElementById('decisionForm');
    
    console.log('Elements found:', {
        decisionRadios: decisionRadios.length,
        revisionDeadlineContainer: revisionDeadlineContainer ? 'yes' : 'no',
        revisionDeadlineInput: revisionDeadlineInput ? 'yes' : 'no',
        form: form ? 'yes' : 'no'
    });
    
    // Function to toggle revision deadline field
    function toggleRevisionDeadline() {
        const selectedDecision = document.querySelector('input[name="decision"]:checked');

        if (!selectedDecision) return;

        if (selectedDecision.value === 'accept_with_minor_revision' || selectedDecision.value === 'accept_with_major_revision') {
            revisionDeadlineContainer.style.display = 'block';
            revisionDeadlineInput.required = true;
            revisionDeadlineInput.disabled = false;
        } else {
            revisionDeadlineContainer.style.display = 'none';
            revisionDeadlineInput.required = false;
            revisionDeadlineInput.disabled = true;
            revisionDeadlineInput.value = '';
        }
    }
    
    // Add change event listeners to all radio buttons
    decisionRadios.forEach(radio => {
        radio.addEventListener('change', toggleRevisionDeadline);
        console.log('Added change listener to radio:', radio.value);
    });
    
    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedDecision = document.querySelector('input[name="decision"]:checked');
            
            console.log('Form submission triggered', {
                selectedDecision: selectedDecision ? selectedDecision.value : 'none',
                revisionDeadlineValue: revisionDeadlineInput ? revisionDeadlineInput.value : 'no input'
            });
            
            if (!selectedDecision) {
                e.preventDefault();
                alert('Please select a decision.');
                return false;
            }
            
            // For revision decisions, ensure deadline is set
            if (selectedDecision.value === 'accept_with_minor_revision' || selectedDecision.value === 'accept_with_major_revision') {
                revisionDeadlineInput.disabled = false;
                
                if (!revisionDeadlineInput.value) {
                    e.preventDefault();
                    alert('Please select a revision deadline date.');
                    revisionDeadlineInput.focus();
                    return false;
                }
            }
            
            console.log('Form submission allowed');
            return true;
        });
    }
    
    // Initial call
    toggleRevisionDeadline();
    
    // Handle old input
    @if(old('decision') === 'accept_with_minor_revision' || old('decision') === 'accept_with_major_revision')
        console.log('Old decision was revision');
        const reviseRadio = document.querySelector('input[name="decision"][value="' + "{{ old('decision') }}" + '"]');
        if (reviseRadio) {
            reviseRadio.checked = true;
            toggleRevisionDeadline();
            @if(old('revision_deadline'))
                revisionDeadlineInput.value = "{{ old('revision_deadline') }}";
            @endif
        }
    @endif
});
</script>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

@endsection