@extends('layouts.app')

@section('page-title', 'Modifier le visa')
@section('page-description', 'Modifier le dossier visa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier le dossier visa</h1>
            <p class="text-gray-600 mt-1">{{ $visa->pilgrim->first_name }} {{ $visa->pilgrim->last_name }}</p>
        </div>
        <a href="{{ route('visas.show', $visa) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('visas.update', $visa) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        @foreach(['not_submitted','submitted','processing','approved','refused'] as $s)
                        <option value="{{ $s }}" {{ old('status', $visa->status) == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Référence dossier</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no', $visa->reference_no) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('reference_no')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $visa->expiry_date?->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('expiry_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif de refus (si refusé)</label>
                    <textarea name="refusal_reason" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">{{ old('refusal_reason', $visa->refusal_reason) }}</textarea>
                    @error('refusal_reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Documents existants</label>
                    @if($visa->documents && count((array)$visa->documents) > 0)
                    <ul class="text-sm text-gray-600 space-y-1 mb-2">
                        @foreach((array)$visa->documents as $path)
                        <li><a href="{{ \Illuminate\Support\Facades\Storage::url($path) }}" target="_blank" class="text-primary-green hover:underline">{{ basename($path) }}</a></li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-sm text-gray-500">Aucun document.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ajouter des documents</label>
                    <input type="file" name="documents_upload[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('visas.show', $visa) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
