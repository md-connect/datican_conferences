@extends('layouts.app')

@section('title', 'All Assignments - Chair Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">All Assignments</h1>
                <p class="text-gray-600 mt-2">Manage all reviewer assignments for DATICAN Conference {{ $year }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('assignments.export', ['year' => $year, 'status' => request('status'), 'reviewer_id' => request('reviewer_id')]) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i> Export CSV
                </a>
                <a href="{{ route('assignments.index') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500">Total</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                <p class="text-xs text-gray-500">Pending</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['under_review'] }}</p>
                <p class="text-xs text-gray-500">Under Review</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
                <p class="text-xs text-gray-500">In Progress</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                <p class="text-xs text-gray-500">Completed</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $stats['declined'] }}</p>
                <p class="text-xs text-gray-500">Declined</p>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('assignments.all') }}" class="space-y-4">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Statuses</option>
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
                                {{ $reviewer->first_name }} {{ $reviewer->last_name }} ({{ $reviewer->email }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>
                        <a href="{{ route('assignments.all', ['year' => $year]) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Assignments Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Assignments ({{ $assignments->total() }})</h3>
                <div class="text-sm text-gray-500">
                    {{ $assignments->firstItem() }}-{{ $assignments->lastItem() }} of {{ $assignments->total() }}
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paper ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paper Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50 {{ $assignment->deadline && $assignment->deadline < now() && !in_array($assignment->status, ['completed', 'declined']) ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-sm font-medium">{{ $assignment->paper->anonymous_id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 truncate max-w-xs">{{ Str::limit($assignment->paper->title, 50) }}</div>
                                <div class="text-xs text-gray-500">{{ $assignment->paper->topic_area }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                        <span class="text-xs font-medium text-blue-700">
                                            {{ strtoupper(substr($assignment->reviewer->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium">{{ $assignment->reviewer->first_name }} {{ $assignment->reviewer->last_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $assignment->reviewer->email }}</div>
                                    </div>
                                </div>
                             </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'under_review' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-indigo-100 text-indigo-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'declined' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$assignment->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                </span>
                             </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($assignment->status == 'completed')
                                    @php
                                        $totalScore = ($assignment->criteria_relevance ?? 0) + 
                                                      ($assignment->criteria_originality ?? 0) + 
                                                      ($assignment->criteria_quality ?? 0) + 
                                                      ($assignment->criteria_impact ?? 0) + 
                                                      ($assignment->criteria_clarity ?? 0) + 
                                                      ($assignment->criteria_contribution ?? 0);
                                    @endphp
                                    <div class="text-center">
                                        <span class="text-sm font-bold {{ $totalScore >= 80 ? 'text-green-600' : ($totalScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $totalScore }}
                                        </span>
                                        <span class="text-xs text-gray-500">/100</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                             </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y') : 'N/A' }}
                             </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($assignment->deadline)
                                <div class="text-sm {{ $assignment->deadline < now() && !in_array($assignment->status, ['completed', 'declined']) ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                    {{ $assignment->deadline->format('M d, Y') }}
                                </div>
                                @if($assignment->deadline < now() && !in_array($assignment->status, ['completed', 'declined']))
                                <div class="text-xs text-red-500">Overdue</div>
                                @endif
                                @else
                                <span class="text-sm text-gray-400">No deadline</span>
                                @endif
                             </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('reviews.show', $assignment) }}" 
                                       class="text-blue-600 hover:text-blue-800" title="View Review">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($assignment->status, ['pending', 'under_review', 'in_progress']))
                                    <button onclick="showReminderModal({{ $assignment->id }})"
                                            class="text-yellow-600 hover:text-yellow-800" title="Send Reminder">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                    @endif
                                </div>
                             </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No assignments found</p>
                                    <p class="text-sm mt-2">Try adjusting your filters</p>
                                </div>
                             </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($assignments->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $assignments->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reminder Modal -->
<div id="reminderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Send Reminder</h3>
            
            <form id="reminderForm" method="POST" action="">
                @csrf
                <input type="hidden" id="assignmentId" name="assignment_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Message (Optional)</label>
                    <textarea name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                              placeholder="Dear reviewer, please complete your review..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" 
                            onclick="document.getElementById('reminderModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Send Reminder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showReminderModal(assignmentId) {
    document.getElementById('assignmentId').value = assignmentId;
    document.getElementById('reminderForm').action = '/assignments/' + assignmentId + '/remind';
    document.getElementById('reminderModal').classList.remove('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reminderModal');
    if (event.target == modal) {
        modal.classList.add('hidden');
    }
}
</script>
@endsection