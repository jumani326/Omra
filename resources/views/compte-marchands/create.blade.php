@extends('layouts.app')

@section('page-title', 'Ajouter un compte marchand')
@section('page-description', 'Configurer un numéro de réception D-money, Waafi ou MyCac')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('compte-marchands.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf

        <div>
            <label for="nom_methode" class="block text-sm font-medium text-gray-700">Méthode *</label>
            <select name="nom_methode" id="nom_methode" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                @foreach(\App\Models\CompteMarchand::METHODES as $m)
                    <option value="{{ $m }}" {{ old('nom_methode', request('nom_methode')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            @error('nom_methode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="numero_compte" class="block text-sm font-medium text-gray-700">Numéro de compte *</label>
            <input type="text" name="numero_compte" id="numero_compte" value="{{ old('numero_compte') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green" placeholder="Ex: 77 123 45 67">
            @error('numero_compte') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nom_agence" class="block text-sm font-medium text-gray-700">Nom de l'agence *</label>
            <input type="text" name="nom_agence" id="nom_agence" value="{{ old('nom_agence', auth()->user()->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            @error('nom_agence') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @if(auth()->user()->hasRole('Super Admin Agence'))
        <div>
            <label for="branch_id" class="block text-sm font-medium text-gray-700">Branche</label>
            <select name="branch_id" id="branch_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                <option value="">— Toutes les branches —</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
            @error('branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        @endif

        <div>
            <label for="solde" class="block text-sm font-medium text-gray-700">Solde initial (FDJ)</label>
            <input type="number" name="solde" id="solde" value="{{ old('solde', 0) }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            @error('solde') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="actif" id="actif" value="1" {{ old('actif', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-green focus:ring-primary-green">
            <label for="actif" class="ml-2 text-sm text-gray-700">Compte actif</label>
        </div>
        @error('actif') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Enregistrer</button>
            <a href="{{ route('compte-marchands.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Annuler</a>
        </div>
    </form>

    <p class="mt-4 text-sm text-gray-500">
        <a href="{{ route('comptabilite.index') }}" class="text-primary-green hover:underline">← Retour Comptabilité</a>
    </p>
</div>
@endsection
