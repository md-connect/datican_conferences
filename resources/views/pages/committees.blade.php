@extends('layouts.app')

@section('title', 'Committees')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Conference Committees</h1>
                <p class="text-xl text-gray-200">Meet the organizing committees of DATICAN Conference 2026</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                
                <!-- Scientific/Technical Committee -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 hover-lift">
                    <div class="flex items-center mb-6">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-lg mr-4">
                            <i class="fas fa-users text-primary text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-primary">SCIENTIFIC/TECHNICAL SUBCOMMITTEE</h2>
                            <p class="text-gray-600">Terms of Reference</p>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Co-Chairs:</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="bg-gray-50 px-4 py-3 rounded-lg border border-gray-200">
                                <span class="font-medium">John Akintayo</span>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 rounded-lg border border-gray-200">
                                <span class="font-medium">Mauton Asokere</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-8">
                        @foreach([
                            '1. Define Conference Thematic Areas, Tracks, and Sub-themes' => 'Define specific sub-themes under each track to guide submissions and session organization.',
                            '2. Coordinate the Call for Contributions' => 'Presenters, Authors and Participants are to complete a Google form. From this a link will be available to redirect authors to Easychair to submit their abstracts. Specify submission categories, target audiences, and intended formats for presentation.',
                            '3. Set Submission Guidelines and Formats' => 'Create and provide templates for different submission types to ensure consistency.',
                            '4. Provide Reviewers with Criteria and Evaluation Rubrics' => 'To be provided by the Review Committee',
                            '5. Oversee the Assignment of Submissions to Reviewers' => 'Easychair application to be used, Manage the reviewer database, ensuring expertise matches the submission topics. Implement a fair and conflict-free assignment process using the conference management system.',
                            '6. Recommend High-Quality Submissions for Presentation' => 'To be provided by the Review Committee',
                            '7. Identify and Recommend Keynote and Invited Speakers' => 'To be provided by the DATICAN Director (Fred Howard and Birali Runesha)',
                            '8. Assist with Technical Session Structure and Timetable' => 'Temporary timetable for the event available: <a href="https://docs.google.com/spreadsheets/d/16qkOtlCfQ9k2507mFMWAAHNQNLPODMPk6EUnspALJtg/edit?usp=sharing" target="_blank" class="text-primary hover:text-secondary underline">View Timetable</a>',
                            '9. Collaborate on Publication of Proceedings' => 'Authors would be informed by emails to submit a camera-ready manuscript after the event.',
                            '10. Ensure Academic Integrity and Prevent Plagiarism' => 'Use of plagiarism detection software Turnitin. Every article with more than 15% plagiarism report should be rewritten and given 1 week to revise.'
                        ] as $term => $description)
                        <div class="border-l-4 border-primary pl-4 py-2">
                            <h4 class="font-bold text-lg mb-2 text-gray-800">{{ $term }}</h4>
                            <div class="text-gray-700 prose max-w-none">
                                {!! $description !!}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- IT & Platform Team -->
                <div class="bg-white rounded-xl shadow-lg p-8 hover-lift">
                    <div class="flex items-center mb-6">
                        <div class="bg-accent bg-opacity-10 p-3 rounded-lg mr-4">
                            <i class="fas fa-laptop-code text-accent text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-primary">IT & PLATFORM TEAM</h2>
                            <p class="text-gray-600">Terms of Reference</p>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Team Leads:</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="bg-gray-50 px-4 py-3 rounded-lg border border-gray-200">
                                <span class="font-medium">John Akintayo</span>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 rounded-lg border border-gray-200">
                                <span class="font-medium">Monday Oke</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-8">
                        @foreach([
                            '1. Recommend and configure the virtual conference platform (Zoom).' => '<strong class="text-primary">Plan A:</strong> Use Zoom for the Main Session and also Breakout rooms<br>
                            <strong class="text-primary">Plan B:</strong> Use YouTube live for extending Zoom\'s Capacity<br>
                            • Zoom main session has been Setup<br>
                            • YouTube Live account has been set up<br>
                            • Breakout sessions will be setup when we know the number of Breakout sessions',
                            
                            '2. Manage registration portals, submission platforms, and conference website.' => 'Google Form was created: <a href="https://forms.gle/zuM3qs1qtAAB4SdD8" target="_blank" class="text-primary hover:text-secondary underline">Registration Form</a><br>
                            • Submission Platforms Identified: Easychair and Conference Management Toolkit (CMT)<br>
                            • Easychair was preferred because of ease of use<br>
                            • To open Easychair account requires that we have the conference website ready<br>
                            • This website is currently handled by Mr. Monday Oke',
                            
                            '3. Provide real-time technical support during all sessions.' => 'Technical support team will be available throughout the conference.',
                            
                            '4. Coordinate virtual rooms, breakout sessions, and livestreaming setups.' => 'Coordinated management of all virtual spaces and streaming.',
                            
                            '5. Ensure stable internet connectivity, backups, and redundancy plans.' => 'Multiple backup systems and redundancy plans in place.',
                            
                            '6. Set up digital tools for Q&A, polling, and virtual engagement.' => 'Mentimeter online App will be used for questions and answers',
                            
                            '7. Manage cybersecurity, access controls, and data privacy.' => 'Access will be monitored using Zoom Admin tools',
                            
                            '8. Record and archive sessions for post-conference use.' => 'Recordings will be downloaded and saved in the cloud',
                            
                            '9. Support accessibility for international participants (time zones, support desk).' => 'Time Zone will be given in WAT and CST. 24/7 support desk available.',
                            
                            '10. Assist in design and integration of e-certificates and attendance logs.' => 'Digital certificates will be created by Mr. Paul Wheto'
                        ] as $term => $description)
                        <div class="border-l-4 border-primary pl-4 py-2">
                            <h4 class="font-bold text-lg mb-2 text-gray-800">{{ $term }}</h4>
                            <div class="text-gray-700 prose max-w-none">
                                {!! $description !!}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8 p-6 bg-primary bg-opacity-5 rounded-lg border border-primary border-opacity-20">
                        <h4 class="text-lg font-semibold mb-3 text-primary">Virtual Platform Details:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="font-medium mb-2 text-gray-700">Primary Platform:</h5>
                                <div class="flex items-center">
                                    <i class="fas fa-video text-primary mr-2"></i>
                                    <span>Zoom Conference</span>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium mb-2 text-gray-700">Live Streaming:</h5>
                                <div class="flex items-center">
                                    <i class="fab fa-youtube text-red-600 mr-2"></i>
                                    <span>YouTube Live</span>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium mb-2 text-gray-700">Interaction Tools:</h5>
                                <div class="flex items-center">
                                    <i class="fas fa-poll text-primary mr-2"></i>
                                    <span>Mentimeter for Q&A & Polling</span>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium mb-2 text-gray-700">Certificate Management:</h5>
                                <div class="flex items-center">
                                    <i class="fas fa-certificate text-accent mr-2"></i>
                                    <span>Digital e-Certificates</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection