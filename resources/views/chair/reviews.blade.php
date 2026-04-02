@extends('layouts.app')

@section('title', 'Reviews Management - Chair Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reviews Management</h1>
                <p class="text-gray-600 mt-2">Manage all peer reviews for DATICAN Conference {{ $year }} (2 reviewers per paper)</p>
            </div>
            <a href="{{ route('chair.dashboard') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
            <form method="GET" action="{{ route('chair.reviews') }}" class="space-y-4">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reviewer</label>
                        <select name="reviewer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Reviewers</option>
                            @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}" {{ request('reviewer_id') == $reviewer->id ? 'selected' : '' }}>
                                {{ $reviewer->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Paper</label>
                        <input type="text" name="paper_id" placeholder="Search by Paper ID" 
                               value="{{ request('paper_id') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Reviews Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Reviews ({{ $reviews->total() }})</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $reviews->firstItem() }}-{{ $reviews->lastItem() }} of {{ $reviews->total() }}</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score (0-100)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommendation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reviews as $review)
                        <tr class="{{ $review->deadline && $review->deadline < now() && !in_array($review->status, ['completed', 'declined']) ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                            <td class="px-6 py-4">
                                <div class="font-mono text-sm font-medium text-gray-900">{{ $review->paper->anonymous_id }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($review->paper->title, 40) }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    @php
                                        $paperReviews = $review->paper->reviewAssignments->where('status', 'completed')->count();
                                    @endphp
                                    Reviews: {{ $paperReviews }}/2
                                </div>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $review->reviewer->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $review->reviewer->email }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $review->reviewer->expertise->count() }} expertise areas
                                </div>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'under_review' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-indigo-100 text-indigo-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'declined' => 'bg-red-100 text-red-800',
                                    ];
                                    $colorClass = $statusColors[$review->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $review->status)) }}
                                </span>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($review->total_score)
                                <div class="flex flex-col">
                                    <span class="text-lg font-bold 
                                        {{ $review->total_score >= 80 ? 'text-green-600' : ($review->total_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $review->total_score }}/100
                                    </span>
                                    <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="h-1.5 rounded-full 
                                            {{ $review->total_score >= 80 ? 'bg-green-600' : ($review->total_score >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                             style="width: {{ $review->total_score }}%"></div>
                                    </div>
                                </div>
                                @else
                                <span class="text-sm text-gray-400">Not submitted</span>
                                @endif
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($review->recommendation)
                                <span class="text-sm {{ 
                                    $review->recommendation == 'strong_accept' || $review->recommendation == 'accept' ? 'text-green-600' : 
                                    ($review->recommendation == 'weak_reject' || $review->recommendation == 'reject' || $review->recommendation == 'strong_reject' ? 'text-red-600' : 'text-yellow-600') 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $review->recommendation)) }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">N/A</span>
                                @endif
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($review->deadline)
                                <div class="text-sm {{ $review->deadline < now() && !in_array($review->status, ['completed', 'declined']) ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $review->deadline->format('M d, Y') }}
                                </div>
                                @if($review->deadline < now() && !in_array($review->status, ['completed', 'declined']))
                                <div class="text-xs text-red-500">Overdue</div>
                                @endif
                                @else
                                <span class="text-sm text-gray-400">No deadline</span>
                                @endif
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $review->assigned_at ? $review->assigned_at->format('M d, Y') : 'N/A' }}
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-2">
                                    @if($review->status == 'completed')
                                    <a href="{{ route('reviews.show', $review) }}" 
                                       class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                        View
                                    </a>
                                    @endif
                                    
                                    @if(in_array($review->status, ['pending', 'under_review', 'in_progress']))
                                    <button onclick="showReassignModal({{ $review->id }})"
                                            class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                                        Reassign
                                    </button>
                                    <form method="POST" action="{{ route('chair.reviews.remind', $review) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                            Remind
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($review->status == 'pending')
                                    <form method="POST" action="{{ route('assignments.destroy', $review) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to remove this assignment?')"
                                                class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">
                                            Remove
                                        </button>
                                    </form>
                                    @endif
                                </div>
                             </td>
                         </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-clipboard-check text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No reviews found</p>
                                    <p class="text-sm mt-2">Try adjusting your filters</p>
                                </div>
                             </td>
                         </tr>
                        @endforelse
                    </tbody>
                 </table>
            </div>
            
            <!-- Pagination -->
            @if($reviews->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $reviews->withQueryString()->links() }}
            </div>
            @endif
        </div>
        
        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-tasks text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviews->total() }}</p>
                        <p class="text-sm text-gray-500">Total Reviews</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $reviews->where('status', 'completed')->count() }}
                        </p>
                        <p class="text-sm text-gray-500">Completed</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $reviews->where('status', 'pending')->count() }}
                        </p>
                        <p class="text-sm text-gray-500">Pending</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $reviews->where('status', 'declined')->count() }}
                        </p>
                        <p class="text-sm text-gray-500">Declined</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        @php
                            $avgScore = $reviews->where('status', 'completed')->avg('total_score');
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $avgScore ? number_format($avgScore, 0) : 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500">Avg. Score (/100)</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Peer Review Progress -->
        <div class="mt-8 bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Peer Review Progress</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3">Papers by Review Status</h4>
                    @php
                        $papersWithTwoReviews = \App\Models\Paper::whereHas('reviewAssignments', function($q) {
                            $q->where('status', 'completed');
                        }, '>=', 2)->count();
                        
                        $papersWithOneReview = \App\Models\Paper::whereHas('reviewAssignments', function($q) {
                            $q->where('status', 'completed');
                        }, '=', 1)->count();
                        
                        $papersWithNoReviews = \App\Models\Paper::whereDoesntHave('reviewAssignments', function($q) {
                            $q->where('status', 'completed');
                        })->whereIn('status', ['submitted', 'under_review'])->count();
                    @endphp
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>Both Reviews Completed</span>
                                <span class="font-bold text-green-600">{{ $papersWithTwoReviews }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $papersWithTwoReviews > 0 ? min(($papersWithTwoReviews / ($papersWithTwoReviews + $papersWithOneReview + $papersWithNoReviews)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>One Review Completed</span>
                                <span class="font-bold text-yellow-600">{{ $papersWithOneReview }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $papersWithOneReview > 0 ? min(($papersWithOneReview / ($papersWithTwoReviews + $papersWithOneReview + $papersWithNoReviews)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>No Reviews Yet</span>
                                <span class="font-bold text-red-600">{{ $papersWithNoReviews }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $papersWithNoReviews > 0 ? min(($papersWithNoReviews / ($papersWithTwoReviews + $papersWithOneReview + $papersWithNoReviews)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3">Score Distribution</h4>
                    @php
                        $highScores = $reviews->where('total_score', '>=', 80)->count();
                        $mediumScores = $reviews->whereBetween('total_score', [60, 79])->count();
                        $lowScores = $reviews->where('total_score', '<', 60)->where('status', 'completed')->count();
                        $totalCompleted = $reviews->where('status', 'completed')->count();
                    @endphp
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>High (80-100)</span>
                                <span class="font-bold text-green-600">{{ $highScores }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $totalCompleted > 0 ? ($highScores / $totalCompleted) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>Medium (60-79)</span>
                                <span class="font-bold text-yellow-600">{{ $mediumScores }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $totalCompleted > 0 ? ($mediumScores / $totalCompleted) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>Low (0-59)</span>
                                <span class="font-bold text-red-600">{{ $lowScores }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $totalCompleted > 0 ? ($lowScores / $totalCompleted) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3">Review Completion Timeline</h4>
                    @php
                        $thisWeek = $reviews->where('submitted_at', '>=', now()->startOfWeek())->count();
                        $lastWeek = $reviews->whereBetween('submitted_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
                        $older = $reviews->where('submitted_at', '<', now()->subWeeks(2))->count();
                    @endphp
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>This Week</span>
                            <span class="font-bold text-blue-600">{{ $thisWeek }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last Week</span>
                            <span class="font-bold text-purple-600">{{ $lastWeek }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Older</span>
                            <span class="font-bold text-gray-600">{{ $older }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reassign Modal -->
<div id="reassignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reassign Review</h3>
            
            <form id="reassignForm" method="POST" action="">
                @csrf
                <input type="hidden" id="reviewId" name="review_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select New Reviewer</label>
                    <select name="new_reviewer_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        <option value="">Select Reviewer</option>
                        @foreach($reviewers as $reviewer)
                        <option value="{{ $reviewer->id }}">{{ $reviewer->full_name }} ({{ $reviewer->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                              placeholder="Reason for reassignment..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" 
                            onclick="document.getElementById('reassignModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Reassign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showReassignModal(reviewId) {
    document.getElementById('reviewId').value = reviewId;
    document.getElementById('reassignForm').action = '/reviews/' + reviewId + '/reassign';
    document.getElementById('reassignModal').classList.remove('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reassignModal');
    if (event.target == modal) {
        modal.classList.add('hidden');
    }
}
</script>
@endsection