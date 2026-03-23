@extends('layouts.app')

@section('title', 'Local Organizing Committee - DATICAN Conference')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Local Organizing Committee</h1>
                <p class="text-xl text-gray-200">Meet the dedicated team behind DATICAN Conference 2026</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                
                <!-- Committee Introduction -->
                <div class="text-center mb-12">
                    <div class="inline-block p-3 bg-primary bg-opacity-10 rounded-full mb-6">
                        <i class="fas fa-users text-4xl text-primary"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Local Organizing Committee</h2>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                        The Local Organizing Committee is responsible for the planning, coordination, 
                        and successful execution of the DATICAN Conference 2026. Our dedicated team works 
                        tirelessly to ensure a seamless and enriching experience for all participants.
                    </p>
                </div>
                
                <!-- Committee Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    
                    <!-- Committee Chair -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-chalkboard-user mr-2"></i>
                                Committee Chair
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-20 h-20 bg-primary bg-opacity-10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-tie text-3xl text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">Prof. Adewale Opeoluwa Ogunde</h4>
                                    <p class="text-gray-600 mb-2">Redeemer's University, Ede, Osun State, Nigeria</p>
                                    <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm font-medium rounded-full">Chair</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Committee Co-Chair -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-users mr-2"></i>
                                Committee Co-Chair
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-20 h-20 bg-primary bg-opacity-10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-graduate text-3xl text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">Prof. Bolanle Oladejo</h4>
                                    <p class="text-gray-600 mb-2">University of Ibadan, Ibadan, Oyo State, Nigeria</p>
                                    <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm font-medium rounded-full">Co-Chair</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Committee Members -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-user-friends mr-2"></i>
                            Committee Members
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $members = [
                                    ['name' => 'Mrs. TIJANI, Rukayat', 'role' => 'Secretary', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                    ['name' => 'Mr. John Akintayo', 'role' => 'Member', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                    ['name' => 'Dr. Sotonwa', 'role' => 'Member', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                    ['name' => 'Dr. Adedoyin', 'role' => 'Member', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                    ['name' => 'Dr. Elijah', 'role' => 'Member', 'institution' => 'Redeemer\'s University, Ede, Osun State, Nigeria'],
                                    ['name' => 'Dr. Olowookere', 'role' => 'Member', 'institution' => 'Redeemer\'s University, Ede, Osun State, Nigeria'],
                                    ['name' => 'Mr. Mauton Asokere', 'role' => 'Member', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                    ['name' => 'Mr. Paul Wheto', 'role' => 'Member', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                    ['name' => 'Mr. Monday Oke', 'role' => 'Member', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                    ['name' => 'Mrs. Sefiyah', 'role' => 'Member', 'institution' => 'Lagos State University, Ojo, Lagos Nigeria'],
                                ];
                            @endphp
                            
                            @foreach($members as $member)
                            <div class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="fas fa-user-circle text-primary text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $member['name'] }}</h4>
                                    <p class="text-sm text-gray-500">{{ $member['institution'] }}</p>
                                    <span class="inline-block text-xs text-primary font-medium mt-1">{{ $member['role'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Committee Message -->
                <!-- <div class="mt-12 bg-gradient-to-r from-primary to-secondary text-white rounded-2xl p-8 text-center">
                    <i class="fas fa-quote-left text-3xl mb-4 opacity-50"></i>
                    <p class="text-lg italic mb-4">
                        We are committed to making DATICAN Conference 2026 a memorable and impactful event. 
                        Your participation and contributions are greatly valued.
                    </p>
                    <p class="font-semibold">- DATICAN Local Organizing Committee</p>
                </div> -->
                
            </div>
        </div>
    </div>
@endsection