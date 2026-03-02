@extends('layouts.app')

@section('page-title', 'Guides')
@section('page-description', 'Gestion des guides et attribution des groupes de pèlerins')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Guides</h1>
            <p class="text-gray-600 mt-1">Créez des guides avec email et mot de passe, et assignez-les à un groupe de pèlerins</p>
        </div>
        @can('create', App\Models\Guide::class)
        <a href="{{ route('guides.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">+ Nouveau guide</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('guides.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                <select name="group_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    @foreach($groups as $g)
                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guide</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groupe assigné</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($guides as $guide)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $guide->user->name ?? '—' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $guide->user->email ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($guide->group)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary-green/10 text-primary-green">{{ $guide->group->name }}</span>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        @can('view', $guide)
                        <a href="{{ route('guides.show', $guide) }}" class="text-primary-green hover:underline">Voir</a>
                        @endcan
                        @can('update', $guide)
                        <a href="{{ route('guides.edit', $guide) }}" class="ml-3 text-blue-600 hover:underline">Modifier</a>
                        @endcan
                        @can('delete', $guide)
                        <form action="{{ route('guides.destroy', $guide) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Supprimer ce guide et son compte ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun guide. Créez un guide pour lui attribuer un groupe de pèlerins.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($guides->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">{{ $guides->links() }}</div>
        @endif
    </div>
</div>
@endsection
