@extends('layouts.app')

@section('page-title', 'Paiement ' . $payment->ref_no)
@section('page-description', 'Détails du paiement')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Paiement {{ $payment->ref_no }}</h1>
            <p class="text-gray-600 mt-1">{{ $payment->pilgrim->first_name }} {{ $payment->pilgrim->last_name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Télécharger la facture PDF</a>
            @can('update', $payment)
            <a href="{{ route('payments.edit', $payment) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Modifier</a>
            @endcan
            <a href="{{ route('pilgrims.show', $payment->pilgrim) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Voir le pèlerin</a>
            <a href="{{ route('payments.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Détails du paiement</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">N° Facture</span>
                    <span class="font-mono font-medium">{{ $payment->ref_no }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Montant</span>
                    <span class="font-bold text-lg">{{ number_format($payment->amount, 0, ',', ' ') }} MAD</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mode</span>
                    <span>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Statut</span>
                    @php
                        $statusClass = match($payment->status) {
                            'completed' => 'bg-green-100 text-green-800',
                            'refunded' => 'bg-orange-100 text-orange-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date</span>
                    <span>{{ $payment->payment_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Enregistré par</span>
                    <span>{{ $payment->processedBy->name ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Pèlerin</h2>
            <div class="space-y-2">
                <p class="font-medium">{{ $payment->pilgrim->first_name }} {{ $payment->pilgrim->last_name }}</p>
                <p class="text-sm text-gray-600">Passeport : {{ $payment->pilgrim->passport_no }}</p>
                <p class="text-sm text-gray-600">Email : {{ $payment->pilgrim->email ?? '—' }}</p>
                @if($payment->pilgrim->package)
                <p class="text-sm text-gray-600">Forfait : {{ $payment->pilgrim->package->name }}</p>
                @endif
                <a href="{{ route('pilgrims.show', $payment->pilgrim) }}" class="inline-block mt-2 text-primary-green hover:underline">Voir la fiche pèlerin</a>
            </div>
        </div>
    </div>
</div>
@endsection
