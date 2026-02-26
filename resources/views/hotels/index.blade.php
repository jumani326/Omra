@extends('layouts.app')

@section('page-title', 'Gestion des Hôtels')
@section('page-description', 'Gérez les hôtels de La Mecque et Médine')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hôtels</h1>
            <p class="text-gray-600 mt-1">Gérez les hôtels de La Mecque et Médine</p>
        </div>
        @can('create', App\Models\Hotel::class)
        <a href="{{ route('hotels.create') }}" class="bg-primary-green text-white px-6 py-3 rounded-lg hover:bg-dark-green transition flex items-center space-x-2 shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span class="font-semibold">Ajouter Hôtel</span>
        </a>
        @endcan
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('hotels.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nom de l'hôtel..." 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                <select name="city" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Toutes les villes</option>
                    <option value="mecca" {{ request('city') == 'mecca' ? 'selected' : '' }}>La Mecque</option>
                    <option value="medina" {{ request('city') == 'medina' ? 'selected' : '' }}>Médine</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-green text-white px-4 py-2 rounded-md hover:bg-dark-green transition">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des Hôtels -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($hotels as $hotel)
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <!-- Image principale -->
            @if($hotel->main_image)
            <div class="h-48 bg-cover bg-center relative" style="background-image: url('{{ Storage::url($hotel->main_image) }}')">
                <div class="absolute inset-0 bg-gradient-to-t from-primary-green to-transparent"></div>
                <div class="relative h-full p-4 flex items-end text-white">
            @else
            <div class="bg-gradient-to-r from-primary-green to-dark-green p-4 text-white">
            @endif
                <div class="flex justify-between items-start w-full">
                    <div>
                        <h3 class="text-lg font-bold">{{ $hotel->name }}</h3>
                        <p class="text-sm opacity-90">{{ ucfirst($hotel->city) }}</p>
                    </div>
                    <div class="flex items-center space-x-1">
                        @for($i = 0; $i < $hotel->stars; $i++)
                        <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.363 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118l-2.8-2.034c-.784-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                </div>
            @if($hotel->main_image)
            </div>
            @else
            </div>
            @endif

            <!-- Détails -->
            <div class="p-4">
                <div class="space-y-2 mb-4">
                    @if($hotel->distance_haram)
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm">{{ $hotel->distance_haram }}m du Haram</span>
                    </div>
                    @endif
                    
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-sm">{{ $hotel->packagesMecca->count() + $hotel->packagesMedina->count() }} forfait(s)</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('hotels.show', $hotel) }}" class="flex-1 bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition text-center text-sm">
                        Voir
                    </a>
                    @can('update', $hotel)
                    <a href="{{ route('hotels.edit', $hotel) }}" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center text-sm">
                        Modifier
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <p class="text-gray-500 text-lg mb-2">Aucun hôtel trouvé</p>
            <p class="text-gray-400 text-sm mb-6">Commencez par ajouter votre premier hôtel</p>
            @can('create', App\Models\Hotel::class)
            <a href="{{ route('hotels.create') }}" class="inline-flex items-center space-x-2 bg-primary-green text-white px-6 py-3 rounded-lg hover:bg-dark-green transition shadow-lg font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Ajouter Hôtel</span>
            </a>
            @endcan
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($hotels->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 rounded-lg">
        {{ $hotels->links() }}
    </div>
    @endif
</div>
@endsection

