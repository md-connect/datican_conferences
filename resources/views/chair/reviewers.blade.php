@extends('layouts.app')

@section('title', 'Reviewers Management - Chair Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reviewers Management</h1>
                <p class="text-gray-600 mt-2">Manage reviewers for DATICAN Conference {{ $year }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('assignments.index') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-tasks mr-2"></i> Assignments
                </a>
                <a href="{{ route('chair.dashboard') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <!-- Reviewers Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Reviewers ({{ $reviewers->count() }})</h3>
                <div class="text-sm text-gray-500">
                    Active for {{ $year }} Conference
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reviewers as $reviewer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">
                                            {{ strtoupper(substr($reviewer->first_name, 0, 1) . substr($reviewer->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $reviewer->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $reviewer->affiliation }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $reviewer->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                                    {{ $reviewer->assigned_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">
                                    {{ $reviewer->completed_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reviewer->pending_count > 0)
                                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">
                                    {{ $reviewer->pending_count }}
                                </span>
                                @else
                                <span class="px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded-full">
                                    0
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($reviewer->avg_review_time)
                                {{ number_format($reviewer->avg_review_time, 1) }} days
                                @else
                                N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    @if($reviewer->is_chair)
                                    <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">Chair</span>
                                    @endif
                                    @if($reviewer->is_reviewer)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Reviewer</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('users.show', $reviewer) }}" 
                                       class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                        View
                                    </a>
                                    
                                    <form method="POST" action="{{ route('chair.users.toggle.reviewer', $reviewer) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 text-sm {{ $reviewer->is_reviewer ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded">
                                            {{ $reviewer->is_reviewer ? 'Remove Reviewer' : 'Make Reviewer' }}
                                        </button>
                                    </form>
                                    
                                    @if(auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('chair.users.toggle.chair', $reviewer) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 text-sm {{ $reviewer->is_chair ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-purple-100 text-purple-700 hover:bg-purple-200' }} rounded">
                                            {{ $reviewer->is_chair ? 'Remove Chair' : 'Make Chair' }}
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
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No reviewers found</p>
                                    <p class="text-sm mt-2">No reviewers assigned for {{ $year }} conference</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Performance Summary -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reviewer Performance</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Average Reviews per Reviewer</span>
                            <span class="text-sm font-medium">
                                @php
                                    $avgAssigned = $reviewers->avg('assigned_count');
                                @endphp
                                {{ number_format($avgAssigned, 1) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ min(($avgAssigned / max($reviewers->max('assigned_count'), 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Completion Rate</span>
                            <span class="text-sm font-medium">
                                @php
                                    $totalAssigned = $reviewers->sum('assigned_count');
                                    $totalCompleted = $reviewers->sum('completed_count');
                                    $completionRate = $totalAssigned > 0 ? ($totalCompleted / $totalAssigned) * 100 : 0;
                                @endphp
                                {{ number_format($completionRate, 1) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" 
                                 style="width: {{ $completionRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6 md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reviewer Distribution</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $reviewers->count() }}</p>
                        <p class="text-sm text-gray-500">Total Reviewers</p>
                    </div>
                    
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-green-600">
                            {{ $reviewers->where('completed_count', '>', 0)->count() }}
                        </p>
                        <p class="text-sm text-gray-500">Active Reviewers</p>
                    </div>
                    
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ $reviewers->where('pending_count', '>', 0)->count() }}
                        </p>
                        <p class="text-sm text-gray-500">With Pending Reviews</p>
                    </div>
                    
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-red-600">
                            {{ $reviewers->where('assigned_count', 0)->count() }}
                        </p>
                        <p class="text-sm text-gray-500">Unassigned</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection