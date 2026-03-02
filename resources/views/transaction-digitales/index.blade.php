@extends('layouts.app')

@section('page-title', 'Transactions Digitales')
@section('page-description', 'Historique des paiements D-money, Waafi, MyCac')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transactions Digitales</h1>
            <p class="text-gray-600 mt-1">Historique complet des paiements reçus sur les comptes marchands</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('transaction-digitales.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">+ Enregistrer un paiement</a>
            <a href="{{ route('comptabilite.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Comptabilité</a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('transaction-digitales.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Méthode</label>
                <select name="methode" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Toutes</option>
                    @foreach(\App\Models\CompteMarchand::METHODES as $m)
                        <option value="{{ $m }}" {{ request('methode') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                    <option value="refuse" {{ request('statut') == 'refuse' ? 'selected' : '' }}>Refusé</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-primary-green text-white px-4 py-2 rounded-md hover:bg-dark-green transition">Filtrer</button>
                <a href="{{ route('transaction-digitales.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Numéro du compte</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">#{{ $tx->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $tx->compteMarchand->nom_methode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $tx->compteMarchand->numero_compte }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-primary-green">{{ number_format($tx->montant, 0, ',', ' ') }} FDJ</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tx->client_display }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
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
                            <div class="flex items-center justify-end gap-2">
                                @if($tx->statut === 'en_attente')
                                    <form action="{{ route('transaction-digitales.valider', $tx) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la validation de cette transaction ?');">
                                        @csrf
                                        <button type="submit" class="text-sm px-3 py-1.5 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Valider</button>
                                    </form>
                                    <form action="{{ route('transaction-digitales.refuser', $tx) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer le refus de cette transaction ?');">
                                        @csrf
                                        <button type="submit" class="text-sm px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Refuser</button>
                                    </form>
                                @endif
                                <a href="{{ route('transaction-digitales.show', $tx) }}" class="text-primary-green hover:underline text-sm">Voir</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">Aucune transaction trouvée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
