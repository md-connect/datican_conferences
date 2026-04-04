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
                        <!-- Author viewing abstract-only paper - show submit full paper button -->
                        <a href="{{ route('papers.submit-full-form', $paper) }}" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-upload mr-2"></i>Upload Paper
                        </a>
                        @elseif($isAbstractOnly)
                        <!-- Non-author viewing abstract-only paper -->
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg font-medium inline-flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Abstract Submission
                        </span>
                        @else
                        <!-- Paper should have file but doesn't -->
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

        <!-- Reviews Section (Only for authors and admins) -->
        @if(auth()->user()->is_admin || $paper->authors()->where('users.id', auth()->id())->exists())
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Reviews</h2>
                
                @if($paper->reviews->count() > 0)
                    @foreach($paper->reviews->where('status', 'completed') as $review)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-sm font-medium text-gray-700">
                                    Review {{ $loop->iteration }}
                                </span>
                                <div class="flex items-center mt-1">
                                    <span class="text-sm text-gray-500 mr-4">
                                        Overall Score: {{ $review->overall_score }}/5
                                    </span>
                                    @if($review->confidence)
                                    <span class="text-sm text-gray-500">
                                        Confidence: {{ $review->confidence }}
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
        @if(auth()->user()->is_reviewer && $paper->reviews()->where('reviewer_id', auth()->id())->exists())
            @php
                $userReview = $paper->reviews()->where('reviewer_id', auth()->id())->first();
            @endphp
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Submit Your Review</h2>
                
                @if($userReview && $userReview->status == 'completed')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="font-medium text-green-800">Review Submitted</p>
                                <p class="text-sm text-green-700 mt-1">
                                    You submitted your review on {{ $userReview->submitted_at->format('M d, Y') }}.
                                    @if($userReview->canBeEdited())
                                        <a href="{{ route('reviews.edit', $userReview) }}" class="text-green-600 hover:text-green-800 font-medium">
                                            Edit Review
                                        </a>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(!$userReview || $userReview->status != 'completed')
                <form action="{{ isset($userReview) ? route('reviews.update', $userReview) : route('reviews.store') }}" method="POST" class="space-y-8">
                    @csrf
                    @if(isset($userReview)) @method('PUT') @endif
                    <input type="hidden" name="paper_id" value="{{ $paper->id }}">
                    
                    <!-- Overall Recommendation -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Overall Recommendation</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-4">
                                    What is your recommendation for this paper?
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @php
                                        $recommendations = [
                                            'strong_accept' => ['Strong Accept', 'bg-green-100 text-green-800 border-green-300'],
                                            'accept' => ['Accept', 'bg-green-50 text-green-700 border-green-200'],
                                            'weak_accept' => ['Weak Accept', 'bg-yellow-100 text-yellow-800 border-yellow-300'],
                                            'borderline' => ['Borderline', 'bg-gray-100 text-gray-800 border-gray-300'],
                                            'weak_reject' => ['Weak Reject', 'bg-red-50 text-red-700 border-red-200'],
                                            'reject' => ['Reject', 'bg-red-100 text-red-800 border-red-300'],
                                            'strong_reject' => ['Strong Reject', 'bg-red-200 text-red-900 border-red-400'],
                                        ];
                                    @endphp
                                    
                                    @foreach($recommendations as $value => [$label, $classes])
                                    <label class="relative">
                                        <input type="radio" name="recommendation" value="{{ $value }}" 
                                               class="sr-only peer" 
                                               {{ (isset($userReview) && $userReview->recommendation == $value) ? 'checked' : '' }}
                                               required>
                                        <div class="border-2 rounded-lg p-4 text-center cursor-pointer transition-all peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200 {{ $classes }}">
                                            <p class="font-medium">{{ $label }}</p>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-4">
                                    Overall Score (1-5 scale)
                                </label>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-500">1 (Poor)</span>
                                    <div class="flex-1 flex justify-between">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center">
                                            <input type="radio" name="overall_score" value="{{ $i }}" 
                                                   class="h-6 w-6 text-blue-600" 
                                                   {{ (isset($userReview) && $userReview->overall_score == $i) ? 'checked' : '' }}
                                                   required>
                                            <span class="mt-1 text-sm">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-500">5 (Excellent)</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    
                    <!-- Detailed Assessment -->
                    <div class="space-y-6">
                        <div>
                            <label for="comments_author" class="block text-sm font-medium text-gray-700 mb-2">
                                Comments for Authors (Will be shared with authors)
                            </label>
                            <textarea id="comments_author" name="comments_author" rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Provide constructive feedback for the authors..."
                                      required>{{ isset($userReview) ? $userReview->comments_author : '' }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">Please be constructive and professional in your feedback.</p>
                        </div>
                        
                        <div>
                            <label for="strengths" class="block text-sm font-medium text-gray-700 mb-2">
                                Strengths
                            </label>
                            <textarea id="strengths" name="strengths" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="What are the main strengths of this paper?">{{ isset($userReview) ? $userReview->strengths : '' }}</textarea>
                        </div>
                        
                        <div>
                            <label for="weaknesses" class="block text-sm font-medium text-gray-700 mb-2">
                                Weaknesses and Areas for Improvement
                            </label>
                            <textarea id="weaknesses" name="weaknesses" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="What are the main weaknesses or limitations?">{{ isset($userReview) ? $userReview->weaknesses : '' }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-between pt-6 border-t">
                        <a href="{{ route('reviews.my') }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50">
                            Back to My Reviews
                        </a>
                        <div class="space-x-4">
                            @if(isset($userReview))
                            <button type="submit" 
                                    class="px-8 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                                Update Review
                            </button>
                            @else
                            <button type="submit" 
                                    class="px-8 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700">
                                Submit Review
                            </button>
                            @endif
                        </div>
                    </div>
                </form>
                @endif
            </div>
        @endif

        @if($paper->decision == 'revise' && $paper->revision_deadline)
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-calendar-exclamation text-yellow-600 mr-3"></i>
                <div>
                    <p class="font-medium text-yellow-800">Revision Required</p>
                    <p class="text-sm text-yellow-700">
                        Authors must submit revised version by: 
                        <span class="font-bold">{{ $paper->revision_deadline->format('F d, Y') }}</span>
                    </p>
                    @if($paper->revision_deadline->isPast())
                    <p class="text-sm text-red-600 mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Revision deadline has passed
                    </p>
                    @else
                    <p class="text-sm text-green-600 mt-1">
                        {{ $paper->revision_deadline->diffForHumans() }} remaining
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Admin Actions (Only for admins) -->
        @if(auth()->user()->is_admin)
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
@endsection