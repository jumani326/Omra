@extends('layouts.app')

@section('page-title', 'Modifier le guide')
@section('page-description', 'Modifier les informations et le groupe du guide')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier le guide</h1>
            <p class="text-gray-600 mt-1">{{ $guide->user->name ?? 'Guide' }}</p>
        </div>
        <a href="{{ route('guides.show', $guide) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('guides.update', $guide) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="name" value="{{ old('name', $guide->user->name) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $guide->user->email) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                    <input type="password" name="password" minlength="8" autocomplete="new-password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Laisser vide pour ne pas modifier le mot de passe. Minimum 8 caractères.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Groupe de pèlerins</label>
                    <select name="group_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="">— Aucun groupe —</option>
                        @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id', $guide->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('group_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('guides.show', $guide) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
