@extends('layouts.app')

@section('page-title', 'Utilisateurs')
@section('page-description', 'Gestion des comptes utilisateurs')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
            <p class="text-gray-600 mt-1">Gérez les comptes et rôles</p>
        </div>
        @can('create', App\Models\User::class)
        <a href="{{ route('users.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">+ Nouvel utilisateur</a>
        @endcan
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                <select name="role" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    @foreach($roles as $name => $label)
                    <option value="{{ $name }}" {{ request('role') == $name ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-green text-white px-4 py-2 rounded-md hover:bg-dark-green transition">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branche</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $u->name }}</div>
                        <div class="text-sm text-gray-500">{{ $u->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $u->branch?->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($u->roles->isNotEmpty())
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary-green/10 text-primary-green">{{ $u->roles->first()->name }}</span>
                        @else
                        —
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $u->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $u->active ? 'Actif' : 'Inactif' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        @can('view', $u)
                        <a href="{{ route('users.show', $u) }}" class="text-primary-green hover:underline">Voir</a>
                        @endcan
                        @can('update', $u)
                        <a href="{{ route('users.edit', $u) }}" class="ml-3 text-blue-600 hover:underline">Modifier</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun utilisateur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
