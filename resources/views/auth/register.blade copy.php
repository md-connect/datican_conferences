@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Registration</h1>
                <p class="text-xl text-gray-200">Join DATICAN Conference 2026 - Improving Medical Diagnostics in Nigeria Using AI and Data Science</p>
            </div>
        </div>
    </div>

    <!-- Registration Content -->
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Registration Form Info -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-lg p-8 mb-8 hover-lift">
                            <h2 class="text-2xl font-bold text-primary mb-6">Registration Process</h2>
                            <div class="space-y-6">
                                <div class="flex items-start">
                                    <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Complete Registration Form</h3>
                                        <p class="text-gray-700 mb-3">All participants must complete the official registration form:</p>
                                        <a href="{{ route('conference.registration') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-accent text-white rounded-lg hover:bg-red-600 transition duration-300 hover-lift">
                                            <i class="fas fa-user-plus text-white text-xl mr-3"></i>
                                            <span>Register Here</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Registration Categories</h3>
                                        <ul class="space-y-2 text-gray-700">
                                            <li class="flex items-start">
                                                <i class="fas fa-user-graduate text-primary mt-1 mr-2"></i>
                                                <span><strong>Students:</strong> Undergraduate and postgraduate students</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-user-md text-primary mt-1 mr-2"></i>
                                                <span><strong>Academics/Researchers:</strong> Faculty members and research scientists</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-briefcase text-primary mt-1 mr-2"></i>
                                                <span><strong>Industry Professionals:</strong> Healthcare and technology industry practitioners</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-landmark text-primary mt-1 mr-2"></i>
                                                <span><strong>Policy Makers:</strong> Government and regulatory officials</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Important Registration Dates</h3>
                                        <ul class="space-y-3 text-gray-700">
                                            <li class="flex justify-between items-center">
                                                <span>Early Bird Registration Ends:</span>
                                                <span class="font-semibold text-primary">February 28, 2026</span>
                                            </li>
                                            <li class="flex justify-between items-center">
                                                <span>Regular Registration Ends:</span>
                                                <span class="font-semibold text-primary">April 30, 2026</span>
                                            </li>
                                            <li class="flex justify-between items-center">
                                                <span>Late Registration:</span>
                                                <span class="font-semibold text-primary">After April 30, 2026</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24 border border-gray-200">
                            <h3 class="text-xl font-bold text-primary mb-6">Quick Links</h3>
                            <div class="space-y-4">
                                <a href="" target="_blank" class="flex items-center p-4 bg-primary bg-opacity-5 rounded-lg hover:bg-primary hover:bg-opacity-10 transition duration-300 border border-primary border-opacity-20">
                                    <i class="fas fa-user-plus text-primary text-xl mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Registration Form</h4>
                                        <p class="text-sm text-gray-600">Complete your registration</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('call-for-papers') }}" class="flex items-center p-4 bg-accent bg-opacity-10 rounded-lg hover:bg-accent hover:bg-opacity-20 transition duration-300 border border-accent border-opacity-20">
                                    <i class="fas fa-paper-plane text-accent text-xl mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Submit Paper</h4>
                                        <p class="text-sm text-gray-600">Call for Papers</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('committees') }}" class="flex items-center p-4 bg-primary bg-opacity-5 rounded-lg hover:bg-primary hover:bg-opacity-10 transition duration-300 border border-primary border-opacity-20">
                                    <i class="fas fa-users text-primary text-xl mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Committees</h4>
                                        <p class="text-sm text-gray-600">Meet the organizers</p>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <h4 class="font-semibold text-gray-800 mb-4">Need Help?</h4>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <i class="fas fa-envelope text-primary mt-1 mr-2"></i>
                                        <span class="text-sm text-gray-700">info@datican.org</span>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-clock text-primary mt-1 mr-2"></i>
                                        <span class="text-sm text-gray-700">Support available 24/7</span>
                                    </div>
                                </div>
                            </div>
                             <!-- Important Notice -->
                        <div class="mt-6 bg-accent bg-opacity-10 border-l-4 border-accent p-6 rounded-lg">
                            <h4 class="font-bold text-primary mb-2">Important Notice</h4>
                            <p class="text-sm text-gray-700">
                                Registration is required for all participants including presenters, authors, and attendees. Only registered participants will receive access links and certificates.
                            </p>
                        </div>
                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection