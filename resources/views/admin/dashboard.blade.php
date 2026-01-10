@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Conference Dashboard
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    DATICAN Conference 2026 - Admin Panel
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('admin.export.registrations') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export to Excel
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Registrations -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-users text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Registrations
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ number_format($stats['total_registrations']) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.registrations') }}" class="font-medium text-primary hover:text-secondary">
                            View all
                        </a>
                    </div>
                </div>
            </div>

            <!-- Paper Presenters -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-file-alt text-green-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Paper Presenters
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ number_format($stats['total_presenters']) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.export.registrations') }}?type=presenters" class="font-medium text-primary hover:text-secondary">
                            Export presenters
                        </a>
                    </div>
                </div>
            </div>

            <!-- DATICAN Members -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-user-check text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    DATICAN Members
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ number_format($stats['total_datican_members']) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.export.registrations') }}?type=datican_members" class="font-medium text-primary hover:text-secondary">
                            Export members
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gender Ratio -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-pink-100 p-3 rounded-lg">
                                <i class="fas fa-venus-mars text-pink-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Gender Distribution
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    Male: {{ $stats['gender_distribution']['Male'] ?? 0 }} | 
                                    Female: {{ $stats['gender_distribution']['Female'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-gray-500">
                            Updated {{ now()->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Registrations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Gender Distribution -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Gender Distribution</h3>
                <div class="h-64 flex items-center justify-center text">
                    @php
                        $male = $stats['gender_distribution']['Male'] ?? 0;
                        $female = $stats['gender_distribution']['Female'] ?? 0;
                        $total = $male + $female;
                        $malePercent = $total > 0 ? ($male / $total) * 100 : 0;
                        $femalePercent = $total > 0 ? ($female / $total) * 100 : 0;
                    @endphp
                    <div class="text-center">
                        <div class="flex items-center justify-center space-x-8 mb-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ $male }}</div>
                                <div class="text-sm text-gray-600">Male ({{ round($malePercent, 1) }}%)</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-pink-600">{{ $female }}</div>
                                <div class="text-sm text-gray-600">Female ({{ round($femalePercent, 1) }}%)</div>
                            </div>
                        </div>
                        <div class="w-64 h-4 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full flex">
                                <div class="bg-gray-600" style="width: {{ $malePercent }}%"></div>
                                <div class="bg-pink-600" style="width: {{ $femalePercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DATICAN Status Distribution -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">DATICAN Status Distribution</h3>
                <div class="space-y-4">
                    @foreach($stats['status_distribution'] as $status => $count)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $status }}</span>
                            <span class="text-sm font-medium text-gray-700">{{ $count }}</span>
                        </div>
                        @php
                            $totalStatus = array_sum($stats['status_distribution']);
                            $percent = $totalStatus > 0 ? ($count / $totalStatus) * 100 : 0;
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Recent Registrations
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Latest 10 conference registrations
                </p>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($recentRegistrations as $registration)
                <li>
                    <a href="{{ route('admin.registration.show', $registration->id) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ substr($registration->firstname, 0, 1) }}{{ substr($registration->lastname, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-primary">
                                            {{ $registration->title }} {{ $registration->firstname }} {{ $registration->lastname }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $registration->email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <div class="text-sm text-gray-900">
                                        {{ $registration->institution }}
                                    </div>
                                    <div class="mt-2 flex items-center">
                                        @if($registration->is_presenting_paper)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Presenter
                                        </span>
                                        @endif
                                        @if($registration->is_datican_member)
                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            DATICAN Member
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <div class="mr-6 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-phone-alt mr-1"></i>
                                        {{ $registration->phone_number }}
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <i class="fas fa-venus-mars mr-1"></i>
                                        {{ $registration->gender }}
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Registered {{ $registration->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    No registrations yet.
                </li>
                @endforelse
            </ul>
            <div class="bg-gray-50 px-4 py-3 sm:px-6">
                <div class="flex justify-between">
                    <a href="{{ route('admin.registrations') }}" class="text-sm font-medium text-primary hover:text-secondary">
                        View all registrations
                    </a>
                    <div class="text-sm text-gray-500">
                        Total: {{ $stats['total_registrations'] }} registrations
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection