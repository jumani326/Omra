@extends('layouts.app')

@section('page-title', 'Dashboard Overview')
@section('page-description', 'Welcome back. Here\'s what\'s happening today.')

@section('content')
<div class="space-y-6">
    @if(!empty($isSupervision))
    <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-2 text-amber-800 text-sm">
        <strong>Vue supervision</strong> — Statistiques agrégées sur toutes les agences et branches.
    </div>
    @endif
    <!-- Header avec boutons d'action -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
            <p class="text-gray-600 mt-1">Welcome back. Here's what's happening today.</p>
        </div>
        <div class="flex space-x-3">
            @can('create', App\Models\Package::class)
            <a href="{{ route('packages.create') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                + Create Package
            </a>
            @endcan
            @can('create', App\Models\Pilgrim::class)
            <a href="{{ route('pilgrims.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">
                + Add New Pilgrim
            </a>
            @endcan
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Pilgrims -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pilgrims</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPilgrims ?? 0) }}</p>
                    <p class="text-sm text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        +12%
                    </p>
                </div>
                <div class="bg-primary-green bg-opacity-10 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-primary-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($monthlyRevenue ?? 0, 0) }} MAD</p>
                    <p class="text-sm text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        +8%
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Visa Acceptance -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Visa Acceptance</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($visaAcceptanceRate ?? 0, 1) }}%</p>
                    <p class="text-sm text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        +0.5%
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Groups -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Groups</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeGroups ?? 0 }}</p>
                    <p class="text-sm text-red-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                        -2%
                    </p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Revenus sur 12 mois (MAD)</h2>
            <div class="h-64">
                <canvas id="chartRevenue"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Distribution des visas</h2>
            <div class="h-64 flex items-center justify-center">
                <canvas id="chartVisas"></canvas>
            </div>
        </div>
    </div>
    @if(!empty($pilgrimsByBranch) && count($pilgrimsByBranch) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Pèlerins par branche</h2>
        <div class="h-64">
            <canvas id="chartPilgrimsByBranch"></canvas>
        </div>
    </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var revenueData = @json($revenueByMonth ?? []);
            if (document.getElementById('chartRevenue')) {
                new Chart(document.getElementById('chartRevenue'), {
                    type: 'line',
                    data: {
                        labels: revenueData.map(function(d) { return d.label; }),
                        datasets: [{
                            label: 'Revenus (MAD)',
                            data: revenueData.map(function(d) { return d.value; }),
                            borderColor: '#0F3F2E',
                            backgroundColor: 'rgba(15, 63, 46, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            }
            var visaData = @json($visaDistribution ?? []);
            var statusLabels = { 'not_submitted': 'Non soumis', 'submitted': 'Soumis', 'processing': 'En cours', 'approved': 'Approuvé', 'refused': 'Refusé' };
            var colors = ['#9ca3af', '#eab308', '#3b82f6', '#22c55e', '#ef4444'];
            if (document.getElementById('chartVisas')) {
                new Chart(document.getElementById('chartVisas'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(visaData).map(function(k) { return statusLabels[k] || k; }),
                        datasets: [{
                            data: Object.values(visaData),
                            backgroundColor: colors.slice(0, Object.keys(visaData).length)
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
                });
            }
            var pilgrimsByBranchData = @json($pilgrimsByBranch ?? []);
            if (pilgrimsByBranchData.length && document.getElementById('chartPilgrimsByBranch')) {
                new Chart(document.getElementById('chartPilgrimsByBranch'), {
                    type: 'bar',
                    data: {
                        labels: pilgrimsByBranchData.map(function(d) { return d.label; }),
                        datasets: [{
                            label: 'Pèlerins',
                            data: pilgrimsByBranchData.map(function(d) { return d.value; }),
                            backgroundColor: 'rgba(15, 63, 46, 0.7)',
                            borderColor: '#0F3F2E',
                            borderWidth: 1
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            }
        });
    </script>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Recent Activities</h2>
                <a href="#" class="text-sm text-primary-green hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                @forelse(($recentActivities ?? []) as $activity)
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $activity['description'] ?? 'Activity' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] ?? 'Just now' }} • {{ $activity['group'] ?? 'N/A' }}</p>
                    </div>
                </div>
                @empty
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Ahmed Khan uploaded Passport</p>
                        <p class="text-xs text-gray-500 mt-1">2 minutes ago • Group B-12</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New Pilgrim Registration: Fatimah Ali</p>
                        <p class="text-xs text-gray-500 mt-1">5 minutes ago</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Active Pilgrims -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Active Pilgrims</h2>
                <a href="{{ route('pilgrims.index') }}" class="text-sm text-primary-green hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase py-3">NAME</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase py-3">PACKAGE</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase py-3">STATUS</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase py-3">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($activePilgrims ?? []) as $pilgrim)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary-green rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr($pilgrim->first_name ?? 'A', 0, 1) . substr($pilgrim->last_name ?? 'B', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $pilgrim->first_name ?? '' }} {{ $pilgrim->last_name ?? '' }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-600">{{ $pilgrim->package->name ?? 'N/A' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ strtoupper($pilgrim->status ?? 'APPROVED') }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('pilgrims.show', $pilgrim) }}" class="text-primary-green hover:underline text-sm">Voir</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 text-sm">No active pilgrims</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
