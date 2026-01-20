@extends('layouts.app')

@section('title', 'My Papers - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">My Papers</h1>
                <a href="{{ route('papers.create') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-plus mr-2"></i>Submit New Paper
                </a>
            </div>
            <p class="text-gray-600 mt-2">Manage your submitted papers for DATICAN Conference</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Status</label>
                    <select id="status-filter" class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="under_review">Under Review</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                        <option value="camera_ready">Camera Ready</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Year</label>
                    <select id="year-filter" class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <option value="">All Years</option>
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                        <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                    </select>
                </div>
                <div class="ml-auto">
                    <input type="text" id="search-input" placeholder="Search papers..." 
                           class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64">
                </div>
            </div>
        </div>

        <!-- Papers Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Paper Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Submitted
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($papers as $paper)
                    <tr class="paper-row" 
                        data-status="{{ $paper->status }}"
                        data-year="{{ $paper->conference_year }}"
                        data-title="{{ strtolower($paper->title) }}">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $paper->title }}</div>
                            <div class="text-sm text-gray-500">{{ $paper->topic_area }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $paper->anonymous_id }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($paper->status == 'accepted') bg-green-100 text-green-800
                                @elseif($paper->status == 'rejected') bg-red-100 text-red-800
                                @elseif($paper->status == 'under_review') bg-yellow-100 text-yellow-800
                                @elseif($paper->status == 'submitted') bg-blue-100 text-blue-800
                                @elseif($paper->status == 'camera_ready') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $paper->submitted_at ? $paper->submitted_at->format('M d, Y') : 'Not submitted' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('papers.show', $paper) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-4">View</a>
                            @if($paper->status == 'draft')
                            <a href="{{ route('papers.edit', $paper) }}" 
                               class="text-green-600 hover:text-green-900 mr-4">Edit</a>
                            @endif
                            @if($paper->status == 'draft' || $paper->status == 'submitted')
                            <a href="{{ route('papers.download', $paper) }}" 
                               class="text-purple-600 hover:text-purple-900">Download</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-file-alt text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No papers yet</p>
                                <p class="mt-2">Submit your first paper to get started</p>
                                <a href="{{ route('papers.create') }}" 
                                   class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                    Submit Paper
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($papers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $papers->links() }}
            </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $papers->total() }}</p>
                        <p class="text-sm text-gray-500">Total Papers</p>
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
                            {{ $papers->where('status', 'accepted')->count() }}
                        </p>
                        <p class="text-sm text-gray-500">Accepted</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-spinner text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $papers->whereIn('status', ['submitted', 'under_review'])->count() }}
                        </p>
                        <p class="text-sm text-gray-500">In Review</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const yearFilter = document.getElementById('year-filter');
    const searchInput = document.getElementById('search-input');
    const paperRows = document.querySelectorAll('.paper-row');
    
    function filterPapers() {
        const status = statusFilter.value;
        const year = yearFilter.value;
        const search = searchInput.value.toLowerCase();
        
        paperRows.forEach(row => {
            const rowStatus = row.dataset.status;
            const rowYear = row.dataset.year;
            const rowTitle = row.dataset.title;
            
            let show = true;
            
            // Filter by status
            if (status && rowStatus !== status) {
                show = false;
            }
            
            // Filter by year
            if (year && rowYear !== year) {
                show = false;
            }
            
            // Filter by search
            if (search && !rowTitle.includes(search)) {
                show = false;
            }
            
            row.style.display = show ? '' : 'none';
        });
    }
    
    // Add event listeners
    statusFilter.addEventListener('change', filterPapers);
    yearFilter.addEventListener('change', filterPapers);
    searchInput.addEventListener('input', filterPapers);
    
    // Set filter values from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
        statusFilter.value = urlParams.get('status');
        filterPapers();
    }
    if (urlParams.has('year')) {
        yearFilter.value = urlParams.get('year');
        filterPapers();
    }
    
    // Highlight current year filter
    const currentYear = new Date().getFullYear().toString();
    const yearOptions = yearFilter.querySelectorAll('option');
    yearOptions.forEach(option => {
        if (option.value === currentYear) {
            option.selected = true;
            filterPapers();
        }
    });
});
</script>
@endpush