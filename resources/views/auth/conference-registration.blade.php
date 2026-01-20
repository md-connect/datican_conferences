@extends('layouts.app')

@section('title', 'DATICAN Conference 2026 Registration')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-primary mb-4">DATICAN CONFERENCE 2026</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-2">Registration Form</h2>
                <p class="text-gray-600">Please fill out all required fields to complete your registration</p>
            </div>

            <!-- Check for existing registration -->
            @php
                $existingRegistration = null;
                if (auth()->check()) {
                    $existingRegistration = \App\Models\ConferenceRegistration::where('user_id', auth()->id())
                        ->orWhere('email', auth()->user()->email)
                        ->first();
                }
            @endphp

            @if($existingRegistration)
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
            
            @else
            <!-- Registration Form (only shown if not already registered) -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <form id="registrationForm" action="{{ route('conference.register') }}" method="POST">
                    @csrf

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-gray-700 font-medium mb-2">
                            Title *
                            <span class="text-red-500 ml-1 text-sm">This is a required question</span>
                        </label>
                        <select name="title" id="title" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Title</option>
                            <option value="Prof.">Prof.</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Miss">Miss</option>
                        </select>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="firstname" class="block text-gray-700 font-medium mb-2">
                                Firstname *
                            </label>
                            <input type="text" name="firstname" id="firstname" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter your first name"
                                value="{{ old('firstname', auth()->user()->first_name ?? '') }}">
                            @error('firstname')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="middlename" class="block text-gray-700 font-medium mb-2">
                                Middle name
                            </label>
                            <input type="text" name="middlename" id="middlename"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter your middle name"
                                value="{{ old('middlename') }}">
                            @error('middlename')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="lastname" class="block text-gray-700 font-medium mb-2">
                                Lastname *
                            </label>
                            <input type="text" name="lastname" id="lastname" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter your last name"
                                value="{{ old('lastname', auth()->user()->last_name ?? '') }}">
                            @error('lastname')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 font-medium mb-2">
                            Email Address *
                        </label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="example@domain.com"
                            value="{{ old('email', auth()->user()->email ?? '') }}">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-6">
                        <label for="phone_number" class="block text-gray-700 font-medium mb-2">
                            Phone Number *
                        </label>
                        <input type="tel" name="phone_number" id="phone_number" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="+1234567890"
                            value="{{ old('phone_number') }}">
                        @error('phone_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Institution -->
                    <div class="mb-6">
                        <label for="institution" class="block text-gray-700 font-medium mb-2">
                            Institution/Organization *
                        </label>
                        <input type="text" name="institution" id="institution" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Enter your institution or organization"
                            value="{{ old('institution') }}">
                        @error('institution')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">
                            Gender *
                        </label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="Male" required
                                    class="text-primary focus:ring-primary"
                                    {{ old('gender') == 'Male' ? 'checked' : '' }}>
                                <span class="ml-2">Male</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="Female" required
                                    class="text-primary focus:ring-primary"
                                    {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                <span class="ml-2">Female</span>
                            </label>
                        </div>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DATICAN Member -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">
                            Are you a DATICAN Member? *
                        </label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_datican_member" value="1" required
                                    class="text-primary focus:ring-primary" id="datican_member_yes"
                                    {{ old('is_datican_member') == '1' ? 'checked' : '' }}>
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_datican_member" value="0" required
                                    class="text-primary focus:ring-primary" id="datican_member_no"
                                    {{ old('is_datican_member') == '0' ? 'checked' : '' }}>
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        @error('is_datican_member')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DATICAN Status (Conditional) -->
                    <div class="mb-6 {{ old('is_datican_member') != '1' ? 'hidden' : '' }}" id="datican_status_section">
                        <label for="datican_status" class="block text-gray-700 font-medium mb-2">
                            DATICAN Status *
                            <span class="text-sm text-gray-500">PI, Faculty, Trainer, PhD Student, MSc. Student</span>
                        </label>
                        <select name="datican_status" id="datican_status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Status</option>
                            <option value="PI" {{ old('datican_status') == 'PI' ? 'selected' : '' }}>PI</option>
                            <option value="Faculty" {{ old('datican_status') == 'Faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="Trainer" {{ old('datican_status') == 'Trainer' ? 'selected' : '' }}>Trainer</option>
                            <option value="PhD Student" {{ old('datican_status') == 'PhD Student' ? 'selected' : '' }}>PhD Student</option>
                            <option value="MSc. Student" {{ old('datican_status') == 'MSc. Student' ? 'selected' : '' }}>MSc. Student</option>
                        </select>
                        @error('datican_status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Presenting Paper -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">
                            Are you presenting a Paper? *
                        </label>
                        <div class="flex space-x-6 mb-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_presenting_paper" value="1" required
                                    class="text-primary focus:ring-primary"
                                    {{ old('is_presenting_paper') == '1' ? 'checked' : '' }}>
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_presenting_paper" value="0" required
                                    class="text-primary focus:ring-primary"
                                    {{ old('is_presenting_paper') == '0' ? 'checked' : '' }}>
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        
                        <!-- Abstract Submission Link -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800 font-medium mb-2">For paper presenters:</p>
                            <p class="text-blue-700 mb-2">Please, submit your abstract to the link below:</p>
                            <a href="https://cmt3.research.microsoft.com/DATICANCONF2026" target="_blank"
                                class="inline-flex items-center text-primary hover:text-secondary font-semibold">
                                <span>cmt3.research.microsoft.com</span>
                                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                        @error('is_presenting_paper')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button type="submit"
                            class="w-full bg-primary hover:bg-secondary text-white font-bold py-4 px-6 rounded-lg transition duration-300 transform hover:-translate-y-1">
                            Submit Registration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Note -->
            <div class="mt-8 text-center text-gray-600 text-sm">
                <p>All fields marked with * are required.</p>
                <p class="mt-2">Need help? Contact conference organizers at info@datican2026.org</p>
            </div>
            
            @endif {{-- End of registration check --}}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only run this script if registration form exists (user is not already registered)
        if (document.getElementById('datican_member_yes')) {
            // Get DOM elements
            const daticanMemberYes = document.getElementById('datican_member_yes');
            const daticanMemberNo = document.getElementById('datican_member_no');
            const daticanStatusSection = document.getElementById('datican_status_section');
            const daticanStatusSelect = document.getElementById('datican_status');
            
            // Function to toggle DATICAN Status section
            function toggleDaticanStatus() {
                if (daticanMemberYes.checked) {
                    // Show the DATICAN Status section
                    daticanStatusSection.classList.remove('hidden');
                    daticanStatusSelect.required = true;
                } else {
                    // Hide the DATICAN Status section
                    daticanStatusSection.classList.add('hidden');
                    daticanStatusSelect.required = false;
                    daticanStatusSelect.value = '';
                }
            }
            
            // Add event listeners to both radio buttons
            daticanMemberYes.addEventListener('change', toggleDaticanStatus);
            daticanMemberNo.addEventListener('change', toggleDaticanStatus);
            
            // Initialize on page load
            toggleDaticanStatus();
            
            // Optional: Add form validation before submission
            document.getElementById('registrationForm').addEventListener('submit', function(e) {
                // If DATICAN Member is Yes but status is not selected
                if (daticanMemberYes.checked && !daticanStatusSelect.value) {
                    e.preventDefault();
                    alert('Please select your DATICAN Status');
                    daticanStatusSelect.focus();
                }
            });
        }
    });
</script>
@endsection