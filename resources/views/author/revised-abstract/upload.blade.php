@extends('layouts.app')

@section('title', 'Upload Revised Abstract - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
                <h1 class="text-xl font-bold text-white">Upload Revised Abstract</h1>
                <p class="text-gray-100 text-sm mt-1">Please upload your revised abstract in MS Word format</p>
            </div>
            
            <!-- Paper Information -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Paper Information</h2>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Paper ID</p>
                        <p class="font-medium text-gray-900">{{ $paper->anonymous_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Decision</p>
                        <p class="font-medium text-yellow-700">
                            {{ $paper->decision == 'accept_with_minor_revision' ? 'Accepted with Minor Revision' : 'Accepted with Major Revision' }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Paper Title</p>
                        <p class="font-medium text-gray-900">{{ $paper->title }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Document Requirements -->
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 m-6 rounded">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-yellow-600 mr-3 mt-1"></i>
        <div class="flex-1">
            <h3 class="font-semibold text-yellow-800">Document Requirements</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                <!-- Left Column -->
                <div>
                    <p class="text-sm font-medium text-yellow-800 mb-2">Required Sections:</p>
                    <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                        <li><strong>Abstract Title</strong> - Full title of your paper</li>
                        <li><strong>Authors' Names</strong> - Full names of all authors</li>
                        <li><strong>Affiliations</strong> - Institution, department, country</li>
                        <li><strong>Abstract</strong> - Maximum of 250 words</li>
                        <li><strong>Keywords</strong> - 3-5 relevant keywords</li>
                    </ul>
                </div>
                
                <!-- Right Column -->
                <div>
                    <p class="text-sm font-medium text-yellow-800 mb-2">Format Requirements:</p>
                    <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                        <li>File format: <strong>.doc or .docx</strong></li>
                        <li>Maximum size: <strong>5 MB</strong></li>
                        <li>Font: <strong>Times New Roman, 12pt</strong></li>
                        <li>Line spacing: <strong>1.5</strong></li>
                        <li>Deadline: <strong>May 1, 2026</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
            
            <!-- Upload Form -->
            <div class="p-6">
                <form action="{{ route('author.revised-abstract.upload.post', $paper) }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      id="uploadForm">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Revised Abstract Document <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition" id="dropzone">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-file-word text-4xl text-gray-400 mb-3"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="revised_abstract" class="relative cursor-pointer bg-white rounded-md font-medium text-gray-600 hover:text-gray-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-gray-500">
                                        <span>Upload a file</span>
                                        <input id="revised_abstract" name="revised_abstract" type="file" class="sr-only" accept=".doc,.docx" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    DOC or DOCX up to 5MB
                                </p>
                            </div>
                        </div>
                        @error('revised_abstract')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div id="fileNameDisplay" class="mt-2 text-sm text-green-600 hidden">
                            <i class="fas fa-check-circle mr-1"></i> Selected: <span id="fileName"></span>
                        </div>
                    </div>
                    
                    <!-- Upload Progress -->
                    <div id="uploadProgress" class="hidden mb-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Uploading...</span>
                            <span id="progressPercent">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progressBar" class="bg-gray-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                    
                    <!-- Tracking Information -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-chart-line text-gray-600 mr-2"></i>
                            Submission Tracking
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Submission Status:</span>
                                <span class="ml-2 text-yellow-600 font-medium">Pending</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Deadline:</span>
                                <span class="ml-2 text-red-600 font-medium">May 1, 2026</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Format Required:</span>
                                <span class="ml-2 text-gray-700">Microsoft Word (.doc/.docx)</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Max Size:</span>
                                <span class="ml-2 text-gray-700">5 MB</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-4 border-t">
                        <a href="{{ route('author.revised-abstract.select') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>
                        <button type="submit" 
                                id="submitBtn"
                                class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-upload mr-2"></i> Upload Revised Abstract
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Help Section -->
        <div class="mt-6 bg-white rounded-xl shadow-md p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Need Help?</h3>
            <p class="text-sm text-gray-600">
                If you have any questions about the revised abstract submission process, please contact us at 
                <a href="mailto:manager.datican@gmail.com" class="text-gray-600 hover:underline">manager.datican@gmail.com</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('revised_abstract');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const fileNameSpan = document.getElementById('fileName');
    const uploadForm = document.getElementById('uploadForm');
    const submitBtn = document.getElementById('submitBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    
    // Show file name when selected
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            fileNameSpan.textContent = file.name;
            fileNameDisplay.classList.remove('hidden');
            
            // Validate file type
            const validTypes = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a valid Microsoft Word document (.doc or .docx)');
                fileInput.value = '';
                fileNameDisplay.classList.add('hidden');
                submitBtn.disabled = true;
            } else if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                fileInput.value = '';
                fileNameDisplay.classList.add('hidden');
                submitBtn.disabled = true;
            } else {
                submitBtn.disabled = false;
            }
        }
    });
    
    // Simulate upload progress (optional - real progress requires additional implementation)
    uploadForm.addEventListener('submit', function(e) {
        if (fileInput.files.length > 0) {
            uploadProgress.classList.remove('hidden');
            let percent = 0;
            const interval = setInterval(function() {
                percent += 10;
                if (percent <= 100) {
                    progressBar.style.width = percent + '%';
                    progressPercent.textContent = percent + '%';
                }
                if (percent >= 100) {
                    clearInterval(interval);
                }
            }, 100);
        }
    });
});
</script>

<style>
#dropzone {
    transition: all 0.3s ease;
}
#dropzone:hover {
    border-color: #3B82F6;
    background-color: #EFF6FF;
}
</style>
@endsection