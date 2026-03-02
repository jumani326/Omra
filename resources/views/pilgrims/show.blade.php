@extends('layouts.app')

@section('page-title', $pilgrim->first_name . ' ' . $pilgrim->last_name)
@section('page-description', 'Détails du pèlerin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $pilgrim->first_name }} {{ $pilgrim->last_name }}</h1>
            <p class="text-gray-600 mt-1">Détails et historique</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($pilgrim->status === 'pending' && $pilgrim->package)
                @can('update', $pilgrim->package)
                <form action="{{ route('packages.applications.approve', ['package' => $pilgrim->package, 'pilgrim' => $pilgrim]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">
                        Accepter la candidature
                    </button>
                </form>
                @endcan
            @endif
            @can('create', App\Models\Visa::class)
            <a href="{{ route('visas.create', ['pilgrim_id' => $pilgrim->id]) }}" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">
                + Dossier visa
            </a>
            @endcan
            @can('create', App\Models\Payment::class)
            <a href="{{ route('payments.create', ['pilgrim_id' => $pilgrim->id]) }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                + Paiement
            </a>
            @endcan
            @can('update', $pilgrim)
            <a href="{{ route('pilgrims.edit', $pilgrim) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Modifier
            </a>
            @endcan
            <a href="{{ route('pilgrims.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informations Personnelles</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Prénom</p>
                        <p class="font-medium">{{ $pilgrim->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nom</p>
                        <p class="font-medium">{{ $pilgrim->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $pilgrim->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="font-medium">{{ $pilgrim->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Passeport</p>
                        <p class="font-medium">{{ $pilgrim->passport_no }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nationalité</p>
                        <p class="font-medium">{{ $pilgrim->nationality }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Statut</p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($pilgrim->status == 'pending') bg-amber-100 text-amber-800
                            @elseif($pilgrim->status == 'registered') bg-yellow-100 text-yellow-800
                            @elseif($pilgrim->status == 'dossier_complete') bg-blue-100 text-blue-800
                            @elseif($pilgrim->status == 'visa_approved') bg-green-100 text-green-800
                            @elseif($pilgrim->status == 'departed') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $pilgrim->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Forfait -->
            @if($pilgrim->package)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Forfait Assigné</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nom</p>
                        <p class="font-medium">{{ $pilgrim->package->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="font-medium">{{ ucfirst($pilgrim->package->type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date de départ</p>
                        <p class="font-medium">{{ $pilgrim->package->departure_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date de retour</p>
                        <p class="font-medium">{{ $pilgrim->package->return_date->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Documents -->
            @if($pilgrim->documents->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($pilgrim->documents as $document)
                    <div class="border rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $document->type)) }}</p>
                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" 
                           class="text-sm text-primary-green hover:underline mt-2 inline-block">
                            Voir le document
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @auth
        @if(auth()->user()->hasRole('Super Admin Agence') && $branches->count() > 1)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Transférer vers une autre branche</h2>
            <form action="{{ route('pilgrims.transfer', $pilgrim) }}" method="POST" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle branche</label>
                    <select name="branch_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $pilgrim->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition">Transférer</button>
            </form>
        </div>
        @endif
        @endauth

        <!-- Visa & Paiements -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Visa & Paiements</h2>
            <div class="space-y-3">
                @if($pilgrim->visa)
                <p class="text-sm text-gray-600">Visa : <a href="{{ route('visas.show', $pilgrim->visa) }}" class="text-primary-green hover:underline font-medium">{{ $pilgrim->visa->reference_no ?? 'Dossier #' . $pilgrim->visa->id }}</a> — {{ ucfirst(str_replace('_', ' ', $pilgrim->visa->status)) }}</p>
                @else
                @can('create', App\Models\Visa::class)
                <a href="{{ route('visas.create', ['pilgrim_id' => $pilgrim->id]) }}" class="block text-sm text-primary-green hover:underline">+ Créer un dossier visa</a>
                @endcan
                @endif
                @if($pilgrim->payments->count() > 0)
                <p class="text-sm text-gray-600">{{ $pilgrim->payments->count() }} paiement(s) — Total : <strong>{{ number_format($pilgrim->payments->where('status', 'completed')->sum('amount'), 0) }} FDJ</strong></p>
                <a href="{{ route('payments.index', ['pilgrim_id' => $pilgrim->id]) }}" class="text-sm text-primary-green hover:underline">Voir les paiements</a>
                @endif
                @can('create', App\Models\Payment::class)
                <a href="{{ route('payments.create', ['pilgrim_id' => $pilgrim->id]) }}" class="block text-sm text-primary-green hover:underline">+ Enregistrer un paiement</a>
                @endcan
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Timeline d'Activité</h2>
            <div class="space-y-4">
                @forelse($pilgrim->activityLogs()->orderBy('created_at', 'desc')->get() as $log)
                <div class="border-l-2 border-primary-green pl-4 pb-4">
                    <p class="text-sm font-medium text-gray-900">{{ $log->description }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $log->user->name ?? 'Système' }} - {{ $log->created_at->diffForHumans() }}
                    </p>
                </div>
                @empty
                <p class="text-sm text-gray-500">Aucune activité enregistrée</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

