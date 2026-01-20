@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Create Your Account</h1>
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
                                <h2 class="text-2xl font-bold text-primary mb-2">Create Your Account</h2>
                                <p class="text-gray-600">Already have an account? <a href="{{ route('login') }}" class="text-accent hover:text-red-700 font-medium">Login here</a></p>
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
                                    
                                    <!-- Title Field -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Title
                                            </label>
                                            <select name="title" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select Title</option>
                                                <option value="Prof." {{ old('title') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                                <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                                <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                                <option value="Mrs." {{ old('title') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                                <option value="Miss" {{ old('title') == 'Miss' ? 'selected' : '' }}>Miss</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                First Name *
                                            </label>
                                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                   placeholder="Enter your first name">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Last Name *
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
                                                <input type="radio" name="gender" value="Male" 
                                                       class="text-blue-600 focus:ring-blue-500"
                                                       {{ old('gender') == 'Male' ? 'checked' : '' }}>
                                                <span class="ml-2">Male</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="gender" value="Female" 
                                                       class="text-blue-600 focus:ring-blue-500"
                                                       {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                                <span class="ml-2">Female</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- DATICAN Membership Field -->
                                    <div class="mt-6" id="datican-fields">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Are you a DATICAN member?
                                        </label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="is_datican_member" value="1" 
                                                       class="text-blue-600 focus:ring-blue-500"
                                                       {{ old('is_datican_member') == '1' ? 'checked' : '' }}
                                                       id="datican-yes">
                                                <span class="ml-2">Yes</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="is_datican_member" value="0" 
                                                       class="text-blue-600 focus:ring-blue-500"
                                                       {{ old('is_datican_member') == '0' ? 'checked' : 'checked' }}
                                                       id="datican-no">
                                                <span class="ml-2">No</span>
                                            </label>
                                        </div>
                                        
                                        <!-- DATICAN status (shown only if member is Yes) -->
                                        <div id="datican-status-field" style="display: none;">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                DATICAN Status
                                            </label>
                                            <select name="datican_status" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select Status</option>
                                                <option value="PI" {{ old('datican_status') == 'PI' ? 'selected' : '' }}>PI</option>
                                                <option value="Faculty" {{ old('datican_status') == 'Faculty' ? 'selected' : '' }}>Faculty</option>
                                                <option value="Trainer" {{ old('datican_status') == 'Trainer' ? 'selected' : '' }}>Trainer</option>
                                                <option value="PhD Student" {{ old('datican_status') == 'PhD Student' ? 'selected' : '' }}>PhD Student</option>
                                                <option value="MSc. Student" {{ old('datican_status') == 'MSc. Student' ? 'selected' : '' }}>MSc. Student</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Account Information</h3>
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Email Address *
                                            </label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                   placeholder="your.email@example.com">
                                            <p class="mt-1 text-sm text-gray-500">This will be used for all conference communications</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Institution/Affiliation *
                                            </label>
                                            <input type="text" name="affiliation" value="{{ old('affiliation') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                   placeholder="University, Organization, or Company">
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Password *
                                                </label>
                                                <input type="password" name="password" 
                                                       required
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                       placeholder="Create a password">
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Confirm Password *
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
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Conference Participation</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="register_conference" name="register_conference" type="checkbox" value="1" 
                                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                       {{ old('register_conference') ? 'checked' : '' }}>
                                            </div>
                                            <label for="register_conference" class="ml-3 text-gray-700">
                                                <span class="font-medium">Register for DATICAN Conference 2026</span>
                                                <p class="text-sm text-gray-500 mt-1">Create conference registration along with your account</p>
                                            </label>
                                        </div>
                                        
                                        <div class="pl-7 mt-3 space-y-3" id="conference-fields">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Will you be presenting a paper? *
                                                </label>
                                                <div class="flex space-x-4">
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" name="presenting_paper" value="yes" 
                                                               class="text-blue-600 focus:ring-blue-500"
                                                               {{ old('presenting_paper') == 'yes' ? 'checked' : '' }}>
                                                        <span class="ml-2">Yes</span>
                                                    </label>
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" name="presenting_paper" value="no" 
                                                               class="text-blue-600 focus:ring-blue-500"
                                                               {{ old('presenting_paper') == 'no' ? 'checked' : 'checked' }}>
                                                        <span class="ml-2">No</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Terms & Conditions</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="terms" name="terms" type="checkbox" required
                                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            </div>
                                            <label for="terms" class="ml-3 text-gray-700">
                                                <span class="font-medium">I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a></span>
                                            </label>
                                        </div>
                                        
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="newsletter" name="newsletter" type="checkbox" 
                                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                                            </div>
                                            <label for="newsletter" class="ml-3 text-gray-700">
                                                <span class="font-medium">Subscribe to conference updates and announcements</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-6 border-t">
                                    <div>
                                        <p class="text-sm text-gray-600">
                                            Already have an account? 
                                            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Login here</a>
                                        </p>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="inline-flex items-center px-8 py-3 bg-accent text-white rounded-lg hover:bg-red-600 font-semibold hover-lift transition duration-300">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Create Account
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
                                
                                <div class="flex items-start p-3 bg-purple-50 rounded-lg">
                                    <i class="fas fa-calendar-check text-purple-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Conference Registration</h4>
                                        <p class="text-sm text-gray-600 mt-1">Manage your conference attendance</p>
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle conference registration fields
        const conferenceCheckbox = document.getElementById('register_conference');
        const conferenceFields = document.getElementById('conference-fields');
        
        function toggleConferenceFields() {
            if (conferenceCheckbox.checked) {
                conferenceFields.style.display = 'block';
            } else {
                conferenceFields.style.display = 'none';
            }
        }
        
        conferenceCheckbox.addEventListener('change', toggleConferenceFields);
        toggleConferenceFields(); // Initial check
        
        // Toggle DATICAN status field
        const daticanYes = document.getElementById('datican-yes');
        const daticanNo = document.getElementById('datican-no');
        const daticanStatusField = document.getElementById('datican-status-field');
        
        function toggleDaticanStatus() {
            if (daticanYes.checked) {
                daticanStatusField.style.display = 'block';
            } else {
                daticanStatusField.style.display = 'none';
            }
        }
        
        daticanYes.addEventListener('change', toggleDaticanStatus);
        daticanNo.addEventListener('change', toggleDaticanStatus);
        
        // Check initial state for DATICAN fields
        toggleDaticanStatus();
    });
</script>
@endsection