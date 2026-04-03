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
        <form method="POST" action="{{ $review->exists ? route('reviews.update', $review) : route('reviews.store') }}" class="space-y-8" id="reviewForm">
            @csrf
            @if($review->exists)
                @method('PUT')
            @endif
            
            <input type="hidden" name="paper_id" value="{{ $paper->id }}">
            
            <!-- Scoring Criteria Section -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Scoring Criteria</h3>
                <p class="text-sm text-gray-600 mb-6">Please rate each criterion (Total: 100 points)</p>
                
                <div class="space-y-8">
                    <!-- 1. Relevance to Conference Theme (20) -->
                    <div class="border-b pb-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <label class="block text-md font-medium text-gray-800 mb-1">
                                    1. Relevance to Conference Theme <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">How well does this paper align with the conference theme?</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">Max: 20</span>
                        </div>
                        <input type="number" 
                               name="criteria_relevance" 
                               id="criteria_relevance"
                               value="{{ old('criteria_relevance', $review->criteria_relevance ?? '') }}"
                               min="0" 
                               max="20" 
                               step="1"
                               required
                               class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                               onchange="updateTotalScore()">
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            <p>• <strong>17-20:</strong> Uses medical images and data science</p>
                            <p>• <strong>11-16:</strong> Non-images but medical and data science</p>
                            <p>• <strong>6-10:</strong> Medical but no data science OR data science without medicine</p>
                            <p>• <strong>0-5:</strong> Not relevant</p>
                        </div>
                    </div>
                    
                    <!-- 2. Originality & Innovation (20) -->
                    <div class="border-b pb-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <label class="block text-md font-medium text-gray-800 mb-1">
                                    2. Originality & Innovation <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Does the paper present novel ideas or approaches?</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">Max: 20</span>
                        </div>
                        <input type="number" 
                               name="criteria_originality" 
                               id="criteria_originality"
                               value="{{ old('criteria_originality', $review->criteria_originality ?? '') }}"
                               min="0" 
                               max="20" 
                               step="1"
                               required
                               class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                               onchange="updateTotalScore()">
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            <p>• <strong>16-20:</strong> Highly original</p>
                            <p>• <strong>11-15:</strong> Some originality</p>
                            <p>• <strong>6-10:</strong> Limited originality</p>
                            <p>• <strong>0-5:</strong> No originality</p>
                        </div>
                    </div>
                    
                    <!-- 3. Technical/Academic Quality (15) -->
                    <div class="border-b pb-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <label class="block text-md font-medium text-gray-800 mb-1">
                                    3. Technical/Academic Quality <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Rigor of methodology, soundness of approach, depth of analysis</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">Max: 15</span>
                        </div>
                        <input type="number" 
                               name="criteria_quality" 
                               id="criteria_quality"
                               value="{{ old('criteria_quality', $review->criteria_quality ?? '') }}"
                               min="0" 
                               max="15" 
                               step="1"
                               required
                               class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                               onchange="updateTotalScore()">
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            <p>• <strong>12-15:</strong> Excellent rigor and depth</p>
                            <p>• <strong>8-11:</strong> Good quality</p>
                            <p>• <strong>4-7:</strong> Fair quality</p>
                            <p>• <strong>0-3:</strong> Weak or flawed</p>
                        </div>
                    </div>
                    
                    <!-- 4. Practical Impact & Applicability (15) -->
                    <div class="border-b pb-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <label class="block text-md font-medium text-gray-800 mb-1">
                                    4. Practical Impact & Applicability <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Real-world relevance and potential for adoption</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">Max: 15</span>
                        </div>
                        <input type="number" 
                               name="criteria_impact" 
                               id="criteria_impact"
                               value="{{ old('criteria_impact', $review->criteria_impact ?? '') }}"
                               min="0" 
                               max="15" 
                               step="1"
                               required
                               class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                               onchange="updateTotalScore()">
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            <p>• <strong>12-15:</strong> Highly impactful with great adoption potential</p>
                            <p>• <strong>8-11:</strong> Moderately useful</p>
                            <p>• <strong>4-7:</strong> Limited impact</p>
                            <p>• <strong>0-3:</strong> No clear application</p>
                        </div>
                    </div>
                    
                    <!-- 5. Clarity & Organization (15) -->
                    <div class="border-b pb-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <label class="block text-md font-medium text-gray-800 mb-1">
                                    5. Clarity & Organization <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Structure, writing quality, and presentation</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">Max: 15</span>
                        </div>
                        <input type="number" 
                               name="criteria_clarity" 
                               id="criteria_clarity"
                               value="{{ old('criteria_clarity', $review->criteria_clarity ?? '') }}"
                               min="0" 
                               max="15" 
                               step="1"
                               required
                               class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                               onchange="updateTotalScore()">
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            <p>• <strong>12-15:</strong> Very clear and well-structured</p>
                            <p>• <strong>8-11:</strong> Generally clear</p>
                            <p>• <strong>4-7:</strong> Somewhat unclear</p>
                            <p>• <strong>0-3:</strong> Poorly organized</p>
                        </div>
                    </div>
                    
                    <!-- 6. Contribution to Knowledge (15) -->
                    <div class="pb-2">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <label class="block text-md font-medium text-gray-800 mb-1">
                                    6. Contribution to Knowledge <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Advances the field and adds new insights</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">Max: 15</span>
                        </div>
                        <input type="number" 
                               name="criteria_contribution" 
                               id="criteria_contribution"
                               value="{{ old('criteria_contribution', $review->criteria_contribution ?? '') }}"
                               min="0" 
                               max="15" 
                               step="1"
                               required
                               class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                               onchange="updateTotalScore()">
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            <p>• <strong>12-15:</strong> Excellent contribution</p>
                            <p>• <strong>8-11:</strong> Moderate contribution</p>
                            <p>• <strong>4-7:</strong> Fair contribution</p>
                            <p>• <strong>0-3:</strong> Very weak contribution</p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Score Display -->
                <div class="mt-8 p-4 bg-gray-100 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-800">Total Score:</span>
                        <span class="text-2xl font-bold text-blue-600" id="total_score_display">0</span>
                        <span class="text-gray-600">/ 100</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div id="score_progress" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
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
                    <button type="submit" 
                            name="save_draft" 
                            value="1"
                            class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-save mr-2"></i>Save as Draft
                    </button>
                    @endif
                    
                    <button type="submit" 
                            name="submit_review" 
                            value="1"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700"
                            id="submitBtn">
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
                    <span>Score fairly based on the criteria provided</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Total score calculation
    function updateTotalScore() {
        const relevance = parseInt(document.getElementById('criteria_relevance').value) || 0;
        const originality = parseInt(document.getElementById('criteria_originality').value) || 0;
        const quality = parseInt(document.getElementById('criteria_quality').value) || 0;
        const impact = parseInt(document.getElementById('criteria_impact').value) || 0;
        const clarity = parseInt(document.getElementById('criteria_clarity').value) || 0;
        const contribution = parseInt(document.getElementById('criteria_contribution').value) || 0;
        
        const total = relevance + originality + quality + impact + clarity + contribution;
        
        document.getElementById('total_score_display').innerText = total;
        
        const percentage = (total / 100) * 100;
        document.getElementById('score_progress').style.width = percentage + '%';
        
        // Change color based on score
        const progressBar = document.getElementById('score_progress');
        if (total >= 80) {
            progressBar.classList.remove('bg-yellow-500', 'bg-red-500');
            progressBar.classList.add('bg-green-600');
        } else if (total >= 60) {
            progressBar.classList.remove('bg-green-600', 'bg-red-500');
            progressBar.classList.add('bg-yellow-500');
        } else {
            progressBar.classList.remove('bg-green-600', 'bg-yellow-500');
            progressBar.classList.add('bg-red-500');
        }
        
        return total;
    }
    
    // Validate form before submission
    const form = document.getElementById('reviewForm');
    if (form) {
        form.addEventListener('submit', function(e) {
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
            
            // Validate for final submission - only check criteria
            const relevance = document.getElementById('criteria_relevance').value;
            const originality = document.getElementById('criteria_originality').value;
            const quality = document.getElementById('criteria_quality').value;
            const impact = document.getElementById('criteria_impact').value;
            const clarity = document.getElementById('criteria_clarity').value;
            const contribution = document.getElementById('criteria_contribution').value;
            
            let errors = [];
            
            // Validate each criterion
            if (relevance === '') errors.push('Please enter a score for Relevance to Conference Theme.');
            if (originality === '') errors.push('Please enter a score for Originality & Innovation.');
            if (quality === '') errors.push('Please enter a score for Technical/Academic Quality.');
            if (impact === '') errors.push('Please enter a score for Practical Impact & Applicability.');
            if (clarity === '') errors.push('Please enter a score for Clarity & Organization.');
            if (contribution === '') errors.push('Please enter a score for Contribution to Knowledge.');
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errors.join('\n'));
                return false;
            }
            
            // Calculate total score
            const total = updateTotalScore();
            
            // Optional: Add a confirmation for low scores
            if (total < 40) {
                if (!confirm('The paper received a low score (' + total + '/100). Are you sure you want to submit this review?')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            console.log('Validation passed, total score: ' + total);
            return true;
        });
    }
    
    // Initialize total score on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateTotalScore();
    });
</script>
@endsection