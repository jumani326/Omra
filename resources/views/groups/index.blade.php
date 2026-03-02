@extends('layouts.app')

@section('page-title', 'Groupes de pèlerins')
@section('page-description', 'Créez des groupes pour les assigner aux guides')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Groupes de pèlerins</h1>
            <p class="text-gray-600 mt-1">Créez des groupes, puis assignez-les à vos guides lors de la création ou modification d'un guide</p>
        </div>
        @can('create', App\Models\Group::class)
        <a href="{{ route('groups.create') }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">+ Nouveau groupe</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom du groupe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre de pèlerins</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($groups as $group)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <a href="{{ route('groups.show', $group) }}" class="text-primary-green hover:underline">{{ $group->name }}</a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $group->pilgrims_count ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-8 text-center text-gray-500">Aucun groupe. Créez un groupe pour pouvoir l'assigner à un guide.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-sm text-gray-500">
        <a href="{{ route('guides.index') }}" class="text-primary-green hover:underline">Retour aux guides</a>
    </p>
</div>
@endsection
