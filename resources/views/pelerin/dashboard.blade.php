@extends('layouts.app')

@section('page-title', 'Mon espace')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Mon espace pèlerin</h1>
    @if($pilgrim)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold text-gray-900 mb-2">Mon dossier</h2>
                <p>{{ $pilgrim->first_name }} {{ $pilgrim->last_name }}</p>
                <p class="text-sm text-gray-500">Statut : {{ $pilgrim->status }}</p>
                @if($pilgrim->visa)
                    <p class="text-sm mt-2">Visa : {{ $pilgrim->visa->status }}</p>
                @endif
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold text-gray-900 mb-2">Mon groupe & guide</h2>
                @if($pilgrim->group)
                    <p>Groupe : {{ $pilgrim->group->name }}</p>
                @endif
                @if($pilgrim->guide && $pilgrim->guide->user)
                    <p>Guide : {{ $pilgrim->guide->user->name }}</p>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-2">Paiements</h2>
            @forelse($pilgrim->payments ?? [] as $pay)
                <p class="text-sm">{{ number_format($pay->amount, 0, ',', ' ') }} — {{ $pay->status }}</p>
            @empty
                <p class="text-gray-500">Aucun paiement.</p>
            @endforelse
        </div>
    @else
        <p class="text-gray-500">Aucun dossier pèlerin associé à votre compte.</p>
    @endif
</div>
@endsection
