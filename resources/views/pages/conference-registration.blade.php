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

            <!-- Registration Form -->
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
                                placeholder="Enter your first name">
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
                                placeholder="Enter your middle name">
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
                                placeholder="Enter your last name">
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
                            placeholder="example@domain.com">
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
                            placeholder="+1234567890">
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
                            placeholder="Enter your institution or organization">
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
                                    class="text-primary focus:ring-primary">
                                <span class="ml-2">Male</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="Female" required
                                    class="text-primary focus:ring-primary">
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
                                <input type="radio" name="is_datican_member" value="Yes" required
                                    class="text-primary focus:ring-primary" id="datican_member_yes">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_datican_member" value="No" required
                                    class="text-primary focus:ring-primary" id="datican_member_no">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        @error('is_datican_member')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DATICAN Status (Conditional) -->
                    <div class="mb-6 hidden" id="datican_status_section">
                        <label for="datican_status" class="block text-gray-700 font-medium mb-2">
                            DATICAN Status *
                            <span class="text-sm text-gray-500">PI, Faculty, Trainer, PhD Student, MSc. Student</span>
                        </label>
                        <select name="datican_status" id="datican_status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Status</option>
                            <option value="PI">PI</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Trainer">Trainer</option>
                            <option value="PhD Student">PhD Student</option>
                            <option value="MSc. Student">MSc. Student</option>
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
                                <input type="radio" name="is_presenting_paper" value="Yes" required
                                    class="text-primary focus:ring-primary">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_presenting_paper" value="No" required
                                    class="text-primary focus:ring-primary">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        
                        <!-- Abstract Submission Link -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800 font-medium mb-2">For paper presenters:</p>
                            <p class="text-blue-700 mb-2">Please, submit your abstract to the link below:</p>
                            <a href="https://easychair.com/first_datican_conference" target="_blank"
                                class="inline-flex items-center text-primary hover:text-secondary font-semibold">
                                <span>easychair.com/first_datican_conference</span>
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
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection