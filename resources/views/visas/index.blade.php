@extends('layouts.app')

@section('page-title', 'Gestion des Visas')
@section('page-description', 'Suivi des dossiers visa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Visas</h1>
            <p class="text-gray-600 mt-1">Suivi des dossiers visa par pèlerin</p>
        </div>
        @can('create', App\Models\Visa::class)
        <a href="{{ route('visas.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">
            + Nouveau dossier visa
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('visas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche (pèlerin)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, passeport, email..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    <option value="not_submitted" {{ request('status') == 'not_submitted' ? 'selected' : '' }}>Non soumis</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Soumis</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                    <option value="refused" {{ request('status') == 'refused' ? 'selected' : '' }}>Refusé</option>
                </select>
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="expiring_soon" value="1" {{ request('expiring_soon') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Expire dans 30 jours</span>
                </label>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-green text-white px-4 py-2 rounded-md hover:bg-dark-green transition">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pèlerin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Soumis le</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($visas as $visa)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $visa->pilgrim->first_name }} {{ $visa->pilgrim->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $visa->pilgrim->passport_no }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $visa->reference_no ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($visa->status) {
                                    'approved' => 'bg-green-100 text-green-800',
                                    'refused' => 'bg-red-100 text-red-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'submitted' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $visa->status)) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $visa->submitted_at ? $visa->submitted_at->format('d/m/Y') : '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $visa->expiry_date ? $visa->expiry_date->format('d/m/Y') : '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            @can('view', $visa)
                            <a href="{{ route('visas.show', $visa) }}" class="text-primary-green hover:underline">Voir</a>
                            @endcan
                            @can('update', $visa)
                            <a href="{{ route('visas.edit', $visa) }}" class="ml-3 text-blue-600 hover:underline">Modifier</a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucun dossier visa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($visas->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">{{ $visas->links() }}</div>
        @endif
    </div>
</div>
@endsection
