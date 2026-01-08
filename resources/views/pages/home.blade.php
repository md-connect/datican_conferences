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
                    <span class="bg-accent text-white px-4 py-1 rounded-full text-sm font-semibold">International Conference</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Improving Medical Diagnostics in Nigeria Using AI and Data Science</h1>
                <p class="text-xl mb-8 text-gray-200">Join leading experts, researchers, and practitioners in advancing healthcare through artificial intelligence and data science.</p>
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 hover-lift">Register Now</a>
                    <a href="{{ route('call-for-papers') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition duration-300 hover-lift">Submit Paper</a>
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
        <!-- Section label -->
        <div class="flex items-center gap-4 mb-6">
          <span class="h-px w-12 bg-accent"></span>
          <span class="text-sm font-semibold uppercase tracking-wide text-accent">
            About DATICAN Conference
          </span>
        </div>

        <!-- Main heading -->
        <h4 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">
          Improving Medical Diagnostics in Nigeria Using AI and Data Science
        </h4>

        <!-- Description -->
        <p class="text-gray-700 mb-6 leading-relaxed">
          Aligned with its mission to advance healthcare delivery in Nigeria through data science
          and medical image analysis training, the Data Science and Medical Image Analysis for
          Improved Healthcare Delivery in Nigeria (DATICAN) project is proud to announce its
          first international conference.
        </p>

        <p class="text-gray-700 mb-8 leading-relaxed">
          This conference will establish a platform for dialogue and knowledge exchange among
          key stakeholders, exploring transformative AI-driven solutions and fostering
          collaborations that directly address Nigeria’s healthcare challenges.
        </p>

        <!-- Sub-themes -->
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

        <!-- CTA -->
        <a href="#call-for-papers"
           class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary/90 transition">
          Call for Papers
        </a>
      </div>

      <!-- RIGHT: IMAGE -->
      <div class="relative">
        <img
          src="{{ asset('images/general/prof-aribisala.JPG') }}"
          alt="DATICAN Conference"
          class="rounded-xl shadow-xl w-full object-cover"
        />
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
                                <span class="font-bold">Mar 1</span>
                            </div>
                            <span class="font-medium text-gray-800">Abstract Submission Deadline</span>
                        </div>
                        <span class="text-gray-600">2026</span>
                    </div>
                    
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center">
                            <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-lg mr-4">
                                <span class="font-bold">Apr 1</span>
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
        
        <!-- Single card container -->
        <div class="bg-white rounded-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Guest 1 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden">
                        <img src="{{ asset('images/speakers/fred.jpg') }}" alt="Prof. Fred Howard" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">Keynote Speaker</p>
                    <h3 class="text-lg font-bold mb-1 text-primary relative">
                        Prof. Frederick Howard, MD
                        <div class="w-12 h-0.5 bg-primary mx-auto mt-1"></div>
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Assistant Professor of Medicine<br>
                       University of Chicago, USA</p>
                </div>
                
                <!-- Guest 2 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden">
                        <img src="{{ asset('images/speakers/olopade.jpg') }}" alt="Prof. Funmi Olopade" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">Chairman, Steering Committee</p>
                    <h3 class="text-lg font-bold mb-1 text-primary relative">
                        Prof. Olufunmilayo I. Olopade MD, FACP
                        <div class="w-12 h-0.5 bg-primary mx-auto mt-1"></div>
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Professor of Medicine, Professor of Human Genetics
                        <br>
                        Director, Center for Clinical Cancer Genetics and Global Health
                        <br>University of Chicago, USA</p>
                </div>
                
                <!-- Guest 3 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden">
                        <img src="{{ asset('images/speakers/aribisala.jpg') }}" alt="Prof. Benjamin Aribisala" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">Chief Host/Program Director</p>
                    <h3 class="text-lg font-bold mb-1 text-primary relative">
                        Prof. Benjamin Aribisala PhD, MBCS, MCPN, FIDPM
                        <div class="w-12 h-0.5 bg-primary mx-auto mt-1"></div>
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Professor of Computer Science<br>
                      Department of Computer Science<br>  
                      Lagos State University, Ojo, Lagos, Nigeria</p>
                </div>

                <!-- Guest 4 -->
                <div class="text-center">
                    <div class="w-48 h-48 mx-auto mb-4 overflow-hidden">
                        <img src="{{ asset('images/speakers/prof-ogunde.jpg') }}" alt="Bayo Mohammed Onimode" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-bold text-gray-800 mb-1">LOC Chairman</p>
                    <h3 class="text-lg font-bold mb-1 text-primary relative">
                        Prof. Adewale Opeoluwa Ogunde
                        <div class="w-12 h-0.5 bg-primary mx-auto mt-1"></div>
                    </h3>
                    <p class="text-gray-700 text-sm mt-2">Professor of Computer Science <br> 
                      Department of Computer Science<br>  
                      Redeemer's University, Ede, Osun State, Nigeria</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection