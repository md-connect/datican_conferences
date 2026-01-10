@extends('layouts.admin')

@section('title', 'Registration Statistics')
@section('subtitle', 'Conference Analytics Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Registration Statistics</h1>
                    <p class="mt-2 text-gray-600">DATICAN Conference 2026 - Real-time Analytics</p>
                    <p class="mt-1 text-sm text-gray-500">Last updated: {{ now()->format('F d, Y h:i A') }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="location.reload()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-200">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh Now
                    </button>
                    <a href="{{ route('admin.export.registrations') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export to Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Registrations -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Registrations</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_registrations']) }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500">All conference participants</p>
                </div>
            </div>

            <!-- Paper Presenters -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Paper Presenters</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_presenters']) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-file-alt text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        {{ $stats['total_registrations'] > 0 ? round(($stats['total_presenters'] / $stats['total_registrations']) * 100, 1) : 0 }}% of total
                    </p>
                </div>
            </div>

            <!-- DATICAN Members -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">DATICAN Members</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_datican_members']) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        {{ $stats['total_registrations'] > 0 ? round(($stats['total_datican_members'] / $stats['total_registrations']) * 100, 1) : 0 }}% of total
                    </p>
                </div>
            </div>

            <!-- Gender Ratio -->
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-pink-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Gender Ratio</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">
                            <span class="text-blue-600">{{ $stats['gender_distribution']['Male'] ?? 0 }}</span> : 
                            <span class="text-pink-600">{{ $stats['gender_distribution']['Female'] ?? 0 }}</span>
                        </p>
                    </div>
                    <div class="bg-pink-100 p-3 rounded-full">
                        <i class="fas fa-venus-mars text-pink-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500">Male : Female distribution</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Gender Distribution -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Gender Distribution</h3>
                <div class="h-64 flex items-center justify-center">
                    @php
                        $male = $stats['gender_distribution']['Male'] ?? 0;
                        $female = $stats['gender_distribution']['Female'] ?? 0;
                        $total = $male + $female;
                        $malePercent = $total > 0 ? ($male / $total) * 100 : 0;
                        $femalePercent = $total > 0 ? ($female / $total) * 100 : 0;
                    @endphp
                    <div class="text-center">
                        <!-- Pie Chart Visualization -->
                        <div class="relative w-48 h-48 mx-auto mb-6">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <!-- Male segment -->
                                <circle cx="50" cy="50" r="40" fill="transparent" 
                                        stroke="#3b82f6" stroke-width="20" stroke-dasharray="{{ $malePercent * 2.513 }} 251.3"
                                        stroke-dashoffset="0" transform="rotate(-90 50 50)" />
                                <!-- Female segment -->
                                <circle cx="50" cy="50" r="40" fill="transparent" 
                                        stroke="#ec4899" stroke-width="20" stroke-dasharray="{{ $femalePercent * 2.513 }} 251.3"
                                        stroke-dashoffset="{{ -$malePercent * 2.513 }}" transform="rotate(-90 50 50)" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
                                    <div class="text-sm text-gray-500">Total</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="flex justify-center space-x-8">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-sm">Male ({{ round($malePercent, 1) }}%)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-pink-500 rounded-full mr-2"></div>
                                <span class="text-sm">Female ({{ round($femalePercent, 1) }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DATICAN Status Distribution -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">DATICAN Member Status</h3>
                <div class="space-y-4">
                    @foreach($stats['status_distribution'] as $status => $count)
                    @php
                        $totalStatus = array_sum($stats['status_distribution']);
                        $percent = $totalStatus > 0 ? ($count / $totalStatus) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $status }}</span>
                            <span class="text-sm font-medium text-gray-700">{{ $count }} ({{ round($percent, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        Total DATICAN Members: {{ $stats['total_datican_members'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Detailed Registration Breakdown</h3>
            
            <!-- Presenter vs Non-Presenter -->
            <div class="mb-8">
                <h4 class="text-md font-medium text-gray-700 mb-4">Paper Presentation Status</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-800">Presenting Papers</p>
                                <p class="text-2xl font-bold text-green-900 mt-2">{{ $stats['total_presenters'] }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-file-alt text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-green-600">
                                {{ $stats['total_registrations'] > 0 ? round(($stats['total_presenters'] / $stats['total_registrations']) * 100, 1) : 0 }}% of all registrations
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Not Presenting</p>
                                <p class="text-2xl font-bold text-gray-900 mt-2">
                                    {{ $stats['total_registrations'] - $stats['total_presenters'] }}
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-full">
                                <i class="fas fa-user text-gray-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-600">
                                {{ $stats['total_registrations'] > 0 ? round((($stats['total_registrations'] - $stats['total_presenters']) / $stats['total_registrations']) * 100, 1) : 0 }}% of all registrations
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DATICAN Members vs Non-Members -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-4">DATICAN Membership</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-800">DATICAN Members</p>
                                <p class="text-2xl font-bold text-purple-900 mt-2">{{ $stats['total_datican_members'] }}</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-user-check text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-purple-600">
                                {{ $stats['total_registrations'] > 0 ? round(($stats['total_datican_members'] / $stats['total_registrations']) * 100, 1) : 0 }}% of all registrations
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Non-Members</p>
                                <p class="text-2xl font-bold text-gray-900 mt-2">
                                    {{ $stats['total_registrations'] - $stats['total_datican_members'] }}
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-full">
                                <i class="fas fa-user-times text-gray-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-600">
                                {{ $stats['total_registrations'] > 0 ? round((($stats['total_registrations'] - $stats['total_datican_members']) / $stats['total_registrations']) * 100, 1) : 0 }}% of all registrations
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Export Options -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Quick Export Options</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.export.registrations') }}" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">All Registrations</h4>
                            <p class="text-sm text-gray-500">Complete dataset</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
                
                <a href="{{ route('admin.export.registrations') }}?type=presenters" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-file-alt text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">Paper Presenters</h4>
                            <p class="text-sm text-gray-500">Only presenters</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
                
                <a href="{{ route('admin.export.registrations') }}?type=datican_members" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-user-check text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">DATICAN Members</h4>
                            <p class="text-sm text-gray-500">Only members</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            </div>
        </div>

        <!-- System Info -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>DATICAN Conference 2026 - Registration Statistics Dashboard</p>
            <p class="mt-1">Data as of {{ now()->format('F d, Y \a\t h:i A') }}</p>
            <p class="mt-2 text-xs">
                Statistics are automatically updated every 5 minutes
            </p>
        </div>
    </div>
</div>

<script>
    // Auto-refresh every 5 minutes (300000 ms)
    setTimeout(() => {
        location.reload();
    }, 300000);
    
    // Add animation to stats on load
    document.addEventListener('DOMContentLoaded', function() {
        const stats = document.querySelectorAll('[class*="text-3xl"], [class*="text-2xl"]');
        stats.forEach(stat => {
            stat.style.opacity = '0';
            stat.style.transform = 'translateY(20px)';
        });
        
        setTimeout(() => {
            stats.forEach((stat, index) => {
                setTimeout(() => {
                    stat.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    stat.style.opacity = '1';
                    stat.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }, 500);
    });
</script>
@endsection