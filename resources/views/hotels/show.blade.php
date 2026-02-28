@extends('layouts.app')

@section('page-title', $hotel->name)
@section('page-description', 'Détails de l\'hôtel')

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $hotel->name }}</h1>
            <p class="text-gray-600 mt-1">
                {{ ucfirst($hotel->city) }} · {{ $hotel->stars }} étoile(s)
                @if($hotel->distance_haram)
                    · {{ $hotel->distance_haram }}m du Haram
                @endif
            </p>
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
        <!-- Colonne gauche : images et infos -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Cadre visuel hôtel (style carte) -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                @if($hotel->main_image)
                <a href="{{ Storage::url($hotel->main_image) }}" target="_blank" rel="noopener" class="block focus:outline-none">
                    <div class="h-80 w-full overflow-hidden rounded-t-2xl">
                        <img src="{{ Storage::url($hotel->main_image) }}" alt="{{ $hotel->name }}" 
                             class="w-full h-full object-cover hover:opacity-98 transition">
                    </div>
                </a>
                <div class="px-6 pt-4 flex items-center">
                    @for($i = 0; $i < $hotel->stars; $i++)
                    <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.363 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118l-2.8-2.034c-.784-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    @endfor
                </div>
                <div class="px-6 pt-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $hotel->name }}</h2>
                    <p class="text-gray-500 mt-0.5">{{ $hotel->city == 'mecca' ? 'La Mecque' : 'Médine' }}</p>
                </div>
                <div class="px-6 py-4 mt-2 flex items-center justify-between border-t border-gray-100 bg-gray-50/50">
                    <span class="text-sm font-semibold text-primary-green">{{ $hotel->distance_haram ? $hotel->distance_haram . ' m du Haram' : $hotel->stars . ' étoile(s)' }}</span>
                    <a href="{{ Storage::url($hotel->main_image) }}" target="_blank" rel="noopener" class="text-sm font-semibold text-primary-green hover:text-dark-green flex items-center gap-1">
                        Agrandir l'image
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                @else
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-t-2xl">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        </svg>
                        <p class="text-gray-500">Aucune photo principale</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800">{{ $hotel->name }}</h2>
                    <p class="text-gray-500 mt-0.5">{{ $hotel->city == 'mecca' ? 'La Mecque' : 'Médine' }}</p>
                </div>
                @endif
            </div>

            <!-- Images des chambres (cadres) -->
            @if($hotel->room_images && count($hotel->room_images) > 0)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 px-6 py-4 border-b border-gray-100">Images des chambres</h2>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($hotel->room_images as $index => $image)
                        <a href="{{ Storage::url($image) }}" target="_blank" rel="noopener" class="block rounded-xl overflow-hidden border border-gray-200 hover:border-primary-green hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-primary-green focus:ring-offset-2">
                            <img src="{{ Storage::url($image) }}" alt="Chambre {{ $index + 1 }}" 
                                 class="w-full h-44 object-cover">
                            <p class="text-center text-xs text-gray-500 py-2 bg-gray-50">Chambre {{ $index + 1 }}</p>
                        </a>
                        @endforeach
                    </div>
                    <p class="mt-3 text-sm text-gray-500">Cliquez sur une image pour l'ouvrir en grand.</p>
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Images des chambres</h2>
                <p class="text-gray-500 text-sm">Aucune image de chambre pour le moment.</p>
            </div>
            @endif

            <!-- Fiche informations -->
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
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
                        <p class="font-medium">{{ $hotel->distance_haram }} m</p>
                    </div>
                    @endif
                </div>
            </div>
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

