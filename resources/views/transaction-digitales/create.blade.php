@extends('layouts.app')

@section('page-title', 'Enregistrer une transaction digitale')
@section('page-description', 'Paiement reçu sur D-money, Waafi ou MyCac')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('transaction-digitales.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf

        <div>
            <label for="compte_marchand_id" class="block text-sm font-medium text-gray-700">Compte marchand *</label>
            <select name="compte_marchand_id" id="compte_marchand_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                <option value="">— Choisir —</option>
                @foreach($comptes as $c)
                    <option value="{{ $c->id }}" {{ old('compte_marchand_id') == $c->id ? 'selected' : '' }}>{{ $c->nom_methode }} — {{ $c->numero_compte }}</option>
                @endforeach
            </select>
            @error('compte_marchand_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="montant" class="block text-sm font-medium text-gray-700">Montant (FDJ) *</label>
            <input type="number" name="montant" id="montant" value="{{ old('montant') }}" step="0.01" min="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            @error('montant') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="pilgrim_id" class="block text-sm font-medium text-gray-700">Pèlerin (optionnel)</label>
            <select name="pilgrim_id" id="pilgrim_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                <option value="">— Aucun / Client externe —</option>
                @foreach($pilgrims as $p)
                    <option value="{{ $p->id }}" {{ old('pilgrim_id') == $p->id ? 'selected' : '' }}>{{ $p->first_name }} {{ $p->last_name }} ({{ $p->passport_no }})</option>
                @endforeach
            </select>
            @error('pilgrim_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="client_nom" class="block text-sm font-medium text-gray-700">Nom du client (si pas de pèlerin)</label>
            <input type="text" name="client_nom" id="client_nom" value="{{ old('client_nom') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green" placeholder="Nom du payeur">
            @error('client_nom') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="statut" class="block text-sm font-medium text-gray-700">Statut *</label>
            <select name="statut" id="statut" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                <option value="en_attente" {{ old('statut', 'en_attente') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="valide" {{ old('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                <option value="refuse" {{ old('statut') == 'refuse' ? 'selected' : '' }}>Refusé</option>
            </select>
            @error('statut') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
            <input type="text" name="reference" id="reference" value="{{ old('reference') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green" placeholder="N° transaction opérateur">
            @error('reference') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">{{ old('notes') }}</textarea>
            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Enregistrer</button>
            <a href="{{ route('transaction-digitales.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Annuler</a>
        </div>
    </form>

    <p class="mt-4 text-sm text-gray-500">
        <a href="{{ route('comptabilite.index') }}" class="text-primary-green hover:underline">← Retour Comptabilité</a>
    </p>
</div>
@endsection
