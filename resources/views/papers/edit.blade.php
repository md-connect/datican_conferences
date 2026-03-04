@extends('layouts.app')

@section('title', 'Edit Paper')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Paper Submission</h1>
            <p class="text-gray-600">Edit your research paper for DATICAN Conference {{ $paper->conference_year }}</p>
        </div>

        <!-- Status Banner -->
        @if($paper->status === 'submitted' || $paper->status === 'abstract_submitted')
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <p class="text-blue-800">This paper has been submitted for review. You can still edit it until the review process begins.</p>
            </div>
        </div>
        @elseif($paper->status === 'under_review' || $paper->status === 'reviewing')
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                <p class="text-yellow-800">This paper is currently under review. Some fields may be locked for editing.</p>
            </div>
        </div>
        @elseif($paper->status === 'accepted')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <p class="text-green-800">Congratulations! Your paper has been accepted.</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg p-6">
            <form action="{{ route('papers.update', $paper->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-8">
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                                <div>
                                    <p class="font-medium text-red-800">Please fix the following errors:</p>
                                    <ul class="mt-1 list-disc list-inside text-red-700 text-sm">
                                        @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Paper Information</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Paper Title *
                        </label>
                        <input type="text" name="title" required
                               value="{{ old('title', $paper->title) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter paper title"
                               {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'readonly' : '' }}>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Abstract * (Maximum 250 words)
                        </label>
                        <textarea name="abstract" rows="4" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter paper abstract (maximum 250 words)"
                                id="abstract-textarea"
                                {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'readonly' : '' }}>{{ old('abstract', $paper->abstract) }}</textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-sm text-gray-500">Maximum 250 words</p>
                            <p class="text-sm" id="word-count-display"></p>
                        </div>
                        @error('abstract')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keywords *
                        </label>
                        <input type="text" name="keywords" required
                               value="{{ old('keywords', $paper->keywords) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., AI, Machine Learning, Healthcare"
                               {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'readonly' : '' }}>
                        <p class="text-sm text-gray-500 mt-1">Separate keywords with commas</p>
                    </div>
                </div>

                <!-- Conference Details -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Conference Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Conference Year *
                            </label>
                            <select name="conference_year" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                <option value="2026" {{ old('conference_year', $paper->conference_year) == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ old('conference_year', $paper->conference_year) == '2025' ? 'selected' : '' }}>2025</option>
                            </select>
                            @if($paper->status === 'under_review' || $paper->status === 'reviewing')
                                <input type="hidden" name="conference_year" value="{{ $paper->conference_year }}">
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Topic Area *
                            </label>
                            <select name="topic_area" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                <option value="">Select Topic Area</option>
                                <option value="ai_ml" {{ old('topic_area', $paper->topic_area) == 'ai_ml' ? 'selected' : '' }}>Artificial Intelligence & Machine Learning</option>
                                <option value="data_science" {{ old('topic_area', $paper->topic_area) == 'data_science' ? 'selected' : '' }}>Data Science & Analytics</option>
                                <option value="healthcare" {{ old('topic_area', $paper->topic_area) == 'healthcare' ? 'selected' : '' }}>Healthcare Applications</option>
                                <option value="clinical" {{ old('topic_area', $paper->topic_area) == 'clinical' ? 'selected' : '' }}>Clinical Decision Support</option>
                                <option value="imaging" {{ old('topic_area', $paper->topic_area) == 'imaging' ? 'selected' : '' }}>Medical Imaging</option>
                                <option value="other" {{ old('topic_area', $paper->topic_area) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @if($paper->status === 'under_review' || $paper->status === 'reviewing')
                                <input type="hidden" name="topic_area" value="{{ $paper->topic_area }}">
                            @endif
                        </div>
                    </div>
                    
                    <!-- Submission Type -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Submission Type *
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="submission_type" value="abstract_only" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 submission-type-radio"
                                    {{ old('submission_type', $paper->submission_type) == 'abstract_only' ? 'checked' : '' }}
                                    {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                <span class="ml-3">
                                    <span class="text-gray-900 font-medium">Abstract Only</span>
                                    <span class="text-gray-500 text-sm block mt-1">
                                        Submit abstract now, upload full paper later.
                                    </span>
                                </span>
                            </label>
            
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="submission_type" value="full_paper" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 submission-type-radio"
                                    {{ old('submission_type', $paper->submission_type) == 'full_paper' ? 'checked' : '' }}
                                    {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                <span class="ml-3">
                                    <span class="text-gray-900 font-medium">Full Paper</span>
                                    <span class="text-gray-500 text-sm block mt-1">
                                        Submit complete paper with abstract. Requires file upload.
                                    </span>
                                </span>
                            </label>
                        </div>
                        @if($paper->status === 'under_review' || $paper->status === 'reviewing')
                            <input type="hidden" name="submission_type" value="{{ $paper->submission_type }}">
                        @endif
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="mb-8" id="file-upload-wrapper">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Paper File</h2>
                    
                    @if($paper->file_path)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-red-600 text-2xl mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-900">Current Paper File</p>
                                    <p class="text-sm text-gray-500">Uploaded on {{ $paper->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="space-x-2">
                                <a href="{{ Storage::url($paper->file_path) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-eye mr-2"></i> View
                                </a>
                                <a href="{{ Storage::url($paper->file_path) }}" 
                                   download
                                   class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-4" id="file_upload_section">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload New Paper (PDF only) <span class="text-red-500" id="file_required_indicator">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
                            id="file_upload_area">
                            <input type="file" name="paper_file" accept=".pdf" 
                                class="hidden" id="paper_file_input"
                                {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                            <div class="space-y-2" id="file_upload_content">
                                <div class="mx-auto w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-cloud-upload-alt text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Click to upload or drag and drop</p>
                                    <p class="text-xs text-gray-500 mt-1">PDF only (Max. 10MB)</p>
                                </div>
                            </div>
                            <div class="hidden" id="file_selected_content">
                                <div class="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mt-2" id="file_name_display"></p>
                                <button type="button" class="text-sm text-blue-600 hover:text-blue-800 mt-1" 
                                        onclick="document.getElementById('paper_file_input').click()">
                                    Change file
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            @if($paper->file_path)
                                Leave empty to keep current file. Uploading a new file will replace the existing one.
                            @else
                                Please ensure your paper follows the conference template.
                            @endif
                        </p>
                        <div id="file_error" class="mt-2 text-sm text-red-600 hidden"></div>
                    </div>
                    
                    <!-- Anonymous submission checkbox -->
                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="is_anonymous" value="1" 
                            {{ old('is_anonymous', $paper->is_anonymous) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            id="is_anonymous"
                            {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                        <label for="is_anonymous" class="ml-2 text-gray-700">
                            Submit as anonymous (for double-blind review)
                        </label>
                        @if($paper->status === 'under_review' || $paper->status === 'reviewing')
                            <input type="hidden" name="is_anonymous" value="{{ $paper->is_anonymous }}">
                        @endif
                    </div>
                </div>

                <!-- Authors -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Authors</h2>
                    <p class="text-sm text-gray-600 mb-4">You are automatically added as the first author.</p>
                    
                    @php
                        $existingAuthors = $paper->authors->sortBy('pivot.author_order');
                        $correspondingAuthorIndex = $existingAuthors->search(function ($author) {
                            return $author->pivot->is_corresponding;
                        });
                        $correspondingAuthorIndex = $correspondingAuthorIndex !== false ? $correspondingAuthorIndex : 0;
                        
                        // Separate main author from co-authors
                        $mainAuthor = $existingAuthors->firstWhere('user_id', auth()->id());
                        $coAuthors = $existingAuthors->filter(function($author) {
                            return $author->user_id !== auth()->id();
                        })->values(); // Reset indices
                    @endphp
                    
                    <div id="authors-section">
                        <!-- First author (current user) - Always present, cannot be removed -->
                        <div class="author-field mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-700">Author 1 (You)</span>
                                <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Primary Author</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Author *</label>
                                    <input type="text" 
                                        value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }} ({{ auth()->user()->email }})"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
                                        readonly>
                                    <input type="hidden" name="authors[0][user_id]" value="{{ auth()->id() }}">
                                </div>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="corresponding_author" value="0"
                                            class="text-blue-600 focus:ring-blue-500 corresponding-radio"
                                            {{ $correspondingAuthorIndex == 0 ? 'checked' : '' }}
                                            {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600">Corresponding Author</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Co-authors container - Only shows existing co-authors -->
                        <div id="co-authors-container">
                            @foreach($coAuthors as $index => $author)
                            @php $authorIndex = $index + 1; @endphp
                            <div class="author-field co-author mb-4 p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-700">Author {{ $authorIndex + 1 }}</span>
                                    @if($paper->status !== 'under_review' && $paper->status !== 'reviewing')
                                    <button type="button" class="remove-author text-red-600 hover:text-red-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @else
                                    <button type="button" class="text-gray-400 cursor-not-allowed" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Select Co-Author *</label>
                                        <select name="authors[{{ $authorIndex }}][user_id]" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg author-select"
                                                {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                            <option value="">Select an author...</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $author->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @if($paper->status === 'under_review' || $paper->status === 'reviewing')
                                            <input type="hidden" name="authors[{{ $authorIndex }}][user_id]" value="{{ $author->user_id }}">
                                        @endif
                                    </div>
                                    <div class="flex items-center">
                                        <label class="flex items-center">
                                            <input type="radio" name="corresponding_author" value="{{ $authorIndex }}"
                                                class="text-blue-600 focus:ring-blue-500 corresponding-radio"
                                                {{ $correspondingAuthorIndex == $authorIndex ? 'checked' : '' }}
                                                {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                            <span class="ml-2 text-sm text-gray-600">Corresponding Author</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    @if($paper->status !== 'under_review' && $paper->status !== 'reviewing')
                    <button type="button" id="add-author" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-plus mr-2"></i>
                        Add Co-Author
                    </button>
                    @endif
                </div>

                <!-- Conference Registration Link -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Conference Registration</h2>
                    
                    @if($registrations->count() > 0)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Link to Conference Registration(s)
                            </label>
                            <div class="space-y-2">
                                @php
                                    $paperRegistrationIds = $paper->registrations->pluck('id')->toArray();
                                @endphp
                                @foreach($registrations as $registration)
                                <label class="flex items-center">
                                    <input type="checkbox" name="registration_ids[]" value="{{ $registration->id }}"
                                           class="text-blue-600 focus:ring-blue-500"
                                           {{ in_array($registration->id, old('registration_ids', $paperRegistrationIds)) ? 'checked' : '' }}
                                           {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'disabled' : '' }}>
                                    <span class="ml-2">{{ $registration->firstname }} {{ $registration->lastname }} 
                                        ({{ $registration->email }}) - {{ $registration->institution }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-yellow-800">No conference registration found for your account.</p>
                        </div>
                    @endif
                </div>

                <!-- Additional Comments -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Additional Information</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Comments for Reviewers (Optional)
                        </label>
                        <textarea name="author_comments" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any special instructions or comments for reviewers"
                                  {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'readonly' : '' }}>{{ old('author_comments', $paper->author_comments) }}</textarea>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Current Status</h2>
                    <div class="flex items-center">
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            @if($paper->status == 'draft') bg-gray-200 text-gray-800
                            @elseif($paper->status == 'abstract_submitted') bg-blue-200 text-blue-800
                            @elseif($paper->status == 'submitted') bg-blue-200 text-blue-800
                            @elseif($paper->status == 'under_review' || $paper->status == 'reviewing') bg-yellow-200 text-yellow-800
                            @elseif($paper->status == 'accepted') bg-green-200 text-green-800
                            @elseif($paper->status == 'rejected') bg-red-200 text-red-800
                            @elseif($paper->status == 'camera_ready') bg-green-200 text-green-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                        </span>
                        <span class="ml-3 text-sm text-gray-600">
                            Last updated: {{ $paper->updated_at->format('M d, Y H:i') }}
                        </span>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <div class="space-x-4">
                        <a href="{{ route('papers.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                    </div>
                    
                    <div class="space-x-4">
                        @if($paper->status === 'draft')
                        <button type="submit" name="action" value="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit for Review
                        </button>
                        @endif
                        
                        @if($paper->status !== 'under_review' && $paper->status !== 'reviewing')
                        <button type="submit" name="action" value="update"
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            <i class="fas fa-check mr-2"></i>
                            Save Changes
                        </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // -----------------------------
    // VARIABLES
    //-----------------------------
    const coAuthorsContainer = document.getElementById('co-authors-container');
    const addAuthorBtn = document.getElementById('add-author');
    const abstractTextarea = document.getElementById('abstract-textarea');
    const wordCountDisplay = document.getElementById('word-count-display');

    const submissionTypeRadios = document.querySelectorAll('.submission-type-radio');
    const fileUploadWrapper = document.getElementById('file-upload-wrapper');
    const fileUploadSection = document.getElementById('file_upload_section');
    const fileInput = document.getElementById('paper_file_input');
    const fileRequiredIndicator = document.getElementById('file_required_indicator');
    const fileUploadContent = document.getElementById('file_upload_content');
    const fileSelectedContent = document.getElementById('file_selected_content');
    const fileNameDisplay = document.getElementById('file_name_display');
    const fileError = document.getElementById('file_error');
    const fileUploadArea = document.getElementById('file_upload_area');

    const hasExistingFile = {{ $paper->file_path ? 'true' : 'false' }};
    const isUnderReview = {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'true' : 'false' }};

    // Count existing co-authors (excluding the main author)
    const existingCoAuthors = {{ $coAuthors->count() }};
    let coAuthorCount = existingCoAuthors;

    // Log for debugging
    console.log('Author counts:', {
        existingCoAuthors: existingCoAuthors,
        coAuthorCount: coAuthorCount,
        totalAuthors: {{ $existingAuthors->count() }}
    });

    // -----------------------------
    // ABSTRACT WORD COUNT
    // -----------------------------
    function updateWordCount() {
        if (!abstractTextarea) return;
        
        const text = abstractTextarea.value.trim();
        let wordCount = 0;
        if (text.length > 0) {
            wordCount = text.split(/\s+/).filter(w => w.length > 0).length;
        }
        
        // Update display
        wordCountDisplay.textContent = `${wordCount}/250 words`;
        
        // Update styling based on word count
        wordCountDisplay.classList.remove('text-gray-500', 'text-yellow-600', 'text-red-600');
        if (wordCount > 250) {
            wordCountDisplay.classList.add('text-red-600', 'font-bold');
        } else if (wordCount > 240) {
            wordCountDisplay.classList.add('text-red-600');
        } else if (wordCount > 200) {
            wordCountDisplay.classList.add('text-yellow-600');
        } else {
            wordCountDisplay.classList.add('text-gray-500');
        }
        
        return wordCount;
    }

    if (abstractTextarea && !abstractTextarea.readOnly) {
        updateWordCount();
        abstractTextarea.addEventListener('input', updateWordCount);
        abstractTextarea.addEventListener('blur', updateWordCount);
    }

    // -----------------------------
    // SUBMISSION TYPE TOGGLE FILE UPLOAD
    // -----------------------------
    function toggleFileUpload() {
        const selectedRadio = document.querySelector('input[name="submission_type"]:checked');
        if (!selectedRadio || !fileUploadWrapper) return;

        const isAbstractOnly = selectedRadio.value === 'abstract_only';
        
        if (isAbstractOnly) {
            // Hide and disable file upload for abstract only
            fileUploadWrapper.style.opacity = '0.5';
            fileUploadWrapper.style.pointerEvents = 'none';
            if (fileInput) {
                fileInput.disabled = true;
                fileInput.removeAttribute('required');
            }
            if (fileRequiredIndicator) {
                fileRequiredIndicator.classList.add('hidden');
            }
        } else {
            // Show and enable file upload for full paper
            fileUploadWrapper.style.opacity = '1';
            fileUploadWrapper.style.pointerEvents = 'auto';
            
            if (fileInput) {
                // Only enable if not under review
                fileInput.disabled = isUnderReview;
                
                // Set required if no existing file
                if (!hasExistingFile && !isUnderReview) {
                    fileInput.setAttribute('required', 'required');
                    if (fileRequiredIndicator) {
                        fileRequiredIndicator.classList.remove('hidden');
                    }
                } else {
                    fileInput.removeAttribute('required');
                    if (fileRequiredIndicator) {
                        fileRequiredIndicator.classList.add('hidden');
                    }
                }
            }
        }
    }

    // Add event listeners to radio buttons
    submissionTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleFileUpload);
    });
    
    // Initial call
    toggleFileUpload();

    // -----------------------------
    // FILE UPLOAD DISPLAY & VALIDATION
    // -----------------------------
    function resetFileDisplay() {
        if (fileUploadContent) fileUploadContent.classList.remove('hidden');
        if (fileSelectedContent) fileSelectedContent.classList.add('hidden');
        if (fileNameDisplay) fileNameDisplay.textContent = '';
    }

    if (fileUploadSection && fileInput) {
        fileUploadSection.addEventListener('click', function(e) {
            if (!fileInput.disabled) fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (fileError) { 
                fileError.classList.add('hidden'); 
                fileError.textContent = ''; 
            }

            if (file) {
                // Validate file type
                if (!file.name.toLowerCase().endsWith('.pdf')) {
                    if (fileError) { 
                        fileError.textContent = 'File must be PDF format'; 
                        fileError.classList.remove('hidden'); 
                    }
                    this.value = '';
                    resetFileDisplay();
                    return;
                }
                
                // Validate file size
                if (file.size > maxSize) {
                    if (fileError) { 
                        fileError.textContent = 'File size must be less than 10MB'; 
                        fileError.classList.remove('hidden'); 
                    }
                    this.value = '';
                    resetFileDisplay();
                    return;
                }

                // Show selected file
                if (fileNameDisplay) fileNameDisplay.textContent = file.name;
                if (fileUploadContent) fileUploadContent.classList.add('hidden');
                if (fileSelectedContent) fileSelectedContent.classList.remove('hidden');
            } else {
                resetFileDisplay();
            }
        });

        // Drag and drop functionality
        fileUploadSection.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!fileInput.disabled) {
                this.classList.add('border-blue-400', 'bg-blue-50');
            }
        });

        fileUploadSection.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
        });

        fileUploadSection.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
            
            if (!fileInput.disabled && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }

    // -----------------------------
    // CO-AUTHORS MANAGEMENT
    // -----------------------------
    function reindexCoAuthors() {
        const coAuthorFields = document.querySelectorAll('.co-author');
        coAuthorFields.forEach((field, idx) => {
            // Form index starts at 1 because author 0 is the main author
            const formIndex = idx + 1;
            
            // Update label
            const label = field.querySelector('span.font-medium');
            if (label) label.textContent = `Author ${formIndex + 1}`;

            // Update select name
            const select = field.querySelector('select.author-select');
            if (select) select.name = `authors[${formIndex}][user_id]`;

            // Update corresponding radio value
            const radio = field.querySelector('input.corresponding-radio');
            if (radio) radio.value = formIndex;
        });
        coAuthorCount = coAuthorFields.length;
    }

    function addCoAuthorField() {
        const newIndex = coAuthorCount + 1; // +1 because main author is index 0
        
        const field = document.createElement('div');
        field.className = 'author-field co-author mb-4 p-4 border border-gray-200 rounded-lg';
        field.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium text-gray-700">Author ${newIndex + 1}</span>
                <button type="button" class="remove-author text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Select Co-Author *</label>
                    <select name="authors[${newIndex}][user_id]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg author-select">
                        <option value="">Select an author...</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="radio" name="corresponding_author" value="${newIndex}" class="text-blue-600 focus:ring-blue-500 corresponding-radio">
                        <span class="ml-2 text-sm text-gray-600">Corresponding Author</span>
                    </label>
                </div>
            </div>
        `;
        
        if (coAuthorsContainer) {
            coAuthorsContainer.appendChild(field);
        }

        // Add remove event listener to the new field
        field.querySelector('.remove-author').addEventListener('click', function() {
            field.remove();
            reindexCoAuthors();
        });

        coAuthorCount++;
    }

    // Add click event to Add Co-Author button
    if (addAuthorBtn) {
        addAuthorBtn.addEventListener('click', addCoAuthorField);
    }

    // Add remove event listeners to existing co-author remove buttons
    document.querySelectorAll('.remove-author').forEach(btn => {
        btn.addEventListener('click', function() {
            const field = this.closest('.co-author');
            if (field) {
                field.remove();
                reindexCoAuthors();
            } else {
                alert('You cannot remove the primary author.');
            }
        });
    });

    // -----------------------------
    // FORM SUBMIT VALIDATION
    // -----------------------------
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (isUnderReview) return true;

            // Check submission type
            const submissionType = document.querySelector('input[name="submission_type"]:checked');
            if (!submissionType) { 
                e.preventDefault(); 
                alert('Please select a submission type.'); 
                return; 
            }

            // File validation for full paper
            if (submissionType.value === 'full_paper' && !hasExistingFile) {
                if (!fileInput.files || !fileInput.files[0]) { 
                    e.preventDefault(); 
                    alert('Please upload your paper file.'); 
                    return; 
                }
            }

            // Abstract word count validation
            if (abstractTextarea) {
                const wordCount = abstractTextarea.value.trim().split(/\s+/).filter(w => w.length > 0).length;
                if (wordCount > 250) { 
                    e.preventDefault(); 
                    alert(`Abstract exceeds 250 words (${wordCount} words). Please shorten it.`); 
                    abstractTextarea.focus(); 
                    return; 
                }
                if (wordCount < 50) {
                    e.preventDefault(); 
                    alert(`Abstract is too short (${wordCount} words). Minimum 50 words required.`); 
                    abstractTextarea.focus(); 
                    return; 
                }
            }

            // Co-authors validation - check if any co-author fields are empty
            const coAuthorSelects = document.querySelectorAll('.co-author select.author-select');
            let hasEmptyCoAuthor = false;
            coAuthorSelects.forEach(select => {
                if (!select.value) {
                    hasEmptyCoAuthor = true;
                }
            });
            
            if (hasEmptyCoAuthor) { 
                e.preventDefault(); 
                alert('Please select all co-authors or remove empty fields.'); 
                return; 
            }

            // Corresponding author validation
            const corrAuthor = document.querySelector('input[name="corresponding_author"]:checked');
            if (!corrAuthor) { 
                e.preventDefault(); 
                alert('Please select a corresponding author.'); 
                return; 
            }
        });
    }
});
</script>
@endsection
