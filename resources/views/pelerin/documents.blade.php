@extends('layouts.app')

@section('page-title', 'Mes documents')
@section('page-description', 'Déposez et consultez vos documents pour l\'agence')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mes documents</h1>
        <p class="text-gray-600 mt-1">Déposez ici vos pièces pour l'agence (passeport, photo, certificat médical).</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Dépôt des documents pour l'agence --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Mes documents pour l'agence</h2>
            <p class="text-xs text-gray-600 mb-4">
                Téléversez ici votre passeport, votre photo et votre certificat médical. L'agence utilisera ces pièces pour déposer votre demande de visa.
            </p>
            <form action="{{ route('pelerin.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Passeport</label>
                        <input type="file" name="documents[passport]" accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        @error('documents.passport')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @if($passportDoc ?? null)
                            <p class="mt-1 text-[11px] text-emerald-700">
                                Dernier reçu le {{ $passportDoc->uploaded_at?->format('d/m/Y') }} –
                                <a href="{{ Storage::url($passportDoc->file_path) }}" target="_blank" class="underline">voir</a>
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Photo</label>
                        <input type="file" name="documents[photo]" accept=".jpg,.jpeg,.png"
                               class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        @error('documents.photo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @if($photoDoc ?? null)
                            <p class="mt-1 text-[11px] text-emerald-700">
                                Dernière reçue le {{ $photoDoc->uploaded_at?->format('d/m/Y') }} –
                                <a href="{{ Storage::url($photoDoc->file_path) }}" target="_blank" class="underline">voir</a>
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Certificat médical</label>
                        <input type="file" name="documents[medical_certificate]" accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        @error('documents.medical_certificate')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @if($medicalDoc ?? null)
                            <p class="mt-1 text-[11px] text-emerald-700">
                                Dernier reçu le {{ $medicalDoc->uploaded_at?->format('d/m/Y') }} –
                                <a href="{{ Storage::url($medicalDoc->file_path) }}" target="_blank" class="underline">voir</a>
                            </p>
                        @endif
                    </div>
                </div>
                <p class="text-[11px] text-gray-500">
                    Formats acceptés : PDF, JPG, JPEG, PNG. Taille max 5 Mo par document.
                </p>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary-green text-white text-xs font-semibold hover:bg-dark-green transition">
                        Envoyer les documents à l'agence
                    </button>
                </div>
            </form>
        </div>

        {{-- Documents de voyage (informations) --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Documents de voyage</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="border rounded-lg p-3 text-center">
                    <p class="text-sm font-medium text-gray-900">Visa</p>
                    <p class="text-xs text-gray-500">
                        @if($pilgrim->visa && $pilgrim->visa->status === 'approved')
                            Prêt (approuvé par l'agence)
                        @else
                            En attente de traitement
                        @endif
                    </p>
                </div>
                <div class="border rounded-lg p-3 text-center">
                    <p class="text-sm font-medium text-gray-900">Billet</p>
                    <p class="text-xs text-gray-500">Sur demande auprès de votre agence</p>
                </div>
                <div class="border rounded-lg p-3 text-center">
                    <p class="text-sm font-medium text-gray-900">Badge groupe</p>
                    <p class="text-xs text-gray-500">Fourni avant le départ</p>
                </div>
                <div class="border rounded-lg p-3 text-center">
                    <p class="text-sm font-medium text-gray-900">Contrat</p>
                    <p class="text-xs text-gray-500">Signé avec votre agence</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
