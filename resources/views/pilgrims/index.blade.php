@extends('layouts.app')

@section('page-title', 'Gestion des Pèlerins')
@section('page-description', 'Liste et gestion de tous les pèlerins')

@section('content')
<div class="space-y-6">
    <!-- Header avec bouton d'ajout -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pèlerins</h1>
            <p class="text-gray-600 mt-1">Gérez tous vos pèlerins</p>
        </div>
        <div class="flex gap-2">
            @can('viewAny', App\Models\Pilgrim::class)
            <a href="{{ route('pilgrims.export', request()->query()) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Export CSV
            </a>
            @endcan
            @can('create', App\Models\Pilgrim::class)
            <a href="{{ route('pilgrims.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">
                + Nouveau Pèlerin
            </a>
            @endcan
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('pilgrims.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nom, email, passeport..." 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente de validation</option>
                    <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Inscrit</option>
                    <option value="dossier_complete" {{ request('status') == 'dossier_complete' ? 'selected' : '' }}>Dossier complet</option>
                    <option value="visa_submitted" {{ request('status') == 'visa_submitted' ? 'selected' : '' }}>Visa déposé</option>
                    <option value="visa_approved" {{ request('status') == 'visa_approved' ? 'selected' : '' }}>Visa approuvé</option>
                    <option value="departed" {{ request('status') == 'departed' ? 'selected' : '' }}>Parti</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Revenu</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                <select name="group_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    @foreach($groups ?? [] as $g)
                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nationalité</label>
                <input type="text" name="nationality" value="{{ request('nationality') }}" 
                       placeholder="Ex: Maroc" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-green text-white px-4 py-2 rounded-md hover:bg-dark-green transition">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pèlerin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Forfait</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pilgrims as $pilgrim)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-green flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($pilgrim->first_name, 0, 1) . substr($pilgrim->last_name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $pilgrim->first_name }} {{ $pilgrim->last_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $pilgrim->passport_no }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $pilgrim->email ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $pilgrim->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $pilgrim->package->name ?? 'Aucun' }}</div>
                            <div class="text-sm text-gray-500">{{ $pilgrim->package->type ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($pilgrim->status == 'pending') bg-amber-100 text-amber-800
                                @elseif($pilgrim->status == 'registered') bg-yellow-100 text-yellow-800
                                @elseif($pilgrim->status == 'dossier_complete') bg-blue-100 text-blue-800
                                @elseif($pilgrim->status == 'visa_approved') bg-green-100 text-green-800
                                @elseif($pilgrim->status == 'departed') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $pilgrim->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pilgrim->agent->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('pilgrims.show', $pilgrim) }}" class="text-primary-green hover:text-dark-green">Voir</a>
                                @can('update', $pilgrim)
                                <a href="{{ route('pilgrims.edit', $pilgrim) }}" class="text-blue-600 hover:text-blue-900">Modifier</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Aucun pèlerin trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $pilgrims->links() }}
        </div>
    </div>
</div>
@endsection


