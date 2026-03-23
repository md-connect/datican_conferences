@extends('layouts.app')

@section('title', 'Conference Registrations')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Conference Registrations</h1>
                <p class="text-gray-600 mt-2">View all conference registrations</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('chair.export.registrations') }}" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
                <a href="{{ route('chair.dashboard') }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <form method="GET" action="{{ route('chair.registrations') }}" class="flex flex-wrap gap-4">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Search by name, email, institution..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <select name="year" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Years</option>
                        <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ request('year') == '2025' ? 'selected' : '' }}>2025</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    @if(request()->hasAny(['search', 'year']))
                    <a href="{{ route('chair.registrations') }}" class="ml-2 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Registrations Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institution</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presenting</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DATIAN Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($registrations as $index => $reg)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $registrations->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $reg->title }} {{ $reg->firstname }} {{ $reg->lastname }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $reg->email }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $reg->institution }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $reg->phone_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $reg->gender }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reg->is_presenting_paper)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Yes</span>
                                @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reg->is_datican_member)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Yes</span>
                                @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $reg->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users-slash text-4xl mb-4"></i>
                                <p class="text-lg">No registrations found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($registrations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $registrations->links() }}
            </div>
            @endif
        </div>

        <!-- Statistics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-users text-3xl text-blue-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">{{ $registrations->total() }}</p>
                    <p class="text-sm text-gray-500">Total Registrations</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-chalkboard-user text-3xl text-green-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $registrations->where('is_presenting_paper', true)->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Presenting Papers</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-user-check text-3xl text-purple-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $registrations->where('is_datican_member', true)->count() }}
                    </p>
                    <p class="text-sm text-gray-500">DATIAN Members</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-venus-mars text-3xl text-pink-600 mb-3"></i>
                    <p class="text-sm font-bold text-gray-900">
                        M: {{ $registrations->where('gender', 'Male')->count() }} | 
                        F: {{ $registrations->where('gender', 'Female')->count() }}
                    </p>
                    <p class="text-xs text-gray-500">Gender Distribution</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection