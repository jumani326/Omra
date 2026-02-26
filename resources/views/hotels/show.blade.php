@extends('layouts.app')

@section('page-title', $hotel->name)
@section('page-description', 'Détails de l\'hôtel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $hotel->name }}</h1>
            <p class="text-gray-600 mt-1">Détails de l'hôtel</p>
        </div>
        <div class="flex space-x-3">
            @can('update', $hotel)
            <a href="{{ route('hotels.edit', $hotel) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Modifier
            </a>
            @endcan
            <a href="{{ route('hotels.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Photo principale -->
            @if($hotel->main_image)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <img src="{{ Storage::url($hotel->main_image) }}" alt="{{ $hotel->name }}" 
                     class="w-full h-64 object-cover">
            </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informations</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nom</p>
                        <p class="font-medium">{{ $hotel->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ville</p>
                        <p class="font-medium">{{ ucfirst($hotel->city) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Étoiles</p>
                        <div class="flex items-center">
                            @for($i = 0; $i < $hotel->stars; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.363 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118l-2.8-2.034c-.784-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                    </div>
                    @if($hotel->distance_haram)
                    <div>
                        <p class="text-sm text-gray-500">Distance du Haram</p>
                        <p class="font-medium">{{ $hotel->distance_haram }}m</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Images de chambres -->
            @if($hotel->room_images && count($hotel->room_images) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Images de Chambres</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($hotel->room_images as $index => $image)
                    <div class="relative group">
                        <img src="{{ Storage::url($image) }}" alt="Chambre {{ $index + 1 }}" 
                             class="w-full h-48 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-lg"></div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Forfaits utilisant cet hôtel -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Forfaits</h2>
            <div class="space-y-3">
                @php
                    $allPackages = $hotel->packagesMecca->merge($hotel->packagesMedina);
                @endphp
                @forelse($allPackages as $package)
                <div class="border rounded-lg p-3">
                    <p class="font-medium text-sm">{{ $package->name }}</p>
                    <p class="text-xs text-gray-500">{{ $package->type }}</p>
                    <a href="{{ route('packages.show', $package) }}" class="text-xs text-primary-green hover:underline mt-1 inline-block">
                        Voir le forfait
                    </a>
                </div>
                @empty
                <p class="text-sm text-gray-500">Aucun forfait n'utilise cet hôtel</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

