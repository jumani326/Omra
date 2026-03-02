@extends('layouts.app')

@section('page-title', 'Nouveau guide')
@section('page-description', 'Créer un guide avec email et mot de passe, et lui attribuer un groupe')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau guide</h1>
            <p class="text-gray-600 mt-1">Le guide pourra se connecter avec l'email et le mot de passe définis ci-dessous</p>
        </div>
        <a href="{{ route('guides.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('guides.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('email') border-red-500 @enderror" placeholder="exemple@domaine.com">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
                    <input type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe *</label>
                    <input type="password" name="password_confirmation" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Groupe de pèlerins</label>
                    <select name="group_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="">— Aucun groupe pour l'instant —</option>
                        @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('group_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @if($groups->isEmpty())
                    <p class="mt-1 text-sm text-gray-500">Aucun groupe ? <a href="{{ route('groups.create') }}" class="text-primary-green hover:underline">Créer un groupe</a></p>
                    @endif
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('guides.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Créer le guide</button>
            </div>
        </form>
    </div>
</div>
@endsection
