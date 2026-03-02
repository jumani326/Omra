@extends('layouts.app')

@section('page-title', 'Nouveau groupe')
@section('page-description', 'Créer un groupe de pèlerins à assigner à un guide')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau groupe</h1>
            <p class="text-gray-600 mt-1">Donnez un nom au groupe (ex : Groupe 1, Départ mars 2026)</p>
        </div>
        <a href="{{ route('groups.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-md">
        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du groupe *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex : Groupe 1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('groups.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Créer le groupe</button>
            </div>
        </form>
    </div>
</div>
@endsection
