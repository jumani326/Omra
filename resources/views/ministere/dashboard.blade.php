@extends('layouts.app')

@section('page-title', 'Dashboard Ministère')
@push('styles')
<style>
    /* Palette dashboard ministère */
    .chart-colors-primary { background-color: #0F3F2E; }
    .chart-colors-success { background-color: #22C55E; }
    .chart-colors-warning { background-color: #F59E0B; }
    .chart-colors-danger  { background-color: #EF4444; }
    .chart-colors-dark    { background-color: #0B2C21; }
</style>
@endpush

@section('content')
<div class="space-y-8">
    {{-- En-tête --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard Ministère</h1>
        <p class="mt-1 text-gray-600">Supervision et statistiques globales.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif

    {{-- Cartes KPI avec style amélioré --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Agences</p>
                    <p class="mt-1 text-3xl font-bold" style="color: #0F3F2E;">{{ number_format($totalAgencies ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(15, 63, 46, 0.1);">
                    <svg class="w-6 h-6" style="color: #0F3F2E;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Agences actives</p>
                    <p class="mt-1 text-3xl font-bold text-green-600">{{ number_format($activeAgencies ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Visas en attente</p>
                    <p class="mt-1 text-3xl font-bold text-amber-600">{{ number_format($visasEnAttente ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques : ligne avec donut statuts + barres pèlerins --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Répartition des agences par statut</h2>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-full max-w-[220px] h-[220px] mx-auto">
                    <canvas id="chartAgenciesStatus"></canvas>
                </div>
                <div class="flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span>Validées : <strong>{{ $activeAgencies ?? 0 }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span>En attente : <strong>{{ $inactiveAgencies ?? 0 }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Pèlerins par agence</h2>
            <div class="h-[260px]">
                <canvas id="chartPilgrimsByAgency"></canvas>
            </div>
        </div>
    </div>

    {{-- Tableau agences --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Agences (validation / suspension)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agence</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Licence</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pèlerins</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($agencies ?? [] as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $a->name }}</td>
                        <td class="px-4 py-3 text-gray-600 text-sm">{{ $a->license_no }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $a->pilgrims_count }}</td>
                        <td class="px-4 py-3">
                            @if($a->validated)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Validée</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">En attente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if(!$a->validated)
                                <form action="{{ route('ministere.agencies.validate', $a) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-700 font-medium text-sm">Valider</button>
                                </form>
                            @else
                                <form action="{{ route('ministere.agencies.suspend', $a) }}" method="POST" class="inline" onsubmit="return confirm('Suspendre cette agence ?');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm">Suspendre</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucune agence.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    const primaryGreen = '#0F3F2E';
    const successGreen = '#22C55E';
    const warningAmber = '#F59E0B';
    const dangerRed = '#EF4444';
    const darkGreen = '#0B2C21';

    // Donut : Agences par statut
    const ctxStatus = document.getElementById('chartAgenciesStatus');
    if (ctxStatus) {
        const active = {{ (int) ($activeAgencies ?? 0) }};
        const inactive = {{ (int) ($inactiveAgencies ?? 0) }};
        const total = active + inactive;
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: total === 0 ? ['Aucune agence'] : ['Validées', 'En attente'],
                datasets: [{
                    data: total === 0 ? [1] : [active, inactive],
                    backgroundColor: total === 0 ? ['#E5E7EB'] : [successGreen, warningAmber],
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                cutout: '60%'
            }
        });
    }

    // Barres : Pèlerins par agence
    const ctxPilgrims = document.getElementById('chartPilgrimsByAgency');
    if (ctxPilgrims) {
        const labels = @json(collect($pilgrimsByAgency ?? [])->pluck('agency')->toArray());
        const data = @json(collect($pilgrimsByAgency ?? [])->pluck('total')->map(fn ($v) => (int) $v)->toArray());
        new Chart(ctxPilgrims, {
            type: 'bar',
            data: {
                labels: labels.length ? labels : ['Aucune agence'],
                datasets: [{
                    label: 'Pèlerins',
                    data: data.length ? data : [0],
                    backgroundColor: primaryGreen,
                    borderColor: darkGreen,
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: darkGreen
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0,0,0,0.06)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
@endsection
