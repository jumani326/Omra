@extends('layouts.app')

@section('page-title', $package->name . ' - Détail du forfait')
@section('page-description', 'Détails complets du forfait Omra')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <a href="{{ route('client.packages.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary-green font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour aux forfaits
        </a>
    </div>

    @php
        $hotelMecca = $package->hotelMecca;
        $hotelMedina = $package->hotelMedina;
        $totalNights = ($package->nights_mecca ?? 0) + ($package->nights_medina ?? 0);
        $agencyName = $package->branch?->agency?->name ?? 'Agence';
    @endphp

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        {{-- Image principale --}}
        @php
            $mainHotel = $hotelMecca ?? $hotelMedina;
            $imageUrl = $mainHotel && $mainHotel->main_image ? \Storage::url($mainHotel->main_image) : null;
        @endphp
        <div class="relative h-56 md:h-72 overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            @endif
            <div class="absolute top-4 left-4">
                <span class="px-3 py-1 rounded-lg text-sm font-semibold bg-white/95 text-primary-green uppercase">{{ $package->type }}</span>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-poppins">{{ $package->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $agencyName }} · Forfait Omra</p>

            <div class="flex flex-wrap items-center gap-2 mt-4">
                @if($mainHotel)
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= ($mainHotel->stars ?? 5) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    <span class="text-sm text-gray-500 ml-1">{{ $mainHotel->stars ?? 5 }} étoiles</span>
                @endif
            </div>

            {{-- Prix et dates --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-3xl font-bold text-primary-green">{{ number_format($package->price, 0, ',', ' ') }} FDJ</p>
                <p class="text-sm text-gray-500 mt-1">{{ $package->departure_date?->translatedFormat('d F Y') }} → {{ $package->return_date?->translatedFormat('d F Y') }}</p>
                <p class="text-sm font-medium text-primary-green mt-2">Séjour : {{ $totalNights }} nuit(s) · {{ $package->slots_remaining > 0 ? $package->slots_remaining . ' place(s) disponible(s)' : 'Complet' }}</p>
            </div>

            {{-- Hôtels --}}
            <div class="mt-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Hébergement</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($hotelMecca)
                        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                            <p class="text-xs font-semibold text-primary-green uppercase">La Mecque</p>
                            <p class="font-semibold text-gray-900">{{ $hotelMecca->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $package->nights_mecca }} nuit(s)</p>
                            @if($hotelMecca->distance_haram)
                                <p class="text-xs text-gray-500 mt-1">Distance Haram : {{ number_format($hotelMecca->distance_haram, 0) }} m</p>
                            @endif
                        </div>
                    @endif
                    @if($hotelMedina)
                        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                            <p class="text-xs font-semibold text-primary-green uppercase">Médine</p>
                            <p class="font-semibold text-gray-900">{{ $hotelMedina->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $package->nights_medina }} nuit(s)</p>
                            @if($hotelMedina->distance_haram)
                                <p class="text-xs text-gray-500 mt-1">Distance Haram : {{ number_format($hotelMedina->distance_haram, 0) }} m</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Carte / Localisation --}}
            @if($mainHotel)
                <div class="mt-6">
                    <a href="https://www.google.com/maps/search/{{ urlencode($mainHotel->name . ' ' . ($mainHotel->city === 'mecca' ? 'La Mecque' : 'Médine')) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-primary-green font-medium hover:underline">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Voir sur la carte
                    </a>
                </div>
            @endif

            {{-- Actions --}}
            <div class="mt-8 pt-6 border-t border-gray-100">
                @if($pilgrim && $pilgrim->package_id == $package->id)
                    @if($pilgrim->status === 'pending')
                        <p class="text-amber-600 font-medium mb-3">Demande en attente de validation par l'agence</p>
                        <a href="{{ route('pelerin.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">Mon espace</a>
                    @else
                        <p class="text-primary-green font-medium mb-3">✓ Votre forfait actuel</p>
                        <a href="{{ route('pelerin.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-green text-white rounded-xl font-semibold hover:bg-dark-green transition">Mon espace</a>
                    @endif
                @elseif($pilgrim)
                    <p class="text-gray-500 mb-3">Vous avez déjà un forfait ou une demande en cours.</p>
                    <a href="{{ route('client.packages.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">Retour aux forfaits</a>
                @else
                    @if($package->slots_remaining <= 0)
                        <span class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-500 rounded-xl font-medium cursor-not-allowed">Plus de place</span>
                    @else
                        <a href="{{ route('client.package.choose', $package) }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-green text-white rounded-xl font-semibold hover:bg-dark-green transition">
                            Postuler à ce forfait
                        </a>
                        <p class="text-xs text-gray-500 mt-2">Votre demande sera validée par l'agence.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
