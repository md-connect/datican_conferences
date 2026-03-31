@extends('layouts.app')

@section('title', 'Call for Abstracts')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Call for Abstracts</h1>
                <p class="text-xl text-gray-200">Submit your research to DATICAN Conference 2026</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-lg p-8 hover-lift">
                            <!-- Conference Theme -->
                            <h2 class="text-2xl font-bold text-primary mb-6">Conference Theme: Improving Medical Diagnostics in Nigeria Using AI and Data Science</h2>
                            <p class="text-gray-700 mb-8">
                                We invite scholars, researchers, industry practitioners, policymakers, and students to contribute to this vital discourse by submitting original research papers for presentation.
                            </p>
                            
                            <!-- Sub-themes -->
                            <div class="bg-primary bg-opacity-5 border-l-4 border-primary p-6 mb-8">
                                <h3 class="text-xl font-bold mb-4 text-primary">Conference Sub-themes:</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach([
                                        'AI-Powered Medical Imaging and Radiology',
                                        'Data Science for Early Disease Prediction, Screening, and Detection',
                                        'Big Data and Precision Medicine for Cancer Care',
                                        'AI-Driven Pathology and Laboratory Diagnostics',
                                        'Wearable Technology and early health warning system',
                                        'Smart Hospitals',
                                        'Capacity Building for AI in medicine, Oncology and Biological Research',
                                        'AI Innovation, Startups, and Entrepreneurship in Health Technology',
                                        'Ethical, Legal, and Quality issues in AI Health care Deployment',
                                        'Genomic Data Analysis using AI and Data Science',
                                        'Synthetic Generation of Medical Data Using AI',
                                        'Electronic Medical Records and Health Data Infrastructure',
                                        'Environmental Data, AI, and Infectious Disease',
                                        'Bias and Fairness in AI Healthcare Solutions',
                                        'Global Health Research Refinement'
                                    ] as $subtheme)
                                    <div class="flex items-start">
                                        <i class="fas fa-circle text-accent text-xs mt-2 mr-3"></i>
                                        <span class="text-gray-700">{{ $subtheme }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- FREE CONFERENCE SECTION -->
                            <div class="mb-8 relative overflow-hidden rounded-xl border-2 border-primary">
                                <div class="bg-primary bg-opacity-5 p-8">
                                    
                                    <div class="relative z-10 text-center">
                                        
                                        <div class="inline-block bg-primary text-white px-6 py-3 rounded-full mb-6">
                                            <span class="font-bold text-xl">CONFERENCE IS FREE!</span>
                                        </div>
                                        
                                        <p class="text-lg text-gray-700 mb-6 max-w-2xl mx-auto">
                                            Zero cost to attend, present, and publish.
                                        </p>
                                        
                                        <div class="flex flex-wrap justify-center gap-4 mt-4">
                                            <div class="flex items-center space-x-2 bg-primary bg-opacity-10 rounded-lg px-4 py-2">
                                                <i class="fas fa-check-circle text-primary"></i>
                                                <span class="text-gray-700">Free Registration</span>
                                            </div>
                                            <div class="flex items-center space-x-2 bg-primary bg-opacity-10 rounded-lg px-4 py-2">
                                                <i class="fas fa-check-circle text-primary"></i>
                                                <span class="text-gray-700">Free Presentation</span>
                                            </div>
                                            <div class="flex items-center space-x-2 bg-primary bg-opacity-10 rounded-lg px-4 py-2">
                                                <i class="fas fa-check-circle text-primary"></i>
                                                <span class="text-gray-700">Free Publication</span>
                                            </div>
                                            <div class="flex items-center space-x-2 bg-primary bg-opacity-10 rounded-lg px-4 py-2">
                                                <i class="fas fa-check-circle text-primary"></i>
                                                <span class="text-gray-700">Free Certificate</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6">
                                            <a href="{{ route('register') }}" 
                                               class="inline-flex items-center px-6 py-3 bg-accent text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                                                Claim Your Free Spot Now!
                                                <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submission Guidelines -->
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-primary mb-6">Submission Guidelines</h2>
                                
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Paper Types:</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <h4 class="font-bold mb-2 text-primary">Research Papers</h4>
                                            <p class="text-gray-600">Original research contributions</p>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <h4 class="font-bold mb-2 text-primary">Review Papers</h4>
                                            <p class="text-gray-600">Comprehensive reviews of existing literature</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-xl font-semibold mb-3 text-gray-800">Abstract Submission Requirements:</h3>
                                        <ul class="space-y-2 text-gray-700">
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                                <span>Title, abstract (250 words maximum)</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                                <span>4–5 keywords</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                                <span>Authors' names, affiliation(s), and email address(es)</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                                <span>Font: Times New Roman, Size 12</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div>
                                        <h3 class="text-xl font-semibold mb-3 text-gray-800">Full Paper Submission:</h3>
                                        <ul class="space-y-2 text-gray-700">
                                            <li class="flex items-start">
                                                <i class="fas fa-file-alt text-primary mt-1 mr-2"></i>
                                                <span>Length: 5-6 pages</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-file-alt text-primary mt-1 mr-2"></i>
                                                <span>Single-spaced, Two-column format</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-file-alt text-primary mt-1 mr-2"></i>
                                                <span>IEEE referencing style</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-file-alt text-primary mt-1 mr-2"></i>
                                                <span>Font: Times New Roman, Size 12</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Virtual Conference Platform -->
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-primary mb-6">Virtual Conference Platform</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="bg-primary bg-opacity-5 p-6 rounded-lg border border-primary border-opacity-20">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-primary p-3 rounded-lg mr-4">
                                                <i class="fas fa-video text-white text-xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">Zoom Conference</h3>
                                        </div>
                                        <p class="text-gray-700">Primary platform for main sessions and breakout rooms. Links will be provided after registration.</p>
                                    </div>
                                    
                                    <div class="bg-red-50 p-6 rounded-lg border border-red-100">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-red-100 p-3 rounded-lg mr-4">
                                                <i class="fab fa-youtube text-red-600 text-xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">YouTube Live</h3>
                                        </div>
                                        <p class="text-gray-700">Live streaming for extended capacity. Public access to main sessions.</p>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-primary bg-opacity-5 rounded-lg border border-primary border-opacity-20">
                                    <h3 class="text-lg font-semibold mb-4 text-primary">What You'll Get:</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-certificate text-primary mr-3"></i>
                                            <span class="text-gray-700">Digital Certificate of Participation</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-video text-primary mr-3"></i>
                                            <span class="text-gray-700">Access to Session Recordings</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-file-pdf text-primary mr-3"></i>
                                            <span class="text-gray-700">Conference Proceedings</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-comments text-primary mr-3"></i>
                                            <span class="text-gray-700">Networking Opportunities</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Important Dates -->
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-primary mb-6">Important Dates</h2>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-primary bg-opacity-5">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Event</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach([
                                                'Conference Announcement' => '7th January, 2026',
                                                'Abstract Submission Opens' => '1st February, 2026',
                                                'Abstract Submission Deadline' => '30th March, 2026',
                                                'Full Paper Submission Deadline' => '15th April, 2026',
                                                'Reviewers\' Feedback Returned' => '1st May, 2026',
                                                'Conference Dates' => '13th - 14th May, 2026',
                                                'Camera-Ready Paper Submission' => '21st May, 2026'
                                            ] as $event => $date)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $event }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $date }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Submission Process -->
                            <div>
                                <h2 class="text-2xl font-bold text-primary mb-6">Submission Process</h2>
                                <div class="space-y-6">
                                    <!-- Step 1: Account Registration -->
                                    <div class="flex items-start">
                                        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                            <span class="font-bold">1</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Create Your Account</h3>
                                            <p class="text-gray-700">Authors and reviewers must create an account to access the submission and review system.</p>
                                            <div class="mt-3">
                                                @auth
                                                <span class="inline-flex items-center text-green-600">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    <span>You are logged in as {{ auth()->user()->first_name }}</span>
                                                </span>
                                                @else
                                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white rounded-lg hover:bg-red-600 mr-3">
                                                    <i class="fas fa-user-plus mr-2"></i>
                                                    <span>Create Account</span>
                                                </a>
                                                <a href="{{ route('login') }}" class="inline-flex items-center text-accent hover:text-secondary">
                                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                                    <span>Login to Existing Account</span>
                                                </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 2: Conference Registration -->
                                    <div class="flex items-start">
                                        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                            <span class="font-bold">2</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Register for the Conference</h3>
                                            <p class="text-gray-700">All participants must register for the conference. Presenters should indicate they will be submitting a paper.</p>
                                            <div class="mt-3">
                                                @auth
                                                    @php
                                                        $hasConferenceReg = \App\Models\ConferenceRegistration::where('user_id', auth()->id())
                                                            ->orWhere('email', auth()->user()->email)
                                                            ->exists();
                                                    @endphp
                                                    @if($hasConferenceReg)
                                                    <span class="inline-flex items-center text-green-600">
                                                        <i class="fas fa-check-circle mr-2"></i>
                                                        <span>Conference Registration Complete</span>
                                                    </span>
                                                    @else
                                                    <a href="{{ route('conference.registration') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                        <i class="fas fa-calendar-plus mr-2"></i>
                                                        <span>Register for Conference</span>
                                                    </a>
                                                    @endif
                                                @else
                                                <p class="text-gray-600 text-sm">Create an account first to register for the conference.</p>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3: Abstract Submission -->
                                    <div class="flex items-start">
                                        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                            <span class="font-bold">3</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Submit Your Abstract</h3>
                                            <p class="text-gray-700">Submit your abstract or full paper through our integrated submission system.</p>
                                            <div class="mt-3">
                                                @auth
                                                <a href="{{ route('papers.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 mr-3">
                                                    <i class="fas fa-file-upload mr-2"></i>
                                                    <span>Submit New Abstract</span>
                                                </a>
                                                <a href="{{ route('papers.index') }}" class="inline-flex items-center text-accent hover:text-secondary">
                                                    <i class="fas fa-list mr-2"></i>
                                                    <span>View My Submissions</span>
                                                </a>
                                                @else
                                                <p class="text-gray-600 text-sm">Login to submit Abstracts.</p>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 4: Review Process -->
                                    <div class="flex items-start">
                                        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                            <span class="font-bold">4</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Review Process</h3>
                                            <p class="text-gray-700">All submissions undergo double-blind peer review. Authors can track submission status in their dashboard.</p>
                                            <div class="mt-3 space-y-2">
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-user-friends mr-2"></i>
                                                    <span>Double-blind peer review</span>
                                                </div>
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-search mr-2"></i>
                                                    <span>Plagiarism detection (Turnitin)</span>
                                                </div>
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-comments mr-2"></i>
                                                    <span>Author rebuttal phase available</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 5: Decision & Camera-Ready -->
                                    <div class="flex items-start">
                                        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                            <span class="font-bold">5</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Final Submission</h3>
                                            <p class="text-gray-700">Accepted papers submit camera-ready versions through the system.</p>
                                            <div class="mt-2 text-gray-600 text-sm">
                                                <p>• Authors notified of acceptance via email and dashboard</p>
                                                <p>• Submit camera-ready manuscript with revisions</p>
                                                <p>• Complete copyright transfer</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 6: Paper Publication -->
                                    <div class="flex items-start">
                                        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                            <span class="font-bold">6</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Paper Publication</h3>
                                            <p class="text-gray-700">Manuscripts of good quality will be published at LASU Journal of PG school or Journal of Research and Review in Science (JRRS).</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quick Access Dashboard (for logged-in users) -->
                                @auth
                                <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-lg font-semibold mb-1 text-primary">Your Dashboard</h4>
                                            <p class="text-gray-700">Access all conference activities from one place</p>
                                        </div>
                                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                                            <i class="fas fa-tachometer-alt mr-2"></i>
                                            Go to Dashboard
                                        </a>
                                    </div>
                                </div>
                                @else
                                <!-- Call to Action for non-logged users -->
                                <div class="mt-8 p-6 bg-gradient-to-r from-accent/10 to-red-100 rounded-lg border border-accent/20">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-lg font-semibold mb-1 text-primary">Ready to Submit?</h4>
                                            <p class="text-gray-700">Create an account to start your submission process</p>
                                        </div>
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-accent text-white rounded-lg hover:bg-red-600 font-semibold">
                                            <i class="fas fa-rocket mr-2"></i>
                                            Get Started
                                        </a>
                                    </div>
                                </div>
                                @endauth
                                
                                <!-- Important Notes -->
                                <div class="mt-8 p-6 bg-accent bg-opacity-10 border-l-4 border-accent">
                                    <h4 class="text-lg font-semibold mb-2 text-primary">Important Notes:</h4>
                                    <div class="space-y-2 text-gray-700">
                                        <p>• Submissions must be original, unpublished, and not under review elsewhere</p>
                                        <p>• Maximum 15% plagiarism allowed (Turnitin check implemented)</p>
                                        <p>• Follow the provided template for paper formatting</p>
                                        <p>• All communication will be through the system dashboard and registered email</p>
                                    </div>
                                </div>
                                
                                <!-- System Features -->
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-shield-alt text-green-600 text-xl mr-3"></i>
                                        <div>
                                            <p class="font-medium text-gray-800">Secure System</p>
                                            <p class="text-sm text-gray-600">Bank-level security for all submissions</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-sync-alt text-blue-600 text-xl mr-3"></i>
                                        <div>
                                            <p class="font-medium text-gray-800">Real-time Tracking</p>
                                            <p class="text-sm text-gray-600">Track submission status in real-time</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-comments text-purple-600 text-xl mr-3"></i>
                                        <div>
                                            <p class="font-medium text-gray-800">Discussion Platform</p>
                                            <p class="text-sm text-gray-600">Communicate with reviewers and chairs</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-mobile-alt text-red-600 text-xl mr-3"></i>
                                        <div>
                                            <p class="font-medium text-gray-800">Mobile Friendly</p>
                                            <p class="text-sm text-gray-600">Access from any device</p>
                                        </div>
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
                                <a href="{{ route('register') }}" class="flex items-center p-4 bg-primary bg-opacity-5 rounded-lg hover:bg-primary hover:bg-opacity-10 transition duration-300 border border-primary border-opacity-20">
                                    <i class="fas fa-user-plus text-primary text-xl mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Registration Form</h4>
                                        <p class="text-sm text-gray-600">Complete your registration</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('call-for-papers') }}" class="flex items-center p-4 bg-accent bg-opacity-10 rounded-lg hover:bg-accent hover:bg-opacity-20 transition duration-300 border border-accent border-opacity-20">
                                    <i class="fas fa-paper-plane text-accent text-xl mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Submit Abstract</h4>
                                        <p class="text-sm text-gray-600">Call for Abstracts</p>
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
                                        <span class="text-sm text-gray-700">manager.datican@gmail.com</span>
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