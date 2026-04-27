@extends('layouts.app')

@section('title', 'Author Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Author Dashboard</h1>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $paperStats['total'] }}</p>
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
                        <p class="text-2xl font-bold text-gray-900">{{ $paperStats['accepted'] }}</p>
                        <p class="text-sm text-gray-500">Accepted</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $paperStats['under_review'] }}</p>
                        <p class="text-sm text-gray-500">Under Review</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-camera text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $paperStats['camera_ready'] }}</p>
                        <p class="text-sm text-gray-500">Camera Ready</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                @php
                    $papersNeedingRevision = \App\Models\Paper::where('created_by', auth()->id())
                        ->whereIn('decision', ['accept_with_minor_revision', 'accept_with_major_revision'])
                        ->whereNull('revised_abstract_file_path')
                        ->count();
                @endphp
                
                @if($papersNeedingRevision > 0)
                <a href="{{ route('author.revised-abstract.select') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-upload mr-2"></i>Upload Revised Abstract ({{ $papersNeedingRevision }})
                </a>
                @else
                <button type="button" 
                        class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed"
                        disabled>
                    <i class="fas fa-upload mr-2"></i>No Papers Need Revision
                </button>
                @endif
                
                <a href="{{ route('papers.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-list mr-2"></i>View My Papers
                </a>
            </div>
        </div>
        
        <!-- Papers Needing Revision - Alert -->
        @php
            $papersWithRevision = \App\Models\Paper::where('created_by', auth()->id())
                ->whereIn('decision', ['accept_with_minor_revision', 'accept_with_major_revision'])
                ->whereNull('revised_abstract_file_path')
                ->get();
        @endphp
        
        @if($papersWithRevision->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-yellow-800">Action Required: Revised Abstract Submission</h3>
                    <p class="text-yellow-700 text-sm mt-1">
                        You have {{ $papersWithRevision->count() }} paper(s) that require revised abstract submission latest by <strong>May 1, 2026</strong>.
                        Please upload your revised abstract in MS Word format following the required structure.
                    </p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Recent Papers -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Recent Papers</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentPapers as $paper)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $paper->title }}</h3>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-sm text-gray-500">{{ $paper->anonymous_id }}</span>
                                {!! $paper->status_badge !!}
                                <span class="text-sm text-gray-500">
                                    {{ $paper->submitted_at ? $paper->submitted_at->format('M d, Y') : 'Not submitted' }}
                                </span>
                                @if($paper->revised_abstract_file_path)
                                <span class="text-xs text-green-600">
                                    <i class="fas fa-check-circle"></i> Revised Abstract Uploaded
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('papers.show', $paper) }}" 
                               class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                View
                            </a>
                            @if(in_array($paper->decision, ['accept_with_minor_revision', 'accept_with_major_revision']) && !$paper->revised_abstract_file_path)
                            <a href="{{ route('author.revised-abstract.upload', $paper) }}" 
                               class="px-3 py-1 text-sm bg-orange-100 text-orange-700 rounded hover:bg-orange-200">
                                <i class="fas fa-upload mr-1"></i> Upload Revision
                            </a>
                            @endif
                            @if($paper->revised_abstract_file_path)
                            <a href="{{ route('author.revised-abstract.download', $paper) }}" 
                               class="px-3 py-1 text-sm bg-purple-100 text-purple-700 rounded hover:bg-purple-200">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                    <p>No papers submitted yet.</p>
                    <a href="{{ route('papers.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Submit your first paper →
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection