@extends('layouts.app')

@section('title', ($review->exists ? 'Edit' : 'Create') . ' Review - ' . $paper->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            {{ $review->status === 'accepted' ? 'Start' : ($review->status === 'in_progress' ? 'Continue' : 'Edit') }} Review
        </h1>
        <p class="text-gray-600 mb-8">Paper: {{ $paper->title }}</p>
        
        <!-- Paper Info -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <div>
                    <p class="text-sm text-gray-600">Deadline</p>
                    <p class="font-medium {{ $review->deadline && $review->deadline < now() && $review->status !== 'completed' ? 'text-red-600' : '' }}">
                        {{ $review->deadline ? $review->deadline->format('F d, Y') : 'Not set' }}
                        @if($review->deadline && $review->deadline < now() && $review->status !== 'completed')
                            <span class="text-sm">(Overdue)</span>
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Review Status -->
            <div class="mt-4 pt-4 border-t">
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-4">Review Status:</span>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'accepted' => 'bg-blue-100 text-blue-800',
                            'in_progress' => 'bg-indigo-100 text-indigo-800',
                            'completed' => 'bg-green-100 text-green-800',
                        ];
                        $colorClass = $statusColors[$review->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                        {{ ucfirst(str_replace('_', ' ', $review->status)) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Review Form -->
        <form method="POST" action="{{ $review->exists ? route('reviews.update', $review) : route('reviews.store') }}" class="space-y-8">
            @csrf
            @if($review->exists)
                @method('PUT')
            @endif
            
            <input type="hidden" name="paper_id" value="{{ $paper->id }}">
            
            <!-- Overall Evaluation -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Overall Evaluation</h3>
                
                <div class="space-y-6">
                    <!-- Recommendation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Recommendation *
                        </label>
                        <select name="recommendation" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Select a recommendation</option>
                            <option value="strong_accept" {{ old('recommendation', $review->recommendation) == 'strong_accept' ? 'selected' : '' }}>
                                Strong Accept (Excellent paper, accept without revision)
                            </option>
                            <option value="accept" {{ old('recommendation', $review->recommendation) == 'accept' ? 'selected' : '' }}>
                                Accept (Good paper, minor revisions needed)
                            </option>
                            <option value="weak_accept" {{ old('recommendation', $review->recommendation) == 'weak_accept' ? 'selected' : '' }}>
                                Weak Accept (Borderline paper, needs moderate revisions)
                            </option>
                            <option value="borderline" {{ old('recommendation', $review->recommendation) == 'borderline' ? 'selected' : '' }}>
                                Borderline (Major revisions needed, consider rejecting)
                            </option>
                            <option value="weak_reject" {{ old('recommendation', $review->recommendation) == 'weak_reject' ? 'selected' : '' }}>
                                Weak Reject (Below threshold, needs major improvements)
                            </option>
                            <option value="reject" {{ old('recommendation', $review->recommendation) == 'reject' ? 'selected' : '' }}>
                                Reject (Does not meet conference standards)
                            </option>
                            <option value="strong_reject" {{ old('recommendation', $review->recommendation) == 'strong_reject' ? 'selected' : '' }}>
                                Strong Reject (Poor quality, serious flaws)
                            </option>
                        </select>
                    </div>
                    
                    <!-- Overall Score -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Overall Score (1-5) *
                        </label>
                        <div class="flex items-center space-x-4">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="overall_score" 
                                       value="{{ $i }}"
                                       {{ (int) old('overall_score', $review->overall_score) == $i ? 'checked' : '' }}
                                       class="mr-2" required>
                                <span class="text-lg">{{ $i }}</span>
                            </label>
                            @endfor
                            <div class="ml-4 text-sm text-gray-500">
                                <span>1 = Poor</span>
                                <span class="mx-2">|</span>
                                <span>3 = Average</span>
                                <span class="mx-2">|</span>
                                <span>5 = Excellent</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Confidence -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Your Confidence in This Review
                        </label>
                        <select name="confidence" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select confidence level</option>
                            <option value="expert" {{ old('confidence', $review->confidence) == 'expert' ? 'selected' : '' }}>
                                Expert (I am an expert in this area)
                            </option>
                            <option value="familiar" {{ old('confidence', $review->confidence) == 'familiar' ? 'selected' : '' }}>
                                Familiar (I am familiar with this area)
                            </option>
                            <option value="passing" {{ old('confidence', $review->confidence) == 'passing' ? 'selected' : '' }}>
                                Passing (I have passing knowledge of this area)
                            </option>
                            <option value="knowledgeable" {{ old('confidence', $review->confidence) == 'knowledgeable' ? 'selected' : '' }}>
                                Knowledgeable (I am knowledgeable about the general area)
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Detailed Comments -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Detailed Comments</h3>
                
                <div class="space-y-6">
                    <!-- Summary -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Summary of Contributions (3-5 sentences)
                        </label>
                        <textarea name="summary" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Briefly summarize the paper's main contributions...">{{ old('summary', $review->summary) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">What is this paper about? What are its key contributions?</p>
                    </div>
                    
                    <!-- Strengths -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Strengths
                        </label>
                        <textarea name="strengths" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="What are the main strengths of this paper?">{{ old('strengths', $review->strengths) }}</textarea>
                    </div>
                    
                    <!-- Weaknesses -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Weaknesses and Limitations
                        </label>
                        <textarea name="weaknesses" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="What are the main weaknesses or limitations?">{{ old('weaknesses', $review->weaknesses) }}</textarea>
                    </div>
                    
                    <!-- Suggestions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Suggestions for Improvement
                        </label>
                        <textarea name="suggestions" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Specific suggestions to improve the paper...">{{ old('suggestions', $review->suggestions) }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Comments for Different Audiences -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Comments for Different Audiences</h3>
                
                <div class="space-y-6">
                    <!-- Comments for Authors -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Comments for Authors (Will be shared with authors) *
                        </label>
                        <textarea name="comments_author" 
                                  rows="5"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Write constructive feedback for the authors. This will be shared with them."
                                  required>{{ old('comments_author', $review->comments_author) }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-sm text-gray-500">
                                Be constructive and specific. This feedback will be sent to the authors.
                            </p>
                            <div id="author-chars" class="text-sm text-gray-500"></div>
                        </div>
                    </div>
                    
                    <!-- Comments for Chairs (Confidential) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Confidential Comments for Chairs
                        </label>
                        <textarea name="comments_chair" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any confidential comments for program chairs (authors won't see this)...">{{ old('comments_chair', $review->comments_chair) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">
                            These comments are confidential and will only be seen by program chairs.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons - Using Hidden Input Method -->
            <div class="flex justify-between pt-6 border-t">
                <div>
                    <a href="{{ route('reviews.my') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Back to My Reviews
                    </a>
                    @if($review->exists)
                    <a href="{{ route('papers.show', $paper) }}" 
                       target="_blank"
                       class="ml-4 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        View Paper
                    </a>
                    @endif
                </div>
                
                <div class="space-x-4">
                    @if($review->exists && $review->status !== 'completed')
                    <!-- Save as Draft Button -->
                    <button type="submit" 
                            name="save_draft" 
                            value="1"
                            class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-save mr-2"></i>Save as Draft
                    </button>
                    @endif
                    
                    <!-- Submit/Update Review Button -->
                    <button type="submit" 
                            name="submit_review" 
                            value="1"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        @if($review->exists)
                            {{ $review->status === 'completed' ? 'Update' : 'Submit' }} Review
                        @else
                            Submit Review
                        @endif
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Guidelines -->
        <div class="mt-8 bg-blue-50 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">Review Guidelines</h3>
            <ul class="space-y-2 text-blue-700">
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-1 mr-2 text-blue-600"></i>
                    <span>Be constructive and specific in your feedback</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-1 mr-2 text-blue-600"></i>
                    <span>Focus on the paper's scientific content and methodology</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-1 mr-2 text-blue-600"></i>
                    <span>Maintain a professional and respectful tone</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-1 mr-2 text-blue-600"></i>
                    <span>Provide actionable suggestions for improvement</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-1 mr-2 text-blue-600"></i>
                    <span>Keep confidential comments in the designated section</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Character counter for comments_author
    const authorTextarea = document.querySelector('textarea[name="comments_author"]');
    const authorCharsDiv = document.getElementById('author-chars');
    
    function updateCharacterCount() {
        const length = authorTextarea.value.length;
        authorCharsDiv.innerHTML = `Characters: <span class="font-medium ${length < 50 ? 'text-red-600' : 'text-green-600'}">${length}</span>`;
    }
    
    if (authorTextarea && authorCharsDiv) {
        authorTextarea.addEventListener('input', updateCharacterCount);
        updateCharacterCount(); // Initial call
    }
    
    // Validate form before submission - using hidden input method
    document.querySelector('form').addEventListener('submit', function(e) {
        // Check if this is a save draft submission
        const isSaveDraft = document.activeElement && 
                            document.activeElement.getAttribute('name') === 'save_draft';
        
        console.log('Form submission:', {
            activeElement: document.activeElement ? document.activeElement.getAttribute('name') : 'unknown',
            isSaveDraft: isSaveDraft
        });
        
        // Skip validation for save draft
        if (isSaveDraft) {
            console.log('Saving draft - skipping validation');
            return true;
        }
        
        // Validate for final submission
        const recommendation = document.querySelector('select[name="recommendation"]').value;
        const overallScore = document.querySelector('input[name="overall_score"]:checked');
        const authorComments = authorTextarea ? authorTextarea.value : '';
        
        let errors = [];
        
        if (!recommendation) {
            errors.push('Please select a recommendation.');
        }
        
        if (!overallScore) {
            errors.push('Please select an overall score.');
        }
        
        if (authorComments.length < 50) {
            errors.push('Comments for authors must be at least 50 characters long.');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return false;
        }
        
        console.log('Validation passed, form will submit');
        return true;
    });
</script>
@endsection