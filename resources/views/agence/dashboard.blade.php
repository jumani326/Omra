@extends('layouts.app')

@section('page-title', 'Dashboard Agence')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Agence</h1>
    <p class="text-gray-600">Vue d'ensemble de votre agence.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Pèlerins</p>
            <p class="text-2xl font-bold text-primary-green">{{ number_format($totalPilgrims ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Guides</p>
            <p class="text-2xl font-bold text-primary-green">{{ number_format($totalGuides ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Groupes</p>
            <p class="text-2xl font-bold text-primary-green">{{ number_format($totalGroups ?? 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Visas en cours</h2>
            @forelse($visasEnCours ?? [] as $v)
                <p class="text-sm">{{ $v->pilgrim->first_name ?? '' }} {{ $v->pilgrim->last_name ?? '' }} — {{ $v->status }}</p>
            @empty
                <p class="text-gray-500">Aucun visa en cours.</p>
            @endforelse
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Derniers paiements</h2>
            @forelse($recentPayments ?? [] as $p)
                <p class="text-sm">{{ $p->pilgrim->first_name ?? '' }} — {{ number_format($p->amount, 0, ',', ' ') }}</p>
            @empty
                <p class="text-gray-500">Aucun paiement récent.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
