@extends('layouts.admin')

@section('title', 'Registration Details')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Registration Details
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Registration ID: {{ $registration->id }}
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('admin.registrations') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Registration Details -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Personal Information
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Registered on {{ $registration->created_at->format('F d, Y \a\t h:i A') }}
                </p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <!-- Personal Details -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Full Name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $registration->title }} {{ $registration->firstname }} {{ $registration->middlename }} {{ $registration->lastname }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Contact Information
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="mb-2">
                                <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                {{ $registration->email }}
                            </div>
                            <div>
                                <i class="fas fa-phone-alt text-gray-400 mr-2"></i>
                                {{ $registration->phone_number }}
                            </div>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Institution & Gender
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="mb-2">
                                <i class="fas fa-university text-gray-400 mr-2"></i>
                                {{ $registration->institution }}
                            </div>
                            <div>
                                <i class="fas fa-venus-mars text-gray-400 mr-2"></i>
                                {{ $registration->gender }}
                            </div>
                        </dd>
                    </div>

                    <!-- Conference Details -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            DATICAN Membership
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($registration->is_datican_member)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-user-check mr-2"></i>
                                Yes - {{ $registration->datican_status }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-user-times mr-2"></i>
                                No
                            </span>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Paper Presentation
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($registration->is_presenting_paper)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-file-alt mr-2"></i>
                                Yes - Presenting Paper
                            </span>
                            <div class="mt-2 text-sm text-gray-600">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                Abstract should be submitted via: 
                                <a href="https://easychair.com/first_datican_conference" target="_blank" class="text-primary hover:text-secondary">
                                    easychair.com/first_datican_conference
                                </a>
                            </div>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-times-circle mr-2"></i>
                                Not Presenting
                            </span>
                            @endif
                        </dd>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Registration Details
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-2">
                                <div>
                                    <span class="font-medium">Registration ID:</span> {{ $registration->id }}
                                </div>
                                <div>
                                    <span class="font-medium">Registered:</span> {{ $registration->created_at->format('F d, Y \a\t h:i A') }}
                                </div>
                                <div>
                                    <span class="font-medium">Last Updated:</span> {{ $registration->updated_at->format('F d, Y \a\t h:i A') }}
                                </div>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="copyRegistrationDetails()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-copy mr-2"></i>
                    Copy Details
                </button>
                <a href="mailto:{{ $registration->email }}?subject=DATICAN Conference 2026" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary">
                    <i class="fas fa-envelope mr-2"></i>
                    Send Email
                </a>
                <a href="{{ route('admin.export.registrations') }}?type=single&id={{ $registration->id }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-file-export mr-2"></i>
                    Export to Excel
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyRegistrationDetails() {
    const details = `
Registration Details:
Name: {{ $registration->title }} {{ $registration->firstname }} {{ $registration->middlename }} {{ $registration->lastname }}
Email: {{ $registration->email }}
Phone: {{ $registration->phone_number }}
Institution: {{ $registration->institution }}
Gender: {{ $registration->gender }}
DATICAN Member: {{ $registration->is_datican_member ? 'Yes' : 'No' }}
DATICAN Status: {{ $registration->datican_status ?? 'N/A' }}
Presenting Paper: {{ $registration->is_presenting_paper ? 'Yes' : 'No' }}
    `.trim();
    
    navigator.clipboard.writeText(details).then(() => {
        alert('Registration details copied to clipboard');
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>
@endsection