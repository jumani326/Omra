@extends('layouts.app')

@section('page-title', 'Profil agence')
@section('page-description', 'Consultez et modifiez les informations de votre agence et de votre compte.')

@section('content')
<div
    x-data="{ openEdit: {{ $errors->any() ? 'true' : 'false' }} }"
    class="space-y-6"
>
    @php
        $contact = $agency?->contact ?? [];
        $contactPhone = is_array($contact) ? ($contact['phone'] ?? '') : '';
        $contactEmail = is_array($contact) ? ($contact['email'] ?? '') : '';
        $contactAddress = is_array($contact) ? ($contact['address'] ?? '') : '';
    @endphp

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Profil agence</h1>
            <p class="text-gray-600 mt-1 text-sm">Informations de votre agence et de votre compte.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('agence.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Retour au dashboard
            </a>
            <button
                type="button"
                @click="openEdit = true"
                class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-[var(--gold-accent)] text-gray-900 text-sm font-semibold shadow hover:opacity-90 transition"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l9.586-9.586a2 2 0 000-2.828l-1.172-1.172a2 2 0 00-2.828 0L4 16v4z" />
                </svg>
                Modifier le profil
            </button>
        </div>
    </div>

    @if(!$agency)
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-10 text-center">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Aucune agence associée</h2>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                Votre compte n'est pas encore lié à une agence. Contactez l'administrateur pour finaliser la configuration.
            </p>
            {{-- Affichage des infos utilisateur uniquement --}}
            <div class="mt-8 text-left max-w-md mx-auto bg-gray-50 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Votre compte</h3>
                <p class="text-sm text-gray-700"><span class="font-medium">Nom :</span> {{ $user->name }}</p>
                <p class="text-sm text-gray-700 mt-1"><span class="font-medium">Email :</span> {{ $user->email }}</p>
            </div>
        </div>
    @else
        {{-- En-tête profil agence --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-100 p-6 md:p-7 flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex items-center gap-4 md:gap-6">
                    @if($agency->logo)
                        <img src="{{ Storage::url($agency->logo) }}" alt="{{ $agency->name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover border-2 border-gray-100">
                    @else
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-[var(--primary-green)] flex items-center justify-center text-white font-bold text-2xl uppercase">
                            {{ mb_substr($agency->name, 0, 2) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-poppins">{{ $agency->name }}</h1>
                        <p class="text-sm text-gray-500 mt-1">N° licence : {{ $agency->license_no }}</p>
                        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($agency->ministry_status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($agency->ministry_status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                            @else bg-red-50 text-red-700 border border-red-200
                            @endif">
                            {{ $agency->ministry_status === 'approved' ? 'Agréée' : ($agency->ministry_status === 'pending' ? 'En attente' : 'Révoquée') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 flex flex-col justify-between">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Votre compte</p>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Nom</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $user->email }}</p>
                    </div>
                    @if($branch)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Branche</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $branch->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Informations de contact de l'agence --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Contact agence</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Téléphone</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $contactPhone ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $contactEmail ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Adresse</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $contactAddress ?: '—' }}</p>
                    </div>
                </div>
            </div>
            @if($agency->branches->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Branches</h2>
                    <ul class="space-y-2">
                        @foreach($agency->branches as $b)
                            <li class="text-sm text-gray-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[var(--primary-green)]"></span>
                                {{ $b->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Modal d'édition --}}
    <div
        x-show="openEdit"
        x-cloak
        class="fixed inset-0 z-40 flex items-center justify-center px-4 py-6 bg-black/40"
    >
        <div
            @click.away="openEdit = false"
            class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl"
        >
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Modifier le profil</h2>
                    <p class="text-sm text-gray-500 mt-1">Mettez à jour vos informations et celles de votre agence.</p>
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

            <form action="{{ route('agence.profile.update') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Votre compte</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nom *</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="w-full rounded-lg border @error('name') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-green)]/70 focus:border-[var(--primary-green)]"
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email *</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-green)]/70 focus:border-[var(--primary-green)]"
                            >
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                @if($agency)
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Informations de l'agence</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Nom de l'agence *</label>
                                <input
                                    type="text"
                                    name="agency_name"
                                    value="{{ old('agency_name', $agency->name) }}"
                                    required
                                    class="w-full rounded-lg border @error('agency_name') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-green)]/70 focus:border-[var(--primary-green)]"
                                >
                                @error('agency_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Téléphone</label>
                                    <input
                                        type="text"
                                        name="contact_phone"
                                        value="{{ old('contact_phone', $contactPhone) }}"
                                        class="w-full rounded-lg border @error('contact_phone') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-green)]/70 focus:border-[var(--primary-green)]"
                                    >
                                    @error('contact_phone')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Email contact</label>
                                    <input
                                        type="email"
                                        name="contact_email"
                                        value="{{ old('contact_email', $contactEmail) }}"
                                        class="w-full rounded-lg border @error('contact_email') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-green)]/70 focus:border-[var(--primary-green)]"
                                    >
                                    @error('contact_email')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Adresse</label>
                                <textarea
                                    name="contact_address"
                                    rows="2"
                                    class="w-full rounded-lg border @error('contact_address') border-red-500 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary-green)]/70 focus:border-[var(--primary-green)]"
                                >{{ old('contact_address', $contactAddress) }}</textarea>
                                @error('contact_address')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Logo</label>
                                <input
                                    type="file"
                                    name="logo"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                                >
                                @error('logo')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                @if($agency->logo)
                                    <p class="mt-1 text-xs text-gray-500">Logo actuel affiché. Envoyez un nouveau fichier pour le remplacer.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
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
</div>
@endsection
