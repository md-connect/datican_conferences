@extends('layouts.app')

@section('title', 'Committees')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">COMMITTEES</h1>
                <p class="text-xl text-gray-200">Conference Committees and Members</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- LEFT COLUMN -->
                    <div class="space-y-8">
                        
                        <!-- Organising Committee - Left Column -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                                <i class="fas fa-users-cog text-primary text-2xl"></i>
                                Organising Committee</h2>
                            
                            <div class="space-y-4">
                                @php
                                    $organisingLeft = [
                                        'Prof. Adewale Opeoluwa Ogunde, Redeemer\'s University, Ede, Osun State, Nigeria (Chair)',
                                        'Prof. Bolanle Oladejo, University of Ibadan, Ibadan, Oyo State, Nigeria (Co-Chair)',
                                        'Mrs. TIJANI, Rukayat,  Lagos State University, Ojo, Lagos Nigeria (Secretary)',
                                        'Mr. John Akintayo, Lagos State University, Ojo, Lagos Nigeria (Member)',
                                        'Dr. Sotonwa, Lagos State University, Ojo, Lagos Nigeria (Member)',
                                        'Dr. Adedoyin, Lagos State University, Ojo, Lagos Nigeria (Member)',
                                        'Dr. Elijah, Redeemer\'s University, Ede, Osun State, Nigeria (Member)', 
                                        'Dr. Olowookere, Redeemer\'s University, Ede, Osun State, Nigeria (Member)', 
                                        'Mr. Mauton Asokere, Lagos State University, Ojo, Lagos Nigeria (Member)', 
                                        'Mr. Paul Wheto, Lagos State University, Ojo, Lagos Nigeria (Member)',  
                                        'Mr. Monday Oke, Lagos State University, Ojo, Lagos Nigeria (Member)',  
                                        'Mrs. Sefiyah, Lagos State University, Ojo, Lagos Nigeria (Member)', 
                                    ];
                              2  @endphp
                                
                                @foreach($organisingLeft as $index => $member)
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">{{ $index + 1 }}.</span>
                                    <p class="text-gray-800 flex-1">
                                        {!! str_replace(['(Chair)', '(Co-Chair)'], ['<span class="font-semibold text-primary">(Chair)</span>', '<span class="font-semibold text-primary">(Co-Chair)</span>'], $member) !!}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- PC Members/Reviewers - Left Column -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                                <i class="fas fa-clipboard-check text-purple-600 text-2xl"></i>
                                Review Panel</h2>
                            
                            <div class="space-y-4">
                                @php
                                    $pcMembersLeft = [
                                        'Prof. Bolanle Oladejo, University of Ibadan, Ibadan, Oyo State, Nigeria',
                                        'Dr. Olowookere, Redeemer\'s University, Ede, Osun State, Nigeria', 
                                        'Dr. Adedoyin, Lagos State University, Ojo, Lagos Nigeria',
                                    ];
                                @endphp
                                
                                @foreach($pcMembersLeft as $index => $member)
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">{{ $index + 1 }}.</span>
                                    <p class="text-gray-800 flex-1">
                                        {!! str_replace('(PC Member)', '<span class="font-medium text-primary">(PC Member)</span>', $member) !!}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- RIGHT COLUMN -->
                    <div class="space-y-8">
                        
                        <!-- Subcommittee - IT & Platform -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3"> 
                                <i class="fas fa-laptop-code text-accent text-2xl"></i>
                                IT & PLATFORM TEAM</h2>

                            <div class="space-y-4">
                                @php
                                    $organisingRight = [
                                        'Mr. John Akintayo, Lagos State University, Ojo, Lagos Nigeria (Lead)',
                                        'Mr. Monday Oke, Lagos State University, Ojo, Lagos Nigeria (Technical and Web Developer)',
                                    ];
                                @endphp
                                
                                @foreach($organisingRight as $index => $member)
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">{{ $index + 1 }}.</span>
                                    <p class="text-gray-800 flex-1">
                                        {!! str_replace(['(Lead)', '(Technical and Web Developer)'], ['<span class="font-semibold text-primary">(Lead)</span>', '<span class="font-semibold text-primary">(Technical and Web Developer)</span>'], $member) !!}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Publicity Committee -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                            <i class="fas fa-bullhorn text-orange-600 text-2xl"></i>
                                        Publicity Subcommittee</h2>
                            
                            <div class="space-y-4">
                                @php
                                    $organisingRight = [
                                        'Mrs. Sefiyat, Lagos State University, Ojo, Lagos Nigeria',
                                        'Mr. Paul Wheto, Lagos State University, Ojo, Lagos Nigeria',
                                    ];
                                @endphp
                                
                                @foreach($organisingRight as $index => $member)
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">{{ $index + 1 }}.</span>
                                    <p class="text-gray-800 flex-1">
                                        {!! str_replace(['(Lead)', '(Technical and Web Developer)'], ['<span class="font-semibold text-primary">(Lead)</span>', '<span class="font-semibold text-primary">(Technical and Web Developer)</span>'], $member) !!}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                         <!-- Sponsorship/Finance Subcommittee -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                                <i class="fas fa-hand-holding-usd text-green-600 text-2xl"></i>
                                Sponsorship/Finance Subcommittee</h2>
                            
                            <div class="space-y-4">
                                @php
                                    $organisingRight = [
                                        'Dr. Sotonwa, Lagos State University, Ojo, Lagos Nigeria (Member)',
                                        'Dr. Elijah, Redeemer\'s University, Ede, Osun State, Nigeria (Member)', 
                                    ];
                                @endphp
                                
                                @foreach($organisingRight as $index => $member)
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">{{ $index + 1 }}.</span>
                                    <p class="text-gray-800 flex-1">
                                        {!! str_replace(['(Lead)', '(Technical and Web Developer)'], ['<span class="font-semibold text-primary">(Lead)</span>', '<span class="font-semibold text-primary">(Technical and Web Developer)</span>'], $member) !!}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>

                         <!-- Programme, Documentation and Publication - Left Column -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                                <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                                Programme, Documentation and Publication</h2>
                            
                            <div class="space-y-4">
                                @php
                                    $pcMembersLeft = [
                                        'Dr. Adedoyin, Lagos State University, Ojo, Lagos Nigeria',
                                        'Dr. Olowookere, Redeemer\'s University, Ede, Osun State, Nigeria', 
                                         'Mr. Paul Wheto, Lagos State University, Ojo, Lagos Nigeria',
                                    ];
                                @endphp
                                
                                @foreach($pcMembersLeft as $index => $member)
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">{{ $index + 1 }}.</span>
                                    <p class="text-gray-800 flex-1">
                                        {!! str_replace('(PC Member)', '<span class="font-medium text-primary">(PC Member)</span>', $member) !!}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                       
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection