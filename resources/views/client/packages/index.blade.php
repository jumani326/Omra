@extends('layouts.app')

@section('page-title', 'Forfaits disponibles')
@section('page-description', 'Parcourez les forfaits Omra publiés par les agences et postulez à celui de votre choix.')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Forfaits disponibles</h1>
            <p class="text-gray-600 mt-1">Choisissez un forfait et postulez auprès de l'agence. Une fois votre demande validée, vous pourrez poursuivre la procédure.</p>
        </div>
        <a href="{{ route('pelerin.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour à mon espace
        </a>
    </div>

    @if($pilgrim && $pilgrim->status === 'pending')
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-800 flex items-center">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium">Demande en attente</p>
                <p class="text-sm">Votre candidature pour le forfait <strong>{{ $pilgrim->package?->name }}</strong> est en cours d'examen par l'agence. Vous serez notifié dès validation.</p>
            </div>
        </div>
    @endif

    @if($packages->isEmpty())
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <p class="text-gray-600 text-lg">Aucun forfait pour le moment.</p>
            <p class="text-gray-500 mt-2">Les agences n'ont pas encore créé de forfaits, ou ceux-ci ont été supprimés. Revenez plus tard.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $package)
                @php
                    $hotel = $package->hotelMecca ?? $package->hotelMedina;
                    $hotelName = $hotel?->name ?? $package->name;
                    $location = $hotel ? ($hotel->city === 'mecca' ? 'La Mecque' : 'Médine') : ($package->branch?->agency?->name ?? 'Omra');
                    $stars = $hotel->stars ?? 5;
                    $totalNights = ($package->nights_mecca ?? 0) + ($package->nights_medina ?? 0);
                    $imageUrl = $hotel && $hotel->main_image ? \Storage::url($hotel->main_image) : null;
                @endphp
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition flex flex-col">
                    {{-- En-tête image (style carte visuelle : image type hôtel) --}}
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $hotelName }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        @endif
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-white/90 text-primary-green uppercase">{{ $package->type }}</span>
                        </div>
                    </div>
                    {{-- Corps : étoiles, titre, lieu, séjour, carte, prix, bouton Postuler --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex gap-0.5 mb-1" aria-label="{{ $stars }} étoiles">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $stars ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $hotelName }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $package->branch?->agency?->name ?? 'Agence' }} · {{ $location }}</p>
                        <div class="flex items-center justify-between mt-3 text-xs">
                            <span class="font-semibold text-primary-green uppercase">Séjour : {{ $totalNights }} nuit(s)</span>
                            <a href="https://www.google.com/maps/search/{{ urlencode($hotelName . ' ' . $location) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-primary-green font-medium hover:underline">
                                Carte
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                        <p class="text-xl font-bold text-primary-green mt-3">{{ number_format($package->price, 0, ',', ' ') }} FDJ</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $package->departure_date?->translatedFormat('d M Y') }} → {{ $package->return_date?->translatedFormat('d M Y') }} · {{ $package->slots_remaining > 0 ? $package->slots_remaining . ' place(s)' : 'Complet' }}</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            @if($pilgrim && $pilgrim->package_id == $package->id)
                                @if($pilgrim->status === 'pending')
                                    <p class="text-center text-sm text-amber-600 font-medium">Demande en attente de validation par l'agence</p>
                                    <a href="{{ route('pelerin.dashboard') }}" class="block w-full text-center mt-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium">Mon espace</a>
                                @else
                                    <p class="text-center text-sm text-primary-green font-medium">✓ Votre forfait</p>
                                    <a href="{{ route('pelerin.dashboard') }}" class="block w-full text-center mt-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium">Mon espace</a>
                                @endif
                            @elseif($pilgrim)
                                <p class="text-center text-sm text-gray-500">Vous avez déjà un forfait ou une demande en cours.</p>
                            @else
                                @if($package->slots_remaining <= 0)
                                    <span class="block w-full text-center bg-gray-100 text-gray-500 px-4 py-3 rounded-xl font-medium cursor-not-allowed">Plus de place</span>
                                @else
                                    <a href="{{ route('client.package.choose', $package) }}" class="block w-full text-center bg-primary-green text-white px-4 py-3 rounded-xl hover:bg-dark-green transition font-semibold">
                                        Postuler
                                    </a>
                                    <p class="text-xs text-gray-500 text-center mt-2">Votre demande sera validée par l'agence.</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($packages->hasPages())
            <div class="mt-6">
                {{ $packages->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
