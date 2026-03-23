@extends('layouts.app')

@section('title', 'Paper Bidding - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Paper Bidding</h1>
        
        <!-- Stats & Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Available Papers for Review</h2>
                    <p class="text-gray-600">Express your interest in reviewing these papers</p>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <select id="topicFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Topics</option>
                        <option value="AI in Healthcare">AI in Healthcare</option>
                        <option value="Medical Imaging">Medical Imaging</option>
                        <option value="Clinical Data Science">Clinical Data Science</option>
                        <option value="Diagnostic Algorithms">Diagnostic Algorithms</option>
                        <option value="Healthcare Analytics">Healthcare Analytics</option>
                    </select>
                    
                    <select id="sortFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="newest">Newest First</option>
                        <option value="title">Title (A-Z)</option>
                        <option value="topic">Topic Area</option>
                    </select>
                    
                    <button id="saveBids" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Save All Bids
                    </button>
                </div>
            </div>
            
            <!-- My Bids Summary -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-700">{{ $biddingStats['very_high'] }}</p>
                    <p class="text-sm text-blue-600">Very High Interest</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-700">{{ $biddingStats['high'] + $biddingStats['medium'] }}</p>
                    <p class="text-sm text-green-600">Positive Bids</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-700">{{ $biddingStats['low'] + $biddingStats['very_low'] }}</p>
                    <p class="text-sm text-yellow-600">Low Interest</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-700">{{ $biddingStats['conflict'] }}</p>
                    <p class="text-sm text-red-600">Conflicts</p>
                </div>
            </div>
        </div>
        
        <!-- Papers Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($papers as $paper)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover-lift">
                <div class="p-6">
                    <!-- Paper Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mb-2">
                                {{ $paper->anonymous_id }}
                            </span>
                            <h3 class="font-bold text-gray-900 mb-2">{{ $paper->title }}</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                {{ $paper->submission_type }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Paper Details -->
                    <div class="mb-6">
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $paper->abstract }}</p>
                        
                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-tag mr-2"></i>
                                <span>{{ $paper->topic_area }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-key mr-2"></i>
                                <span>{{ $paper->keywords }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar mr-2"></i>
                                <span>Submitted: {{ $paper->submitted_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bidding Interface -->
                    <div class="border-t pt-6">
                        <h4 class="font-medium text-gray-700 mb-4">Your Interest Level</h4>
                        
                        <div class="space-y-3">
                            <!-- Bidding Options -->
                            <div class="grid grid-cols-3 gap-2">
                                @php
                                    $biddingOptions = [
                                        'very_high' => ['Very High', 'bg-green-100 text-green-800 border-green-300'],
                                        'high' => ['High', 'bg-green-50 text-green-700 border-green-200'],
                                        'medium' => ['Medium', 'bg-blue-50 text-blue-700 border-blue-200'],
                                        'low' => ['Low', 'bg-yellow-50 text-yellow-700 border-yellow-200'],
                                        'very_low' => ['Very Low', 'bg-red-50 text-red-700 border-red-200'],
                                        'conflict' => ['Conflict', 'bg-red-100 text-red-800 border-red-300'],
                                        'no_bid' => ['No Bid', 'bg-gray-100 text-gray-700 border-gray-300'],
                                    ];
                                    
                                    $currentBid = $paper->bids->where('reviewer_id', auth()->id())->first();
                                    $currentPreference = $currentBid ? $currentBid->preference : 'no_bid';
                                @endphp
                                
                                @foreach($biddingOptions as $value => [$label, $classes])
                                <label class="relative">
                                    <input type="radio" name="bid[{{ $paper->id }}]" value="{{ $value }}" 
                                           class="sr-only peer bid-radio" 
                                           data-paper="{{ $paper->id }}"
                                           {{ $currentPreference == $value ? 'checked' : '' }}>
                                    <div class="border rounded-lg p-3 text-center cursor-pointer transition-all peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200 {{ $classes }}">
                                        <p class="text-xs font-medium">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            
                            <!-- Comments -->
                            <div class="mt-4">
                                <textarea id="comments-{{ $paper->id }}" 
                                          class="bid-comments w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                          placeholder="Optional comments about your bid..."
                                          rows="2">{{ $currentBid ? $currentBid->comments : '' }}</textarea>
                            </div>
                            
                            <!-- Expertise Assessment -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Expertise in this Area</label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500">Novice</span>
                                    <div class="flex-1 flex justify-between px-2">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center">
                                            <input type="radio" name="expertise[{{ $paper->id }}]" value="{{ $i }}" 
                                                   class="h-4 w-4 text-blue-600 expertise-radio"
                                                   data-paper="{{ $paper->id }}"
                                                   {{ ($currentBid && isset($currentBid->expertise_scores['overall']) && $currentBid->expertise_scores['overall'] == $i) ? 'checked' : '' }}>
                                            <span class="mt-1 text-xs">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500">Expert</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="bg-gray-50 px-6 py-4 border-t">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('papers.download', $paper) }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-download mr-1"></i> Download Paper
                        </a>
                        <button type="button" onclick="saveBid({{ $paper->id }})" 
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            Save Bid
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($papers->hasPages())
        <div class="mt-8">
            {{ $papers->links() }}
        </div>
        @endif
        
        @if($papers->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No papers available for bidding at the moment.</p>
        </div>
        @endif
    </div>
</div>

<script>
    let bidsToSave = {};
    let isSaving = false;
    
    console.log('Bidding page loaded');
    
    // Function to initialize bids from existing data
    function initializeBids() {
        document.querySelectorAll('.bid-radio:checked').forEach(radio => {
            const paperId = radio.dataset.paper;
            if (!paperId) {
                console.error('No paper ID found for radio');
                return;
            }
            
            const comments = document.getElementById(`comments-${paperId}`);
            const expertise = document.querySelector(`input[name="expertise[${paperId}]"]:checked`);
            
            bidsToSave[paperId] = {
                preference: radio.value,
                comments: comments ? comments.value : '',
                expertise_scores: expertise ? { overall: parseInt(expertise.value) } : null
            };
            
            console.log(`Initialized bid for paper ${paperId}:`, bidsToSave[paperId]);
        });
    }
    
    // Track bid changes
    document.querySelectorAll('.bid-radio').forEach(radio => {
        radio.addEventListener('change', function(e) {
            const paperId = this.dataset.paper;
            if (!paperId) {
                console.error('No paper ID found for radio');
                return;
            }
            
            console.log(`Bid changed for paper ${paperId} to:`, this.value);
            
            if (!bidsToSave[paperId]) {
                bidsToSave[paperId] = {};
            }
            bidsToSave[paperId].preference = this.value;
            
            // Visual feedback
            const container = this.closest('.grid');
            if (container) {
                container.querySelectorAll('.bid-radio').forEach(r => {
                    const div = r.nextElementSibling;
                    if (div) {
                        div.classList.remove('ring-2', 'ring-blue-200', 'border-blue-500');
                    }
                });
                const selectedDiv = this.nextElementSibling;
                if (selectedDiv) {
                    selectedDiv.classList.add('ring-2', 'ring-blue-200', 'border-blue-500');
                }
            }
        });
    });
    
    // Track comment changes
    document.querySelectorAll('.bid-comments').forEach(textarea => {
        textarea.addEventListener('input', function() {
            const paperId = this.id.split('-')[1];
            if (!paperId) {
                console.error('Could not extract paper ID from:', this.id);
                return;
            }
            
            console.log(`Comment changed for paper ${paperId}`);
            
            if (!bidsToSave[paperId]) {
                bidsToSave[paperId] = {};
            }
            bidsToSave[paperId].comments = this.value;
        });
    });
    
    // Track expertise changes - FIXED to send as array/object
    document.querySelectorAll('.expertise-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const paperId = this.dataset.paper;
            if (!paperId) {
                console.error('No paper ID found for expertise radio');
                return;
            }
            
            const expertiseValue = parseInt(this.value);
            console.log(`Expertise changed for paper ${paperId} to:`, expertiseValue);
            
            if (!bidsToSave[paperId]) {
                bidsToSave[paperId] = {};
            }
            
            // Format as expertise_scores array/object as expected by controller
            bidsToSave[paperId].expertise_scores = { overall: expertiseValue };
        });
    });
    
    // Save individual bid
    function saveBid(paperId) {
        console.log('saveBid called for paper:', paperId);
        console.log('Current bidsToSave:', bidsToSave);
        
        const bidData = bidsToSave[paperId];
        if (!bidData || !bidData.preference) {
            showNotification('Please select an interest level first.', 'error');
            return;
        }
        
        if (isSaving) {
            showNotification('Please wait, saving in progress...', 'warning');
            return;
        }
        
        isSaving = true;
        
        // Get the button that triggered this
        const button = document.querySelector(`button[onclick="saveBid(${paperId})"]`);
        const originalText = button ? button.innerHTML : 'Save Bid';
        if (button) {
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';
            button.disabled = true;
        }
        
        // Prepare payload - matches controller expectations
        const payload = {
            paper_id: paperId,
            preference: bidData.preference,
            comments: bidData.comments || '',
            expertise_scores: bidData.expertise_scores || null
        };
        
        console.log('Sending payload:', payload);
        
        fetch('{{ route("bids.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showNotification('Bid saved successfully!', 'success');
                delete bidsToSave[paperId];
            } else {
                showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showNotification('Error saving bid: ' + (error.message || 'Network error'), 'error');
        })
        .finally(() => {
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
            isSaving = false;
        });
    }
    
    // Save all bids
    const saveAllBtn = document.getElementById('saveBids');
    if (saveAllBtn) {
        saveAllBtn.addEventListener('click', function() {
            console.log('Save All Bids clicked');
            console.log('Bids to save:', bidsToSave);
            
            const pendingBids = Object.keys(bidsToSave);
            if (pendingBids.length === 0) {
                showNotification('No changes to save.', 'warning');
                return;
            }
            
            if (isSaving) {
                showNotification('Please wait, saving in progress...', 'warning');
                return;
            }
            
            // Validate all bids have preference selected
            let hasInvalid = false;
            for (const [paperId, data] of Object.entries(bidsToSave)) {
                if (!data.preference) {
                    showNotification(`Paper ${paperId} has no interest level selected.`, 'error');
                    hasInvalid = true;
                    break;
                }
            }
            
            if (hasInvalid) return;
            
            isSaving = true;
            
            // Format bids array as expected by controller
            const bidsArray = Object.entries(bidsToSave).map(([paperId, data]) => ({
                paper_id: parseInt(paperId),
                preference: data.preference,
                comments: data.comments || '',
                expertise_scores: data.expertise_scores || null
            }));
            
            console.log('Sending bids:', bidsArray);
            
            saveAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            saveAllBtn.disabled = true;
            
            fetch('{{ route("bids.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ bids: bidsArray })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification(`Saved ${data.count || bidsArray.length} bids successfully!`, 'success');
                    bidsToSave = {};
                } else {
                    showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving bids: ' + (error.message || 'Network error'), 'error');
            })
            .finally(() => {
                saveAllBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save All Bids';
                saveAllBtn.disabled = false;
                isSaving = false;
            });
        });
    }
    
    // Filter and sort functions
    function filterPapers() {
        const topic = document.getElementById('topicFilter').value;
        const sort = document.getElementById('sortFilter').value;
        console.log('Filtering by:', { topic, sort });
        // You can implement actual filtering logic here
    }
    
    // Notification function
    function showNotification(message, type) {
        console.log('Notification:', type, message);
        
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${colors[type] || 'bg-blue-500'} z-50`;
        notification.textContent = message;
        notification.style.zIndex = '1000';
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded');
        initializeBids();
        
        // Add filter event listeners
        const topicFilter = document.getElementById('topicFilter');
        const sortFilter = document.getElementById('sortFilter');
        
        if (topicFilter) topicFilter.addEventListener('change', filterPapers);
        if (sortFilter) sortFilter.addEventListener('change', filterPapers);
        
        // Test if buttons exist
        const saveBtns = document.querySelectorAll('[onclick^="saveBid"]');
        console.log('Found save buttons:', saveBtns.length);
        
        const saveAllBtn = document.getElementById('saveBids');
        console.log('Save All Bids button:', saveAllBtn ? 'found' : 'not found');
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