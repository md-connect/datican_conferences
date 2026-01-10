@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('subtitle', 'Conference Overview & Analytics')

@section('content')
<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Registrations -->
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Registrations</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_registrations']) }}</p>
                <p class="text-xs text-gray-500 mt-1">All conference registrations</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-users text-blue-600 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('admin.registrations') }}" class="mt-4 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
            View all <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <!-- Paper Presenters -->
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Paper Presenters</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_presenters']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Submitting papers</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-file-alt text-green-600 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('admin.registrations') }}?filter=presenters" class="mt-4 inline-flex items-center text-sm text-green-600 hover:text-green-800">
            View presenters <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <!-- DATICAN Members -->
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">DATICAN Members</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_datican_members']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Registered members</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-user-check text-purple-600 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('admin.registrations') }}?filter=datican_members" class="mt-4 inline-flex items-center text-sm text-purple-600 hover:text-purple-800">
            View members <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <!-- Gender Distribution -->
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Gender Ratio</p>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-800 mr-4">
                        <span class="text-blue-600">{{ $stats['gender_distribution']['Male'] ?? 0 }}</span> : 
                        <span class="text-pink-600">{{ $stats['gender_distribution']['Female'] ?? 0 }}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Male : Female</p>
            </div>
            <div class="bg-pink-100 p-3 rounded-full">
                <i class="fas fa-venus-mars text-pink-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Gender Distribution Chart -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Gender Distribution</h3>
            <span class="text-sm text-gray-500">Visual breakdown</span>
        </div>
        <div class="h-64">
            @php
                $male = $stats['gender_distribution']['Male'] ?? 0;
                $female = $stats['gender_distribution']['Female'] ?? 0;
                $total = $male + $female;
                $malePercent = $total > 0 ? ($male / $total) * 100 : 0;
                $femalePercent = $total > 0 ? ($female / $total) * 100 : 0;
            @endphp
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-12 mb-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                                <span class="font-medium">Male</span>
                            </div>
                            <div class="text-2xl font-bold text-blue-600">{{ $male }}</div>
                            <div class="text-sm text-gray-500">{{ round($malePercent, 1) }}%</div>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <div class="w-4 h-4 bg-pink-500 rounded-full mr-2"></div>
                                <span class="font-medium">Female</span>
                            </div>
                            <div class="text-2xl font-bold text-pink-600">{{ $female }}</div>
                            <div class="text-sm text-gray-500">{{ round($femalePercent, 1) }}%</div>
                        </div>
                    </div>
                    <div class="w-64 h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full flex">
                            <div class="bg-blue-500" style="width: {{ $malePercent }}%"></div>
                            <div class="bg-pink-500" style="width: {{ $femalePercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- DATICAN Status Distribution -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">DATICAN Status</h3>
            <span class="text-sm text-gray-500">Member categories</span>
        </div>
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
    </div>
</div>

<!-- Recent Registrations -->
<div class="bg-white rounded-xl shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Recent Registrations</h3>
            <a href="{{ route('admin.registrations') }}" class="text-sm text-primary hover:text-secondary">
                View all <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentRegistrations as $registration)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                <span class="text-white font-medium">
                                    {{ substr($registration->firstname, 0, 1) }}{{ substr($registration->lastname, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $registration->title }} {{ $registration->firstname }} {{ $registration->lastname }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $registration->gender }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $registration->email }}</div>
                        <button onclick="copyToClipboard('{{ $registration->email }}')" 
                                class="text-xs text-primary hover:text-secondary mt-1">
                            <i class="fas fa-copy mr-1"></i> Copy
                        </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $registration->institution }}</div>
                        <div class="text-xs text-gray-500">{{ $registration->phone_number }}</div>
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
                        <div class="text-xs text-gray-400">{{ $registration->created_at->diffForHumans() }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users-slash text-4xl mb-4 text-gray-300"></i>
                        <p class="text-lg">No registrations yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="text-sm text-gray-500">
            Showing {{ $recentRegistrations->count() }} of {{ $stats['total_registrations'] }} total registrations
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Quick Export</h4>
        <div class="space-y-3">
            <a href="{{ route('admin.export.registrations') }}?type=all" 
               class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                <span class="text-gray-700">All Registrations</span>
                <i class="fas fa-file-excel text-green-600"></i>
            </a>
            <a href="{{ route('admin.export.registrations') }}?type=presenters" 
               class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                <span class="text-gray-700">Paper Presenters Only</span>
                <i class="fas fa-file-excel text-blue-600"></i>
            </a>
            <a href="{{ route('admin.export.registrations') }}?type=datican_members" 
               class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                <span class="text-gray-700">DATICAN Members Only</span>
                <i class="fas fa-file-excel text-purple-600"></i>
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Registration Analytics</h4>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-600">Daily Average</span>
                    <span class="text-sm font-medium">
                        {{ $stats['total_registrations'] > 0 ? round($stats['total_registrations'] / max(1, count($stats['registrations_by_date'])), 1) : 0 }}/day
                    </span>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-600">Presenter Rate</span>
                    <span class="text-sm font-medium">
                        {{ $stats['total_registrations'] > 0 ? round(($stats['total_presenters'] / $stats['total_registrations']) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-600">Member Rate</span>
                    <span class="text-sm font-medium">
                        {{ $stats['total_registrations'] > 0 ? round(($stats['total_datican_members'] / $stats['total_registrations']) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">System Status</h4>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-gray-700">Database</span>
                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Healthy</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-700">Cache</span>
                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Active</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-700">Export Service</span>
                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Ready</span>
            </div>
            <div class="pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">Last updated: {{ now()->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    // Auto-refresh dashboard every 60 seconds
    setInterval(() => {
        window.location.reload();
    }, 60000);
</script>
