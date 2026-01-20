@extends('layouts.app')

@section('title', 'Already Registered - DATICAN Conference 2026')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-primary mb-4">DATICAN CONFERENCE 2026</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-2">Registration Status</h2>
            </div>

            <!-- Already Registered Message -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center">
                    <div class="mb-6">
                        <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">You're Already Registered!</h3>
                        <p class="text-gray-600 mb-6">You have already registered for DATICAN Conference 2026.</p>
                    </div>
                    
                    <!-- Registration Details -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-gray-700 mb-4">Your Registration Details:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="font-medium">{{ $existingRegistration->title }} {{ $existingRegistration->firstname }} {{ $existingRegistration->lastname }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ $existingRegistration->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Institution</p>
                                <p class="font-medium">{{ $existingRegistration->institution }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Registration Date</p>
                                <p class="font-medium">{{ $existingRegistration->created_at->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">DATICAN Member</p>
                                <p class="font-medium">{{ $existingRegistration->is_datican_member ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Presenting Paper</p>
                                <p class="font-medium">{{ $existingRegistration->is_presenting_paper ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-secondary transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                        </a>
                        
                        @if($existingRegistration->is_presenting_paper)
                        <a href="{{ route('papers.create') }}" 
                           class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Submit Paper
                        </a>
                        @endif
                        
                        <a href="{{ route('conference.registration.success') }}" 
                           class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-file-invoice mr-2"></i>View Registration Confirmation
                        </a>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-gray-600 text-sm">
                            Need to update your registration? Contact conference organizers at 
                            <a href="mailto:manager.datican@gmail.com" class="text-primary hover:text-secondary font-medium">
                                manager.datican@gmail.com
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection