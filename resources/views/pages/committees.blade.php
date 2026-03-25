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
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
    
    <!-- Committee Chair -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">
        <div class="bg-gradient-to-r from-primary to-secondary px-5 py-3">
            <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-chalkboard-user mr-2 text-sm"></i>
                LOC Chairman
            </h3>
        </div>
        <div class="p-5 flex-1">
            <div class="flex flex-col items-center text-center">
                <div class="flex-shrink-0 mb-4">
                    <div class="w-24 h-24 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-user-tie text-3xl text-primary"></i>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-1">Prof. Adewale Opeoluwa Ogunde</h4>
                    <p class="text-gray-600 text-sm mb-2">Redeemer's University</p>
                    <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary text-xs font-medium rounded-full break-all">
                        ogundea@run.edu.ng
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Committee Co-Chair -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">
        <div class="bg-gradient-to-r from-primary to-secondary px-5 py-3">
            <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-users mr-2 text-sm"></i>
                LOC Co-Chair
            </h3>
        </div>
        <div class="p-5 flex-1">
            <div class="flex flex-col items-center text-center">
                <div class="flex-shrink-0 mb-4">
                    <div class="w-24 h-24 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-user-graduate text-3xl text-primary"></i>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-1">Prof. Bolanle Oladejo</h4>
                    <p class="text-gray-600 text-sm mb-2">University of Ibadan</p>
                    <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary text-xs font-medium rounded-full break-all">
                        oladejobola2002@gmail.com
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Committee Secretary -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">
        <div class="bg-gradient-to-r from-primary to-secondary px-5 py-3">
            <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-file-alt mr-2 text-sm"></i>
                LOC Secretary
            </h3>
        </div>
        <div class="p-5 flex-1">
            <div class="flex flex-col items-center text-center">
                <div class="flex-shrink-0 mb-4">
                    <div class="w-24 h-24 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-user-graduate text-3xl text-primary"></i>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-1">Tijani Rukayat</h4>
                    <p class="text-gray-600 text-sm mb-2">Lagos State University</p>
                    <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary text-xs font-medium rounded-full break-all">
                        tounadekunle.tijani@gmail.com
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add CSS for email wrapping if needed -->
<style>
    .break-all {
        word-break: break-all;
        overflow-wrap: break-word;
        max-width: 100%;
    }
</style>
                
                <!-- Committee Members -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-user-friends mr-2"></i>
                            LOC Members
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $members = [
                                    ['name' => 'John Akintayo', 'email' => 'oluwafemi.j.akintayo@gmail.com', 'institution' => 'Lagos State University'],
                                    ['name' => 'Dr. Kehinde Sotonwa ', 'email' => 'kehindesotonwa8@gmail.com', 'institution' => 'Lagos State University'],
                                    ['name' => 'Dr. Mary Adedoyin', 'email' => 'mary.adedoyin@lasu.edu.ng', 'institution' => 'Lagos State University'],
                                    ['name' => 'Dr. Adenike Adekoge-Elijah', 'email' => 'adegoke-elijaha@run.edu.ng', 'institution' => 'Redeemer\'s University'],
                                    ['name' => 'Dr. Toluwase Olowookere', 'email' => 'olowookereta@run.edu.ng', 'institution' => 'Redeemer\'s University'],
                                    ['name' => 'Mauton Asokere', 'email' => 'mmasokere@gmail.com', 'institution' => 'Lagos State University'],
                                    ['name' => 'Paul Wheto', 'email' => 'whetopaul@gmail.com', 'institution' => 'Lagos State University'],
                                    ['name' => 'Monday Oke', 'email' => 'mondayoke93@gmail.com', 'institution' => 'Lagos State University'],
                                    ['name' => 'Sefiyah Salami-Ohida', 'email' => 'sefiyahsalami@gmail.com', 'institution' => 'Lagos State University'],
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
                                    <span class="inline-block text-xs text-primary font-medium mt-1">{{ $member['email'] }}</span>
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