@extends('layouts.app')

@section('title', 'Review Assignments - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Review Assignments</h1>
        
        <!-- Admin Controls -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Assignment Management</h2>
                    <p class="text-gray-600">Assign reviewers to papers and manage assignments</p>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <form method="POST" action="{{ route('assignments.auto') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-robot mr-2"></i>Auto Assign
                        </button>
                    </form>
                    
                    <a href="{{ route('assignments.config') }}" 
                       class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        <i class="fas fa-cog mr-2"></i>Configure Auto-Assign
                    </a>
                    
                    <form method="GET" action="{{ route('assignments.index') }}" class="inline">
                        <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="{{ date('Y') }}" {{ request('year', date('Y')) == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                            <option value="{{ date('Y') + 1 }}" {{ request('year') == date('Y') + 1 ? 'selected' : '' }}>{{ date('Y') + 1 }}</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <!-- Assignment Stats -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['papers'] }}</p>
                    <p class="text-sm text-blue-600">Papers to Assign</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-700">{{ $stats['reviewers'] }}</p>
                    <p class="text-sm text-green-600">Available Reviewers</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['avg_load'] }}</p>
                    <p class="text-sm text-yellow-600">Avg Load/Reviewer</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-700">{{ $stats['coverage'] }}%</p>
                    <p class="text-sm text-purple-600">Coverage</p>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('assignments.index', ['tab' => 'papers', 'year' => request('year', date('Y'))]) }}" 
                       class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ request('tab', 'papers') == 'papers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Papers
                    </a>
                    <a href="{{ route('assignments.index', ['tab' => 'reviewers', 'year' => request('year', date('Y'))]) }}" 
                       class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') == 'reviewers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Reviewers
                    </a>
                    <a href="{{ route('assignments.index', ['tab' => 'assignments', 'year' => request('year', date('Y'))]) }}" 
                       class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') == 'assignments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Assignments
                    </a>
                    <a href="{{ route('assignments.index', ['tab' => 'conflicts', 'year' => request('year', date('Y'))]) }}" 
                       class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') == 'conflicts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Conflicts
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Papers Tab -->
        @if(request('tab', 'papers') == 'papers')
        <div id="tab-content-papers" class="tab-content">
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Papers Needing Assignments</h3>
                    <div class="flex space-x-2">
                        <input type="text" placeholder="Search papers..." 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-sm"
                               id="searchPapers">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                            <option>Filter by topic...</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Assignments</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bid Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($papers as $paper)
                            <tr>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $paper->anonymous_id }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($paper->title, 50) }}</div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-400">{{ $paper->topic_area }}</span>
                                            @if($paper->status === 'abstract_submitted')
                                            <span class="inline-block px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded">
                                                Abstract Only
                                            </span>
                                            @endif
                                            <!-- Status badge -->
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($paper->status === 'abstract_submitted') bg-orange-100 text-orange-800
                                                @elseif($paper->status === 'submitted') bg-blue-100 text-blue-800
                                                @elseif($paper->status === 'under_review') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <!-- Active Reviewers Section -->
                                    @php
                                        $activeReviews = $paper->reviews->whereIn('status', ['pending', 'under_review', 'in_progress']);
                                        $declinedReviews = $paper->reviews->where('status', 'declined');
                                    @endphp
                                    
                                    @if($activeReviews->count() > 0)
                                    <div class="mb-2">
                                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Reviewers</div>
                                        <div class="flex -space-x-2">
                                            @foreach($activeReviews->take(3) as $review)
                                            <div class="w-8 h-8 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center" 
                                                title="{{ $review->reviewer->full_name ?? 'Unknown' }} ({{ $review->status }})">
                                                <span class="text-xs font-medium text-blue-700">
                                                    @if($review->reviewer)
                                                        {{ strtoupper(substr($review->reviewer->first_name, 0, 1)) }}
                                                    @else
                                                        ?
                                                    @endif
                                                </span>
                                            </div>
                                            @endforeach
                                            @if($activeReviews->count() > 3)
                                            <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700">+{{ $activeReviews->count() - 3 }}</span>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $activeReviews->count() }} active reviewer(s)
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Declined Reviewers Section -->
                                    @if($declinedReviews->count() > 0)
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        <div class="text-xs font-semibold text-red-500 uppercase tracking-wider mb-1 flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-1 text-xs"></i>
                                            Declined ({{ $declinedReviews->count() }})
                                        </div>
                                        <div class="space-y-1">
                                            @foreach($declinedReviews as $review)
                                            <div class="flex items-center text-xs">
                                                <i class="fas fa-user-times text-red-400 mr-2"></i>
                                                <span class="text-gray-600">{{ $review->reviewer->full_name ?? 'Unknown' }}</span>
                                                @if($review->declined_at)
                                                <span class="text-gray-400 ml-2">({{ $review->declined_at->format('M d') }})</span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($activeReviews->count() === 0 && $declinedReviews->count() === 0)
                                    <span class="text-sm text-gray-500">No assignments</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    @php
                                        $bids = $paper->bids;
                                        $positiveBids = $bids->whereIn('preference', ['very_high', 'high', 'medium'])->count();
                                        $conflictBids = $bids->where('preference', 'conflict')->count();
                                    @endphp
                                    <div class="space-y-1">
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                            <span class="text-sm">{{ $positiveBids }} positive bids</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                                            <span class="text-sm">{{ $conflictBids }} conflicts</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('assignments.suggest', ['paper' => $paper->id, 'year' => request('year', date('Y')), 'tab' => 'papers']) }}" 
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">
                                            Suggest
                                        </a>
                                        <a href="{{ route('assignments.assign', ['paper' => $paper->id, 'year' => request('year', date('Y')), 'tab' => 'papers']) }}" 
                                        class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200">
                                            Assign
                                        </a>
                                    </div>
                                    
                                    <!-- Show reassign button if all reviews declined -->
                                    @if($declinedReviews->count() > 0 && $activeReviews->count() === 0)
                                    <div class="mt-2">
                                        <form action="{{ route('assignments.reset', $paper) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-xs text-yellow-600 hover:text-yellow-800"
                                                    onclick="return confirm('This will mark the paper as needing new reviewers. Continue?')">
                                                <i class="fas fa-redo-alt mr-1"></i> Reset for new assignments
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Reviewers Tab -->
        @if(request('tab') == 'reviewers')
        <div id="tab-content-reviewers" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reviewers as $reviewer)
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-start mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <span class="text-lg font-medium text-blue-700">
                                {{ strtoupper(substr($reviewer->first_name, 0, 1) . substr($reviewer->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $reviewer->full_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $reviewer->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Current Load</span>
                                <span class="font-medium">{{ $reviewer->reviewAssignments->whereIn('status', ['pending', 'accepted'])->count() }} papers</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     style="width: {{ min(($reviewer->reviewAssignments->whereIn('status', ['pending', 'accepted'])->count() / 10) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Expertise Areas</h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($reviewer->expertise->take(3) as $expertise)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                    {{ $expertise->topic }}
                                </span>
                                @endforeach
                                @if($reviewer->expertise->count() > 3)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                    +{{ $reviewer->expertise->count() - 3 }} more
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                                View detailed profile →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
        @endif
    </div>
</div>
@endsection