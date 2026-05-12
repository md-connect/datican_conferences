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
            <div>
                <div class="flex items-center gap-4 mb-6">
                    <span class="h-px w-12 bg-accent"></span>
                    <span class="text-sm font-semibold uppercase tracking-wide text-accent">About The Conference</span>
                </div>
                <h4 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">Improving Medical Diagnostics in Nigeria Using AI and Data Science</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">Aligned with its mission to advance healthcare delivery in Nigeria through data science and medical image analysis training, the Data Science and Medical Image Analysis for Improved Healthcare Delivery in Nigeria (DATICAN) project is proud to announce its first international conference.</p>
                <p class="text-gray-700 mb-8 leading-relaxed">This conference will establish a platform for dialogue and knowledge exchange among key stakeholders, exploring transformative AI-driven solutions and fostering collaborations that directly address Nigeria's healthcare challenges.</p>
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Conference Sub-themes Include:</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3"><span class="text-accent font-semibold">01.</span> <span>AI-Powered Medical Imaging and Radiology</span></li>
                        <li class="flex items-start gap-3"><span class="text-accent font-semibold">02.</span> <span>Data Science for Early Disease Prediction</span></li>
                        <li class="flex items-start gap-3"><span class="text-accent font-semibold">03.</span> <span>Big Data and Precision Medicine for Cancer Care</span></li>
                        <li class="flex items-start gap-3"><span class="text-accent font-semibold">04.</span> <span>AI-Driven Pathology and Laboratory Diagnostics</span></li>
                    </ul>
                </div>
                <a href="{{ route('call-for-papers') }}" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary/90 transition">Call for Papers</a>
            </div>
            <div class="relative">
                <img src="{{ asset('images/general/prof-aribisala.png') }}" alt="DATICAN Conference" class="rounded-xl shadow-xl w-full object-cover">
            </div>
        </div>
    </div>
</section>

<!-- Important Dates -->
<div class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-primary">Important Dates</h2>
        <div class="max-w-3xl mx-auto space-y-6">
            <div class="flex items-center justify-between border-b pb-4"><div class="flex items-center"><div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4"><span class="font-bold">Jan 7</span></div><span class="font-medium text-gray-800">Conference Announcement</span></div><span class="text-gray-600">2026</span></div>
            <div class="flex items-center justify-between border-b pb-4"><div class="flex items-center"><div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4"><span class="font-bold">February 1</span></div><span class="font-medium text-gray-800">Abstract Submission Opens</span></div><span class="text-gray-600">2026</span></div>
            <div class="flex items-center justify-between border-b pb-4"><div class="flex items-center"><div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4"><span class="font-bold">March 30</span></div><span class="font-medium text-gray-800">Abstract Submission Deadline</span></div><span class="text-gray-600">2026</span></div>
            <div class="flex items-center justify-between border-b pb-4"><div class="flex items-center"><div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4"><span class="font-bold">April 15</span></div><span class="font-medium text-gray-800">Full Paper Submission Deadline</span></div><span class="text-gray-600">2026</span></div>
            <div class="flex items-center justify-between border-b pb-4"><div class="flex items-center"><div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4"><span class="font-bold">May 1</span></div><span class="font-medium text-gray-800">Reviewer's Feedback Returned</span></div><span class="text-gray-600">2026</span></div>
            <div class="flex items-center justify-between border-b pb-4"><div class="flex items-center"><div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4"><span class="font-bold">May 13-14</span></div><span class="font-medium text-gray-800">Conference Dates</span></div><span class="text-gray-600">2026</span></div>
            <div class="flex items-center justify-between border-b pb-4"><div class="flex items-center"><div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4"><span class="font-bold">May 21</span></div><span class="font-medium text-gray-800">Camera-Ready Paper Submission</span></div><span class="text-gray-600">2026</span></div>
        </div>
    </div>
</div>

<!-- Special Guests -->
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8 text-primary">Special Guests</h2>
        <div class="bg-white rounded-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="text-center"><div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full"><img src="{{ asset('images/speakers/fred.jpg') }}" alt="Prof. Fred Howard" class="w-full h-full object-cover"></div><p class="text-sm font-bold text-gray-800 mb-1">Keynote Speaker</p><h3 class="text-lg font-bold mb-1 text-primary">Prof. Frederick Howard</h3><p class="text-gray-700 text-sm mt-2">Assistant Professor of Medicine<br>University of Chicago, USA</p></div>
                <div class="text-center"><div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full"><img src="{{ asset('images/speakers/simon-cox.jpeg') }}" alt="Prof. Simon Cox" class="w-full h-full object-cover"></div><p class="text-sm font-bold text-gray-800 mb-1">Plenary Speaker</p><h3 class="text-lg font-bold mb-1 text-primary">Prof. Simon Cox</h3><p class="text-gray-700 text-sm mt-2">Professor of Brain and Cognitive Ageing<br>University of Edinburgh, UK</p></div>
                <div class="text-center"><div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full"><img src="{{ asset('images/speakers/aribisala.jpg') }}" alt="Prof. Benjamin Aribisala" class="w-full h-full object-cover"></div><p class="text-sm font-bold text-gray-800 mb-1">Chief Host/Program Director</p><h3 class="text-lg font-bold mb-1 text-primary">Prof. Benjamin Aribisala</h3><p class="text-gray-700 text-sm mt-2">Professor of Computer Science<br>Lagos State University, Nigeria</p></div>
                <div class="text-center"><div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full"><img src="{{ asset('images/speakers/prof-ogunde.jpg') }}" alt="Bayo Mohammed Onimode" class="w-full h-full object-cover"></div><p class="text-sm font-bold text-gray-800 mb-1">LOC Chairman</p><h3 class="text-lg font-bold mb-1 text-primary">Prof. Adewale Opeoluwa Ogunde</h3><p class="text-gray-700 text-sm mt-2">Professor of Computer Science<br>Redeemer's University, Nigeria</p></div>
                <div class="text-center"><div class="w-48 h-48 mx-auto mb-4 overflow-hidden rounded-full"><img src="{{ asset('images/speakers/olopade.jpg') }}" alt="Prof. Funmi Olopade" class="w-full h-full object-cover"></div><p class="text-sm font-bold text-gray-800 mb-1">Chairman, Steering Committee</p><h3 class="text-lg font-bold mb-1 text-primary">Prof. Olufunmilayo I. Olopade</h3><p class="text-gray-700 text-sm mt-2">Professor of Medicine<br>Professor of Human Genetics<br>Director, Center for Clinical Cancer Genetics and Global Health<br>University of Chicago, USA</p></div>
            </div>
        </div>
    </div>
</div>

<!-- ZOOM GUIDELINES MODAL (POPUP ON LOAD) -->
<div id="zoomModal" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black bg-opacity-60">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-xl font-bold text-primary">Conference Access Guidelines</h2>
                    <p class="text-gray-500 text-sm">Virtual Conference via Zoom</p>
                </div>
                <button onclick="closeZoomModal()" class="text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-2xl"></i></button>
            </div>
            <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 130px);">
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6"><div class="flex items-start gap-3"><i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i><div><h3 class="font-semibold text-yellow-800">Important Notice</h3><p class="text-sm text-yellow-700">All sessions will be conducted virtually via Zoom. Please read the guidelines below carefully.</p></div></div></div>
                
                <div class="bg-blue-50 p-4 rounded mb-6"><h3 class="font-semibold text-blue-800 mb-3 flex items-center gap-2"><i class="fas fa-video"></i> Zoom Meeting Details</h3><div class="space-y-2 text-sm"><div class="flex flex-wrap gap-2"><span class="font-medium text-gray-700 w-28">Zoom Link:</span><a href="https://tinyurl.com/DATICANCONF" target="_blank" class="text-blue-600 hover:underline break-all">https://tinyurl.com/DATICANCONF</a></div><div class="flex flex-wrap gap-2"><span class="font-medium text-gray-700 w-28">Meeting ID:</span><span class="text-gray-800 font-mono">929 3790 2652</span></div><div class="flex flex-wrap gap-2"><span class="font-medium text-gray-700 w-28">Passcode:</span><span class="text-gray-800 font-mono">DATICANCNF</span></div></div></div>
                
                <div class="space-y-6">
                    <div><h3 class="font-semibold text-primary text-lg mb-3 pb-2 border-b">A. General Conference Guidelines</h3><ul class="space-y-2 text-gray-700"><li class="flex items-start gap-2"><span class="text-accent font-bold">1.</span> All participants should join the Zoom meeting at least <strong>10 minutes</strong> before the official start time.</li><li class="flex items-start gap-2"><span class="text-accent font-bold">2.</span> Ensure a stable internet connection, functional microphone, and camera before joining.</li><li class="flex items-start gap-2"><span class="text-accent font-bold">3.</span> Name your device using your <strong>full name and Institution</strong>.</li><li class="flex items-start gap-2"><span class="text-accent font-bold">4.</span> Keep microphones muted unless speaking.</li><li class="flex items-start gap-2"><span class="text-accent font-bold">5.</span> All questions and comments should be directed to the chatroom.</li></ul></div>
                    
                    <div><h3 class="font-semibold text-primary text-lg mb-3 pb-2 border-b">B. Breakout Session Structure</h3><p class="text-gray-700 mb-2">There will be <strong>2 breakout sessions</strong> named <strong>Group 1</strong> and <strong>Group 2</strong>.</p><p class="text-gray-700 mb-2">Each breakout session will include:</p><ul class="list-disc list-inside text-gray-700 mb-3 ml-4"><li>Session Chairmen</li><li>Technical Assistant</li><li>Presenters</li><li>Audience participants</li></ul><p class="text-gray-700 mb-2">Suggested presentation format:</p><ul class="list-disc list-inside text-gray-700 ml-4"><li>Presentation: <strong>7 minutes</strong></li><li>Questions & Answers: <strong>3 minutes</strong></li></ul></div>
                    
                    <div><h3 class="font-semibold text-primary text-lg mb-3 pb-2 border-b">C. Guidelines for Presenters</h3>
                        <div class="mb-3"><p class="font-semibold text-gray-800 mb-2">Before the Session</p><ul class="list-disc list-inside text-gray-700 ml-4 space-y-1"><li>Join the Zoom meeting at least <strong>15 minutes</strong> before your presentation time.</li><li>Test your: Audio, screen sharing, presentation slides, rename yourself using: <strong>Full Name – Institution</strong> (e.g Mauton Wheto, LASU)</li><li>Download the conference Zoom background attached to this email.</li><li>Keep presentation slides concise and professional.</li><li>Presenters should remain available throughout their assigned breakout session.</li><li>All presentation slides should be uploaded to the Google link provided below <strong>latest by May 12</strong>.<br><a href="https://drive.google.com/drive/folders/1HMwYD3Hj_8ttT2sTN1hYeYJBzgk9Ve0Q?usp=sharing" target="_blank" class="text-blue-600 hover:underline break-all text-sm">https://drive.google.com/drive/folders/1HMwYD3Hj_8ttT2sTN1hYeYJBzgk9Ve0Q?usp=sharing</a></li><li>Time management is critical due to the high number of presenters.</li></ul></div>
                        <div class="mb-3"><p class="font-semibold text-gray-800 mb-2">During the presentation, presenters should:</p><ul class="list-disc list-inside text-gray-700 ml-4 space-y-1"><li>Turn on video when presenting (if bandwidth allows).</li><li>Share your screen only when instructed by the Chairman.</li><li>Adhere strictly to the allocated presentation time.</li><li>Speak clearly and professionally.</li><li>Respond briefly and respectfully during Q&A.</li></ul></div>
                        <div><p class="font-semibold text-gray-800 mb-2">Technical Etiquette</p><ul class="list-disc list-inside text-gray-700 ml-4 space-y-1"><li>Mute your microphone when not speaking.</li><li>Avoid background noise and distractions.</li><li>Use a quiet, well-lit environment.</li><li>Notify the Chairman immediately if technical difficulties arise.</li><li>Avoid disruptive behaviour in chat or audio.</li><li>Follow instructions from conference organizers and technical staff.</li></ul></div>
                        <div class="mt-3 p-3 bg-gray-50 rounded"><p class="font-semibold text-gray-800 mb-2">Instructions on how to join/leave the breakout session</p><ul class="list-decimal list-inside text-gray-700 ml-4 space-y-1"><li>Join the main session</li><li>Click the "More" button below the Zoom app</li><li>Click the breakout session and choose your group.</li><li>To leave kindly, click the leave button and choose leave breakout</li></ul></div>
                    </div>
                    
                    <div><h3 class="font-semibold text-primary text-lg mb-3 pb-2 border-b">D. How to Upload a Custom Background on Zoom</h3>
                        <div class="mb-3"><p class="font-semibold text-gray-800 mb-2">On Desktop (Windows/Mac)</p><ul class="list-decimal list-inside text-gray-700 ml-4 space-y-1"><li>Open Zoom and sign in to your account.</li><li>Start or join a meeting.</li><li>Click the "^" icon at the bottom of the Zoom window (next to "Stop Video").</li><li>Select "Choose Virtual Background".</li><li>Click on the "+" icon in the top-right corner of the background options.</li><li>Browse to the location of your custom image/video and select it.</li><li>Click "Open" to upload the image/video.</li><li>Select your new background from the list.</li></ul></div>
                        <div><p class="font-semibold text-gray-800 mb-2">On Mobile (iOS/Android)</p><ul class="list-decimal list-inside text-gray-700 ml-4 space-y-1"><li>Open the Zoom app and join or start a meeting.</li><li>Tap the "..." (more) icon at the bottom right.</li><li>Select "Virtual Background".</li><li>Tap the "+" icon in the top right corner.</li><li>Choose from your photos or take a new one.</li><li>Adjust the background if needed, then tap "Done".</li></ul></div>
                    </div>
                    
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded"><div class="flex items-start gap-3"><i class="fas fa-clock text-red-600 mt-0.5"></i><div><h3 class="font-semibold text-red-800">Important Note to Presenters</h3><p class="text-sm text-red-700">If You Miss Your Slot, the presenters may be moved to the end of the session at the Chairman's discretion, if the session schedule may not permit additional time extensions.</p></div></div></div>
                </div>
                
                <div class="mt-6 p-4 bg-gray-100 rounded"><h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2"><i class="fas fa-headset text-accent"></i> Technical Support</h4><p class="text-sm text-gray-600">For technical assistance, contact our support team:</p><div class="mt-2 flex flex-wrap gap-4 text-sm"><span class="flex items-center gap-2"><span class="flex items-center gap-2"><i class="fas fa-envelope text-accent"></i> manager.datican@gmail.com</span></div></div>
                
                <div class="mt-4 text-center text-gray-600 text-sm italic">Thank you for your cooperation and participation.</div>
                <div class="mt-4"></div>
            </div>
            <div class="p-4 border-t bg-gray-50 sticky bottom-0 flex justify-between gap-3">
                <button onclick="closeZoomModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition"><i class="fas fa-times mr-2"></i> Close</button>
                <div class="flex gap-3">
                    <button onclick="downloadGuidelines()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition"><i class="fas fa-download mr-2"></i> Download Guidelines</button>
                    <a href="https://tinyurl.com/DATICANCONF" target="_blank" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition"><i class="fas fa-video mr-2"></i> Join Zoom Meeting</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONFERENCE TIMETABLE MODAL -->
<div id="timetableModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-calendar-alt text-accent mr-2"></i> DATICAN Conference 2026 Timetable</h2>
                <button onclick="closeTimetableModal()" class="text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-2xl"></i></button>
            </div>
            <div class="p-4 overflow-y-auto" style="max-height: calc(90vh - 70px);">
                <div class="flex flex-wrap justify-between gap-3 mb-4">
                    <div class="flex gap-2">
                        <button onclick="onPrevPage()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition flex items-center"><i class="fas fa-chevron-left mr-2"></i> Previous</button>
                        <button onclick="onNextPage()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition flex items-center">Next <i class="fas fa-chevron-right ml-2"></i></button>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded"><span class="text-gray-700">Page</span><span id="page_num" class="font-bold text-primary">1</span><span class="text-gray-700">of</span><span id="page_count" class="font-bold">?</span></div>
                    <div class="flex gap-2">
                        <button onclick="downloadTimetable()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center"><i class="fas fa-download mr-2"></i> Download</button>
                        <button onclick="printTimetable()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition flex items-center"><i class="fas fa-print mr-2"></i> Print</button>
                        <button onclick="openFullscreen()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition flex items-center"><i class="fas fa-expand mr-2"></i> Fullscreen</button>
                    </div>
                </div>
                <div class="flex justify-center bg-gray-100 rounded p-4 overflow-auto min-h-[500px]">
                    <canvas id="pdf-canvas" class="border rounded shadow-lg" style="max-width: 100%; height: auto;"></canvas>
                </div>
                <div id="loadingIndicator" class="text-center py-8 hidden"><i class="fas fa-spinner fa-spin text-3xl text-primary"></i><p class="text-gray-600 mt-2">Loading timetable...</p></div>
            </div>
            <div class="p-4 border-t bg-gray-50"><div class="flex justify-between items-center text-sm text-gray-500"><span><i class="fas fa-calendar mr-1"></i> Conference Dates: May 13-14, 2026</span><span><i class="fas fa-map-marker-alt mr-1"></i> Mode: Virtual (Zoom)</span></div></div>
        </div>
    </div>
</div>
@endsection

<style>
    html, body { height: 100%; }
    body { min-height: 100vh; overflow-x: hidden; }
    body.modal-open { overflow: hidden; }
    .zoom-image { transition: transform 10s ease-in-out; }
    .slide.active .zoom-image { transform: scale(1.1); }
    #zoomModal, #timetableModal { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.6); }
    #zoomModal.show, #timetableModal.show { display: flex; }
    #pdf-canvas { max-width: 100%; height: auto; display: block; }
    @keyframes slideInRight { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .animate-slide-in { animation: slideInRight 0.4s ease-out; }
    main { min-height: calc(100vh - 120px); }
    .container { width: 100%; max-width: 1200px; margin: auto; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    let pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null, scale = 1.5;
    let canvas = null, ctx = null;

    function renderPage(num) {
        pageRendering = true;
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
        
        pdfDoc.getPage(num).then(function(page) {
            const viewport = page.getViewport({ scale: scale });
            canvas = canvas || document.getElementById('pdf-canvas');
            ctx = ctx || canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            const renderTask = page.render({ canvasContext: ctx, viewport: viewport });
            renderTask.promise.then(function() {
                pageRendering = false;
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
                if (pageNumPending !== null) { renderPage(pageNumPending); pageNumPending = null; }
            });
        }).catch(function(error) { if (loadingIndicator) loadingIndicator.classList.add('hidden'); });
        document.getElementById('page_num').textContent = num;
    }

    function queueRenderPage(num) { pageRendering ? pageNumPending = num : renderPage(num); }
    function onPrevPage() { if (pageNum <= 1) return; pageNum--; queueRenderPage(pageNum); }
    function onNextPage() { if (pageNum >= pdfDoc.numPages) return; pageNum++; queueRenderPage(pageNum); }

    function loadPDF() {
        const url = "{{ asset('files/DATICAN_Conference_Presentation_Schedule.pdf') }}";
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page_count').textContent = pdfDoc.numPages;
            renderPage(pageNum);
        }).catch(function(error) { if (loadingIndicator) { loadingIndicator.classList.remove('hidden'); loadingIndicator.innerHTML = '<p class="text-red-600">Failed to load timetable PDF</p>'; } });
    }

    function openTimetableModal() { const modal = document.getElementById('timetableModal'); modal.classList.add('show'); document.body.classList.add('modal-open'); if (!pdfDoc) loadPDF(); }
    function closeTimetableModal() { document.getElementById('timetableModal').classList.remove('show'); document.body.classList.remove('modal-open'); }
    function downloadTimetable() { const link = document.createElement('a'); link.href = "{{ asset('files/DATICAN_Conference_Presentation_Schedule.pdf') }}"; link.download = "DATICAN_Conference_Timetable_2026.pdf"; link.click(); }
    function printTimetable() { const win = window.open("{{ asset('files/DATICAN_Conference_Presentation_Schedule.pdf') }}"); if (win) win.onload = setTimeout(() => win.print(), 1000); else alert("Allow popups to print timetable."); }
    function openFullscreen() { const viewer = document.getElementById('pdf-canvas'); if (viewer.requestFullscreen) viewer.requestFullscreen(); else if (viewer.webkitRequestFullscreen) viewer.webkitRequestFullscreen(); }

    function openZoomModal() { document.getElementById('zoomModal').classList.add('show'); document.body.classList.add('modal-open'); }
    function closeZoomModal() { document.getElementById('zoomModal').classList.remove('show'); document.body.classList.remove('modal-open'); }
    
    function downloadGuidelines() {
        const guidelines = `DATI CAN Conference 2026 - Zoom Guidelines\n\nZoom Meeting Details:\n- Zoom Link: https://tinyurl.com/DATICANCONF\n- Meeting ID: 929 3790 2652\n- Passcode: DATICANCNF\n\nA. General Conference Guidelines\n1. All participants should join the Zoom meeting at least 10 minutes before the official start time.\n2. Ensure a stable internet connection, functional microphone, and camera before joining.\n3. Name your device using your full name and Institution.\n4. Keep microphones muted unless speaking.\n5. All questions and comments should be directed to the chatroom.\n\nB. Breakout Session Structure\nThere will be 2 breakout sessions named Group 1 and Group 2.\nEach breakout session will include: Session Chairmen, Technical Assistant, Presenters, Audience participants.\nSuggested presentation format: Presentation 7 minutes, Q&A 3 minutes.\n\nC. Guidelines for Presenters\nBefore the Session:\n- Join the Zoom meeting at least 15 minutes before your presentation time.\n- Test your audio, screen sharing, presentation slides.\n- Keep presentation slides concise and professional.\n- Use the conference Zoom background.\n\nDuring presentation:\n- Turn on video when presenting.\n- Share screen only when instructed.\n- Adhere strictly to allocated time.\n- Speak clearly and professionally.\n\nTechnical Etiquette:\n- Mute microphone when not speaking.\n- Avoid background noise.\n- Use quiet, well-lit environment.\n\nD. Upload Custom Background on Zoom\nDesktop: Click "^" icon > Choose Virtual Background > "+" icon > Select image > Open\nMobile: Tap "..." > Virtual Background > "+" icon > Choose photo > Done\n\nNote: If you miss your slot, presenters may be moved to the end of the session.\n\nThank you for your cooperation and participation.`;
        const blob = new Blob([guidelines], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'DATI_CAN_Zoom_Guidelines.txt'; a.click(); URL.revokeObjectURL(url);
    }

    document.getElementById('timetableModal')?.addEventListener('click', function(e) { if (e.target === this) closeTimetableModal(); });
    document.getElementById('zoomModal')?.addEventListener('click', function(e) { if (e.target === this) closeZoomModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeTimetableModal(); closeZoomModal(); } });
    
    document.addEventListener('DOMContentLoaded', function() { setTimeout(openZoomModal, 800); });
</script>