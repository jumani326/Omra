@extends('layouts.app')

@section('page-title', $user->name)
@section('page-description', 'Profil utilisateur')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $user->email }}</p>
        </div>
        <div class="flex gap-3">
            @can('update', $user)
            <a href="{{ route('users.edit', $user) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Modifier</a>
            @endcan
            <a href="{{ route('users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Informations</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nom</span>
                    <span class="font-medium">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email</span>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Branche</span>
                    <span>{{ $user->branch?->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Rôle(s)</span>
                    <span>
                        @if($user->roles->isNotEmpty())
                        {{ $user->roles->pluck('name')->join(', ') }}
                        @else
                        —
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Statut</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $user->active ? 'Actif' : 'Inactif' }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Activité</h2>
            <div class="space-y-2 text-sm text-gray-600">
                <p>Pèlerins assignés : <strong>{{ $user->pilgrims()->count() }}</strong></p>
                <p>Paiements traités : <strong>{{ $user->payments()->count() }}</strong></p>
            </div>
        </div>
    </div>
</div>
@endsection
