@extends('layouts.app')

@section('page-title', $group->name)
@section('page-description', 'Pèlerins du groupe et envoi de la liste au guide')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h1>
            <p class="text-gray-600 mt-1">
                @if($group->guide && $group->guide->user)
                    Guide assigné : {{ $group->guide->user->name }} ({{ $group->guide->user->email }})
                @else
                    Aucun guide assigné à ce groupe. <a href="{{ route('guides.index') }}" class="text-primary-green hover:underline">Assigner un guide</a>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            @if($group->guide && $group->guide->user)
            <form action="{{ route('groups.send-list-to-guide', $group) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg font-medium text-white transition shadow-md hover:opacity-90" style="background-color: #0F3F2E;">
                    Envoyer la liste des pèlerins au guide par email
                </button>
            </form>
            @endif
            <a href="{{ route('groups.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour aux groupes</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Ajouter des pèlerins existants à ce groupe</h2>
        <p class="text-gray-600 text-sm mb-4">Le système gère à la fois les pèlerins que l'agence crée (ex. personnes en âge avancé) et ceux qui s'inscrivent eux-mêmes sur la plateforme. Sélectionnez ci-dessous les pèlerins à ajouter à ce groupe.</p>

        @if($pilgrimsNotInGroup->isEmpty())
            <p class="text-sm text-gray-500">Aucun pèlerin disponible à ajouter : tous vos pèlerins sont déjà dans ce groupe ou vous n'avez pas encore de pèlerins.</p>
            <p class="text-sm mt-2">
                <a href="{{ route('pilgrims.create') }}" class="text-primary-green hover:underline">Créer un pèlerin</a>
                ·
                <a href="{{ route('pilgrims.index') }}" class="text-primary-green hover:underline">Voir tous les pèlerins</a>
            </p>
        @else
            <form action="{{ route('groups.add-pilgrims', $group) }}" method="POST">
                @csrf
                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3 mb-3">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="text-left py-2 px-2 w-10"><input type="checkbox" id="select-all-not-in-group" class="rounded border-gray-300 text-primary-green"></th>
                                <th class="text-left py-2 px-2">Pèlerin</th>
                                <th class="text-left py-2 px-2">Contact</th>
                                <th class="text-left py-2 px-2">Groupe actuel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pilgrimsNotInGroup as $p)
                            <tr class="border-t border-gray-100 hover:bg-gray-50">
                                <td class="py-2 px-2">
                                    <input type="checkbox" name="pilgrim_ids[]" value="{{ $p->id }}" class="pilgrim-checkbox rounded border-gray-300 text-primary-green">
                                </td>
                                <td class="py-2 px-2 font-medium">{{ $p->first_name }} {{ $p->last_name }}</td>
                                <td class="py-2 px-2">{{ $p->email ?? '—' }} · {{ $p->phone ?? '—' }}</td>
                                <td class="py-2 px-2 text-gray-500">{{ $p->group?->name ?? 'Aucun' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Ajouter les pèlerins sélectionnés au groupe</button>
            </form>
        @endif
        <p class="text-sm text-gray-500 mt-4">
            <a href="{{ route('pilgrims.index') }}?group_id={{ $group->id }}" class="text-primary-green hover:underline">Voir les pèlerins de ce groupe</a>
            ·
            <a href="{{ route('pilgrims.create') }}" class="text-primary-green hover:underline">Nouveau pèlerin</a>
        </p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <h2 class="text-lg font-semibold text-gray-900 px-6 py-4 border-b border-gray-200">Pèlerins du groupe ({{ $group->pilgrims->count() }})</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pèlerin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Passeport</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($group->pilgrims as $pilgrim)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $pilgrim->first_name }} {{ $pilgrim->last_name }}</div>
                        <div class="text-sm text-gray-500">{{ $pilgrim->nationality ?? '—' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div>{{ $pilgrim->email ?? '—' }}</div>
                        <div class="text-gray-500">{{ $pilgrim->phone ?? '—' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $pilgrim->passport_no ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('pilgrims.edit', $pilgrim) }}" class="text-primary-green hover:underline">Modifier</a>
                        <form action="{{ route('groups.remove-pilgrim', $group) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Retirer ce pèlerin du groupe ?');">
                            @csrf
                            <input type="hidden" name="pilgrim_id" value="{{ $pilgrim->id }}">
                            <button type="submit" class="text-red-600 hover:underline text-sm">Retirer du groupe</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun pèlerin dans ce groupe. Ajoutez-en en créant ou modifiant un pèlerin et en sélectionnant ce groupe.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($pilgrimsNotInGroup->isNotEmpty())
<script>
    document.getElementById('select-all-not-in-group')?.addEventListener('change', function() {
        document.querySelectorAll('.pilgrim-checkbox').forEach(function(cb) { cb.checked = this.checked; }.bind(this));
    });
</script>
@endif
@endsection
