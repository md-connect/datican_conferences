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
                            Abstract *
                        </label>
                        <textarea name="abstract" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter paper abstract"></textarea>
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
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Submission Type *
                        </label>
                        <select name="submission_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="full_paper">Full Paper</option>
                            <option value="short_paper">Short Paper</option>
                            <option value="poster">Poster Abstract</option>
                            <option value="demo">Demo Paper</option>
                        </select>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Paper File</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Paper (PDF only) *
                        </label>
                        <input type="file" name="paper_file" accept=".pdf" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Maximum file size: 10MB. Please ensure your paper follows the conference template.</p>
                    </div>
                    
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize author count
    let authorCount = 1; // Start with 1 because first author is already in HTML
    
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
    
    // File validation
    const fileInput = document.getElementById('paper_file');
    const fileError = document.getElementById('file-error');
    
    if (fileInput && fileError) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            fileError.classList.add('hidden');
            
            if (file) {
                // Check file size
                if (file.size > maxSize) {
                    fileError.textContent = 'File size must be less than 10MB';
                    fileError.classList.remove('hidden');
                    this.value = '';
                    return;
                }
                
                // Check file extension
                const fileName = file.name.toLowerCase();
                const validExtensions = ['.pdf', '.doc', '.docx'];
                const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
                
                if (!hasValidExtension) {
                    fileError.textContent = 'File must be PDF, DOC, or DOCX format';
                    fileError.classList.remove('hidden');
                    this.value = '';
                }
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('paperForm');
    if (form) {
        form.addEventListener('submit', function(e) {
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
            
            // Ensure file is selected
            if (fileInput && !fileInput.files[0]) {
                e.preventDefault();
                alert('Please select a paper file to upload.');
                return;
            }
        });
    }
    
    // Debug: Check if elements exist
    console.log('Add author button:', addAuthorBtn);
    console.log('Authors section:', authorsSection);
    console.log('File input:', fileInput);
});
</script>
@endsection