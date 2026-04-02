@extends('layouts.app')

@section('title', 'Assign Reviewers - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Assign Reviewers to Paper</h1>
        
        <!-- Paper Info -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Paper Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Paper ID</p>
                    <p class="font-medium">{{ $paper->anonymous_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Title</p>
                    <p class="font-medium">{{ Str::limit($paper->title, 50) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Topic Area</p>
                    <p class="font-medium">{{ $paper->topic_area }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Current Assignments</p>
                    <p class="font-medium">{{ $paper->reviews->whereIn('status', ['pending', 'accepted'])->count() }}/3 reviewers</p>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('assignments.manual') }}">
            @csrf
            <input type="hidden" name="paper_id" value="{{ $paper->id }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Suggested Reviewers -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Suggested Reviewers</h3>
                    <p class="text-sm text-gray-600 mb-4">Based on expertise matching and availability (100-point scale)</p>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($suggestedReviewers as $suggestion)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-blue-700">
                                            {{ strtoupper(substr($suggestion['first_name'] ?? '', 0, 1) . substr($suggestion['last_name'] ?? '', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $suggestion['full_name'] ?? 'Unknown Reviewer' }}</div>
                                        <div class="text-sm text-gray-500">{{ $suggestion['email'] ?? '' }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <!-- <span class="text-xs px-2 py-1 rounded-full 
                                                {{ ($suggestion['match_score'] ?? 0) >= 80 ? 'bg-green-100 text-green-800' : 
                                                (($suggestion['match_score'] ?? 0) >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                Score: {{ number_format($suggestion['match_score'] ?? 0, 0) }}%
                                            </span> -->
                                            <span class="text-xs px-2 py-1 bg-gray-100 rounded-full">
                                                Load: {{ $suggestion['assigned_count'] ?? 0 }}/10
                                            </span>
                                            @if(isset($suggestion['expertise_score']))
                                            <span class="text-xs px-2 py-1 bg-purple-100 rounded-full">
                                                Expertise: {{ $suggestion['expertise_score'] ?? 0 }}%
                                            </span>
                                            @endif
                                        </div>
                                        @if(isset($suggestion['expertise']) && count($suggestion['expertise']) > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($suggestion['expertise']->take(2) as $exp)
                                            <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">{{ $exp['name'] }}</span>
                                            @endforeach
                                            @if(count($suggestion['expertise']) > 2)
                                            <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">+{{ count($suggestion['expertise']) - 2 }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="ml-3">
                                <input type="checkbox" 
                                    name="reviewer_ids[]" 
                                    value="{{ $suggestion['id'] ?? 0 }}" 
                                    id="reviewer_{{ $suggestion['id'] ?? 0 }}"
                                    class="rounded text-blue-600 w-5 h-5">
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center p-4">No suggestions available. Use manual selection below.</p>
                        @endforelse
                    </div>
                </div>
                
                <!-- Manual Selection -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">All Reviewers ({{ $reviewers->count() }})</h3>
                    <p class="text-sm text-gray-600 mb-4">Only users marked as reviewers are shown</p>
                    
                    <div class="mb-4">
                        <input type="text" 
                               placeholder="Search reviewers by name or email..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                               id="searchReviewers">
                    </div>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($reviewers as $reviewer)
                        <div class="reviewer-item flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50" 
                             data-name="{{ strtolower($reviewer->full_name) }}"
                             data-email="{{ strtolower($reviewer->email) }}">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-blue-700">
                                        {{ strtoupper(substr($reviewer->first_name, 0, 1) . substr($reviewer->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $reviewer->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reviewer->email }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @php
                                            $loadCount = $reviewer->reviewAssignments->whereIn('status', ['pending', 'accepted'])->count();
                                        @endphp
                                        Current load: {{ $loadCount }} papers
                                        @if($loadCount >= 5)
                                            <span class="text-red-500 ml-2">(High load)</span>
                                        @elseif($loadCount >= 3)
                                            <span class="text-yellow-500 ml-2">(Medium load)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <input type="checkbox" 
                                   name="reviewer_ids[]" 
                                   value="{{ $reviewer->id }}" 
                                   id="manual_reviewer_{{ $reviewer->id }}"
                                   class="rounded text-blue-600">
                        </div>
                        @empty
                        <p class="text-gray-500 text-center p-4">No reviewers found. Make sure users are marked as reviewers.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Selected Reviewers Summary -->
            <div class="bg-blue-50 rounded-xl shadow p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Selected Reviewers</h3>
                <div id="selectedReviewers" class="space-y-3">
                    <p class="text-gray-500" id="noSelectionText">No reviewers selected yet.</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('assignments.index', ['year' => $year, 'tab' => $tab]) }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-user-check mr-2"></i>Assign Selected Reviewers
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('searchReviewers')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const reviewerItems = document.querySelectorAll('.reviewer-item');
        
        reviewerItems.forEach(item => {
            const name = item.dataset.name;
            const email = item.dataset.email;
            
            if (name.includes(query) || email.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Update selected reviewers display
    function updateSelectedReviewers() {
        const selectedCheckboxes = document.querySelectorAll('input[name="reviewer_ids[]"]:checked');
        const container = document.getElementById('selectedReviewers');
        const noSelectionText = document.getElementById('noSelectionText');
        
        container.innerHTML = '';
        
        if (selectedCheckboxes.length === 0) {
            container.innerHTML = '<p class="text-gray-500" id="noSelectionText">No reviewers selected yet.</p>';
            return;
        }
        
        selectedCheckboxes.forEach(checkbox => {
            const reviewerId = checkbox.value;
            const reviewerItem = checkbox.closest('.flex.items-center');
            const reviewerName = reviewerItem?.querySelector('.font-medium')?.textContent || `Reviewer ${reviewerId}`;
            
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-white rounded-lg border border-blue-100';
            div.innerHTML = `
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center mr-3">
                        <span class="text-xs font-medium text-blue-800">R</span>
                    </div>
                    <span class="font-medium text-gray-900">${reviewerName}</span>
                </div>
                <button type="button" onclick="uncheckReviewer(${reviewerId})" 
                        class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        });
    }
    
    // Uncheck reviewer
    function uncheckReviewer(reviewerId) {
        const checkbox = document.querySelector(`input[value="${reviewerId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            updateSelectedReviewers();
        }
    }
    
    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="reviewer_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedReviewers);
        });
        
        // Initial update
        updateSelectedReviewers();
    });
</script>
@endsection