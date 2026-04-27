@extends('layouts.app')

@section('title', 'My Papers - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Papers</h1>
                    <p class="text-gray-600 mt-2">Manage your submitted papers for DATICAN Conference</p>
                </div>
                <div class="flex gap-3">
                    @php
                        $papersNeedingRevision = \App\Models\Paper::where('created_by', auth()->id())
                            ->whereIn('decision', ['accept_with_minor_revision', 'accept_with_major_revision'])
                            ->whereNull('revised_abstract_file_path')
                            ->count();
                    @endphp
                    
                    @if($papersNeedingRevision > 0)
                    <a href="{{ route('author.revised-abstract.select') }}" 
                       class="bg-brand-600 text-white px-6 py-3 rounded-lg hover:bg-brand-700 font-medium transition">
                        <i class="fas fa-upload mr-2"></i>Upload Revised Abstract ({{ $papersNeedingRevision }})
                    </a>
                    @endif
                    
                    <a href="{{ route('papers.create') }}" 
                       class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-700 font-medium transition">
                        <i class="fas fa-plus mr-2"></i>Submit New Paper
                    </a>
                </div>
            </div>
        </div>

        <!-- Papers Needing Revision Alert -->
        @php
            $papersWithRevision = \App\Models\Paper::where('created_by', auth()->id())
                ->whereIn('decision', ['accept_with_minor_revision', 'accept_with_major_revision'])
                ->whereNull('revised_abstract_file_path')
                ->get();
        @endphp
        
        @if($papersWithRevision->count() > 0)
        <div class="bg-amber-50 border-l-4 border-amber-400 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-amber-600 mr-3 mt-1"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-amber-800">Action Required: Revised Abstract Submission</h3>
                    <p class="text-amber-700 text-sm mt-1">
                        You have {{ $papersWithRevision->count() }} paper(s) that require revised abstract submission by <strong>May 1, 2026</strong>.
                        Please upload your revised abstract in MS Word format following the required structure.
                    </p>
                    <div class="mt-2">
                        <a href="{{ route('author.revised-abstract.select') }}" class="text-amber-800 text-sm font-medium hover:underline">
                            Click here to upload →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                        <option value="needs_revision">Needs Revision</option>
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
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Submission Type</label>
                    <select id="type-filter" class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <option value="">All Types</option>
                        <option value="abstract_only">Abstract Only</option>
                        <option value="full_paper">Full Paper</option>
                    </select>
                </div>
                <div class="ml-auto">
                    <input type="text" id="search-input" placeholder="Search by title or authors..." 
                           class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64">
                </div>
            </div>
        </div>

        <!-- Papers Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authors</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author's Institution</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revised Abstract</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($papers as $index => $paper)
                        <tr class="paper-row hover:bg-gray-50 transition-colors duration-150" 
                            data-status="{{ $paper->status }}"
                            data-year="{{ $paper->conference_year }}"
                            data-type="{{ $paper->submission_type }}"
                            data-title="{{ strtolower($paper->title) }}"
                            data-authors="{{ strtolower($paper->author_list) }}">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $papers->firstItem() + $index }}</td>
                            
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $paper->anonymous_id }}</span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $paper->author_list }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $paper->authors->count() }} author(s)</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">
                                    @php
                                        $institutions = $paper->authors->map(function($author) {
                                            return $author->institution ?? 'Not specified';
                                        })->unique()->implode(';<br>');
                                    @endphp
                                    {!! $institutions ?: '<span class="text-gray-400">Not specified</span>' !!}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $paper->title }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $paper->topic_area }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($paper->submission_type == 'abstract_only') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $paper->submission_type == 'abstract_only' ? 'Abstract Only' : 'Full Paper' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $paper->submitted_at ? $paper->submitted_at->format('M d, Y') : 'Not submitted' }}
                                @if($paper->submitted_at)
                                <div class="text-xs text-gray-400">{{ $paper->submitted_at->diffForHumans() }}</div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'submitted' => 'bg-blue-100 text-blue-800',
                                        'under_review' => 'bg-yellow-100 text-yellow-800',
                                        'reviewed' => 'bg-purple-100 text-purple-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                        'accept_with_minor_revision' => 'bg-yellow-100 text-yellow-800',
                                        'accept_with_major_revision' => 'bg-orange-100 text-orange-800',
                                        'needs_revision' => 'bg-amber-100 text-amber-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'camera_ready' => 'bg-emerald-100 text-emerald-800',
                                    ];
                                    $colorClass = $statusColors[$paper->status] ?? 'bg-gray-100 text-gray-800';
                                    $statusDisplay = match($paper->status) {
                                        'accept_with_minor_revision' => 'Minor Revision',
                                        'accept_with_major_revision' => 'Major Revision',
                                        'needs_revision' => 'Needs Revision',
                                        default => ucfirst(str_replace('_', ' ', $paper->status))
                                    };
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                                    {{ $statusDisplay }}
                                </span>
                            </td>
                            
                            <!-- Revised Abstract Column -->
                            <td class="px-6 py-4 text-center">
                                @if(in_array($paper->decision, ['accept_with_minor_revision', 'accept_with_major_revision']))
                                    @if($paper->revised_abstract_file_path)
                                        <span class="text-green-600 text-sm flex items-center justify-center">
                                            <i class="fas fa-check-circle mr-1"></i> Uploaded
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $paper->revised_abstract_uploaded_at ? $paper->revised_abstract_uploaded_at->format('M d, Y') : '' }}
                                        </div>
                                    @else
                                        <span class="text-red-500 text-sm flex items-center justify-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Pending
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Deadline: May 1, 2026
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            
                            <!-- Actions Column -->
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('papers.show', $paper) }}" 
                                       class="text-brand-600 hover:text-brand-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($paper->status == 'draft')
                                    <a href="{{ route('papers.edit', $paper) }}" 
                                       class="text-green-600 hover:text-green-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if($paper->status == 'draft' || $paper->status == 'submitted')
                                    <a href="{{ route('papers.download', $paper) }}" 
                                       class="text-purple-600 hover:text-purple-900" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                    
                                    @if(in_array($paper->decision, ['accept_with_minor_revision', 'accept_with_major_revision']) && !$paper->revised_abstract_file_path)
                                    <a href="{{ route('author.revised-abstract.upload', $paper) }}" 
                                       class="text-orange-600 hover:text-orange-900" title="Upload Revised Abstract">
                                        <i class="fas fa-upload"></i>
                                    </a>
                                    @endif
                                    
                                    @if($paper->revised_abstract_file_path)
                                    <a href="{{ route('author.revised-abstract.download', $paper) }}" 
                                       class="text-teal-600 hover:text-teal-900" title="Download Revised Abstract">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                    
                                    @if($paper->status == 'accepted' && !$paper->file_path)
                                    <a href="{{ route('papers.submit-full-form', $paper) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Submit Full Paper">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No papers yet</p>
                                    <p class="mt-2">Submit your first paper to get started</p>
                                    <a href="{{ route('papers.create') }}" 
                                       class="inline-block mt-4 bg-brand-600 text-white px-6 py-2 rounded-lg hover:bg-brand-700">
                                        Submit Paper
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($papers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $papers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const yearFilter = document.getElementById('year-filter');
    const typeFilter = document.getElementById('type-filter');
    const searchInput = document.getElementById('search-input');
    const paperRows = document.querySelectorAll('.paper-row');
    
    function filterPapers() {
        const status = statusFilter.value;
        const year = yearFilter.value;
        const type = typeFilter.value;
        const search = searchInput.value.toLowerCase();
        
        paperRows.forEach(row => {
            const rowStatus = row.dataset.status;
            const rowYear = row.dataset.year;
            const rowType = row.dataset.type;
            const rowTitle = row.dataset.title;
            const rowAuthors = row.dataset.authors;
            
            let show = true;
            
            if (status && rowStatus !== status) show = false;
            if (year && rowYear !== year) show = false;
            if (type && rowType !== type) show = false;
            if (search && !rowTitle.includes(search) && !(rowAuthors && rowAuthors.includes(search))) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    }
    
    if (statusFilter) statusFilter.addEventListener('change', filterPapers);
    if (yearFilter) yearFilter.addEventListener('change', filterPapers);
    if (typeFilter) typeFilter.addEventListener('change', filterPapers);
    if (searchInput) searchInput.addEventListener('input', filterPapers);
    
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
    if (urlParams.has('type')) {
        typeFilter.value = urlParams.get('type');
        filterPapers();
    }
});
</script>
@endpush