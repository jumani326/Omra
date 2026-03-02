@extends('layouts.app')

@section('page-title', 'Mon espace pèlerin')
@section('page-description', 'Choisissez votre forfait, suivez votre procédure et posez vos questions à l\'assistant.')

@section('content')
<div class="space-y-8">
    <!-- Message de bienvenue -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-900 font-poppins">Bienvenue, {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 mt-1">Consultez les forfaits disponibles, suivez l'avancement de votre dossier et utilisez l'assistant pour toute question.</p>
    </div>

    <!-- Ma procédure -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Ma procédure
        </h2>
        @if($pilgrim)
            <p class="text-sm text-gray-600 mb-4">Votre forfait : <strong>{{ $pilgrim->package?->name ?? 'Non assigné' }}</strong> — Statut : <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ $pilgrim->status }}</span></p>
        @endif
        <div class="flex flex-wrap gap-4">
            @foreach($procedureSteps as $step)
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step['done'] ? 'bg-primary-green text-white' : 'bg-gray-200 text-gray-500' }}">
                        @if($step['done'])
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            <span class="text-sm font-semibold">{{ $step['id'] }}</span>
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step['done'] ? 'text-gray-900' : 'text-gray-500' }}">{{ $step['label'] }}</span>
                    @if(!$loop->last)
                        <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Forfaits disponibles -->
    <div>
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Forfaits disponibles
        </h2>
        @if($packages->isEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-amber-800 text-center">
                <p>Aucun forfait disponible pour le moment. Revenez plus tard ou contactez votre agence.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($packages as $package)
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
                        <div class="p-5 border-b bg-gray-50">
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-primary-green text-white uppercase">{{ $package->type }}</span>
                            <h3 class="text-lg font-bold text-gray-900 mt-2">{{ $package->name }}</h3>
                            <p class="text-2xl font-bold text-primary-green mt-2">{{ number_format($package->price, 0) }} FDJ</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $package->departure_date?->translatedFormat('d M Y') }} → {{ $package->return_date?->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $package->nights_mecca }} nuit(s) La Mecque · {{ $package->nights_medina }} nuit(s) Médine</p>
                            <p class="text-xs text-gray-500">{{ $package->slots_remaining }} place(s) restante(s)</p>
                        </div>
                        <div class="p-4">
                            @if($pilgrim && $pilgrim->package_id == $package->id)
                                <div class="text-center py-2 text-sm text-primary-green font-medium">✓ Votre forfait actuel</div>
                                <a href="{{ route('pilgrims.show', $pilgrim) }}" class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Voir mon dossier</a>
                            @elseif($pilgrim)
                                <p class="text-center text-sm text-gray-500 py-2">Vous avez déjà un forfait.</p>
                            @else
                                <a href="{{ route('client.package.choose', $package) }}" class="block w-full text-center bg-primary-green text-white px-4 py-3 rounded-lg hover:bg-dark-green transition font-medium">
                                    Choisir ce forfait
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- CTA Procédure -->
    @if(!$pilgrim && $packages->isNotEmpty())
        <div class="bg-primary-green text-white rounded-xl p-6 text-center">
            <p class="font-medium">Prêt à partir en Omra ? Choisissez un forfait ci-dessus pour démarrer votre procédure.</p>
        </div>
    @endif
</div>

@include('dashboard.partials.chatbot-widget')
@endsection
