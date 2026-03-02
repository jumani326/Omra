@extends('layouts.app')

@section('page-title', 'Comptabilité')
@section('page-description', 'Tableau de bord et comptes marchands digitaux')

@section('content')
<div class="space-y-6">
    {{-- Liens rapides --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('comptabilite.index') }}" class="px-4 py-2 rounded-lg bg-primary-green text-white font-medium hover:bg-dark-green transition">
            Tableau de bord
        </a>
        <a href="{{ route('compte-marchands.index') }}" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
            Comptes Marchands
        </a>
        <a href="{{ route('transaction-digitales.index') }}" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
            Transactions Digitales
        </a>
        <a href="{{ route('payments.index') }}" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
            Paiements
        </a>
    </div>

    <h2 class="text-lg font-semibold text-gray-800">Récapitulatif des comptes marchands</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $logos = [
                'D-money' => 'D-money.jpeg',
                'Waafi' => 'Waafi.jpeg',
                'MyCac' => 'Mycac.jpeg',
            ];
        @endphp
        @foreach(['D-money', 'Waafi', 'MyCac'] as $methode)
            @php $data = $byMethode[$methode] ?? ['numero' => '—', 'solde' => 0, 'total_transactions' => 0, 'compte' => null]; @endphp
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg hover:border-primary-green/20 transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                            @if(file_exists(public_path('img/' . ($logos[$methode] ?? 'Mycac.jpeg'))))
                                <img src="{{ asset('img/' . ($logos[$methode] ?? 'Mycac.jpeg')) }}" alt="{{ $methode }}" class="w-10 h-10 object-contain">
                            @else
                                <span class="text-xl font-bold text-primary-green">{{ substr($methode, 0, 1) }}</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">💳 {{ $methode }}</h3>
                    </div>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-gray-500">Numéro</dt>
                            <dd class="font-mono font-medium text-gray-900">{{ $data['numero'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Solde actuel</dt>
                            <dd class="font-semibold text-primary-green">{{ number_format($data['solde'], 0, ',', ' ') }} FDJ</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Total des transactions (validées)</dt>
                            <dd class="font-medium text-gray-800">{{ number_format($data['total_transactions'], 0, ',', ' ') }} FDJ</dd>
                        </div>
                    </dl>
                    @if($data['compte'])
                        <a href="{{ str_contains($data['numero'], 'compte(s)') ? route('compte-marchands.index') : route('compte-marchands.show', $data['compte']) }}" class="mt-4 inline-block text-primary-green text-sm font-medium hover:underline">Voir {{ str_contains($data['numero'], 'compte(s)') ? 'les comptes' : 'le compte' }} →</a>
                    @else
                        <a href="{{ route('compte-marchands.create') }}?nom_methode={{ urlencode($methode) }}" class="mt-4 inline-block text-amber-600 text-sm font-medium hover:underline">Configurer ce compte</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
