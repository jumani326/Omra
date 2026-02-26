@extends('layouts.app')

@section('page-title', $package->name)
@section('page-description', 'Détails du forfait')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $package->name }}</h1>
            <p class="text-gray-600 mt-1">Détails du forfait</p>
        </div>
        <div class="flex space-x-3">
            @can('update', $package)
            <a href="{{ route('packages.edit', $package) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Modifier
            </a>
            @endcan
            @can('create', App\Models\Package::class)
            <form action="{{ route('packages.clone', $package) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Cloner
                </button>
            </form>
            @endcan
            <a href="{{ route('packages.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails du forfait -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informations du Forfait</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="font-medium">{{ ucfirst($package->type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Branche</p>
                        <p class="font-medium">{{ $package->branch->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date de départ</p>
                        <p class="font-medium">{{ $package->departure_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date de retour</p>
                        <p class="font-medium">{{ $package->return_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Places totales</p>
                        <p class="font-medium">{{ $package->slots }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Places restantes</p>
                        <p class="font-medium">{{ $package->slots_remaining }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Prix de vente</p>
                        <p class="font-medium text-lg text-primary-green">{{ number_format($package->price, 2) }} MAD</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Coût</p>
                        <p class="font-medium">{{ number_format($package->cost, 2) }} MAD</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Profit</p>
                        <p class="font-medium text-lg text-green-600">{{ number_format($package->price - $package->cost, 2) }} MAD</p>
                    </div>
                </div>
            </div>

            <!-- Hôtels -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Hôtels</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($package->hotel_mecca_id)
                    <div class="border rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900">La Mecque</p>
                        <p class="text-sm text-gray-600">{{ $package->hotelMecca->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $package->nights_mecca }} nuits</p>
                    </div>
                    @endif
                    @if($package->hotel_medina_id)
                    <div class="border rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900">Médine</p>
                        <p class="text-sm text-gray-600">{{ $package->hotelMedina->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $package->nights_medina }} nuits</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pèlerins inscrits -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Pèlerins Inscrits</h2>
            <div class="space-y-3">
                @forelse($package->pilgrims as $pilgrim)
                <div class="border rounded-lg p-3">
                    <p class="font-medium text-sm">{{ $pilgrim->first_name }} {{ $pilgrim->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ $pilgrim->email ?? 'N/A' }}</p>
                    <a href="{{ route('pilgrims.show', $pilgrim) }}" class="text-xs text-primary-green hover:underline mt-1 inline-block">
                        Voir détails
                    </a>
                </div>
                @empty
                <p class="text-sm text-gray-500">Aucun pèlerin inscrit</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

