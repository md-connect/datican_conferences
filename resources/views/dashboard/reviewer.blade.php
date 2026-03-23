@extends('layouts.app')

@section('title', 'Reviewer Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Reviewer Dashboard</h1>
        <p class="text-gray-600 mb-8">Welcome back, {{ auth()->user()->first_name }}! Manage your review assignments here.</p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 hover-lift">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['assigned'] }}</p>
                        <p class="text-sm text-gray-500">Assigned Papers</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6 hover-lift">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-spinner text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['in_progress'] }}</p>
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
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['completed'] }}</p>
                        <p class="text-sm text-gray-500">Completed</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6 hover-lift">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $reviewStats['overdue'] }}</p>
                        <p class="text-sm text-gray-500">Overdue</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow p-6 mb-8 hover-lift">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('bidding.index') }}" 
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover-lift transition duration-300">
                    <i class="fas fa-hand-paper mr-2"></i>Browse & Bid on Papers
                </a>
                <a href="{{ route('reviews.my') }}" 
                class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 hover-lift transition duration-300">
                    <i class="fas fa-clipboard-check mr-2"></i>My Reviews
                </a>
                @if($reviewStats['assigned'] > 0)
                <a href="{{ route('reviews.my') }}?status=assigned" 
                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 hover-lift transition duration-300">
                    <i class="fas fa-file-alt mr-2"></i>Review Assigned Papers ({{ $reviewStats['assigned'] }})
                </a>
                @endif
                <a href="{{ route('reviewer.expertise') }}" 
                class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 hover-lift transition duration-300">
                    <i class="fas fa-user-graduate mr-2"></i>Update Expertise Profile
                </a>
            </div>
        </div>
        
        <!-- Pending Reviews -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Assignments -->
            <div class="bg-white rounded-xl shadow hover-lift">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Active Assignments</h2>
                        <span class="text-sm text-gray-500">{{ count($activeReviews) }} papers</span>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($activeReviews as $review)
                    <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="font-mono text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">
                                        {{ $review->paper->anonymous_id }}
                                    </span>
                                    {!! $review->status_badge !!}
                                </div>
                                <h3 class="font-medium text-gray-900 mb-1">{{ Str::limit($review->paper->title, 80) }}</h3>
                                <div class="flex items-center space-x-4 mt-3">
                                    @if($review->due_date)
                                    <span class="flex items-center text-sm {{ $review->due_date->isPast() ? 'text-red-600' : 'text-gray-500' }}">
                                        <i class="fas fa-calendar-day mr-1"></i>
                                        Due: {{ $review->due_date->format('M d, Y') }}
                                    </span>
                                    @endif
                                    <span class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-tag mr-1"></i>
                                        {{ $review->paper->topic_area }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                @if($review->status === 'assigned')
                                <a href="{{ route('reviews.accept', $review->id) }}" 
                                   class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 hover-lift transition duration-300 flex items-center">
                                    <i class="fas fa-check mr-2"></i>Accept
                                </a>
                                <a href="{{ route('reviews.show', $review->id) }}" 
                                   class="mt-2 px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 hover-lift transition duration-300 flex items-center">
                                    <i class="fas fa-eye mr-2"></i>Preview
                                </a>
                                @elseif($review->status === 'in_progress')
                                <a href="{{ route('reviews.edit', $review->id) }}" 
                                   class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover-lift transition duration-300 flex items-center">
                                    <i class="fas fa-edit mr-2"></i>Continue Review
                                </a>
                                <a href="{{ route('reviews.show', $review->id) }}" 
                                   class="mt-2 px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 hover-lift transition duration-300 flex items-center">
                                    <i class="fas fa-eye mr-2"></i>View
                                </a>
                                @elseif($review->status === 'completed')
                                <a href="{{ route('reviews.show', $review->id) }}" 
                                   class="px-4 py-2 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 hover-lift transition duration-300 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>View
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-700 mb-2">No active assignments</p>
                        <p class="text-gray-500 mb-6">You don't have any active review assignments at the moment.</p>
                        <a href="{{ route('bidding.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover-lift transition duration-300">
                            <i class="fas fa-hand-paper mr-2"></i>Browse papers for bidding
                        </a>
                    </div>
                    @endforelse
                </div>
                @if(count($activeReviews) > 0)
                <div class="px-6 py-4 border-t bg-gray-50">
                    <a href="{{ route('reviews.my') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        <span>View all reviews</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Available for Bidding (Excluding Assigned Papers) -->
            <div class="bg-white rounded-xl shadow hover-lift">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Available for Bidding</h2>
                        <span class="text-sm text-gray-500">{{ count($availablePapers) }} papers</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Papers you haven't bid on or been assigned yet</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($availablePapers as $paper)
                    @php
                        // Check if this paper is already assigned to the reviewer
                        $isAssigned = $activeReviews->contains(function($review) use ($paper) {
                            return $review->paper_id === $paper->id;
                        });
                    @endphp
                    
                    @if(!$isAssigned)
                    <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="font-mono text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">
                                        {{ $paper->anonymous_id }}
                                    </span>
                                    <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">
                                        {{ $paper->submission_type }}
                                    </span>
                                </div>
                                <h3 class="font-medium text-gray-900 mb-1">{{ Str::limit($paper->title, 80) }}</h3>
                                <div class="flex items-center space-x-4 mt-3">
                                    <span class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-tag mr-1"></i>
                                        {{ $paper->topic_area }}
                                    </span>
                                    <span class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Submitted: {{ $paper->created_at->format('M d') }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <a href="{{ route('bidding.index') }}#paper-{{ $paper->id }}" 
                                   class="px-4 py-2 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 hover-lift transition duration-300 flex items-center">
                                    <i class="fas fa-hand-paper mr-2"></i>Bid Now
                                </a>
                                <a href="{{ route('papers.show', $paper->id) }}" 
                                   class="mt-2 px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 hover-lift transition duration-300 flex items-center">
                                    <i class="fas fa-eye mr-2"></i>Preview
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-700 mb-2">No papers available for bidding</p>
                        <p class="text-gray-500 mb-6">Check back later for new paper submissions.</p>
                        @if($reviewStats['assigned'] > 0)
                        <a href="{{ route('reviews.my') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 hover-lift transition duration-300">
                            <i class="fas fa-clipboard-check mr-2"></i>Work on your assigned papers
                        </a>
                        @endif
                    </div>
                    @endforelse
                    
                    {{-- Show message if all papers are assigned --}}
                    @if(count($availablePapers) > 0)
                        @php
                            $unassignedCount = 0;
                            foreach($availablePapers as $paper) {
                                $isAssigned = $activeReviews->contains(function($review) use ($paper) {
                                    return $review->paper_id === $paper->id;
                                });
                                if(!$isAssigned) {
                                    $unassignedCount++;
                                }
                            }
                        @endphp
                        
                        @if($unassignedCount === 0 && count($availablePapers) > 0)
                        <div class="px-6 py-8 text-center text-gray-500">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-circle text-2xl text-green-600"></i>
                            </div>
                            <p class="text-lg font-medium text-gray-700 mb-2">All available papers have been assigned to you</p>
                            <p class="text-gray-500 mb-6">Great work! You're currently assigned to all available papers in your expertise area.</p>
                            <a href="{{ route('reviews.my') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover-lift transition duration-300">
                                <i class="fas fa-clipboard-check mr-2"></i>View your assignments
                            </a>
                        </div>
                        @endif
                    @endif
                </div>
                @if(count($availablePapers) > 0)
                    @php
                        $hasUnassignedPapers = false;
                        foreach($availablePapers as $paper) {
                            $isAssigned = $activeReviews->contains(function($review) use ($paper) {
                                return $review->paper_id === $paper->id;
                            });
                            if(!$isAssigned) {
                                $hasUnassignedPapers = true;
                                break;
                            }
                        }
                    @endphp
                    
                    @if($hasUnassignedPapers)
                    <div class="px-6 py-4 border-t bg-gray-50">
                        <a href="{{ route('bidding.index') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                            <span>Browse all papers for bidding</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        
        <!-- Performance Metrics (Optional Section) -->
        @if($reviewStats['completed'] > 0)
        <div class="mt-8 bg-white rounded-xl shadow p-6 hover-lift">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Review Performance</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 border rounded-lg">
                    <p class="text-3xl font-bold text-blue-600">{{ $reviewStats['avg_rating'] ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">Average Rating</p>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <p class="text-3xl font-bold text-green-600">{{ $reviewStats['on_time_percentage'] }}%</p>
                    <p class="text-sm text-gray-500">Reviews on Time</p>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <p class="text-3xl font-bold text-purple-600">{{ $reviewStats['completion_rate'] }}%</p>
                    <p class="text-sm text-gray-500">Completion Rate</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation to stats cards
        const statCards = document.querySelectorAll('.hover-lift');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection