@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">DATICAN CONFERENCE 2026 REGISTRATION</h1>
                <p class="text-xl text-gray-200">Join the DATICAN Conference community to submit papers, review submissions, and manage your conference activities</p>
            </div>
        </div>
    </div>

    <!-- Registration Content -->
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Registration Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-lg p-8 hover-lift">
                            <div class="text-center mb-8">
                                <div class="w-20 h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-plus text-white text-3xl"></i>
                                </div>
                                <h2 class="text-2xl font-bold text-primary mb-2">Register Now</h2>
                                <p class="text-gray-600">Already Registered? <a href="{{ route('login') }}" class="text-accent hover:text-red-700 font-medium">Login here</a></p>
                            </div>

                            @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-red-800">Please fix the following errors:</p>
                                        <ul class="mt-1 list-disc list-inside text-red-700 text-sm">
                                            @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Personal Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    <!-- Title Field -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Title <span class="text-red-500">*</span>   
                                            </label>
                                            <select name="title" 
                                                    required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select Title</option>
                                                <option value="Prof." {{ old('title') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                                <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                                <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                                <option value="Mrs." {{ old('title') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                                <option value="Miss." {{ old('title') == 'Miss.' ? 'selected' : '' }}>Miss.</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                First Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                   placeholder="Enter your first name">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Middle Name
                                            </label>
                                            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                   placeholder="Enter your middle name">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Last Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                   placeholder="Enter your last name">
                                        </div>
                                    </div>
                                    
                                    <!-- Phone Field -->
                                    <div class="mt-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone Number
                                        </label>
                                        <input type="tel" name="phone_number" value="{{ old('phone_number') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                               placeholder="+234 800 000 0000">
                                    </div>
                                    
                                    <!-- Gender Field -->
                                    <div class="mt-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Gender
                                        </label>
                                        <div class="flex space-x-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="gender" value="Male" required
                                                       class="text-blue-600 focus:ring-blue-500"
                                                       {{ old('gender') == 'Male' ? 'checked' : '' }}>
                                                <span class="ml-2">Male</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="gender" value="Female" required
                                                       class="text-blue-600 focus:ring-blue-500"
                                                       {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                                <span class="ml-2">Female</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- DATICAN Member -->
                                    <!-- <div class="mt-6">
                                        <label class="block text-gray-700 font-medium mb-2">
                                            Are you a DATICAN Member? <span class="text-red-500">*</span>
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
                                    </div> -->
                                    
                                    <!-- DATICAN Status (Conditional) -->
                                    <!-- <div class="mb-6 {{ old('is_datican_member') != '1' ? 'hidden' : '' }}" id="datican_status_section">
                                        <label for="datican_status" class="block text-gray-700 font-medium mb-2">
                                            DATICAN Status <span class="text-red-500">*</span>
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
                                    </div> -->
                                </div>

                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Account Information</h3>
                                    <div class="space-y-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Institution/Affiliation <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" name="affiliation" value="{{ old('affiliation') }}"
                                                    required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                    placeholder="University, Organization, or Company">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Department <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" name="department" value="{{ old('department') }}"
                                                    required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                    placeholder="Your Department">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Email Address <span class="text-red-500">*</span>
                                            </label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                   placeholder="your.email@example.com">
                                            <p class="mt-1 text-sm text-gray-500">This will be used for all conference communications</p>
                                        </div>
                                        
                                        
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Password <span class="text-red-500">*</span>
                                                </label>
                                                <input type="password" name="password" 
                                                       required
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                       placeholder="Create a password">
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Confirm Password <span class="text-red-500">*</span>
                                                </label>
                                                <input type="password" name="password_confirmation" 
                                                       required
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                       placeholder="Confirm your password">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Paper Presentation</h3>
                                    <div class="space-y-4">
                                        <div class="pl-7 mt-3 space-y-3" id="conference-fields">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Will you be presenting a paper? <span class="text-red-500">*</span>
                                                </label>

                                                <div class="flex space-x-4">
                                                    <label class="inline-flex items-center">
                                                        <input
                                                            type="radio"
                                                            name="presenting_paper"
                                                            value="yes"
                                                            required
                                                            class="text-blue-600 focus:ring-blue-500"
                                                            {{ old('presenting_paper') == 'yes' ? 'checked' : '' }}
                                                        >
                                                        <span class="ml-2">Yes</span>
                                                    </label>

                                                    <label class="inline-flex items-center">
                                                        <input
                                                            type="radio"
                                                            name="presenting_paper"
                                                            value="no"
                                                            class="text-blue-600 focus:ring-blue-500"
                                                            {{ old('presenting_paper') == 'no' ? 'checked' : '' }}
                                                        >
                                                        <span class="ml-2">No</span>
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-6 border-t">
                                    <div>
                                        <p class="text-sm text-gray-600">
                                            Already Registered? 
                                            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Login here</a>
                                        </p>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="inline-flex items-center px-8 py-3 bg-accent text-white rounded-lg hover:bg-red-600 font-semibold hover-lift transition duration-300">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Register
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24 border border-gray-200">
                            <h3 class="text-xl font-bold text-primary mb-6">Why Create an Account?</h3>
                            <div class="space-y-4">
                                <div class="flex items-start p-3 bg-purple-50 rounded-lg">
                                    <i class="fas fa-calendar-check text-purple-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Conference Registration</h4>
                                        <p class="text-sm text-gray-600 mt-1">Participate in the 1st DATICAN International Conference 2026</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                                    <i class="fas fa-file-alt text-blue-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Paper Submission</h4>
                                        <p class="text-sm text-gray-600 mt-1">Submit and track your research papers</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-green-50 rounded-lg">
                                    <i class="fas fa-clipboard-check text-green-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Review Management</h4>
                                        <p class="text-sm text-gray-600 mt-1">Participate as a reviewer (if eligible)</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start p-3 bg-yellow-50 rounded-lg">
                                    <i class="fas fa-tachometer-alt text-yellow-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Personal Dashboard</h4>
                                        <p class="text-sm text-gray-600 mt-1">Track all activities in one place</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <h4 class="font-semibold text-gray-800 mb-4">Account Security</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                                        <span class="text-sm text-gray-700">Bank-level encryption</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-lock text-green-600 mr-2"></i>
                                        <span class="text-sm text-gray-700">Secure password storage</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-user-shield text-green-600 mr-2"></i>
                                        <span class="text-sm text-gray-700">Privacy protected</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Conference Info -->
                            <div class="mt-6 bg-gradient-to-r from-primary to-secondary text-white p-6 rounded-lg">
                                <h4 class="font-bold mb-2">DATICAN Conference 2026</h4>
                                <p class="text-sm opacity-90 mb-3">May 13-14, 2026 • Virtual Conference</p>
                                <div class="text-xs space-y-1 opacity-80">
                                    <p>• AI & Data Science in Medicine</p>
                                    <p>• Peer-reviewed Proceedings</p>
                                    <p>• International Networking</p>
                                    <p>• Certificate of Participation</p>
                                </div>
                            </div>
                            
                            <!-- Need Help -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h4 class="font-semibold text-gray-800 mb-2">Need Help?</h4>
                                <p class="text-sm text-gray-600 mb-3">Contact our support team for assistance</p>
                                <div class="flex items-center text-sm text-primary">
                                    <i class="fas fa-envelope mr-2"></i>
                                    <span>support@datican.org</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
            
        }
    });
</script>
@endsection