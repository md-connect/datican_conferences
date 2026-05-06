@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Image Slider with Continuous Zoom -->
<div class="relative h-[400px] md:h-[600px] overflow-hidden">
    <!-- Slide 1 -->
    <div class="absolute inset-0 slide active">
        <img src="{{ asset('images/general/datican-people-1.JPG') }}" 
             alt="Medical AI Research" 
             class="w-full h-full object-cover zoom-image">
    </div>
    
    <!-- Slide 2 -->
    <div class="absolute inset-0 slide">
        <img src="{{ asset('images/general/datican-people-3.JPG') }}" 
             alt="Medical Imaging Technology" 
             class="w-full h-full object-cover zoom-image">
    </div>
    
    <!-- Slide 3 -->
    <div class="absolute inset-0 slide">
        <img src="{{ asset('images/general/datican-people-2.JPG') }}" 
             alt="Healthcare Innovation" 
             class="w-full h-full object-cover zoom-image">
    </div>
</div>

<!-- Hero Section -->
<div class="gradient-bg text-white py-20 mt-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <div class="mb-6">
                <span class="bg-accent text-white px-4 py-1 rounded-full text-2xl font-semibold">DATICAN International Conference</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Theme: "Improving Medical Diagnostics in Nigeria Using AI and Data Science"</h1>
            <p class="text-xl mb-8 text-gray-200">Join leading experts, researchers, and practitioners in advancing healthcare through artificial intelligence and data science.</p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 hover-lift">Register Now</a>
                <a href="{{ route('call-for-papers') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition duration-300 hover-lift">Submit Paper</a>
                <button onclick="openTimetableModal()" class="bg-accent text-white px-8 py-3 rounded-lg font-semibold hover:bg-accent/90 transition duration-300 hover-lift" id="viewTimetableBtn">
                    <i class="fas fa-calendar-alt mr-2"></i> View Conference Timetable
                </button>
            </div>
        </div>
    </div>
</div>

<!-- About Conference -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <!-- LEFT: TEXT CONTENT -->
            <div>
                <div class="flex items-center gap-4 mb-6">
                    <span class="h-px w-12 bg-accent"></span>
                    <span class="text-sm font-semibold uppercase tracking-wide text-accent">
                        About The Conference
                    </span>
                </div>
                <h4 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                    Improving Medical Diagnostics in Nigeria Using AI and Data Science
                </h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Aligned with its mission to advance healthcare delivery in Nigeria through data science
                    and medical image analysis training, the Data Science and Medical Image Analysis for
                    Improved Healthcare Delivery in Nigeria (DATICAN) project is proud to announce its
                    first international conference.
                </p>
                <p class="text-gray-700 mb-8 leading-relaxed">
                    This conference will establish a platform for dialogue and knowledge exchange among
                    key stakeholders, exploring transformative AI-driven solutions and fostering
                    collaborations that directly address Nigeria's healthcare challenges.
                </p>
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Conference Sub-themes Include:
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="text-accent font-semibold">01.</span>
                            <span>AI-Powered Medical Imaging and Radiology</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-accent font-semibold">02.</span>
                            <span>Data Science for Early Disease Prediction</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-accent font-semibold">03.</span>
                            <span>Big Data and Precision Medicine for Cancer Care</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-accent font-semibold">04.</span>
                            <span>AI-Driven Pathology and Laboratory Diagnostics</span>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('call-for-papers') }}"
                   class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary/90 transition">
                    Call for Papers
                </a>
            </div>

            <!-- RIGHT: IMAGE -->
            <div class="relative">
                <img src="{{ asset('images/general/prof-aribisala.png') }}"
                     alt="DATICAN Conference"
                     class="rounded-xl shadow-xl w-full object-cover">
            </div>
        </div>
    </div>
</section>

<!-- Important Dates -->
<div class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-primary">Important Dates</h2>
        <div class="max-w-3xl mx-auto">
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center">
                        <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                            <span class="font-bold">Jan 7</span>
                        </div>
                        <span class="font-medium text-gray-800">Conference Announcement</span>
                    </div>
                    <span class="text-gray-600">2026</span>
                </div>
                
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center">
                        <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                            <span class="font-bold">February 1</span>
                        </div>
                        <span class="font-medium text-gray-800">Abstract Submission Opens</span>
                    </div>
                    <span class="text-gray-600">2026</span>
                </div>
                
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center">
                        <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                            <span class="font-bold">March 30</span>
                        </div>
                        <span class="font-medium text-gray-800">Abstract Submission Deadline</span>
                    </div>
                    <span class="text-gray-600">2026</span>
                </div>
                
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center">
                        <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                            <span class="font-bold">April 15</span>
                        </div>
                        <span class="font-medium text-gray-800">Full Paper Submission Deadline</span>
                    </div>
                    <span class="text-gray-600">2026</span>
                </div>
                
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center">
                        <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                            <span class="font-bold">May 1</span>
                        </div>
                        <span class="font-medium text-gray-800">Reviewer's Feedback Returned</span>
                    </div>
                    <span class="text-gray-600">2026</span>
                </div>
                
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center">
                        <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                            <span class="font-bold">May 13-14</span>
                        </div>
                        <span class="font-medium text-gray-800">Conference Dates</span>
                    </div>
                    <span class="text-gray-600">2026</span>
                </div>

                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center">
                        <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                            <span class="font-bold">May 21</span>
                        </div>
                        <span class="font-medium text-gray-800">Camera-Ready Paper Submission</span>
                    </div>
                    <span class="text-gray-600">2026</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Special Guests -->
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8 text-primary">Special Guests</h2>
        
        <div class="bg-white rounded-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Guest 1 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full">
                        <img src="{{ asset('images/speakers/fred.jpg') }}" alt="Prof. Fred Howard" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">Keynote Speaker</p>
                    <h3 class="text-lg font-bold mb-1 text-primary">
                        Prof. Frederick Howard
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Assistant Professor of Medicine<br>
                       University of Chicago, USA</p>
                </div>

                <!-- Guest 2 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full">
                        <img src="{{ asset('images/speakers/simon-cox.jpeg') }}" alt="Prof. Simon Cox" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">Plenary Speaker</p>
                    <h3 class="text-lg font-bold mb-1 text-primary">
                        Prof. Simon Cox
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Professor of Brain and Cognitive Ageing<br>
                       University of Edinburgh, UK</p>
                </div>
                
                <!-- Guest 3 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full">
                        <img src="{{ asset('images/speakers/aribisala.jpg') }}" alt="Prof. Benjamin Aribisala" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">Chief Host/Program Director</p>
                    <h3 class="text-lg font-bold mb-1 text-primary">
                        Prof. Benjamin Aribisala
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Professor of Computer Science<br>
                      Lagos State University, Nigeria</p>
                </div>
                
                <!-- Guest 4 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full">
                        <img src="{{ asset('images/speakers/prof-ogunde.jpg') }}" alt="Bayo Mohammed Onimode" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">LOC Chairman</p>
                    <h3 class="text-lg font-bold mb-1 text-primary">
                        Prof. Adewale Opeoluwa Ogunde
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Professor of Computer Science <br> 
                      Redeemer's University, Nigeria</p>
                </div>
                
                <!-- Guest 5 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full">
                        <img src="{{ asset('images/speakers/olopade.jpg') }}" alt="Prof. Funmi Olopade" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">Chairman, Steering Committee</p>
                    <h3 class="text-lg font-bold mb-1 text-primary">
                        Prof. Olufunmilayo I. Olopade
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Professor of Medicine<br> Professor of Human Genetics<br>
                        Director, Center for Clinical Cancer Genetics and Global Health<br>University of Chicago, USA</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conference Timetable Modal -->
<div id="timetableModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-calendar-alt text-accent mr-2"></i>
                    DATICAN Conference 2026 Timetable
                </h2>
                <button onclick="closeTimetableModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Modal Body - PDF Viewer -->
            <div class="p-4 overflow-y-auto max-h-[calc(90vh-120px)]">
                <!-- PDF Controls -->
                <div class="flex flex-wrap justify-between gap-3 mb-4">
                    <div class="flex gap-2">
                        <button onclick="onPrevPage()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center">
                            <i class="fas fa-chevron-left mr-2"></i> Previous
                        </button>
                        <button onclick="onNextPage()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center">
                            Next <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-lg">
                        <span class="text-gray-700">Page</span>
                        <span id="page_num" class="font-bold text-primary">1</span>
                        <span class="text-gray-700">of</span>
                        <span id="page_count" class="font-bold">?</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="downloadTimetable()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                            <i class="fas fa-download mr-2"></i> Download
                        </button>
                        <button onclick="printTimetable()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                        <button onclick="openFullscreen()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center">
                            <i class="fas fa-expand mr-2"></i> Fullscreen
                        </button>
                    </div>
                </div>
                
                <!-- PDF Canvas Container -->
                <div class="flex justify-center bg-gray-100 rounded-lg p-4 overflow-auto">
                    <canvas id="pdf-canvas" class="border rounded-lg shadow-lg max-w-full"></canvas>
                </div>
                
                <!-- Loading indicator -->
                <div id="loadingIndicator" class="text-center py-8 hidden">
                    <i class="fas fa-spinner fa-spin text-3xl text-primary"></i>
                    <p class="text-gray-600 mt-2">Loading timetable...</p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-4 border-t bg-gray-50 sticky bottom-0">
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span><i class="fas fa-calendar mr-1"></i> Conference Dates: May 13-14, 2026</span>
                    <span><i class="fas fa-map-marker-alt mr-1"></i> Mode: Virtual (Zoom)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    /* Modal animation */
    #timetableModal {
        transition: opacity 0.3s ease;
    }
    
    #timetableModal:not(.hidden) {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    /* Notification animation */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    .animate-slide-in {
        animation: slideInRight 0.5s ease-out;
    }
    
    .animate-bounce {
        animation: bounce 1s ease-in-out 2;
    }
    
    /* Image zoom effect */
    .zoom-image {
        transition: transform 10s ease-in-out;
    }
    
    .slide.active .zoom-image {
        transform: scale(1.1);
    }
    
    /* PDF Canvas */
    #pdf-canvas {
        max-width: 100%;
        height: auto;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Set worker path for PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    // PDF variables
    let pdfDoc = null;
    let pageNum = 1;
    let pageRendering = false;
    let pageNumPending = null;
    let scale = 1.5;
    let canvas = null;
    let ctx = null;
    
    /**
     * Render a specific page of the PDF
     */
    function renderPage(num) {
        pageRendering = true;
        
        // Show loading indicator
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
        
        pdfDoc.getPage(num).then(function(page) {
            const viewport = page.getViewport({ scale: scale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            
            const renderTask = page.render(renderContext);
            
            renderTask.promise.then(function() {
                pageRendering = false;
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            }).catch(function(error) {
                console.error('Error rendering page:', error);
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
            });
        }).catch(function(error) {
            console.error('Error getting page:', error);
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
        });
        
        // Update page number display
        document.getElementById('page_num').textContent = num;
    }
    
    /**
     * Queue rendering of a page (for next/prev navigation)
     */
    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }
    
    /**
     * Go to previous page
     */
    function onPrevPage() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    }
    
    /**
     * Go to next page
     */
    function onNextPage() {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum);
    }
    
    /**
     * Load the PDF document
     */
    function loadPDF() {
        const url = "{{ asset('files/DATICAN_Conference_Presentation_Schedule.pdf') }}";
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
        
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page_count').textContent = pdfDoc.numPages;
            renderPage(pageNum);
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
                loadingIndicator.innerHTML = '<p class="text-red-600">Failed to load timetable. Please try again later.</p>';
                loadingIndicator.classList.remove('hidden');
            }
        });
    }
    
    /**
     * Open the timetable modal
     */
    function openTimetableModal() {
        const modal = document.getElementById('timetableModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Initialize canvas and load PDF if not already loaded
        if (!pdfDoc) {
            canvas = document.getElementById('pdf-canvas');
            ctx = canvas.getContext('2d');
            loadPDF();
        }
    }
    
    /**
     * Close the timetable modal
     */
    function closeTimetableModal() {
        const modal = document.getElementById('timetableModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    /**
     * Download the timetable PDF
     */
    function downloadTimetable() {
        const link = document.createElement('a');
        link.href = "{{ asset('files/DATICAN_Conference_Presentation_Schedule.pdf') }}";
        link.download = "DATICAN_Conference_Timetable_2026.pdf";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    /**
 * Print the timetable - Direct print approach
 */
function printTimetable() {
    // Method 1: Open PDF in new tab with print dialog
    const pdfUrl = "{{ asset('files/DATICAN_Conference_Presentation_Schedule.pdf') }}";
    const printWindow = window.open(pdfUrl);
    
    if (printWindow) {
        // Wait for PDF to load then trigger print
        printWindow.onload = function() {
            setTimeout(function() {
                printWindow.print();
            }, 1000);
        };
    } else {
        // If popup blocked, try alternative
        alert('Please allow pop-ups to print the timetable, or use the download button and print from your PDF viewer.');
    }
}
    
    /**
     * Open PDF viewer in fullscreen
     */
    function openFullscreen() {
        const viewer = document.getElementById('pdf-canvas');
        if (viewer.requestFullscreen) {
            viewer.requestFullscreen();
        } else if (viewer.webkitRequestFullscreen) {
            viewer.webkitRequestFullscreen();
        } else if (viewer.msRequestFullscreen) {
            viewer.msRequestFullscreen();
        }
    }
    
    /**
     * Show notification badge (instead of auto-popup)
     */
    function showTimetableNotification() {
        const hasSeenNotification = sessionStorage.getItem('hasSeenTimetableNotification');
        
        if (!hasSeenNotification) {
            // Create floating notification
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-6 right-6 bg-accent text-white p-4 rounded-lg shadow-2xl z-50 animate-slide-in';
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="bg-white bg-opacity-20 rounded-full p-2">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-lg">Conference Timetable Available!</p>
                        <p class="text-sm opacity-90">Click to view the full schedule</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove(); openTimetableModal()" 
                            class="ml-2 bg-white text-accent px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                        View Now
                    </button>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            class="text-white opacity-70 hover:opacity-100 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (notification && notification.remove) {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => {
                        if (notification && notification.remove) notification.remove();
                    }, 300);
                }
            }, 10000);
            
            // Mark as seen
            sessionStorage.setItem('hasSeenTimetableNotification', 'true');
        }
    }
    
    // Close modal when clicking outside
    document.getElementById('timetableModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTimetableModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTimetableModal();
        }
    });
    
    // Show notification when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Show notification after 1 second
        setTimeout(function() {
            showTimetableNotification();
        }, 1000);
    });
</script>
