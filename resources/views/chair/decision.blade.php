@extends('layouts.app')

@section('title', 'Paper Decision - ' . $paper->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
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
                            <p class="font-medium">{{ $paper->submitted_at->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Current Status</p>
                            <span class="px-3 py-1 text-xs font-medium rounded-full 
                                @if($paper->status == 'submitted') bg-blue-100 text-blue-800
                                @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
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
        </div>
        
        <!-- Reviews Summary -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Reviews Summary</h3>
            
            @if($paper->reviewAssignments->where('status', 'completed')->count() > 0)
            <div class="space-y-6">
                @foreach($paper->reviewAssignments->where('status', 'completed') as $review)
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-medium text-gray-900">Reviewer {{ $loop->iteration }}</p>
                            <p class="text-sm text-gray-500">{{ $review->reviewer->full_name }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold {{ $review->overall_score >= 4 ? 'text-green-600' : ($review->overall_score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $review->overall_score }}/5
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $review->recommendation)) }}
                            </div>
                        </div>
                    </div>
                    
                    @if($review->summary)
                    <div class="mt-3">
                        <p class="text-sm font-medium text-gray-700 mb-1">Summary</p>
                        <p class="text-sm text-gray-600">{{ Str::limit($review->summary, 200) }}</p>
                    </div>
                    @endif
                    
                    <!-- Show revision suggestions if present -->
                    @if($review->revision_suggestions)
                    <div class="mt-3 p-3 bg-yellow-50 border-l-4 border-yellow-400">
                        <p class="text-sm font-medium text-yellow-800 mb-1">Revision Suggestions</p>
                        <p class="text-sm text-yellow-700">{{ $review->revision_suggestions }}</p>
                    </div>
                    @endif
                    
                    <div class="mt-3 flex space-x-3">
                        <a href="{{ route('reviews.show', $review) }}" 
                        class="text-sm text-blue-600 hover:text-blue-800">
                            View Full Review
                        </a>
                        
                        <!-- Show original review link if this is a revision -->
                        @if($review->original_review_id)
                        <span class="text-sm text-gray-400">|</span>
                        <a href="{{ route('reviews.show', $review->original_review_id) }}" 
                        class="text-sm text-green-600 hover:text-green-800">
                            View Original Review
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
                
                <!-- Revision Recommendations Summary -->
                @if($paper->has_revision_recommendations)
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-blue-800">Revision Recommendations Detected</p>
                            <p class="text-sm text-blue-600 mt-1">
                                {{ $paper->revision_recommendation_count }} out of {{ $paper->reviewAssignments->where('status', 'completed')->count() }} 
                                reviewers have recommended revisions.
                            </p>
                            @if($paper->revision_recommendations_list->count() > 0)
                            <div class="mt-2 text-sm text-blue-700">
                                <strong>Common suggestions:</strong>
                                <ul class="list-disc list-inside mt-1">
                                    @foreach($paper->revision_recommendations_list->take(3) as $suggestion)
                                    <li>{{ Str::limit($suggestion, 100) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Average Score -->
                <div class="bg-gray-50 rounded-lg p-4 mt-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-900">Overall Average Score</p>
                            <p class="text-sm text-gray-500">Based on {{ $paper->reviewAssignments->where('status', 'completed')->count() }} completed reviews</p>
                        </div>
                        <div class="text-right">
                            @php
                                $avgScore = $paper->reviewAssignments->where('status', 'completed')->avg('overall_score');
                            @endphp
                            <div class="text-3xl font-bold {{ $avgScore >= 4 ? 'text-green-600' : ($avgScore >= 3 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($avgScore, 1) }}/5
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <p>No completed reviews available for decision.</p>
                <p class="text-sm mt-2">Wait for reviewers to complete their reviews.</p>
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
                                @elseif($paper->decision == 'reject') bg-red-100 text-red-800
                                @elseif($paper->decision == 'revise') bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($paper->decision) }}
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
            $totalAssignments = $paper->reviewAssignments->count();
            $completedAssignments = $paper->reviewAssignments->where('status', 'completed')->count();
            $allReviewsCompleted = ($totalAssignments > 0) && ($completedAssignments === $totalAssignments);
        @endphp

        @if($allReviewsCompleted)
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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex flex-col items-center p-4 border-2 border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                                <input type="radio" name="decision" value="accept" class="mb-3" required>
                                <i class="fas fa-check-circle text-3xl text-green-600 mb-2"></i>
                                <span class="font-medium text-green-700">Accept</span>
                                <span class="text-xs text-gray-600 text-center mt-1">Paper meets standards</span>
                            </label>
                            
                            <label class="flex flex-col items-center p-4 border-2 border-yellow-200 rounded-lg hover:bg-yellow-50 cursor-pointer">
                                <input type="radio" name="decision" value="revise" class="mb-3" required>
                                <i class="fas fa-edit text-3xl text-yellow-600 mb-2"></i>
                                <span class="font-medium text-yellow-700">Revise & Resubmit</span>
                                <span class="text-xs text-gray-600 text-center mt-1">Needs improvements</span>
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
                            Decision Notes (Optional)
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
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
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
                This paper has {{ $completedAssignments }}/{{ $totalAssignments }} completed reviews.
                <br>
                Wait for all assigned reviewers to complete their reviews before making a decision.
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
        
        console.log('Toggle function called', {
            selectedDecision: selectedDecision ? selectedDecision.value : 'none'
        });
        
        if (selectedDecision && selectedDecision.value === 'revise') {
            console.log('Showing revision deadline field');
            revisionDeadlineContainer.style.display = 'block';
            revisionDeadlineContainer.classList.remove('hidden');
            revisionDeadlineInput.required = true;
            revisionDeadlineInput.disabled = false;
        } else {
            console.log('Hiding revision deadline field');
            revisionDeadlineContainer.style.display = 'none';
            revisionDeadlineContainer.classList.add('hidden');
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
            
            // For revise decision, ensure deadline is set
            if (selectedDecision.value === 'revise') {
                // Make sure field is enabled so its value is submitted
                revisionDeadlineInput.disabled = false;
                
                if (!revisionDeadlineInput.value) {
                    e.preventDefault();
                    alert('Please select a revision deadline date.');
                    revisionDeadlineInput.focus();
                    return false;
                }
            }
            
            // Log that we're allowing submission
            console.log('Form submission allowed');
            return true;
        });
    }
    
    // Initial call
    toggleRevisionDeadline();
    
    // Handle old input
    @if(old('decision') === 'revise')
        console.log('Old decision was revise');
        const reviseRadio = document.querySelector('input[name="decision"][value="revise"]');
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

@endsection
