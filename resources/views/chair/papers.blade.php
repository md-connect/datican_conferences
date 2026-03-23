@extends('layouts.app')

@section('title', 'Papers Management - Chair Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Papers Management</h1>
                <p class="text-gray-600 mt-2">Manage all paper submissions for DATICAN Conference {{ $year }}</p>
            </div>
            <a href="{{ route('chair.dashboard') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Export Button -->
        <div class="mb-6 flex justify-end">
            <a href="{{ route('chair.export.papers') }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
            <form method="GET" action="{{ route('chair.papers') }}" class="space-y-4">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="camera_ready" {{ request('status') == 'camera_ready' ? 'selected' : '' }}>Camera Ready</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Topic Area</label>
                        <select name="topic" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Topics</option>
                            @foreach($topics as $topic)
                            <option value="{{ $topic }}" {{ request('topic') == $topic ? 'selected' : '' }}>{{ $topic }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Submission Type</label>
                        <select name="submission_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="abstract_only" {{ request('submission_type') == 'abstract_only' ? 'selected' : '' }}>Abstract Only</option>
                            <option value="full_paper" {{ request('submission_type') == 'full_paper' ? 'selected' : '' }}>Full Paper</option>
                        </select>
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
        
        <!-- Papers Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Papers ({{ $papers->total() }})</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $papers->firstItem() }}-{{ $papers->lastItem() }} of {{ $papers->total() }}</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SN</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author(s)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author's Institution</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper Title / Topic Area</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviews</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($papers as $index => $paper)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $papers->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-mono text-sm font-medium text-gray-900">{{ $paper->anonymous_id }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $paper->authors->pluck('full_name')->join(', ') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $paper->authors->count() }} author(s)
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-700">
                                    @php
                                        $institutions = $paper->authors->map(function($author) {
                                            return $author->institution ?? 'Not specified';
                                        })->unique()->implode(';<br>');
                                    @endphp
                                    {!! $institutions ?: '<span class="text-gray-400">Not specified</span>' !!}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $paper->title }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="px-2 py-1 bg-gray-100 rounded">{{ $paper->topic_area }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($paper->submission_type == 'abstract_only') bg-orange-100 text-orange-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $paper->submission_type == 'abstract_only' ? 'Abstract Only' : 'Full Paper' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $paper->submitted_at ? $paper->submitted_at->format('M d, Y') : 'Not submitted' }}
                                @if($paper->submitted_at)
                                <div class="text-xs text-gray-400">{{ $paper->submitted_at->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'submitted' => 'bg-blue-100 text-blue-800',
                                        'under_review' => 'bg-yellow-100 text-yellow-800',
                                        'reviewed' => 'bg-purple-100 text-purple-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'camera_ready' => 'bg-emerald-100 text-emerald-800',
                                    ];
                                    $colorClass = $statusColors[$paper->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @php
                                    $completedReviews = $paper->reviewAssignments->where('status', 'completed');
                                    $avgScore = $completedReviews->avg('overall_score');
                                @endphp
                                <div class="text-sm text-center">
                                    <span class="font-medium">{{ $completedReviews->count() }}/{{ $paper->reviewAssignments->count() }}</span>
                                    @if($avgScore)
                                    <div class="text-xs text-gray-500">Avg: {{ number_format($avgScore, 1) }}/5</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('papers.show', $paper) }}" 
                                       class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                                       title="View Paper">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($paper->status == 'under_review' && $completedReviews->count() >= 3)
                                    <a href="{{ route('chair.papers.decision.form', $paper) }}" 
                                       class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200"
                                       title="Make Decision">
                                        <i class="fas fa-gavel"></i>
                                    </a>
                                    @endif
                                    @if($paper->file_path)
                                    <a href="{{ route('papers.download', $paper) }}" 
                                       class="px-3 py-1 text-sm bg-purple-100 text-purple-700 rounded hover:bg-purple-200"
                                       title="Download Paper">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No papers found</p>
                                    <p class="text-sm mt-2">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($papers->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $papers->withQueryString()->links() }}
            </div>
            @endif
        </div>
        
        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-file-alt text-3xl text-blue-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">{{ $papers->total() }}</p>
                    <p class="text-sm text-gray-500">Total Papers</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-spinner text-3xl text-yellow-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $papers->where('status', 'under_review')->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Under Review</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-check-circle text-3xl text-green-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $papers->where('status', 'accepted')->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Accepted</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-times-circle text-3xl text-red-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $papers->where('status', 'rejected')->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Rejected</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-file-powerpoint text-3xl text-orange-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $papers->where('submission_type', 'abstract_only')->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Abstract Only</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection