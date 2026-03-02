@extends('layouts.app')

@section('page-title', 'Comptes Marchands')
@section('page-description', 'Gestion des comptes marchands digitaux (D-money, Waafi, MyCac)')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Comptes Marchands</h1>
            <p class="text-gray-600 mt-1">Configurer les numéros de réception D-money, Waafi, MyCac</p>
        </div>
        @can('create', App\Models\CompteMarchand::class)
        <a href="{{ route('compte-marchands.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">
            + Ajouter un compte marchand
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Numéro de compte</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom agence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solde</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($compteMarchands as $compte)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900">{{ $compte->nom_methode }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $compte->numero_compte }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $compte->nom_agence }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-primary-green">{{ number_format($compte->solde, 0, ',', ' ') }} FDJ</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($compte->actif)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            @can('view', $compte)
                            <a href="{{ route('compte-marchands.show', $compte) }}" class="text-primary-green hover:underline mr-3">Voir</a>
                            @endcan
                            @can('update', $compte)
                            <a href="{{ route('compte-marchands.edit', $compte) }}" class="text-blue-600 hover:underline mr-3">Modifier</a>
                            @endcan
                            @can('delete', $compte)
                            <form action="{{ route('compte-marchands.destroy', $compte) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce compte marchand ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucun compte marchand. <a href="{{ route('compte-marchands.create') }}" class="text-primary-green hover:underline">En ajouter un</a>.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($compteMarchands->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">{{ $compteMarchands->links() }}</div>
        @endif
    </div>

    <p class="text-sm text-gray-500">
        <a href="{{ route('comptabilite.index') }}" class="text-primary-green hover:underline">← Retour au tableau de bord Comptabilité</a>
    </p>
</div>
@endsection
