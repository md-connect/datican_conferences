@extends('layouts.app')

@section('title', 'Camera Ready Submission - ' . $paper->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mb-2">
                        {{ $paper->anonymous_id }}
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $paper->title }}</h1>
                    <p class="text-gray-600">Camera Ready Submission</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Submission Deadline</div>
                    <div class="font-medium">May 1, 2026</div>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                @if($cameraReady)
                <span class="px-4 py-2 rounded-lg {{ $cameraReady->status === 'approved' ? 'bg-green-100 text-green-800' : ($cameraReady->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ ucfirst($cameraReady->status) }}
                </span>
                @if($cameraReady->status === 'approved')
                <span class="text-sm text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>Approved on {{ $cameraReady->approved_at->format('M d, Y') }}
                </span>
                @endif
                @else
                <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg">Not Submitted</span>
                @endif
            </div>
        </div>
        
        <!-- Submission Form -->
        @if(!$cameraReady || $cameraReady->status === 'rejected')
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Submit Camera Ready Version</h2>
            
            <form action="{{ route('camera-ready.store', $paper) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Upload Final Paper *</label>
                    <div class="relative">
                        <input type="file" id="camera_ready_file" name="camera_ready_file" 
                               class="hidden"
                               accept=".pdf,.docx"
                               required>
                        
                        <div id="drop-zone" 
                             class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 hover:bg-blue-50 transition cursor-pointer">
                            <i class="fas fa-file-upload text-4xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium text-gray-700 mb-2">Drop your final paper here or click to browse</p>
                            <p class="text-sm text-gray-500">PDF or DOCX format only</p>
                            <p id="file-name" class="mt-4 text-sm text-blue-600 font-medium hidden"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Format Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Format *</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative">
                            <input type="radio" name="format" value="pdf" class="sr-only peer" checked>
                            <div class="border-2 border-gray-200 rounded-lg p-4 text-center cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                <i class="fas fa-file-pdf text-2xl text-red-500 mb-2"></i>
                                <p class="font-medium">PDF</p>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="format" value="docx" class="sr-only peer">
                            <div class="border-2 border-gray-200 rounded-lg p-4 text-center cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                <i class="fas fa-file-word text-2xl text-blue-500 mb-2"></i>
                                <p class="font-medium">DOCX</p>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="format" value="latex" class="sr-only peer">
                            <div class="border-2 border-gray-200 rounded-lg p-4 text-center cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                <i class="fas fa-code text-2xl text-green-500 mb-2"></i>
                                <p class="font-medium">LaTeX</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Changes Summary -->
                <div>
                    <label for="changes_summary" class="block text-sm font-medium text-gray-700 mb-2">
                        Summary of Changes from Review *
                    </label>
                    <textarea id="changes_summary" name="changes_summary" rows="6"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe the changes made based on reviewer feedback..."
                              required></textarea>
                    <p class="mt-2 text-sm text-gray-500">Please detail all changes made in response to reviewer comments.</p>
                </div>
                
                <!-- Copyright Form -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Copyright Form</label>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input id="copyright_signed" name="copyright_signed" type="checkbox" 
                                   class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   required>
                            <label for="copyright_signed" class="ml-3">
                                <span class="text-sm text-gray-700">I agree to the conference copyright terms</span>
                                <a href="#" class="ml-2 text-blue-600 hover:text-blue-800 text-sm">(view terms)</a>
                            </label>
                        </div>
                        
                        <div class="relative">
                            <input type="file" id="copyright_form" name="copyright_form" 
                                   class="hidden"
                                   accept=".pdf">
                            
                            <div class="border border-gray-300 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-file-signature text-2xl text-gray-400"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Upload Signed Copyright Form</p>
                                        <p class="text-sm text-gray-500">Optional: Upload scanned signed copyright form</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('copyright_form').click()" 
                                            class="ml-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Browse
                                    </button>
                                </div>
                                <p id="copyright-file-name" class="mt-2 text-sm text-blue-600 hidden"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Author Order Confirmation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Author Order Confirmation</label>
                    <div class="space-y-3">
                        @foreach($paper->authors as $index => $author)
                        <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <span class="text-sm font-medium text-blue-700">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium text-gray-900">{{ $author->full_name }}</span>
                                @if($author->pivot->is_corresponding)
                                <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">Corresponding</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="pt-6 border-t">
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Submit Camera Ready Version
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif
        
        <!-- Current Submission -->
        @if($cameraReady)
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Current Submission</h2>
            
            <div class="space-y-6">
                <!-- File Info -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file text-3xl text-gray-400"></i>
                        </div>
                        <div class="ml-6 flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $cameraReady->file_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $cameraReady->file_size_formatted }} • {{ strtoupper($cameraReady->format) }}</p>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="#" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Submitted on {{ $cameraReady->submitted_at->format('F d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Changes Summary -->
                @if($cameraReady->changes_summary)
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Changes Summary</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $cameraReady->changes_summary }}</p>
                    </div>
                </div>
                @endif
                
                <!-- Copyright Status -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Copyright Status</h4>
                    <div class="flex items-center">
                        @if($cameraReady->copyright_signed)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Copyright Accepted
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-2"></i>Pending Copyright
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Admin Actions -->
                @if(auth()->user()->isChair())
                <div class="pt-6 border-t">
                    <h4 class="font-medium text-gray-700 mb-4">Chair Actions</h4>
                    <div class="flex space-x-4">
                        @if($cameraReady->status === 'submitted')
                        <form action="{{ route('camera-ready.approve', $cameraReady) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                                    onclick="return confirm('Approve this camera ready submission?')">
                                Approve Submission
                            </button>
                        </form>
                        <button onclick="showRejectionModal()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Request Revisions
                        </button>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Rejection Reason -->
                @if($cameraReady->status === 'rejected' && $cameraReady->rejection_reason)
                <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                    <h4 class="font-medium text-red-800 mb-2">Revision Required</h4>
                    <p class="text-red-700">{{ $cameraReady->rejection_reason }}</p>
                    <p class="text-sm text-red-600 mt-2">Please address these issues and resubmit.</p>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Proceedings Information -->
        @if($cameraReady && $cameraReady->status === 'approved' && $cameraReady->proceedings)
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Proceedings Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Track</dt>
                            <dd class="mt-1 text-gray-900">{{ $cameraReady->proceedings->track->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pages</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ $cameraReady->proceedings->page_start }}-{{ $cameraReady->proceedings->page_end }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">DOI</dt>
                            <dd class="mt-1 text-gray-900">
                                <a href="https://doi.org/{{ $cameraReady->proceedings->doi }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $cameraReady->proceedings->doi }}
                                </a>
                            </dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-4">Citation</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 text-sm">{{ $cameraReady->proceedings->citation }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Rejection Modal -->
@if(auth()->user()->isChair())
<div id="rejectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-xl font-bold text-gray-900">Request Revisions</h3>
            </div>
            
            <form action="{{ route('camera-ready.reject', $cameraReady) }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for requesting revisions *
                    </label>
                    <textarea name="rejection_reason" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Explain what needs to be revised..."
                              required></textarea>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeRejectionModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Request Revisions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    // File upload handling
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('camera_ready_file');
    const fileName = document.getElementById('file-name');
    
    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileName.classList.remove('hidden');
                dropZone.classList.add('border-green-500', 'bg-green-50');
            }
        });
        
        // Drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileName.textContent = files[0].name;
                fileName.classList.remove('hidden');
                dropZone.classList.add('border-green-500', 'bg-green-50');
            }
        });
    }
    
    // Copyright form upload
    const copyrightInput = document.getElementById('copyright_form');
    const copyrightFileName = document.getElementById('copyright-file-name');
    
    if (copyrightInput) {
        copyrightInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                copyrightFileName.textContent = file.name;
                copyrightFileName.classList.remove('hidden');
            }
        });
    }
    
    // Rejection modal
    function showRejectionModal() {
        document.getElementById('rejectionModal').classList.remove('hidden');
    }
    
    function closeRejectionModal() {
        document.getElementById('rejectionModal').classList.add('hidden');
    }
</script>
@endsection