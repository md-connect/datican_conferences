@extends('layouts.app')

@section('title', 'Submit Full Paper - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Submit Full Paper</h1>
            <p class="text-gray-600">Submit full paper for: {{ $paper->title }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <!-- Paper Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-medium text-blue-800 mb-2">Paper Information:</h4>
                <p><strong>Paper ID:</strong> {{ $paper->anonymous_id }}</p>
                <p><strong>Title:</strong> {{ $paper->title }}</p>
                <p><strong>Current Status:</strong> {{ ucfirst(str_replace('_', ' ', $paper->status)) }}</p>
                <p><strong>Submission Type:</strong> {{ $paper->submission_type === 'abstract_only' ? 'Abstract Only' : 'Full Paper' }}</p>
            </div>
            
            @if($paper->status === 'accepted')
                @if($paper->submission_type === 'abstract_only')
                <!-- Abstract Accepted - Need to submit full paper -->
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-green-800">Abstract Accepted!</p>
                            <p class="text-green-700">Your abstract has been accepted. Please upload the full paper.</p>
                            <p class="text-sm text-green-600 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Your accepted abstract ID: {{ $paper->anonymous_id }}
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <!-- Full Paper Accepted - Need camera ready version -->
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-green-800">Paper Accepted!</p>
                            <p class="text-green-700">Your paper has been accepted. Please submit the camera-ready version.</p>
                            @if($paper->file_path)
                            <p class="text-sm text-green-600 mt-1">
                                <i class="fas fa-file-pdf mr-1"></i>
                                Current file: {{ $paper->file_name }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @elseif($paper->status === 'abstract_submitted')
            <!-- Abstract submitted, not yet accepted -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-yellow-800">Abstract Submitted</p>
                        <p class="text-yellow-700">Your abstract has been submitted. You can submit the full paper in advance.</p>
                    </div>
                </div>
            </div>
            @elseif($paper->status === 'needs_revision')
            <!-- Paper needs revision -->
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-edit text-orange-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-orange-800">Revisions Required</p>
                        <p class="text-orange-700">Your paper needs revisions. Please submit the revised full paper.</p>
                        @if($paper->decision_notes)
                        <div class="mt-2 p-2 bg-white rounded border border-orange-100">
                            <p class="text-sm font-medium text-orange-800">Reviewer Comments:</p>
                            <p class="text-sm text-orange-700">{{ $paper->decision_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <form action="{{ route('papers.submit-full', $paper) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Full Paper (PDF only) *
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                        <input type="file" name="paper_file" accept=".pdf" required
                               class="mx-auto block"
                               id="paper_file">
                        <p class="text-sm text-gray-500 mt-2">Maximum file size: 10MB</p>
                        
                        @if($paper->file_path && $paper->submission_type === 'full_paper')
                        <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                            <p class="text-sm text-gray-600">Current file: {{ $paper->file_name }}</p>
                            <p class="text-xs text-gray-500">Uploaded: {{ $paper->submitted_at ? $paper->submitted_at->format('M d, Y') : 'Previously' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        @if($paper->status === 'accepted' && $paper->submission_type === 'abstract_only')
                        Author's Notes (Optional)
                        @elseif($paper->status === 'accepted' && $paper->submission_type === 'full_paper')
                        Camera-Ready Notes (Optional)
                        @elseif($paper->status === 'needs_revision')
                        Revision Response (Required)
                        @else
                        Author's Notes (Optional)
                        @endif
                    </label>
                    <textarea name="revision_notes" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="@if($paper->status === 'accepted' && $paper->submission_type === 'abstract_only')
                                            Describe how this full paper extends your accepted abstract...
                                          @elseif($paper->status === 'accepted' && $paper->submission_type === 'full_paper')
                                            Describe changes made for camera-ready version...
                                          @elseif($paper->status === 'needs_revision')
                                            Explain how you addressed the reviewers' comments...
                                          @else
                                            Describe any changes made from the abstract version...
                                          @endif"></textarea>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($paper->status === 'accepted' && $paper->submission_type === 'abstract_only')
                        Optional: Explain how this full paper extends your accepted abstract.
                        @elseif($paper->status === 'accepted' && $paper->submission_type === 'full_paper')
                        Optional: Describe any final changes made for the camera-ready version.
                        @elseif($paper->status === 'needs_revision')
                        Required: Explain how you addressed the reviewers' comments in your revision.
                        @else
                        Optional: Explain how this full paper extends the abstract.
                        @endif
                    </p>
                </div>
                
                <!-- Hidden field to track submission type -->
                <input type="hidden" name="submission_type" value="full_paper">
                
                <div class="flex items-center justify-between pt-6 border-t">
                    <div>
                        <a href="{{ route('papers.show', $paper) }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Paper
                        </a>
                    </div>
                    
                    <div class="space-x-4">
                        @if($paper->status === 'accepted' && $paper->submission_type === 'abstract_only')
                        <button type="submit" 
                                class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            <i class="fas fa-upload mr-2"></i>Submit Full Paper
                        </button>
                        @elseif($paper->status === 'accepted' && $paper->submission_type === 'full_paper')
                        <button type="submit" 
                                class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                            <i class="fas fa-camera mr-2"></i>Submit Camera-Ready Version
                        </button>
                        @elseif($paper->status === 'needs_revision')
                        <button type="submit" 
                                class="px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                            <i class="fas fa-sync-alt mr-2"></i>Submit Revised Paper
                        </button>
                        @else
                        <button type="submit" 
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Full Paper
                        </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // File validation
    document.getElementById('paper_file')?.addEventListener('change', function(e) {
        const file = this.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (file) {
            // Check file size
            if (file.size > maxSize) {
                alert('File size must be less than 10MB');
                this.value = '';
                return;
            }
            
            // Check file extension
            const fileName = file.name.toLowerCase();
            if (!fileName.endsWith('.pdf')) {
                alert('File must be PDF format');
                this.value = '';
                return;
            }
        }
    });
    
    // Form validation
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('paper_file');
        const revisionNotes = document.querySelector('textarea[name="revision_notes"]');
        
        // Check if file is selected
        if (!fileInput.files[0]) {
            e.preventDefault();
            alert('Please select a PDF file to upload.');
            fileInput.focus();
            return;
        }
        
        // If paper needs revision, require revision notes
        @if($paper->status === 'needs_revision')
        if (!revisionNotes.value.trim()) {
            e.preventDefault();
            alert('Please provide revision response explaining how you addressed the reviewers\' comments.');
            revisionNotes.focus();
            return;
        }
        @endif
    });
</script>
@endsection