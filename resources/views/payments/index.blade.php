@extends('layouts.app')

@section('page-title', 'Finance - Paiements')
@section('page-description', 'Liste des paiements')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Paiements</h1>
            <p class="text-gray-600 mt-1">Suivi des paiements par pèlerin</p>
        </div>
        @can('create', App\Models\Payment::class)
        <a href="{{ route('payments.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">
            + Nouveau paiement
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche (pèlerin)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, passeport..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                <select name="method" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                    <option value="cash_espece" {{ request('method') == 'cash_espece' ? 'selected' : '' }}>Cash espèce</option>
                    <option value="transfer" {{ request('method') == 'transfer' ? 'selected' : '' }}>Virement</option>
                    <option value="tpe" {{ request('method') == 'tpe' ? 'selected' : '' }}>TPE</option>
                    <option value="mobile_money" {{ request('method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-green text-white px-4 py-2 rounded-md hover:bg-dark-green transition">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Facture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pèlerin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $payment->ref_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $payment->pilgrim->first_name }} {{ $payment->pilgrim->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->pilgrim->passport_no }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($payment->amount, 0, ',', ' ') }} FDJ</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payment->method_label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($payment->status) {
                                    'completed' => 'bg-green-100 text-green-800',
                                    'refunded' => 'bg-orange-100 text-orange-800',
                                    default => 'bg-yellow-100 text-yellow-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            @can('view', $payment)
                            <a href="{{ route('payments.show', $payment) }}" class="text-primary-green hover:underline">Voir</a>
                            @endcan
                            @can('update', $payment)
                            <a href="{{ route('payments.edit', $payment) }}" class="ml-3 text-blue-600 hover:underline">Modifier</a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Aucun paiement.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
@endsection
