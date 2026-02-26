@extends('layouts.app')

@section('page-title', 'Dashboard Overview')
@section('page-description', 'Welcome back. Here\'s what\'s happening today.')

@section('content')
<div class="space-y-6">
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

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Revenue Growth</h2>
                <select class="border-gray-300 rounded-md text-sm focus:border-primary-green focus:ring-primary-green">
                    <option>Last 6 Months</option>
                    <option>Last 12 Months</option>
                    <option>This Year</option>
                </select>
            </div>
            <div class="h-64 flex items-end justify-between space-x-2">
                @php
                    $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'];
                    $revenueData = [35000, 42000, 38000, 48000, 45000, 52000];
                    $maxRevenue = max($revenueData);
                @endphp
                @foreach($months as $index => $month)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-primary-green rounded-t" style="height: {{ ($revenueData[$index] / $maxRevenue) * 100 }}%"></div>
                    <p class="text-xs text-gray-600 mt-2">{{ $month }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Visa Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Visa Distribution</h2>
            </div>
            <div class="flex items-center justify-center">
                <div class="relative w-48 h-48">
                    <!-- Donut Chart -->
                    <svg class="transform -rotate-90 w-48 h-48">
                        <circle cx="96" cy="96" r="80" stroke="#e5e7eb" stroke-width="16" fill="none"></circle>
                        <circle cx="96" cy="96" r="80" stroke="#0F3F2E" stroke-width="16" fill="none"
                                stroke-dasharray="{{ 2 * pi() * 80 * 0.98 }} {{ 2 * pi() * 80 }}"
                                stroke-dashoffset="0"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">98%</p>
                            <p class="text-sm text-gray-600">APPROVED</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-primary-green rounded-full mr-2"></div>
                        <span class="text-sm text-gray-700">Approved</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">12,608</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-700">Pending</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">212</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-700">Rejected</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">20</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Recent Activities</h2>
                <a href="#" class="text-sm text-primary-green hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($recentActivities ?? [] as $activity)
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
                        @forelse($activePilgrims ?? [] as $pilgrim)
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
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
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
