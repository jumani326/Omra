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

    <!-- Liste des Hôtels - Cartes type cadre visuel -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($hotels as $hotel)
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col">
            <!-- Cadre image : photo en tête -->
            <a href="{{ route('hotels.show', $hotel) }}" class="block focus:outline-none">
            @if($hotel->main_image)
            <div class="h-56 rounded-t-2xl overflow-hidden">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($hotel->main_image) }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
            </div>
            @else
            <div class="h-56 bg-gradient-to-br from-primary-green to-dark-green flex items-center justify-center rounded-t-2xl">
                <svg class="w-20 h-20 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            @endif
            </a>

            <!-- Étoiles (sous l'image) -->
            <div class="px-5 pt-4 flex items-center">
                @for($i = 0; $i < $hotel->stars; $i++)
                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.363 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118l-2.8-2.034c-.784-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                @endfor
            </div>

            <!-- Nom et lieu -->
            <div class="px-5 pt-2 pb-1 flex-1">
                <h3 class="text-lg font-bold text-gray-800 leading-tight">{{ $hotel->name }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">{{ $hotel->city == 'mecca' ? 'La Mecque' : 'Médine' }}</p>
            </div>

            <!-- Barre infos bas (style teal) -->
            <div class="px-5 py-3 flex items-center justify-between border-t border-gray-100 bg-gray-50/50">
                <span class="text-sm font-semibold text-primary-green">
                    @if($hotel->distance_haram)
                    {{ $hotel->distance_haram }} m du Haram
                    @else
                    {{ $hotel->packagesMecca->count() + $hotel->packagesMedina->count() }} forfait(s)
                    @endif
                </span>
                <a href="{{ route('hotels.show', $hotel) }}" class="text-sm font-semibold text-primary-green hover:text-dark-green flex items-center gap-1">
                    Voir
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>

            @can('update', $hotel)
            <div class="px-5 pb-3">
                <a href="{{ route('hotels.edit', $hotel) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Modifier</a>
            </div>
            @endcan
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

