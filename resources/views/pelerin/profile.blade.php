@extends('layouts.app')

@section('page-title', 'Mon profil')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>
    @if($pilgrim)
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <p><strong>Nom :</strong> {{ $pilgrim->first_name }} {{ $pilgrim->last_name }}</p>
            <p><strong>Email :</strong> {{ $pilgrim->email }}</p>
            <p><strong>Téléphone :</strong> {{ $pilgrim->phone }}</p>
            <p><strong>Nationalité :</strong> {{ $pilgrim->nationality }}</p>
            <p><strong>Statut :</strong> {{ $pilgrim->status }}</p>
            @if($pilgrim->visa)
                <p><strong>Visa :</strong> {{ $pilgrim->visa->status }}</p>
            @endif
        </div>
    @else
        <p class="text-gray-500">Aucun dossier associé.</p>
    @endif
</div>
@endsection
