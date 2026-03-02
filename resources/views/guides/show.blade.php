@extends('layouts.app')

@section('page-title', 'Détail du guide')
@section('page-description', 'Informations du guide et pèlerins du groupe')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $guide->user->name ?? 'Guide' }}</h1>
            <p class="text-gray-600 mt-1">{{ $guide->user->email ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            @can('update', $guide)
            <a href="{{ route('guides.edit', $guide) }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Modifier</a>
            @endcan
            <a href="{{ route('guides.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Compte</h2>
            <dl class="space-y-2">
                <div><dt class="text-sm text-gray-500">Nom</dt><dd class="font-medium">{{ $guide->user->name ?? '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Email</dt><dd class="font-medium">{{ $guide->user->email ?? '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Statut</dt><dd><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $guide->user->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $guide->user->active ? 'Actif' : 'Inactif' }}</span></dd></div>
            </dl>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Groupe assigné</h2>
            @if($guide->group)
                <p class="font-medium text-primary-green">{{ $guide->group->name }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $guide->group->pilgrims->count() }} pèlerin(s) dans ce groupe</p>
            @else
                <p class="text-gray-500">Aucun groupe assigné</p>
            @endif
        </div>
    </div>

    @if($guide->group && $guide->group->pilgrims->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <h2 class="text-lg font-semibold text-gray-900 px-6 py-4 border-b border-gray-200">Pèlerins du groupe</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pèlerin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($guide->group->pilgrims as $pilgrim)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $pilgrim->first_name }} {{ $pilgrim->last_name }}</div>
                        <div class="text-sm text-gray-500">{{ $pilgrim->passport_no ?? '—' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $pilgrim->email ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ $pilgrim->status ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('pilgrims.show', $pilgrim) }}" class="text-primary-green hover:underline">Voir</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
