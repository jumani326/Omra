@extends('layouts.app')

@section('page-title', 'Package Management')
@section('page-description', 'Design and manage your seasonal Umrah offerings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-primary-green">Package Management</h1>
            <p class="text-gray-600 mt-1">Design and manage your seasonal Umrah offerings.</p>
        </div>
        @can('create', App\Models\Package::class)
        <a href="{{ route('packages.create') }}" class="bg-primary-green text-white px-6 py-3 rounded-lg hover:bg-dark-green transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Create New Package</span>
        </a>
        @endcan
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Packages -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Packages</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalPackages ?? 0 }}</p>
                    <p class="text-sm text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        {{ $newPackagesThisMonth ?? 0 }} from last month
                    </p>
                </div>
                <div class="bg-primary-green bg-opacity-10 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-primary-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Bookings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($activeBookings ?? 0) }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $fillRate ?? 0 }}% fill rate</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRevenue ?? 0, 0) }} MAD</p>
                    <p class="text-sm text-gray-600 mt-2">Q2 Performance</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Customer Satisfaction -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Customer Satisfaction</p>
                    <p class="text-2xl font-bold text-gray-900">4.9/5.0</p>
                    <p class="text-sm text-gray-600 mt-2">Based on 500+ reviews</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Categories/Filters -->
    <div class="flex space-x-4 border-b border-gray-200">
        <a href="{{ route('packages.index') }}" class="px-4 py-2 border-b-2 border-primary-green text-primary-green font-medium">
            All Packages
        </a>
        <a href="{{ route('packages.index', ['type' => 'vip']) }}" class="px-4 py-2 text-gray-600 hover:text-primary-green">
            VIP Ramadan
        </a>
        <a href="{{ route('packages.index', ['type' => 'premium']) }}" class="px-4 py-2 text-gray-600 hover:text-primary-green">
            Premium
        </a>
        <a href="{{ route('packages.index', ['type' => 'economic']) }}" class="px-4 py-2 text-gray-600 hover:text-primary-green">
            Economic
        </a>
        <a href="{{ route('packages.index', ['archived' => 1]) }}" class="px-4 py-2 text-gray-600 hover:text-primary-green">
            Archived
        </a>
    </div>

    <!-- Packages Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Packages List (2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            @forelse($packages as $package)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Package Image -->
                @if($package->hotelMecca && $package->hotelMecca->main_image)
                <div class="relative h-48 bg-cover bg-center" style="background-image: url('{{ Storage::url($package->hotelMecca->main_image) }}')">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-green to-transparent"></div>
                    <div class="absolute top-4 left-4 z-10">
                        <span class="bg-primary-green text-white px-3 py-1 rounded-full text-xs font-semibold uppercase">
                            {{ strtoupper($package->type) }}
                        </span>
                    </div>
                </div>
                @else
                <div class="relative h-48 bg-gradient-to-r from-primary-green to-dark-green">
                    <div class="absolute top-4 left-4">
                        <span class="bg-primary-green text-white px-3 py-1 rounded-full text-xs font-semibold uppercase">
                            {{ strtoupper($package->type) }}
                        </span>
                    </div>
                    @if($package->hotelMecca)
                    <div class="absolute inset-0 flex items-center justify-center text-white opacity-20">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Package Details -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
                            <div class="flex items-center mt-1">
                                @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.363 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118l-2.8-2.034c-.784-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Package Info -->
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $package->departure_date->diffInDays($package->return_date) }} Days</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-bold text-primary-green">{{ number_format($package->price, 0) }} MAD</span>
                        </div>
                        @if($package->hotelMecca)
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>{{ $package->hotelMecca->name }}</span>
                        </div>
                        @endif
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <span>Direct Flights</span>
                        </div>
                    </div>

                    <!-- Available Slots -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">AVAILABLE SLOTS</span>
                            <span class="text-sm text-gray-600">{{ $package->pilgrims_count ?? 0 }} / {{ $package->slots }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-green h-2 rounded-full" style="width: {{ $package->slots > 0 ? (($package->pilgrims_count ?? 0) / $package->slots) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <a href="{{ route('packages.show', $package) }}" class="flex-1 bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition text-center font-medium">
                            Manage
                        </a>
                        <a href="{{ route('packages.edit', $package) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-gray-500">No packages found</p>
            </div>
            @endforelse

            <!-- Pagination -->
            @if($packages->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 rounded-lg">
                {{ $packages->links() }}
            </div>
            @endif
        </div>

        <!-- New Package Creator (Right Side) -->
        <div class="lg:col-span-1">
            <div class="bg-primary-green rounded-lg shadow-lg p-6 text-white sticky top-6">
                <h2 class="text-xl font-bold mb-6">New Package Creator</h2>
                
                <form action="{{ route('packages.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-2">PACKAGE NAME</label>
                        <input type="text" name="name" placeholder="e.g., Summer Special Umrah" required
                               class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-gold-accent">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">DURATION (DAYS)</label>
                            <input type="number" name="duration" value="10" min="1" required
                                   class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-gold-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">TOTAL SLOTS</label>
                            <input type="number" name="slots" value="50" min="1" required
                                   class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-gold-accent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">HOTEL ASSIGNMENT</label>
                        <select name="hotel_mecca_id" class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-gold-accent">
                            <option value="">Select Hotel</option>
                            @foreach(\App\Models\Hotel::where('city', 'mecca')->get() as $hotel)
                                <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">TRANSPORT DETAILS</label>
                        <textarea name="transport_details" rows="3" placeholder="Flight info and local transport..."
                                  class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-gold-accent"></textarea>
                    </div>

                    <!-- Financials -->
                    <div class="bg-dark-green rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold">Financials</h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium mb-1">BASE COST (MAD)</label>
                                <input type="number" id="base_cost" name="cost" value="1800" step="0.01" min="0" required
                                       class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-gold-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">MARKUP (%)</label>
                                <input type="number" id="markup" name="markup" value="15" step="0.01" min="0" required
                                       class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-gold-accent">
                            </div>
                            <div class="pt-2 border-t border-primary-green">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm">Profit Per Pax:</span>
                                    <span id="profit_per_pax" class="text-green-300 font-semibold">+0.00 MAD</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold">Selling Price:</span>
                                    <span id="selling_price" class="text-gold-accent font-bold text-lg">0.00 MAD</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="type" id="package_type" value="premium">
                    <input type="hidden" name="departure_date" id="departure_date" value="{{ now()->addDays(30)->format('Y-m-d') }}">
                    <input type="hidden" name="return_date" id="return_date" value="{{ now()->addDays(40)->format('Y-m-d') }}">
                    <input type="hidden" name="nights_mecca" value="5">
                    <input type="hidden" name="nights_medina" value="3">
                    <input type="hidden" name="price" id="calculated_price" value="0">

                    <button type="submit" class="w-full bg-gold-accent text-primary-green px-6 py-3 rounded-lg hover:bg-yellow-500 transition font-bold">
                        PUBLISH PACKAGE
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Calculate profit and selling price
    function calculatePrice() {
        const baseCost = parseFloat(document.getElementById('base_cost').value) || 0;
        const markup = parseFloat(document.getElementById('markup').value) || 0;
        
        const profit = (baseCost * markup) / 100;
        const sellingPrice = baseCost + profit;
        
        document.getElementById('profit_per_pax').textContent = `+${profit.toFixed(2)} MAD`;
        document.getElementById('selling_price').textContent = `${sellingPrice.toFixed(2)} MAD`;
        
        // Update hidden price field
        document.getElementById('calculated_price').value = sellingPrice.toFixed(2);
        
        // Calculate return date based on duration
        const duration = parseInt(document.querySelector('input[name="duration"]').value) || 10;
        const departureDate = new Date();
        departureDate.setDate(departureDate.getDate() + 30);
        const returnDate = new Date(departureDate);
        returnDate.setDate(returnDate.getDate() + duration);
        
        document.getElementById('departure_date').value = departureDate.toISOString().split('T')[0];
        document.getElementById('return_date').value = returnDate.toISOString().split('T')[0];
    }
    
    document.getElementById('base_cost').addEventListener('input', calculatePrice);
    document.getElementById('markup').addEventListener('input', calculatePrice);
    document.querySelector('input[name="duration"]').addEventListener('input', calculatePrice);
    calculatePrice(); // Initial calculation
</script>
@endpush
@endsection
