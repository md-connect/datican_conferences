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
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Organising Committee</h2>
                            
                            <div class="space-y-4">
                                @php
                                    $organisingLeft = [
                                        'Prof. Adewale Opeoluwa Ogunde, Redeemer\'s University, Ede, Osun State, Nigeria (Chair)',
                                        'Prof. Bolanle Oladejo, University of Ibadan, Ibadan, Oyo State, Nigeria (Co-Chair)',
                                        'Mr. John Akintayo, Lagos State University, Ojo, Lagos Nigeria (Member)',
                                        'Mrs. TIJANI, Rukayat,  Lagos State University, Ojo, Lagos Nigeria (Member)',
                                    ];
                                @endphp
                                
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
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">PC Members/Reviewers</h2>
                            
                            <div class="space-y-4">
                                @php
                                    $pcMembersLeft = [
                                        'To be announced later'
                                    ];
                                @endphp
                                
                                @foreach($pcMembersLeft as $index => $member)
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">{{ $index + 0 }}.</span>
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
                        
                        <!-- Organising Committee - Right Column -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">IT & PLATFORM TEAM</h2>

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
                                        {!! str_replace(['(Chair)', '(Co-Chair)'], ['<span class="font-semibold text-primary">(Chair)</span>', '<span class="font-semibold text-primary">(Co-Chair)</span>'], $member) !!}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- National Advisory Committee -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">National Advisory Committee</h2>
                            
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <span class="text-gray-600 font-medium mr-3 mt-1">1.</span>
                                    <p class="text-gray-800 flex-1">Committee details to be announced</p>
                                </div>
                            </div>
                        </div>
                        
                       
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection