@extends('layouts.app')

@section('title', 'Revised Abstracts - Chair Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Revised Abstracts</h1>
                <p class="text-gray-600 mt-2">Manage revised abstract submissions from authors</p>
            </div>
            <div class="flex gap-3">
                <div class="relative group">
                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Export
                        <i class="fas fa-chevron-down ml-2 text-sm"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block z-10">
                        <a href="{{ route('chair.revised-abstracts.export', array_merge(request()->all(), ['status' => 'all'])) }}" 
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-csv mr-2"></i> Export All
                        </a>
                        <a href="{{ route('chair.revised-abstracts.export', array_merge(request()->all(), ['status' => 'uploaded'])) }}" 
                        class="block px-4 py-2 text-sm text-green-600 hover:bg-gray-100">
                            <i class="fas fa-check-circle mr-2"></i> Export Uploaded Only
                        </a>
                        <a href="{{ route('chair.revised-abstracts.export', array_merge(request()->all(), ['status' => 'pending'])) }}" 
                        class="block px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100">
                            <i class="fas fa-clock mr-2"></i> Export Pending Only
                        </a>
                    </div>
                </div>
                <a href="{{ route('chair.dashboard') }}" 
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-500">Total Papers Requiring Revision</p>
                </div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-6">
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['uploaded'] }}</p>
                    <p class="text-sm text-green-600">Revised Abstracts Uploaded</p>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-xl shadow p-6">
                <div class="text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    <p class="text-sm text-yellow-600">Pending Upload</p>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Status</label>
                    <select id="status-filter" class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <option value="">All</option>
                        <option value="uploaded" {{ request('status') == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="ml-auto">
                    <input type="text" id="search-input" placeholder="Search by paper ID or title..." 
                           class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64">
                </div>
            </div>
        </div>
        
        <!-- Papers Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author(s)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Decision</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revised Abstract</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($papers as $index => $paper)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $papers->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-medium text-gray-900">{{ $paper->anonymous_id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($paper->title, 50) }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $paper->topic_area }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $paper->author_list }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($paper->decision == 'accept_with_minor_revision') bg-yellow-100 text-yellow-800
                                    @else bg-orange-100 text-orange-800 @endif">
                                    {{ $paper->decision == 'accept_with_minor_revision' ? 'Minor Revision' : 'Major Revision' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paper->revised_abstract_file_path)
                                    <span class="text-green-600 flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i> Uploaded
                                    </span>
                                @else
                                    <span class="text-red-500 flex items-center">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $paper->revised_abstract_uploaded_at ? \Carbon\Carbon::parse($paper->revised_abstract_uploaded_at)->format('M d, Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('papers.show', $paper) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View Paper">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($paper->revised_abstract_file_path)
                                    <a href="{{ route('chair.revised-abstracts.download', $paper) }}" 
                                       class="text-green-600 hover:text-green-900" title="Download Revised Abstract">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                    @if(!$paper->revised_abstract_file_path)
                                    <span class="text-gray-400" title="Not yet uploaded">
                                        <i class="fas fa-download"></i>
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-file-word text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No revised abstracts found</p>
                                    <p class="text-sm mt-2">No papers require revision at this time.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($papers->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $papers->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-input');
    
    function filterAndRedirect() {
        const status = statusFilter.value;
        const search = searchInput.value;
        let url = window.location.pathname + '?';
        
        if (status) url += 'status=' + status + '&';
        if (search) url += 'search=' + encodeURIComponent(search);
        
        window.location.href = url;
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterAndRedirect);
    }
    
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(filterAndRedirect, 500);
        });
    }
});
</script>
@endpush

@endsection