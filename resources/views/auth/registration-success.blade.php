@extends('layouts.app')

@section('title', 'Registration Successful - DATICAN Conference 2026')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            
            <!-- Success Message -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="mb-8">
                    <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Registration Successful!</h1>
                    <p class="text-gray-600">
                        @if(isset($registration) && $registration->is_presenting_paper)
                            Thank you for registering for DATICAN Conference 2026 as a paper presenter.
                        @else
                            Thank you for registering for DATICAN Conference 2026.
                        @endif
                    </p>
                </div>
                
                <!-- Registration Details -->
                @if(isset($registration))
                <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                    <h3 class="font-semibold text-gray-700 mb-4 text-center">Registration Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Registration ID</p>
                            <p class="font-medium">#{{ str_pad($registration->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-medium">{{ $registration->title }} {{ $registration->firstname }} {{ $registration->lastname }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $registration->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Institution</p>
                            <p class="font-medium">{{ $registration->institution }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Registration Date</p>
                            <p class="font-medium">{{ $registration->created_at->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-medium">{{ ucfirst($registration->status ?? 'pending') }}</p>
                        </div>
                    </div>
                    
                    <!-- Presenting Paper Note -->
                    @if($registration->is_presenting_paper)
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
                        <p class="text-blue-800 font-medium mb-2">For paper presenters:</p>
                        <p class="text-blue-700">Please submit your abstract via the CMT system:</p>
                        <a href="https://cmt3.research.microsoft.com/DATICANCONF2026" target="_blank"
                           class="inline-flex items-center mt-2 text-primary hover:text-secondary font-semibold">
                            <span>cmt3.research.microsoft.com/DATICANCONF2026</span>
                            <i class="fas fa-external-link-alt ml-2"></i>
                        </a>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Next Steps -->
                <div class="mb-8">
                    <h3 class="font-semibold text-gray-700 mb-4">What's Next?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <i class="fas fa-paper-plane text-primary text-2xl mb-2"></i>
                            <p class="font-medium mb-1">Submit Abstract</p>
                            <p class="text-sm text-gray-500">Submit your abstract using the Button on your dashboard</p>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <i class="fas fa-calendar-alt text-primary text-2xl mb-2"></i>
                            <p class="font-medium mb-1">Conference Updates</p>
                            <p class="text-sm text-gray-500">Check your email for conference updates</p>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <i class="fas fa-users text-primary text-2xl mb-2"></i>
                            <p class="font-medium mb-1">Network</p>
                            <p class="text-sm text-gray-500">Connect with other participants</p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('dashboard') }}" 
                       class="px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-secondary">
                        <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                    </a>
                    
                    @if(isset($registration) && $registration->is_presenting_paper)
                    <a href="{{ route('papers.create') }}" 
                       class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Submit Paper
                    </a>
                    @endif
                    
                    <a href="{{ route('home') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </a>
                </div>
                
                <!-- Contact Information -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-gray-600 text-sm">
                        Need assistance? Contact conference organizers at 
                        <a href="mailto:manager.datican@gmail.com" class="text-primary hover:text-secondary font-medium">
                            manager.datican@gmail.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection