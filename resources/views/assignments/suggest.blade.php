@extends('layouts.app')

@section('title', 'Suggest Reviewers - ' . $paper->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Suggest Reviewers</h1>
                <p class="text-gray-600 mt-2">For paper: {{ $paper->title }}</p>
                <p class="text-sm text-gray-500">Paper ID: {{ $paper->anonymous_id }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('assignments.index', ['year' => $year, 'tab' => $tab]) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Assignments
                </a>
                <a href="{{ route('assignments.assign', ['paper' => $paper->id, 'year' => $year, 'tab' => $tab]) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-user-plus mr-2"></i> Manual Assignment
                </a>
            </div>
        </div>

        <!-- Paper Information -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Paper Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Title</p>
                    <p class="font-medium">{{ $paper->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Topic Area</p>
                    <p class="font-medium">{{ $paper->topic_area }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Keywords</p>
                    <p class="font-medium">{{ $paper->keywords }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Submission Type</p>
                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $paper->submission_type)) }}</p>
                </div>
            </div>
        </div>

        <!-- Suggested Reviewers -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Suggested Reviewers ({{ count($suggestedReviewers) }})</h3>
                <p class="text-sm text-gray-500 mt-1">Based on expertise and current workload</p>
            </div>
            
            @if(count($suggestedReviewers) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institution</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expertise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Load</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($suggestedReviewers as $reviewer)
                        @php
                            // Handle both object and array data
                            $firstName = is_object($reviewer) ? $reviewer->first_name : ($reviewer['first_name'] ?? '');
                            $lastName = is_object($reviewer) ? $reviewer->last_name : ($reviewer['last_name'] ?? '');
                            $email = is_object($reviewer) ? $reviewer->email : ($reviewer['email'] ?? '');
                            $institution = is_object($reviewer) ? ($reviewer->institution ?? 'Not specified') : ($reviewer['institution'] ?? 'Not specified');
                            $assignedCount = is_object($reviewer) ? ($reviewer->assigned_count ?? 0) : ($reviewer['assigned_count'] ?? 0);
                            $matchScore = is_object($reviewer) ? ($reviewer->match_score ?? 0) : ($reviewer['match_score'] ?? 0);
                            $reviewerId = is_object($reviewer) ? $reviewer->id : ($reviewer['id'] ?? 0);
                            
                            // Get expertise list (array of topics with levels)
                            $expertiseList = is_object($reviewer) ? 
                                ($reviewer->expertise_levels ?? []) : 
                                ($reviewer['expertise_levels'] ?? []);
                            
                            // Get bid, expertise, and load breakdown scores if available
                            $bidScore = is_object($reviewer) ? ($reviewer->bid_score ?? null) : ($reviewer['bid_score'] ?? null);
                            $expertiseScore = is_object($reviewer) ? ($reviewer->expertise_score ?? null) : ($reviewer['expertise_score'] ?? null);
                            $loadScore = is_object($reviewer) ? ($reviewer->load_score ?? null) : ($reviewer['load_score'] ?? null);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <!-- Reviewer Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">
                                            {{ strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $firstName }} {{ $lastName }}</div>
                                        <div class="text-sm text-gray-500">{{ $email }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Institution -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $institution }}
                            </td>
                            
                            <!-- Expertise with Tooltip -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($expertiseList as $exp)
                                        <div class="group relative">
                                            <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded cursor-help">
                                                {{ $exp['topic'] }}
                                            </span>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
                                                Level: {{ $exp['level'] }}
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-xs text-gray-400">No expertise specified</span>
                                    @endforelse
                                </div>
                                @if(count($expertiseList) > 3)
                                    <div class="text-xs text-gray-400 mt-1">
                                        +{{ count($expertiseList) - 3 }} more
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Current Load with Progress Bar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($assignedCount / 10) * 100, 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $assignedCount }} / 10</span>
                                </div>
                            </td>
                            
                            <!-- Match Score with Breakdown Tooltip -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="group relative">
                                    <div class="text-center">
                                        @php
                                            $scoreColor = $matchScore >= 80 ? 'text-green-600' : ($matchScore >= 60 ? 'text-yellow-600' : 'text-red-600');
                                        @endphp
                                        <span class="text-lg font-bold {{ $scoreColor }}">{{ $matchScore }}%</span>
                                    </div>
                                    
                                    <!-- Tooltip with score breakdown -->
                                    @if($bidScore || $expertiseScore || $loadScore)
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
                                        <div class="space-y-1">
                                            <div class="font-semibold mb-1">Score Breakdown</div>
                                            <div>Bid: {{ $bidScore ?? 'N/A' }}%</div>
                                            <div>Expertise: {{ $expertiseScore ?? 'N/A' }}%</div>
                                            <div>Load: {{ $loadScore ?? 'N/A' }}%</div>
                                            <div class="border-t border-gray-600 mt-1 pt-1 font-semibold">Total: {{ $matchScore }}%</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('assignments.manual') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="paper_id" value="{{ $paper->id }}">
                                    <input type="hidden" name="reviewer_ids[]" value="{{ $reviewerId }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="tab" value="{{ $tab }}">
                                    <button type="submit" 
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm transition duration-150"
                                            onclick="return confirm('Assign this reviewer to the paper?')">
                                        <i class="fas fa-user-plus mr-1"></i> Assign
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-users-slash text-4xl mb-4"></i>
                <p class="text-lg">No suggested reviewers found</p>
                <p class="text-sm mt-2">Try adjusting your search criteria or add more reviewers to the system</p>
            </div>
            @endif
        </div>

        <!-- Manual Assignment Option -->
        <div class="mt-8 bg-blue-50 rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-800">Can't find a suitable reviewer?</h3>
                    <p class="text-blue-600 mt-1">Manually assign a reviewer from the complete list</p>
                </div>
                <a href="{{ route('assignments.assign', ['paper' => $paper->id, 'year' => $year, 'tab' => $tab]) }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-user-plus mr-2"></i> Manual Assignment
                </a>
            </div>
        </div>
    </div>
</div>
@endsection