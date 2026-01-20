@extends('layouts.app')

@section('title', 'Analytics Dashboard - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Conference Analytics</h1>
                    <p class="text-gray-600">DATICAN Conference {{ $year }}</p>
                </div>
                <div class="flex space-x-4">
                    <select id="yearSelect" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="{{ date('Y') }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                        <option value="{{ date('Y') + 1 }}" {{ $year == date('Y') + 1 ? 'selected' : '' }}>{{ date('Y') + 1 }}</option>
                    </select>
                    <div class="flex space-x-2">
                        <a href="{{ route('analytics.export', ['type' => 'papers', 'year' => $year]) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Export Papers
                        </a>
                        <a href="{{ route('analytics.export', ['type' => 'reviews', 'year' => $year]) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Export Reviews
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['papers']['total'] }}</p>
                        <p class="text-sm text-gray-500">Papers Submitted</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm">
                        <span class="text-green-600 font-medium">{{ $stats['papers']['acceptance_rate'] }}%</span> acceptance rate
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['users']['unique_authors'] }}</p>
                        <p class="text-sm text-gray-500">Unique Authors</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm">
                        <span class="font-medium">{{ $stats['users']['reviewers'] }}</span> reviewers
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['reviews']['completed'] }}</p>
                        <p class="text-sm text-gray-500">Reviews Completed</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm">
                        Avg score: <span class="font-medium">{{ $stats['reviews']['average_score'] }}/5</span>
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['reviews']['review_turnaround'] }}</p>
                        <p class="text-sm text-gray-500">Avg Review Days</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm">
                        <span class="font-medium">{{ $stats['reviews']['pending'] }}</span> pending
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Paper Status Chart -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Paper Status Distribution</h3>
                <div class="h-64">
                    <canvas id="paperStatusChart"></canvas>
                </div>
            </div>
            
            <!-- Topic Distribution Chart -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Topic Area Distribution</h3>
                <div class="h-64">
                    <canvas id="topicDistributionChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Timeline Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Submission Timeline -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Paper Submissions Timeline</h3>
                <div class="h-64">
                    <canvas id="submissionTimelineChart"></canvas>
                </div>
            </div>
            
            <!-- Review Timeline -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Review Submissions Timeline</h3>
                <div class="h-64">
                    <canvas id="reviewTimelineChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Detailed Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Review Distribution -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Review Recommendations</h3>
                <div class="space-y-4">
                    @php
                        $recommendations = [
                            'strong_accept' => ['Strong Accept', 'bg-green-500'],
                            'accept' => ['Accept', 'bg-green-400'],
                            'weak_accept' => ['Weak Accept', 'bg-yellow-400'],
                            'borderline' => ['Borderline', 'bg-yellow-500'],
                            'weak_reject' => ['Weak Reject', 'bg-red-400'],
                            'reject' => ['Reject', 'bg-red-500'],
                            'strong_reject' => ['Strong Reject', 'bg-red-600'],
                        ];
                    @endphp
                    
                    @foreach($recommendations as $key => [$label, $color])
                    @if(isset($stats['reviews']['by_recommendation'][$key]))
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $label }}</span>
                            <span class="font-medium">{{ $stats['reviews']['by_recommendation'][$key] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $color }}" 
                                 style="width: {{ ($stats['reviews']['by_recommendation'][$key] / max($stats['reviews']['total'], 1)) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            
            <!-- Reviewer Load -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Reviewer Load Distribution</h3>
                <div class="space-y-4">
                    @php
                        $loadDistribution = $stats['reviews']['reviewer_load']['distribution'] ?? [];
                        $totalReviewers = array_sum($loadDistribution);
                    @endphp
                    
                    @foreach($loadDistribution as $load => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $load }} papers</span>
                            <span class="font-medium">{{ $count }} reviewers</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full bg-blue-500" 
                                 style="width: {{ ($count / max($totalReviewers, 1)) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="pt-4 border-t">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['reviews']['reviewer_load']['average'] }}</p>
                                <p class="text-xs text-gray-500">Average</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['reviews']['reviewer_load']['min'] }}</p>
                                <p class="text-xs text-gray-500">Minimum</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['reviews']['reviewer_load']['max'] }}</p>
                                <p class="text-xs text-gray-500">Maximum</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Submission Types -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Submission Types</h3>
                <div class="space-y-4">
                    @php
                        $submissionTypes = [
                            'full_paper' => 'Full Paper',
                            'short_paper' => 'Short Paper',
                            'poster' => 'Poster',
                            'demo' => 'Demo',
                            'workshop' => 'Workshop',
                            'tutorial' => 'Tutorial',
                        ];
                    @endphp
                    
                    @foreach($submissionTypes as $key => $label)
                    @if(isset($stats['papers']['by_type'][$key]))
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $label }}</span>
                            <span class="font-medium">{{ $stats['papers']['by_type'][$key] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full bg-purple-500" 
                                 style="width: {{ ($stats['papers']['by_type'][$key] / max($stats['papers']['total'], 1)) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Top Institutions -->
        @if(!empty($stats['geographic']['top_institutions']))
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Top Submitting Institutions</h3>
            <div class="space-y-4">
                @foreach($stats['geographic']['top_institutions'] as $institution => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">{{ $institution }}</span>
                        <span class="font-medium">{{ $count }} papers</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-indigo-500" 
                             style="width: {{ ($count / max(array_sum($stats['geographic']['top_institutions']), 1)) * 100 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Recent Activity</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- This would be populated from a real activity log -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 hours ago</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Paper Submitted</td>
                            <td class="px-6 py-4 text-sm text-gray-900">"AI for Medical Diagnosis"</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4 hours ago</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Review Completed</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Score: 4/5</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jane Smith</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 day ago</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Decision Made</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Accepted</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Program Chair</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.getElementById('yearSelect').addEventListener('change', function() {
        window.location.href = `{{ route('analytics.index') }}?year=${this.value}`;
    });
    
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        // Paper Status Chart
        const paperStatusCtx = document.getElementById('paperStatusChart').getContext('2d');
        const paperStatusChart = new Chart(paperStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Submitted', 'Under Review', 'Accepted', 'Rejected', 'Camera Ready'],
                datasets: [{
                    data: [
                        {{ $stats['papers']['by_status']['submitted'] ?? 0 }},
                        {{ $stats['papers']['by_status']['under_review'] ?? 0 }},
                        {{ $stats['papers']['by_status']['accepted'] ?? 0 }},
                        {{ $stats['papers']['by_status']['rejected'] ?? 0 }},
                        {{ $stats['papers']['by_status']['camera_ready'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#3B82F6', // blue
                        '#F59E0B', // yellow
                        '#10B981', // green
                        '#EF4444', // red
                        '#8B5CF6'  // purple
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Topic Distribution Chart
        const topicCtx = document.getElementById('topicDistributionChart').getContext('2d');
        const topicChart = new Chart(topicCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($stats['topic_distribution'])) !!},
                datasets: [{
                    label: 'Number of Papers',
                    data: {!! json_encode(array_values($stats['topic_distribution'])) !!},
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Submission Timeline Chart
        const submissionCtx = document.getElementById('submissionTimelineChart').getContext('2d');
        const submissionChart = new Chart(submissionCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($stats['timeline']['paper_submissions'] ?? [])) !!},
                datasets: [{
                    label: 'Submissions',
                    data: {!! json_encode(array_values($stats['timeline']['paper_submissions'] ?? [])) !!},
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Review Timeline Chart
        const reviewCtx = document.getElementById('reviewTimelineChart').getContext('2d');
        const reviewChart = new Chart(reviewCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($stats['timeline']['review_submissions'] ?? [])) !!},
                datasets: [{
                    label: 'Reviews',
                    data: {!! json_encode(array_values($stats['timeline']['review_submissions'] ?? [])) !!},
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

@endsection