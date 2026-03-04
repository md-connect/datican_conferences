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
                                oninput="updateWordCount(this)"
                                {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'readonly' : '' }}>{{ old('abstract', $paper->abstract) }}</textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-sm text-gray-500">Maximum 250 words</p>
                            <p class="text-sm text-gray-500" id="word-count">
                                {{ str_word_count($paper->abstract) }}/250 words
                            </p>
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
                <div class="mb-8">
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
                    @endphp
                    
                    <div id="authors-section">
                        <!-- First author (current user) -->
                        <div class="author-field mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-700">Author 1 (You)</span>
                                <button type="button" class="text-gray-400 cursor-not-allowed" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Author *</label>
                                    <select disabled
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                                        <option selected>
                                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }} ({{ auth()->user()->email }})
                                        </option>
                                    </select>
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
                        
                        <!-- Existing co-authors -->
                        @foreach($existingAuthors->where('user_id', '!=', auth()->id()) as $index => $author)
                        @php $authorIndex = $index + 1; @endphp
                        <div class="author-field mb-4 p-4 border border-gray-200 rounded-lg">
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
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // -----------------------------
    // VARIABLES
    // -----------------------------
    let authorCount = {{ $existingAuthors->count() }};
    const authorsSection = document.getElementById('authors-section');
    const addAuthorBtn = document.getElementById('add-author');
    const abstractTextarea = document.querySelector('textarea[name="abstract"]');
    const wordCountElement = document.getElementById('word-count');

    const submissionTypeRadios = document.querySelectorAll('.submission-type-radio');
    const fileUploadSection = document.getElementById('file_upload_section');
    const fileInput = document.getElementById('paper_file_input');
    const fileRequiredIndicator = document.getElementById('file_required_indicator');
    const fileUploadContent = document.getElementById('file_upload_content');
    const fileSelectedContent = document.getElementById('file_selected_content');
    const fileNameDisplay = document.getElementById('file_name_display');
    const fileError = document.getElementById('file_error');

    const hasExistingFile = {{ $paper->file_path ? 'true' : 'false' }};
    const isUnderReview = {{ $paper->status === 'under_review' || $paper->status === 'reviewing' ? 'true' : 'false' }};

    // -----------------------------
    // ABSTRACT WORD COUNT
    // -----------------------------
    function updateWordCount() {
        const text = abstractTextarea.value.trim();
        let wordCount = 0;
        if (text.length > 0) {
            wordCount = text.split(/\s+/).filter(w => w.length > 0).length;
        }
        wordCountElement.textContent = `${wordCount}/250 words`;

        if (wordCount > 240) {
            wordCountElement.classList.add('text-red-600');
            wordCountElement.classList.remove('text-yellow-600');
        } else if (wordCount > 200) {
            wordCountElement.classList.add('text-yellow-600');
            wordCountElement.classList.remove('text-red-600');
        } else {
            wordCountElement.classList.remove('text-yellow-600', 'text-red-600');
            wordCountElement.classList.add('text-gray-500');
        }
    }

    if (abstractTextarea && !abstractTextarea.readOnly) {
        updateWordCount();
        abstractTextarea.addEventListener('input', updateWordCount);
    }

    // -----------------------------
    // ADD / REMOVE AUTHORS
    // -----------------------------
    function reindexAuthors() {
        document.querySelectorAll('.author-field').forEach((field, idx) => {
            // Update label
            const label = field.querySelector('span.font-medium');
            if (label) label.textContent = `Author ${idx + 1}`;

            // Update select name
            const select = field.querySelector('select.author-select');
            if (select) select.name = `authors[${idx}][user_id]`;

            // Update corresponding radio value
            const radio = field.querySelector('input.corresponding-radio');
            if (radio) radio.value = idx;
        });
        authorCount = document.querySelectorAll('.author-field').length;
    }

    function addAuthorField() {
        const index = authorCount;
        const field = document.createElement('div');
        field.className = 'author-field mb-4 p-4 border border-gray-200 rounded-lg';
        field.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium text-gray-700">Author ${index + 1}</span>
                <button type="button" class="remove-author text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Select Co-Author *</label>
                    <select name="authors[${index}][user_id]" required
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
                        <input type="radio" name="corresponding_author" value="${index}" class="text-blue-600 focus:ring-blue-500 corresponding-radio">
                        <span class="ml-2 text-sm text-gray-600">Corresponding Author</span>
                    </label>
                </div>
            </div>
        `;
        authorsSection.appendChild(field);

        field.querySelector('.remove-author').addEventListener('click', function() {
            if (document.querySelectorAll('.author-field').length > 1) {
                field.remove();
                reindexAuthors();
            } else {
                alert('You must have at least one author (yourself).');
            }
        });

        authorCount++;
    }

    if (addAuthorBtn) {
        addAuthorBtn.addEventListener('click', addAuthorField);
    }

    // Remove buttons on existing authors
    document.querySelectorAll('.remove-author').forEach(btn => {
        btn.addEventListener('click', function() {
            const field = this.closest('.author-field');
            if (document.querySelectorAll('.author-field').length > 1) {
                field.remove();
                reindexAuthors();
            } else {
                alert('You must have at least one author (yourself).');
            }
        });
    });

    // -----------------------------
    // SUBMISSION TYPE TOGGLE FILE UPLOAD
    // -----------------------------
    function toggleFileUpload() {
        const selectedRadio = document.querySelector('input[name="submission_type"]:checked');
        if (!selectedRadio) return;

        const isAbstractOnly = selectedRadio.value === 'abstract_only';
        const wrapper = document.getElementById('file_upload_section_wrapper');

        if (wrapper) {
            if (isAbstractOnly) {
                wrapper.style.opacity = '0';
                wrapper.style.pointerEvents = 'none';
                fileInput.disabled = true;
                fileInput.removeAttribute('required');
                fileRequiredIndicator.classList.add('hidden');
                if (fileError) fileError.classList.add('hidden');
            } else {
                wrapper.style.opacity = '1';
                wrapper.style.pointerEvents = 'auto';
                fileInput.disabled = fileInput.hasAttribute('disabled'); // respect under_review
                if (!hasExistingFile && !fileInput.disabled) {
                    fileInput.setAttribute('required', 'required');
                    fileRequiredIndicator.classList.remove('hidden');
                } else {
                    fileInput.removeAttribute('required');
                    fileRequiredIndicator.classList.add('hidden');
                }
            }
        }
    }
// Drag and drop functionality
if (fileUploadArea) {
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        // Only highlight if not disabled
        if (!fileInput.disabled) {
            this.classList.add('border-blue-400', 'bg-blue-50');
        }
    });

    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
    });

    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');

        // Only allow drop if not disabled
        if (!fileInput.disabled && e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
}


    submissionTypeRadios.forEach(radio => radio.addEventListener('change', toggleFileUpload));
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
        fileUploadSection.addEventListener('click', function() {
            if (!fileInput.disabled) fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024;
            if (fileError) { fileError.classList.add('hidden'); fileError.textContent = ''; }

            if (file) {
                if (!file.name.toLowerCase().endsWith('.pdf')) {
                    if (fileError) { fileError.textContent = 'File must be PDF format'; fileError.classList.remove('hidden'); }
                    this.value = '';
                    resetFileDisplay();
                    return;
                }
                if (file.size > maxSize) {
                    if (fileError) { fileError.textContent = 'File size must be less than 10MB'; fileError.classList.remove('hidden'); }
                    this.value = '';
                    resetFileDisplay();
                    return;
                }

                if (fileNameDisplay) fileNameDisplay.textContent = file.name;
                if (fileUploadContent) fileUploadContent.classList.add('hidden');
                if (fileSelectedContent) fileSelectedContent.classList.remove('hidden');
            } else {
                resetFileDisplay();
            }
        });

        fileUploadSection.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('border-blue-400','bg-blue-50'); });
        fileUploadSection.addEventListener('dragleave', function(e) { e.preventDefault(); this.classList.remove('border-blue-400','bg-blue-50'); });
        fileUploadSection.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400','bg-blue-50');
            if (!fileInput.disabled && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }

    // -----------------------------
    // FORM SUBMIT VALIDATION
    // -----------------------------
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (isUnderReview) return true;

            // Submission type
            const submissionType = document.querySelector('input[name="submission_type"]:checked');
            if (!submissionType) { e.preventDefault(); alert('Please select a submission type.'); return; }

            // File validation
            if (submissionType.value === 'full_paper' && !hasExistingFile) {
                if (!fileInput.files[0]) { e.preventDefault(); alert('Please upload your paper file.'); return; }
            }

            // Abstract word count
            if (abstractTextarea) {
                const wordCount = abstractTextarea.value.trim().split(/\s+/).filter(w => w.length > 0).length;
                if (wordCount > 250) { e.preventDefault(); alert(`Abstract exceeds 250 words (${wordCount}).`); abstractTextarea.focus(); return; }
            }

            // Authors
            const authorSelects = this.querySelectorAll('.author-select');
            let hasAuthor = Array.from(authorSelects).some(sel => sel.value);
            if (!hasAuthor && authorSelects.length > 0) { e.preventDefault(); alert('Select at least one author.'); return; }

            // Corresponding author
            const corrAuthor = document.querySelector('input[name="corresponding_author"]:checked');
            if (!corrAuthor) { e.preventDefault(); alert('Please select a corresponding author.'); return; }
        });
    }
});
</script>

