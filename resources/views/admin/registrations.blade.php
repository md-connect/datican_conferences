@extends('layouts.admin')

@section('title', 'Registrations Management')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Conference Registrations
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage all conference registrations
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <div class="relative">
                    <input type="text" placeholder="Search registrations..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <div class="absolute left-3 top-2.5">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.export.registrations') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export All
                    </a>
                    <a href="{{ route('admin.export.registrations') }}?type=presenters" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-file-export mr-2"></i>
                        Export Presenters
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white shadow rounded-lg p-4">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.registrations') }}" 
                   class="px-4 py-2 rounded-lg {{ !request()->has('filter') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   All ({{ App\Models\ConferenceRegistration::count() }})
                </a>
                <a href="{{ route('admin.registrations') }}?filter=presenters" 
                   class="px-4 py-2 rounded-lg {{ request('filter') == 'presenters' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   Presenters ({{ App\Models\ConferenceRegistration::where('is_presenting_paper', true)->count() }})
                </a>
                <a href="{{ route('admin.registrations') }}?filter=datican_members" 
                   class="px-4 py-2 rounded-lg {{ request('filter') == 'datican_members' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   DATICAN Members ({{ App\Models\ConferenceRegistration::where('is_datican_member', true)->count() }})
                </a>
                <a href="{{ route('admin.registrations') }}?filter=recent" 
                   class="px-4 py-2 rounded-lg {{ request('filter') == 'recent' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   Recent (Last 7 days)
                </a>
            </div>
        </div>

        <!-- Registrations Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Institution
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($registrations as $registration)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ substr($registration->firstname, 0, 1) }}{{ substr($registration->lastname, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $registration->title }} {{ $registration->firstname }} {{ $registration->lastname }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $registration->gender }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $registration->email }}</div>
                                <div class="text-sm text-gray-500">{{ $registration->phone_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $registration->institution }}</div>
                                <div class="text-sm text-gray-500">{{ $registration->datican_status ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @if($registration->is_presenting_paper)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-file-alt mr-1"></i> Presenter
                                    </span>
                                    @endif
                                    @if($registration->is_datican_member)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-check mr-1"></i> DATICAN
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $registration->created_at->format('M d, Y') }}
                                <div class="text-xs text-gray-400">
                                    {{ $registration->created_at->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.registration.show', $registration->id) }}" 
                                   class="text-primary hover:text-secondary mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <button onclick="copyEmail('{{ $registration->email }}')" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-copy"></i> Copy Email
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users-slash text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg">No registrations found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($registrations->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $registrations->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyEmail(email) {
    navigator.clipboard.writeText(email).then(() => {
        alert('Email copied to clipboard: ' + email);
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>
@endsection