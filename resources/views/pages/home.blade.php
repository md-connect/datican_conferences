@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Image Slider with Continuous Zoom -->
<div class="relative h-[500px] md:h-[700px] overflow-hidden">
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
                                <span class="font-bold">Jan 1</span>
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
                                <span class="font-bold">May 13-14</span>
                            </div>
                            <span class="font-medium text-gray-800">Conference Dates</span>
                        </div>
                        <span class="text-gray-600">2026</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Keynote Speakers -->
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-primary">Special Guests</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Speaker 1 -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover-lift">
                <div class="w-32 h-32 mx-auto mb-4  overflow-hidden  border-primary">
                    <img src="{{ asset('images/speakers/aribisala.jpg') }}" alt="Speaker 1" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold mb-2 text-primary">Dr. John Akintayo</h3>
                <p class="text-gray-600 mb-3">Scientific Committee Chair</p>
                <p class="text-gray-700 text-sm">Expert in AI and Medical Imaging</p>
            </div>
            
            <!-- Speaker 2 -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover-lift">
                <div class="w-32 h-32 mx-auto mb-4 overflow-hidden border-primary">
                    <img src="{{ asset('images/speakers/olopade.jpg') }}" alt="Speaker 2" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold mb-2 text-primary">Prof. Fred Howard</h3>
                <p class="text-gray-600 mb-3">DATICA Director</p>
                <p class="text-gray-700 text-sm">Leading Data Science Researcher</p>
            </div>
            
            <!-- Speaker 3 -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover-lift">
                <div class="w-32 h-32 mx-auto mb-4 overflow-hidden border-primary">
                    <img src="{{ asset('images/speakers/sammet.jpg') }}" alt="Speaker 3" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold mb-2 text-primary">Dr. Birali Runesha</h3>
                <p class="text-gray-600 mb-3">DATICA Director</p>
                <p class="text-gray-700 text-sm">Healthcare Technology Innovator</p>
            </div>

            <!-- Speaker 4 -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover-lift">
                <div class="w-32 h-32 mx-auto mb-4 overflow-hidden border-primary">
                    <img src="{{ asset('images/speakers/pearson.jpg') }}" alt="Speaker 4" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold mb-2 text-primary">Dr. Birali Runesha</h3>
                <p class="text-gray-600 mb-3">DATICA Director</p>
                <p class="text-gray-700 text-sm">Healthcare Technology Innovator</p>
            </div>
        </div>
    </div>
</div>
@endsection