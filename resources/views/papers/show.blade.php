@extends('layouts.app')

@section('title', 'Paper Details - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
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
                <span class="px-3 py-1 text-sm rounded-full 
                    @if($paper->status == 'accepted') bg-green-100 text-green-800
                    @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                    @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                    @elseif($paper->status == 'submitted') bg-blue-100 text-blue-800
                    @elseif($paper->status == 'camera_ready') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
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
        @if(auth()->user()->is_admin || auth()->user()->is_chair || $paper->authors()->where('users.id', auth()->id())->exists())
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Reviews Summary</h2>
                
                @php
                    $completedReviews = $paper->reviewAssignments->where('status', 'completed');
                @endphp
                
                @if($completedReviews->count() > 0)
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
                                <p class="text-sm text-gray-500">Based on {{ $completedReviews->count() }} review(s)</p>
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
                    
                    @foreach($completedReviews as $review)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-sm font-medium text-gray-700">
                                    Review by {{ $review->reviewer->full_name }}
                                </span>
                                <div class="flex items-center mt-1">
                                    @php
                                        $reviewTotal = ($review->criteria_relevance ?? 0) + 
                                                      ($review->criteria_originality ?? 0) + 
                                                      ($review->criteria_quality ?? 0) + 
                                                      ($review->criteria_impact ?? 0) + 
                                                      ($review->criteria_clarity ?? 0) + 
                                                      ($review->criteria_contribution ?? 0);
                                    @endphp
                                    <span class="text-sm font-bold mr-4
                                        {{ $reviewTotal >= 80 ? 'text-green-600' : ($reviewTotal >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        Score: {{ $reviewTotal }}/100
                                    </span>
                                    @if($review->confidence)
                                    <span class="text-sm text-gray-500">
                                        Confidence: {{ ucfirst($review->confidence) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $review->recommendation_text }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Scoring Criteria Breakdown -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Scoring Breakdown</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500">Relevance:</span>
                                    <span class="font-medium {{ ($review->criteria_relevance ?? 0) >= 17 ? 'text-green-600' : (($review->criteria_relevance ?? 0) >= 11 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $review->criteria_relevance ?? 'N/A' }}/20
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Originality:</span>
                                    <span class="font-medium {{ ($review->criteria_originality ?? 0) >= 16 ? 'text-green-600' : (($review->criteria_originality ?? 0) >= 11 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $review->criteria_originality ?? 'N/A' }}/20
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Quality:</span>
                                    <span class="font-medium {{ ($review->criteria_quality ?? 0) >= 12 ? 'text-green-600' : (($review->criteria_quality ?? 0) >= 8 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $review->criteria_quality ?? 'N/A' }}/15
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Impact:</span>
                                    <span class="font-medium {{ ($review->criteria_impact ?? 0) >= 12 ? 'text-green-600' : (($review->criteria_impact ?? 0) >= 8 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $review->criteria_impact ?? 'N/A' }}/15
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Clarity:</span>
                                    <span class="font-medium {{ ($review->criteria_clarity ?? 0) >= 8 ? 'text-green-600' : (($review->criteria_clarity ?? 0) >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $review->criteria_clarity ?? 'N/A' }}/10
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Contribution:</span>
                                    <span class="font-medium {{ ($review->criteria_contribution ?? 0) >= 8 ? 'text-green-600' : (($review->criteria_contribution ?? 0) >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $review->criteria_contribution ?? 'N/A' }}/10
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if($review->comments_author)
                        <div class="mb-3">
                            <h4 class="font-medium text-gray-700 mb-2">Feedback for Authors</h4>
                            <div class="bg-gray-50 rounded p-3">
                                <p class="text-gray-700">{{ $review->comments_author }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($review->strengths)
                        <div class="mb-3">
                            <h4 class="font-medium text-gray-700 mb-2">Strengths</h4>
                            <p class="text-gray-700">{{ $review->strengths }}</p>
                        </div>
                        @endif
                        
                        @if($review->weaknesses)
                        <div class="mb-3">
                            <h4 class="font-medium text-gray-700 mb-2">Areas for Improvement</h4>
                            <p class="text-gray-700">{{ $review->weaknesses }}</p>
                        </div>
                        @endif
                        
                        @if($review->suggestions)
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Suggestions</h4>
                            <p class="text-gray-700">{{ $review->suggestions }}</p>
                        </div>
                        @endif
                        
                        @if($review->revision_suggestions)
                        <div class="mt-3 p-3 bg-yellow-50 border-l-4 border-yellow-400">
                            <h4 class="font-medium text-yellow-800 mb-1">Revision Suggestions</h4>
                            <p class="text-sm text-yellow-700">{{ $review->revision_suggestions }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
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

        <!-- Review Form Section (ONLY for reviewers assigned to this paper) -->
        @if(auth()->user()->is_reviewer && $paper->reviewAssignments()->where('reviewer_id', auth()->id())->exists())
            @php
                $userReview = $paper->reviewAssignments()->where('reviewer_id', auth()->id())->first();
                $canReview = $userReview && $userReview->status !== 'completed';
            @endphp
            
            @if($canReview)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Submit Your Review</h2>
                
                <form action="{{ route('reviews.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="paper_id" value="{{ $paper->id }}">
                    
                    <!-- Scoring Criteria Section -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Scoring Criteria (0-100 points)</h3>
                        
                        <div class="space-y-6">
                            <!-- Relevance (20) -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="font-medium text-gray-700">1. Relevance to Conference Theme</label>
                                    <span class="text-sm text-gray-500">Max: 20</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="range" name="criteria_relevance" min="0" max="20" value="0" 
                                           class="flex-1" oninput="this.nextElementSibling.value = this.value">
                                    <input type="number" name="criteria_relevance" class="w-20 px-2 py-1 border rounded text-center" 
                                           value="0" min="0" max="20" oninput="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="text-green-600">17-20:</span> Uses medical images and data science | 
                                    <span class="text-yellow-600">11-16:</span> Non-images but medical and data science | 
                                    <span class="text-red-600">0-10:</span> Medical but no data science OR data science without medicine
                                </div>
                            </div>
                            
                            <!-- Originality (20) -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="font-medium text-gray-700">2. Originality & Innovation</label>
                                    <span class="text-sm text-gray-500">Max: 20</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="range" name="criteria_originality" min="0" max="20" value="0" 
                                           class="flex-1" oninput="this.nextElementSibling.value = this.value">
                                    <input type="number" name="criteria_originality" class="w-20 px-2 py-1 border rounded text-center" 
                                           value="0" min="0" max="20" oninput="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="text-green-600">16-20:</span> Highly original | 
                                    <span class="text-yellow-600">11-15:</span> Some originality | 
                                    <span class="text-red-600">0-10:</span> Limited or no originality
                                </div>
                            </div>
                            
                            <!-- Quality (15) -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="font-medium text-gray-700">3. Technical/Academic Quality</label>
                                    <span class="text-sm text-gray-500">Max: 15</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="range" name="criteria_quality" min="0" max="15" value="0" 
                                           class="flex-1" oninput="this.nextElementSibling.value = this.value">
                                    <input type="number" name="criteria_quality" class="w-20 px-2 py-1 border rounded text-center" 
                                           value="0" min="0" max="15" oninput="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="text-green-600">12-15:</span> Excellent rigor and depth | 
                                    <span class="text-yellow-600">8-11:</span> Good quality | 
                                    <span class="text-red-600">0-7:</span> Fair or weak quality
                                </div>
                            </div>
                            
                            <!-- Impact (15) -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="font-medium text-gray-700">4. Practical Impact & Applicability</label>
                                    <span class="text-sm text-gray-500">Max: 15</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="range" name="criteria_impact" min="0" max="15" value="0" 
                                           class="flex-1" oninput="this.nextElementSibling.value = this.value">
                                    <input type="number" name="criteria_impact" class="w-20 px-2 py-1 border rounded text-center" 
                                           value="0" min="0" max="15" oninput="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="text-green-600">12-15:</span> Highly impactful | 
                                    <span class="text-yellow-600">8-11:</span> Moderately useful | 
                                    <span class="text-red-600">0-7:</span> Limited or no impact
                                </div>
                            </div>
                            
                            <!-- Clarity (10) -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="font-medium text-gray-700">5. Clarity & Organization</label>
                                    <span class="text-sm text-gray-500">Max: 10</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="range" name="criteria_clarity" min="0" max="10" value="0" 
                                           class="flex-1" oninput="this.nextElementSibling.value = this.value">
                                    <input type="number" name="criteria_clarity" class="w-20 px-2 py-1 border rounded text-center" 
                                           value="0" min="0" max="10" oninput="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="text-green-600">8-10:</span> Very clear and well-structured | 
                                    <span class="text-yellow-600">5-7:</span> Generally clear | 
                                    <span class="text-red-600">0-4:</span> Unclear or poorly organized
                                </div>
                            </div>
                            
                            <!-- Contribution (10) -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="font-medium text-gray-700">6. Contribution to Knowledge</label>
                                    <span class="text-sm text-gray-500">Max: 10</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="range" name="criteria_contribution" min="0" max="10" value="0" 
                                           class="flex-1" oninput="this.nextElementSibling.value = this.value">
                                    <input type="number" name="criteria_contribution" class="w-20 px-2 py-1 border rounded text-center" 
                                           value="0" min="0" max="10" oninput="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="text-green-600">8-10:</span> Excellent contribution | 
                                    <span class="text-yellow-600">5-7:</span> Moderate contribution | 
                                    <span class="text-red-600">0-4:</span> Fair or weak contribution
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Score Preview -->
                        <div class="mt-6 p-4 bg-white rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-800">Total Score:</span>
                                <span id="totalScoreDisplay" class="text-2xl font-bold text-blue-600">0</span>
                                <span class="text-gray-500">/ 100</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div id="scoreProgress" class="h-2 rounded-full bg-blue-600" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overall Recommendation -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Overall Recommendation</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                What is your recommendation for this paper?
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                @php
                                    $recommendations = [
                                        'strong_accept' => ['Strong Accept', 'bg-green-600'],
                                        'accept' => ['Accept', 'bg-green-500'],
                                        'weak_accept' => ['Weak Accept', 'bg-yellow-500'],
                                        'borderline' => ['Borderline', 'bg-gray-500'],
                                        'weak_reject' => ['Weak Reject', 'bg-orange-500'],
                                        'reject' => ['Reject', 'bg-red-500'],
                                        'strong_reject' => ['Strong Reject', 'bg-red-700'],
                                    ];
                                @endphp
                                
                                @foreach($recommendations as $value => [$label, $color])
                                <label class="relative">
                                    <input type="radio" name="recommendation" value="{{ $value }}" 
                                           class="sr-only peer" required>
                                    <div class="border-2 rounded-lg p-3 text-center cursor-pointer transition-all peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200 hover:bg-gray-100">
                                        <div class="w-3 h-3 rounded-full {{ $color }} mx-auto mb-1"></div>
                                        <p class="text-sm font-medium">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Comments -->
                    <div class="space-y-6">
                        <div>
                            <label for="comments_author" class="block text-sm font-medium text-gray-700 mb-2">
                                Comments for Authors (Will be shared with authors) *
                            </label>
                            <textarea id="comments_author" name="comments_author" rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Provide constructive feedback for the authors..."
                                      required></textarea>
                            <p class="mt-2 text-sm text-gray-500">Please be constructive and professional in your feedback.</p>
                        </div>
                        
                        <div>
                            <label for="strengths" class="block text-sm font-medium text-gray-700 mb-2">
                                Strengths
                            </label>
                            <textarea id="strengths" name="strengths" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="What are the main strengths of this paper?"></textarea>
                        </div>
                        
                        <div>
                            <label for="weaknesses" class="block text-sm font-medium text-gray-700 mb-2">
                                Weaknesses and Areas for Improvement
                            </label>
                            <textarea id="weaknesses" name="weaknesses" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="What are the main weaknesses or limitations?"></textarea>
                        </div>
                        
                        <div>
                            <label for="suggestions" class="block text-sm font-medium text-gray-700 mb-2">
                                Suggestions for Improvement
                            </label>
                            <textarea id="suggestions" name="suggestions" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Specific suggestions to improve the paper..."></textarea>
                        </div>
                        
                        <div>
                            <label for="revision_suggestions" class="block text-sm font-medium text-gray-700 mb-2">
                                Revision Suggestions (If recommending revisions)
                            </label>
                            <textarea id="revision_suggestions" name="revision_suggestions" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Specific changes required for revision..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-between pt-6 border-t">
                        <a href="{{ route('reviews.my') }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50">
                            Back to My Reviews
                        </a>
                        <div class="space-x-4">
                            <button type="submit" name="save_draft" value="1"
                                    class="px-8 py-3 bg-yellow-600 text-white rounded-lg font-medium hover:bg-yellow-700">
                                Save as Draft
                            </button>
                            <button type="submit" name="submit_review" value="1"
                                    class="px-8 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700">
                                Submit Review
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        @endif

        @if($paper->decision == 'revise' && $paper->revision_deadline)
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-calendar-exclamation text-yellow-600 mr-3"></i>
                <div>
                    <p class="font-medium text-yellow-800">Revision Required</p>
                    <p class="text-sm text-yellow-700">
                        Authors must submit revised version by: 
                        <span class="font-bold">{{ \Carbon\Carbon::parse($paper->revision_deadline)->format('F d, Y') }}</span>
                    </p>
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
        @if(auth()->user()->is_admin || auth()->user()->is_chair)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Admin Actions</h2>
                
                <div class="space-y-4">
                    <!-- Status Update -->
                    <form action="{{ route('papers.update-status', $paper) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="draft" {{ $paper->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ $paper->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ $paper->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="accepted" {{ $paper->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="rejected" {{ $paper->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="camera_ready" {{ $paper->status == 'camera_ready' ? 'selected' : '' }}>Camera Ready</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Decision</label>
                                <select name="decision" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">No Decision</option>
                                    <option value="accept" {{ $paper->decision == 'accept' ? 'selected' : '' }}>Accept</option>
                                    <option value="minor_revisions" {{ $paper->decision == 'minor_revisions' ? 'selected' : '' }}>Minor Revisions</option>
                                    <option value="major_revisions" {{ $paper->decision == 'major_revisions' ? 'selected' : '' }}>Major Revisions</option>
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
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Update Status
                            </button>
                        </div>
                    </form>
                    
                    <!-- Assign Reviewers -->
                    <div class="pt-4 border-t">
                        <a href="{{ route('assignments.index') }}?paper={{ $paper->id }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                            <i class="fas fa-user-plus mr-2"></i>Assign Reviewers
                        </a>
                    </div>
                </div>
            </div>
        @endif
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