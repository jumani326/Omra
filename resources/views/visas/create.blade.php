@extends('layouts.app')

@section('page-title', 'Nouveau dossier visa')
@section('page-description', 'Créer un dossier visa pour un pèlerin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau dossier visa</h1>
            <p class="text-gray-600 mt-1">Associer un dossier visa à un pèlerin</p>
        </div>
        <a href="{{ route('visas.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('visas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pèlerin *</label>
                    <select name="pilgrim_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="">Choisir un pèlerin</option>
                        @foreach($pilgrims as $p)
                        <option value="{{ $p->id }}" {{ (old('pilgrim_id', $pilgrim->id ?? null) == $p->id) ? 'selected' : '' }}>{{ $p->last_name }} {{ $p->first_name }} — {{ $p->passport_no }}</option>
                        @endforeach
                    </select>
                    @error('pilgrim_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="not_submitted" {{ old('status') == 'not_submitted' ? 'selected' : '' }}>Non soumis</option>
                        <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Soumis</option>
                        <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                        <option value="refused" {{ old('status') == 'refused' ? 'selected' : '' }}>Refusé</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Référence dossier</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green" placeholder="Ex: VSA-2026-001">
                    @error('reference_no')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('expiry_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif de refus (si refusé)</label>
                    <textarea name="refusal_reason" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">{{ old('refusal_reason') }}</textarea>
                    @error('refusal_reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Documents consulaires (PDF, images)</label>
                    <input type="file" name="documents_upload[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('documents_upload.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('visas.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
