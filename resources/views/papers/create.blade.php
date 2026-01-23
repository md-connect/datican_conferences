@extends('layouts.app')

@section('title', 'Submit Paper')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Submit New Paper</h1>
            <p class="text-gray-600">Submit your research paper for DATICAN Conference 2026</p>
        </div>



        <div class="bg-white rounded-xl shadow-lg p-6">
            <form action="{{ route('papers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
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
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter paper title">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Abstract * (Maximum 250 words)
                        </label>
                        <textarea name="abstract" rows="4" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter paper abstract (maximum 250 words)"
                                oninput="updateWordCount(this)"></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-sm text-gray-500">Maximum 250 words</p>
                            <p class="text-sm text-gray-500" id="word-count">0/250 words</p>
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
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., AI, Machine Learning, Healthcare">
                        <p class="text-sm text-gray-500 mt-1">Separate keywords with commas</p>
                    </div>
                </div>

                <!-- Conference Details -->
                <!-- Replace the Conference Details section -->
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Conference Details</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Conference Year *
            </label>
            <select name="conference_year" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Topic Area *
            </label>
            <select name="topic_area" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Topic Area</option>
                <option value="ai_ml">Artificial Intelligence & Machine Learning</option>
                <option value="data_science">Data Science & Analytics</option>
                <option value="healthcare">Healthcare Applications</option>
                <option value="clinical">Clinical Decision Support</option>
                <option value="imaging">Medical Imaging</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>
    
    <!-- Replace the Submission Type dropdown with Radio buttons -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    Submission Type *
                </label>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="submission_type" value="abstract_only" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 submission-type-radio"
                            id="submission_type_abstract">
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
                            id="submission_type_full" checked>
                        <span class="ml-3">
                            <span class="text-gray-900 font-medium">Full Paper</span>
                            <span class="text-gray-500 text-sm block mt-1">
                                Submit complete paper with abstract. Requires file upload.
                            </span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- File Upload Section - Now conditional -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Paper File</h2>
                
                <div class="mb-4" id="file_upload_section">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Paper (PDF only) *
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
                        id="file_upload_area">
                        <input type="file" name="paper_file" accept=".pdf" required
                            class="hidden" id="paper_file_input">
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
                    <p class="text-sm text-gray-500 mt-2">Please ensure your paper follows the conference template.</p>
                    <div id="file_error" class="mt-2 text-sm text-red-600 hidden"></div>
                </div>
                
                <!-- Anonymous submission checkbox -->
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="is_anonymous" value="1" checked
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        id="is_anonymous">
                    <label for="is_anonymous" class="ml-2 text-gray-700">
                        Submit as anonymous (for double-blind review)
                    </label>
                </div>
            </div>


                <!-- Authors -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Authors</h2>
                    <p class="text-sm text-gray-600 mb-4">You are automatically added as the first author.</p>
                    
                    <div id="authors-section">
                        <!-- First author (current user) - HARDCODED IN HTML -->
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
                                        <input type="checkbox" checked disabled
                                            class="text-blue-600 focus:ring-blue-500 bg-gray-100">
                                        <span class="ml-2 text-sm text-gray-600">Corresponding Author</span>
                                        <input type="hidden" name="authors[0][is_corresponding]" value="1">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="add-author" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-plus mr-2"></i>
                        Add Co-Author
                    </button>
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
                                @foreach($registrations as $registration)
                                <label class="flex items-center">
                                    <input type="checkbox" name="registration_ids[]" value="{{ $registration->id }}"
                                           class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2">{{ $registration->firstname }} {{ $registration->lastname }} 
                                        ({{ $registration->email }}) - {{ $registration->institution }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-yellow-800">No conference registration found for your account. 
                                <a href="{{ route('conference.register') }}" class="text-blue-600 hover:underline">Register for the conference first</a> 
                                or submit without linking to a registration.
                            </p>
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
                                  placeholder="Any special instructions or comments for the reviewers"></textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <div>
                        <p class="text-sm text-gray-600">
                            Need help? Check the 
                            <a href="#" class="text-blue-600 hover:underline">submission guidelines</a>
                        </p>
                    </div>
                    
                    <div class="space-x-4">
                        <button type="submit" name="action" value="save"
                                class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                            <i class="fas fa-save mr-2"></i>
                            Save as Draft
                        </button>
                        
                        <button type="submit" name="action" value="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Paper
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize author count
    let authorCount = 1; // Start with 1 because first author is already in HTML
    
    // Word counter for abstract
    function updateWordCount(textarea) {
        const wordCountElement = document.getElementById('word-count');
        const text = textarea.value.trim();
        
        // Simple word count - counts words separated by spaces
        let wordCount = 0;
        if (text.length > 0) {
            // Split by spaces and filter out empty strings
            wordCount = text.split(/\s+/).filter(word => word.length > 0).length;
        }
        
        wordCountElement.textContent = `${wordCount}/250 words`;
        
        // Change color when approaching limit
        if (wordCount > 200) {
            wordCountElement.classList.add('text-yellow-600');
            wordCountElement.classList.remove('text-gray-500');
        } else if (wordCount > 240) {
            wordCountElement.classList.add('text-red-600');
            wordCountElement.classList.remove('text-yellow-600');
        } else {
            wordCountElement.classList.remove('text-yellow-600', 'text-red-600');
            wordCountElement.classList.add('text-gray-500');
        }
    }
    
    // Initialize word count
    const abstractTextarea = document.querySelector('textarea[name="abstract"]');
    if (abstractTextarea) {
        updateWordCount(abstractTextarea);
        abstractTextarea.addEventListener('input', function() {
            updateWordCount(this);
        });
    }
    
    // Add author button functionality
    const addAuthorBtn = document.getElementById('add-author');
    const authorsSection = document.getElementById('authors-section');
    
    if (addAuthorBtn && authorsSection) {
        addAuthorBtn.addEventListener('click', function() {
            const index = authorCount;
            
            // Create new author field
            const authorField = document.createElement('div');
            authorField.className = 'author-field mb-4 p-4 border border-gray-200 rounded-lg';
            authorField.innerHTML = `
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
                            <input type="checkbox" name="authors[${index}][is_corresponding]" 
                                   value="1" class="text-blue-600 focus:ring-blue-500 corresponding-checkbox">
                            <span class="ml-2 text-sm text-gray-600">Corresponding Author</span>
                        </label>
                    </div>
                </div>
            `;
            
            // Add the new field
            authorsSection.appendChild(authorField);
            authorCount++;
            
            // Add remove functionality
            const removeBtn = authorField.querySelector('.remove-author');
            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.author-field').length > 1) {
                    authorField.remove();
                    authorCount--;
                } else {
                    alert('You must have at least one author (yourself).');
                }
            });
        });
    }
    
    // Submission Type Radio Button Logic
    const submissionTypeRadios = document.querySelectorAll('.submission-type-radio');
    const fileUploadSection = document.getElementById('file_upload_section');
    const fileInput = document.getElementById('paper_file_input');
    const fileUploadArea = document.getElementById('file_upload_area');
    const fileUploadContent = document.getElementById('file_upload_content');
    const fileSelectedContent = document.getElementById('file_selected_content');
    const fileNameDisplay = document.getElementById('file_name_display');
    const fileError = document.getElementById('file_error');
    
    // Function to toggle file upload section
    function toggleFileUpload() {
        const selectedType = document.querySelector('input[name="submission_type"]:checked').value;
        const isAbstractOnly = selectedType === 'abstract_only';
        
        if (isAbstractOnly) {
            // Hide file upload for abstract only
            fileUploadSection.style.opacity = '0.5';
            fileUploadSection.style.pointerEvents = 'none';
            fileInput.disabled = true;
            fileInput.removeAttribute('required');
            fileError.classList.add('hidden');
            fileError.textContent = '';
            
            // Reset file display
            resetFileDisplay();
        } else {
            // Show file upload for full paper
            fileUploadSection.style.opacity = '1';
            fileUploadSection.style.pointerEvents = 'auto';
            fileInput.disabled = false;
            fileInput.setAttribute('required', 'required');
        }
    }
    
    // Add event listeners to radio buttons
    if (submissionTypeRadios) {
        submissionTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleFileUpload);
        });
    }
    
    // Initialize on page load
    if (fileUploadSection && fileInput) {
        toggleFileUpload();
    }
    
    // File upload click handler
    if (fileUploadArea) {
        fileUploadArea.addEventListener('click', function(e) {
            if (!fileInput.disabled) {
                fileInput.click();
            }
        });
    }
    
    // File input change handler
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (fileError) {
                fileError.classList.add('hidden');
                fileError.textContent = '';
            }
            
            if (file) {
                // Check file size
                if (file.size > maxSize) {
                    if (fileError) {
                        fileError.textContent = 'File size must be less than 10MB';
                        fileError.classList.remove('hidden');
                    }
                    this.value = '';
                    resetFileDisplay();
                    return;
                }
                
                // Check file extension
                const fileName = file.name.toLowerCase();
                if (!fileName.endsWith('.pdf')) {
                    if (fileError) {
                        fileError.textContent = 'File must be PDF format';
                        fileError.classList.remove('hidden');
                    }
                    this.value = '';
                    resetFileDisplay();
                    return;
                }
                
                // Show file selected
                if (fileNameDisplay) {
                    fileNameDisplay.textContent = file.name;
                }
                if (fileUploadContent) {
                    fileUploadContent.classList.add('hidden');
                }
                if (fileSelectedContent) {
                    fileSelectedContent.classList.remove('hidden');
                }
            }
        });
    }
    
    // Drag and drop functionality
    if (fileUploadArea) {
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
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
            
            if (!fileInput.disabled && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }
    
    function resetFileDisplay() {
        if (fileUploadContent) {
            fileUploadContent.classList.remove('hidden');
        }
        if (fileSelectedContent) {
            fileSelectedContent.classList.add('hidden');
        }
        if (fileNameDisplay) {
            fileNameDisplay.textContent = '';
        }
    }
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check submission type
            const submissionType = document.querySelector('input[name="submission_type"]:checked');
            if (!submissionType) {
                e.preventDefault();
                alert('Please select a submission type (Abstract Only or Full Paper).');
                return;
            }
            
            // Check file for full paper submissions
            if (submissionType.value === 'full_paper') {
                const file = fileInput.files[0];
                if (!file) {
                    e.preventDefault();
                    alert('Please upload your paper file for Full Paper submission.');
                    return;
                }
                
                // Re-validate file on submit
                const maxSize = 10 * 1024 * 1024;
                const fileName = file.name.toLowerCase();
                
                if (file.size > maxSize) {
                    e.preventDefault();
                    if (fileError) {
                        fileError.textContent = 'File size must be less than 10MB';
                        fileError.classList.remove('hidden');
                    }
                    return;
                }
                
                if (!fileName.endsWith('.pdf')) {
                    e.preventDefault();
                    if (fileError) {
                        fileError.textContent = 'File must be PDF format';
                        fileError.classList.remove('hidden');
                    }
                    return;
                }
            }
            
            // Abstract word count validation
            if (abstractTextarea && abstractTextarea.value.trim().length > 0) {
                const text = abstractTextarea.value.trim();
                const wordCount = text.split(/\s+/).filter(word => word.length > 0).length;
                
                if (wordCount > 250) {
                    e.preventDefault();
                    alert(`Abstract must not exceed 250 words. Current count: ${wordCount} words.`);
                    abstractTextarea.focus();
                    return;
                }
            } else {
                e.preventDefault();
                alert('Abstract is required.');
                abstractTextarea.focus();
                return;
            }
            
            // Ensure at least one author is selected
            const authorSelects = this.querySelectorAll('.author-select');
            let hasValidAuthor = false;
            
            authorSelects.forEach(select => {
                if (select.value) {
                    hasValidAuthor = true;
                }
            });
            
            if (!hasValidAuthor) {
                e.preventDefault();
                alert('Please select at least one author.');
                return;
            }
        });
    }
});
</script>