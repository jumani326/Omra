@extends('layouts.app')

@section('page-title', 'Compte marchand - ' . $compteMarchand->nom_methode)
@section('page-description', $compteMarchand->numero_compte)

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $compteMarchand->nom_methode }}</h2>
                <p class="text-gray-600 mt-1 font-mono">{{ $compteMarchand->numero_compte }}</p>
                <p class="text-sm text-gray-500">{{ $compteMarchand->nom_agence }}</p>
                <div class="mt-2">
                    @if($compteMarchand->actif)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactif</span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Solde actuel</p>
                <p class="text-2xl font-bold text-primary-green">{{ number_format($compteMarchand->solde, 0, ',', ' ') }} FDJ</p>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            @can('update', $compteMarchand)
            <a href="{{ route('compte-marchands.edit', $compteMarchand) }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Modifier</a>
            @endcan
            @can('delete', $compteMarchand)
            <form action="{{ route('compte-marchands.destroy', $compteMarchand) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce compte marchand ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">Supprimer</button>
            </form>
            @endcan
            <a href="{{ route('compte-marchands.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Liste des comptes</a>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Historique des transactions</h3>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($compteMarchand->transactionsDigitales as $tx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tx->client_display }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($tx->montant, 0, ',', ' ') }} FDJ</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statutClass = match($tx->statut) {
                                        'valide' => 'bg-green-100 text-green-800',
                                        'refuse' => 'bg-red-100 text-red-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statutClass }}">{{ ucfirst(str_replace('_', ' ', $tx->statut)) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('transaction-digitales.show', $tx) }}" class="text-primary-green hover:underline text-sm">Voir</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucune transaction sur ce compte.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="text-sm text-gray-500">
        <a href="{{ route('comptabilite.index') }}" class="text-primary-green hover:underline">← Retour Comptabilité</a>
    </p>
</div>
@endsection
