@extends('layouts.app')

@section('page-title', 'Mon profil')
@section('page-description', 'Gérez vos informations personnelles et vos préférences de voyage.')

@section('content')
<div
    x-data="{ openEdit: {{ $errors->any() ? 'true' : 'false' }} }"
    class="space-y-6"
>
    @php
        $hasPilgrim = isset($pilgrim) && $pilgrim;
        $fullName = $hasPilgrim ? trim($pilgrim->first_name . ' ' . $pilgrim->last_name) : auth()->user()->name;
        $initials = collect(explode(' ', $fullName))->filter()->map(fn($part) => mb_substr($part, 0, 1))->join('');
        $memberSince = $hasPilgrim && $pilgrim->created_at ? $pilgrim->created_at->translatedFormat('M Y') : (auth()->user()->created_at?->translatedFormat('M Y') ?? '—');
        $locationLabel = $hasPilgrim && $pilgrim->nationality ? $pilgrim->nationality : 'Pèlerin';
        $isActivePilgrim = $hasPilgrim && in_array($pilgrim->status, ['registered', 'dossier_complete', 'visa_submitted', 'visa_approved', 'departed', 'returned']);
        $tripsCount = $hasPilgrim && $pilgrim->package ? 1 : 0;
        $statusKey = $hasPilgrim ? $pilgrim->status : null;
        $statusLabel = $statusKey
            ? ucfirst(str_replace('_', ' ', $statusKey))
            : 'Profil incomplet';
        $isReady = $hasPilgrim && in_array($pilgrim->status, ['visa_approved', 'departed', 'returned']);
        $documents = $hasPilgrim ? $pilgrim->documents()->latest()->take(4)->get() : collect();
    @endphp

        {{-- Message de succès --}}
        @if(session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Barre de recherche / actions (haut de page) --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="w-full md:max-w-xl relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
            </span>
            <input
                type="text"
                placeholder="Rechercher des ressources..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-green/70 focus:border-primary-green bg-white shadow-sm"
            >
        </div>
        @if($hasPilgrim)
            <button
                type="button"
                @click="openEdit = true"
                class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-[var(--gold-accent)] text-gray-900 text-sm font-semibold shadow hover:opacity-90 transition"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l9.586-9.586a2 2 0 000-2.828l-1.172-1.172a2 2 0 00-2.828 0L4 16v4z" />
                </svg>
                Modifier mon profil
            </button>
        @endif
    </div>

    @if(!$hasPilgrim)
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-10 text-center">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Complétez votre profil</h2>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                Nous n’avons pas encore trouvé de dossier pèlerin associé à votre compte. Contactez votre agence pour finaliser votre inscription.
            </p>
        </div>
    @else
        {{-- En-tête profil --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Carte principale du profil --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-100 p-6 md:p-7 flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex items-center gap-4 md:gap-6">
                    <div class="relative">
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-primary-green flex items-center justify-center text-white font-bold text-2xl uppercase">
                            {{ $initials }}
                        </div>
                        <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-green-500 ring-2 ring-white"></span>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-poppins">{{ $fullName }}</h1>
                        <p class="text-sm text-gray-500 mt-1 flex flex-wrap items-center gap-1.5">
                            <span>Membre depuis {{ $memberSince }}</span>
                            <span class="text-gray-300">•</span>
                            <span>{{ $locationLabel }}</span>
                        </p>
                        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            {{ $isActivePilgrim ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            <span class="w-2 h-2 rounded-full mr-2 {{ $isActivePilgrim ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                            {{ $isActivePilgrim ? 'Pèlerin actif' : 'En cours de validation' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Carte stats rapides --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Résumé</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        {{ $isReady ? 'Prêt' : 'En préparation' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Voyages</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tripsCount }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Statut</p>
                        <p class="mt-1 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                            @if($statusKey === 'pending')
                                bg-amber-50 text-amber-800 border border-amber-200
                            @elseif(in_array($statusKey, ['registered', 'dossier_complete', 'visa_submitted']))
                                bg-blue-50 text-blue-800 border border-blue-200
                            @elseif(in_array($statusKey, ['visa_approved', 'departed', 'returned']))
                                bg-emerald-50 text-emerald-800 border border-emerald-200
                            @else
                                bg-gray-50 text-gray-700 border border-gray-200
                            @endif">
                            {{ $statusLabel }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informations personnelles + Contact --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Informations personnelles --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Informations personnelles</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Nom complet</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $fullName }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Date de naissance</p>
                        <p class="mt-1 text-sm text-gray-700">Non renseignée</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Nationalité</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $pilgrim->nationality ?? 'Non renseignée' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Numéro de passeport</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $pilgrim->passport_no ?? 'Non renseigné' }}</p>
                    </div>
                </div>
            </div>

            {{-- Coordonnées --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Coordonnées</h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Adresse e-mail</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $pilgrim->email ?? auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Téléphone</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $pilgrim->phone ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Contact d'urgence</p>
                        <p class="mt-1 text-sm text-gray-700">À compléter avec votre agence</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Préférences de voyage --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Préférences de voyage</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Régime alimentaire</p>
                        <p class="mt-0.5 text-sm text-gray-700">Halal (par défaut)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Chambre</p>
                        <p class="mt-0.5 text-sm text-gray-700">Double ou à définir</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Assistance médicale</p>
                        <p class="mt-0.5 text-sm text-gray-700">À préciser si besoin</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Préférences supplémentaires</p>
                        <p class="mt-0.5 text-sm text-gray-700">À communiquer à votre agence</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documents récents --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Documents récents</h2>
                @if($documents->isNotEmpty())
                    <a href="{{ route('pelerin.dashboard') }}#documents" class="text-xs font-medium text-primary-green hover:underline">
                        Voir tous les documents
                    </a>
                @endif
            </div>

            @if($documents->isEmpty())
                <p class="text-sm text-gray-500">Aucun document n’a encore été ajouté à votre dossier.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($documents as $document)
                        <div class="border border-gray-200 rounded-xl p-4 flex flex-col justify-between bg-gray-50/60">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white text-primary-green flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h8l4 4v14H7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v5h5" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $document->type)) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Ajouté le {{ $document->created_at?->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Vérifié
                                </span>
                                <a
                                    href="{{ Storage::url($document->file_path) }}"
                                    target="_blank"
                                    class="text-xs font-medium text-primary-green hover:underline"
                                >
                                    Ouvrir
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Modal d’édition du profil --}}
    @if($hasPilgrim)
        <div
            x-show="openEdit"
            x-cloak
            class="fixed inset-0 z-40 flex items-center justify-center px-4 py-6 bg-black/40"
        >
            <div
                @click.away="openEdit = false"
                class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Modifier votre profil</h2>
                        <p class="text-sm text-gray-500 mt-1">Mettez à jour vos informations personnelles pour une meilleure expérience de voyage.</p>
                    </div>
                    <button
                        type="button"
                        class="p-1.5 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                        @click="openEdit = false"
                        aria-label="Fermer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 pt-5 pb-2 border-b border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-primary-green text-white flex items-center justify-center font-semibold text-lg uppercase">
                        {{ $initials }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $fullName }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Photo de profil gérée par votre agence.</p>
                    </div>
                </div>

                <form action="{{ route('pelerin.profile.update') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Prénom *</label>
                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name', $pilgrim->first_name) }}"
                                class="w-full rounded-lg border @error('first_name') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-green/70 focus:border-primary-green"
                                required
                            >
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nom *</label>
                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name', $pilgrim->last_name) }}"
                                class="w-full rounded-lg border @error('last_name') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-green/70 focus:border-primary-green"
                                required
                            >
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Adresse e-mail *</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $pilgrim->email ?? auth()->user()->email) }}"
                                class="w-full rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-green/70 focus:border-primary-green"
                                required
                            >
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Téléphone *</label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone', $pilgrim->phone) }}"
                                class="w-full rounded-lg border @error('phone') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-green/70 focus:border-primary-green"
                                required
                            >
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nationalité *</label>
                            <input
                                type="text"
                                name="nationality"
                                value="{{ old('nationality', $pilgrim->nationality) }}"
                                class="w-full rounded-lg border @error('nationality') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-green/70 focus:border-primary-green"
                                required
                            >
                            @error('nationality')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Numéro de passeport</label>
                            <input
                                type="text"
                                name="passport_no"
                                value="{{ old('passport_no', $pilgrim->passport_no) }}"
                                class="w-full rounded-lg border @error('passport_no') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-green/70 focus:border-primary-green"
                            >
                            @error('passport_no')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Zone d’upload des documents --}}
                    <div class="mt-4 pt-4 border-t border-dashed border-gray-200">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Ajouter des documents</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Passeport (PDF / Image)</label>
                                <input
                                    type="file"
                                    name="documents[passport]"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                                >
                                @error('documents.passport')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Photo (JPEG / PNG)</label>
                                <input
                                    type="file"
                                    name="documents[photo]"
                                    accept=".jpg,.jpeg,.png"
                                    class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                                >
                                @error('documents.photo')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Certificat médical</label>
                                <input
                                    type="file"
                                    name="documents[medical_certificate]"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                                >
                                @error('documents.medical_certificate')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="mt-2 text-[11px] text-gray-500">
                            Formats acceptés : PDF, JPG, JPEG, PNG. Taille max 5&nbsp;Mo par document.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 mt-4">
                        <button
                            type="button"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            @click="openEdit = false"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-5 py-2.5 rounded-lg bg-[var(--gold-accent)] text-sm font-semibold text-gray-900 shadow hover:opacity-90"
                        >
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
