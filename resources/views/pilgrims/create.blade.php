@extends('layouts.app')

@section('page-title', 'Nouveau Pèlerin')
@section('page-description', 'Ajouter un nouveau pèlerin au système')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Informations du Pèlerin</h2>

        <form action="{{ route('pilgrims.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Prénom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Numéro de passeport -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de passeport *</label>
                    <input type="text" name="passport_no" value="{{ old('passport_no') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('passport_no') border-red-500 @enderror">
                    @error('passport_no')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nationalité -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nationalité *</label>
                    <input type="text" name="nationality" value="{{ old('nationality') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('nationality') border-red-500 @enderror">
                    @error('nationality')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forfait -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Forfait</label>
                    <select name="package_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="">Aucun forfait</option>
                        @foreach(\App\Models\Package::where('slots_remaining', '>', 0)->get() as $package)
                            <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                {{ $package->name }} - {{ $package->type }} ({{ $package->slots_remaining }} places)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="registered" {{ old('status') == 'registered' ? 'selected' : '' }}>Inscrit</option>
                        <option value="dossier_complete" {{ old('status') == 'dossier_complete' ? 'selected' : '' }}>Dossier complet</option>
                        <option value="visa_submitted" {{ old('status') == 'visa_submitted' ? 'selected' : '' }}>Visa déposé</option>
                        <option value="visa_approved" {{ old('status') == 'visa_approved' ? 'selected' : '' }}>Visa approuvé</option>
                    </select>
                </div>
            </div>

            <!-- Documents -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Documents</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passeport</label>
                        <input type="file" name="documents[passport]" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                        <input type="file" name="documents[photo]" accept=".jpg,.jpeg,.png"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Certificat médical</label>
                        <input type="file" name="documents[medical_certificate]" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('pilgrims.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-green text-white rounded-md hover:bg-dark-green transition">
                    Créer le pèlerin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

