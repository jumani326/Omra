@extends('layouts.app')

@section('page-title', 'Dashboard Ministère')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Ministère</h1>
    <p class="text-gray-600">Supervision et statistiques globales.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Agences</p>
            <p class="text-2xl font-bold text-primary-green">{{ number_format($totalAgencies ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Agences actives</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($activeAgencies ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Visas en attente</p>
            <p class="text-2xl font-bold text-amber-600">{{ number_format($visasEnAttente ?? 0) }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold text-gray-900 mb-4">Agences (validation / suspension)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Agence</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Licence</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pèlerins</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($agencies ?? [] as $a)
                    <tr>
                        <td class="px-4 py-2">{{ $a->name }}</td>
                        <td class="px-4 py-2">{{ $a->license_no }}</td>
                        <td class="px-4 py-2">{{ $a->pilgrims_count }}</td>
                        <td class="px-4 py-2">
                            @if($a->validated)
                                <span class="text-green-600 font-medium">Validée</span>
                            @else
                                <span class="text-amber-600 font-medium">En attente</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if(!$a->validated)
                                <form action="{{ route('ministere.agencies.validate', $a) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:underline text-sm">Valider</button>
                                </form>
                            @else
                                <form action="{{ route('ministere.agencies.suspend', $a) }}" method="POST" class="inline" onsubmit="return confirm('Suspendre cette agence ?');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:underline text-sm">Suspendre</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-4 text-gray-500">Aucune agence.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold text-gray-900 mb-4">Pèlerins par agence</h2>
        @forelse($pilgrimsByAgency ?? [] as $row)
            <p class="text-sm">{{ $row['agency'] }} — {{ $row['total'] }} pèlerins</p>
        @empty
            <p class="text-gray-500">Aucune donnée.</p>
        @endforelse
    </div>
</div>
@endsection
