@extends('layouts.app')

@section('title', 'Call for Papers')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Call for Papers</h1>
                <p class="text-xl text-gray-200">Submit your research to DATICAN Conference 2026</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 hover-lift">
                    <h2 class="text-2xl font-bold text-primary mb-6">Conference Theme</h2>
                    <p class="text-lg font-semibold mb-4">Improving Medical Diagnostics in Nigeria Using AI and Data Science</p>
                    <p class="text-gray-700 mb-8">
                        We invite scholars, researchers, industry practitioners, policymakers, and students to contribute to this vital discourse by submitting original research papers for presentation.
                    </p>
                    
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
                </div>

                <!-- Submission Guidelines -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 hover-lift">
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

                <!-- Important Dates -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 hover-lift">
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
                                    'Abstract Submission Deadline' => '1st March, 2026',
                                    'Full Paper Submission Deadline' => '1st April, 2026',
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
                <div class="bg-white rounded-xl shadow-lg p-8 hover-lift">
                    <h2 class="text-2xl font-bold text-primary mb-6">Submission Process</h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                <span class="font-bold">1</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-800">Complete Google Form</h3>
                                <p class="text-gray-700">Presenters, Authors and Participants are to complete a Google form to register their interest.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                <span class="font-bold">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-800">Submit Abstract via EasyChair</h3>
                                <p class="text-gray-700">From the Google form, authors will receive a link to redirect to EasyChair to submit their abstracts.</p>
                                <div class="mt-3">
                                    <a href="https://easychair.org" target="_blank" class="inline-flex items-center text-primary hover:text-secondary">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        <span>EasyChair Submission Portal (Link will be activated)</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0">
                                <span class="font-bold">3</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-800">Review Process</h3>
                                <p class="text-gray-700">All submissions undergo double-blind peer review. Use of plagiarism detection software (Turnitin) will be implemented. Papers with more than 15% plagiarism will need revision.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 p-6 bg-accent bg-opacity-10 border-l-4 border-accent">
                        <h4 class="text-lg font-semibold mb-2 text-primary">Note:</h4>
                        <p class="text-gray-700">Submissions must be original, unpublished, and not under review elsewhere. Authors will be informed via email to submit camera-ready manuscripts after the event.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection