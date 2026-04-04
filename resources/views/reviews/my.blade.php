@extends('layouts.app')

@section('title', 'My Reviews - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Reviews</h1>
                <p class="text-gray-600 mt-2">Review Dashboard - Manage your assigned paper reviews</p>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 hover-lift">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['total'] ?? $reviews->total() }}</p>
                        <p class="text-sm text-gray-500">Total Assigned</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6 hover-lift">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['pending'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Pending Acceptance</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6 hover-lift">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                        <i class="fas fa-spinner text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['in_progress'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">In Progress</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6 hover-lift">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['completed'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Completed</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reviews Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Assigned Papers for Review</h3>
                    @if($overdueCount > 0)
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                        {{ $overdueCount }} Overdue
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reviews as $review)
                        <tr class="{{ $review->deadline && $review->deadline < now() && !in_array($review->status, ['completed', 'declined']) ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-mono font-medium text-gray-900 mb-1">
                                        {{ $review->paper->anonymous_id ?? 'Unknown Paper' }}
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        {{ Str::limit($review->paper->title ?? 'No title', 60) }}
                                    </div>
                                    <div class="flex items-center mt-2">
                                        @if($review->paper->topic_area)
                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded mr-2">
                                            {{ $review->paper->topic_area }}
                                        </span>
                                        @endif
                                        @if($review->paper->submission_type)
                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                            {{ ucfirst($review->paper->submission_type) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-blue-100 text-blue-800',
                                        'under_review' => 'bg-blue-100 text-blue-800',
                                        'declined' => 'bg-red-100 text-red-800',
                                        'in_progress' => 'bg-indigo-100 text-indigo-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                    ];
                                    $colorClass = $statusColors[$review->status] ?? 'bg-gray-100 text-gray-800';
                                    $statusText = $review->status === 'under_review' ? 'Under Review' : ucfirst(str_replace('_', ' ', $review->status));
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($review->deadline)
                                <div class="text-sm text-gray-900">
                                    {{ $review->deadline->format('M d, Y') }}
                                </div>
                                <div class="text-xs {{ $review->deadline < now() && !in_array($review->status, ['completed', 'declined']) ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                    @if($review->deadline < now() && !in_array($review->status, ['completed', 'declined']))
                                    Overdue by {{ $review->deadline->diffForHumans(null, false, false, 2) }}
                                    @elseif(!in_array($review->status, ['completed', 'declined']))
                                    Due {{ $review->deadline->diffForHumans() }}
                                    @else
                                    Completed
                                    @endif
                                </div>
                                @else
                                <span class="text-sm text-gray-400">No deadline</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @if($review->status === 'pending')
                                        <form method="POST" action="{{ route('reviews.accept', $review) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 hover-lift transition duration-300 flex items-center">
                                                <i class="fas fa-clipboard-check mr-1"></i> Accept & Review
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('reviews.decline', $review) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 hover-lift transition duration-300 flex items-center"
                                                    onclick="return confirm('Are you sure you want to decline this review?')">
                                                <i class="fas fa-times mr-1"></i> Decline
                                            </button>
                                        </form>
                                    @elseif($review->status === 'under_review') 
                                        <a href="{{ route('reviews.edit', $review) }}" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 hover-lift transition duration-300 flex items-center">
                                            <i class="fas fa-play mr-1"></i> Start Review
                                        </a>
                                        <a href="{{ route('papers.show', $review->paper) }}" 
                                        target="_blank"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 hover-lift transition duration-300 flex items-center">
                                            <i class="fas fa-file-alt mr-1"></i> View Paper
                                        </a>
                                    @elseif($review->status === 'in_progress')
                                        <a href="{{ route('reviews.edit', $review) }}" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 hover-lift transition duration-300 flex items-center">
                                            <i class="fas fa-edit mr-1"></i> Continue Review
                                        </a>
                                        <a href="{{ route('papers.show', $review->paper) }}" 
                                        target="_blank"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 hover-lift transition duration-300 flex items-center">
                                            <i class="fas fa-file-alt mr-1"></i> View Paper
                                        </a>
                                    @elseif($review->status === 'completed')
                                        <a href="{{ route('reviews.show', $review) }}" 
                                        class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200 hover-lift transition duration-300 flex items-center">
                                            <i class="fas fa-eye mr-1"></i> View Review
                                        </a>
                                        <a href="{{ route('papers.show', $review->paper) }}" 
                                        target="_blank"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 hover-lift transition duration-300 flex items-center">
                                            <i class="fas fa-file-alt mr-1"></i> View Paper
                                        </a>
                                    @elseif($review->status === 'declined')
                                        <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm flex items-center">
                                            <i class="fas fa-ban mr-1"></i> Declined
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-clipboard-list text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-700 mb-2">No reviews assigned</p>
                                    <p class="text-gray-600 mb-6">You don't have any paper reviews assigned yet.</p>
                                    @if(auth()->user()->is_reviewer)
                                    <a href="{{ route('bidding.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover-lift transition duration-300">
                                        <i class="fas fa-hand-paper mr-2"></i>Browse papers for bidding
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reviews->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $reviews->links() }}
            </div>
            @endif
        </div>
        
        <!-- Additional Info -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Review Guidelines</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        <span class="text-gray-700">Provide constructive and specific feedback</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        <span class="text-gray-700">Focus on scientific merit and methodology</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        <span class="text-gray-700">Maintain confidentiality of the review process</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        <span class="text-gray-700">Submit reviews by the deadline</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        <span class="text-gray-700">Provide both comments for authors and confidential comments for chairs</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Review Status Guide</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-yellow-500 mr-3"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Pending</p>
                            <p class="text-xs text-gray-500">Awaiting your acceptance/rejection</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-blue-500 mr-3"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Accepted</p>
                            <p class="text-xs text-gray-500">Accepted, ready to start review</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-indigo-500 mr-3"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">In Progress</p>
                            <p class="text-xs text-gray-500">Review started, draft saved</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-green-500 mr-3"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Completed</p>
                            <p class="text-xs text-gray-500">Review submitted successfully</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-red-500 mr-3"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Declined</p>
                            <p class="text-xs text-gray-500">Review assignment declined</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="mt-8 bg-blue-50 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">Quick Tips</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg">
                    <div class="text-blue-600 mb-2">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <p class="font-medium text-gray-800">Check Deadlines</p>
                    <p class="text-sm text-gray-600 mt-1">Review deadlines are strict. Plan your time accordingly.</p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <div class="text-blue-600 mb-2">
                        <i class="fas fa-save text-xl"></i>
                    </div>
                    <p class="font-medium text-gray-800">Save Drafts</p>
                    <p class="text-sm text-gray-600 mt-1">Save your work regularly using the "Save Draft" button.</p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <div class="text-blue-600 mb-2">
                        <i class="fas fa-question-circle text-xl"></i>
                    </div>
                    <p class="font-medium text-gray-800">Need Help?</p>
                    <p class="text-sm text-gray-600 mt-1">Contact the conference chairs if you have questions.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                if (!this.classList.contains('bg-red-50')) {
                    this.classList.add('bg-gray-50');
                }
            });
            row.addEventListener('mouseleave', function() {
                this.classList.remove('bg-gray-50');
            });
        });
        
        // Auto-refresh page every 5 minutes to check for new assignments
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes
    });
</script>
@endsection