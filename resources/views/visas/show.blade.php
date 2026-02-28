@extends('layouts.app')

@section('page-title', 'Dossier visa')
@section('page-description', 'Détails du dossier visa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dossier visa — {{ $visa->pilgrim->first_name }} {{ $visa->pilgrim->last_name }}</h1>
            <p class="text-gray-600 mt-1">Réf. {{ $visa->reference_no ?? '—' }}</p>
        </div>
        <div class="flex gap-3">
            @can('update', $visa)
            <a href="{{ route('visas.edit', $visa) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Modifier</a>
            @endcan
            <a href="{{ route('pilgrims.show', $visa->pilgrim) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Voir le pèlerin</a>
            <a href="{{ route('visas.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">État du visa</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Statut</span>
                    @php
                        $statusClass = match($visa->status) {
                            'approved' => 'bg-green-100 text-green-800',
                            'refused' => 'bg-red-100 text-red-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'submitted' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $visa->status)) }}</span>
                </div>
                <div class="flex justify-between"><span class="text-gray-600">Référence</span><span class="font-medium">{{ $visa->reference_no ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Soumis le</span><span>{{ $visa->submitted_at ? $visa->submitted_at->format('d/m/Y H:i') : '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Décision le</span><span>{{ $visa->decision_at ? $visa->decision_at->format('d/m/Y H:i') : '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Expiration</span><span>{{ $visa->expiry_date ? $visa->expiry_date->format('d/m/Y') : '—' }}</span></div>
                @if($visa->refusal_reason)
                <div class="pt-2 border-t">
                    <p class="text-sm text-gray-600">Motif de refus</p>
                    <p class="text-gray-900 mt-1">{{ $visa->refusal_reason }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Pèlerin</h2>
            <div class="space-y-2">
                <p class="font-medium">{{ $visa->pilgrim->first_name }} {{ $visa->pilgrim->last_name }}</p>
                <p class="text-sm text-gray-600">Passeport : {{ $visa->pilgrim->passport_no }}</p>
                <p class="text-sm text-gray-600">Email : {{ $visa->pilgrim->email ?? '—' }}</p>
                <p class="text-sm text-gray-600">Téléphone : {{ $visa->pilgrim->phone ?? '—' }}</p>
                <a href="{{ route('pilgrims.show', $visa->pilgrim) }}" class="inline-block mt-2 text-primary-green hover:underline">Voir la fiche pèlerin</a>
            </div>
        </div>
    </div>

    @if($visa->documents && count((array)$visa->documents) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Documents consulaires</h2>
        <ul class="space-y-2">
            @foreach((array)$visa->documents as $path)
            <li><a href="{{ \Illuminate\Support\Facades\Storage::url($path) }}" target="_blank" class="text-primary-green hover:underline">{{ basename($path) }}</a></li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection
