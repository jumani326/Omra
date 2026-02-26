@extends('layouts.app')

@section('page-title', 'Nouvel Hôtel')
@section('page-description', 'Ajouter un nouvel hôtel')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Informations de l'Hôtel</h2>

        <form action="{{ route('hotels.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'hôtel *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ex: Fairmont Makkah Clock Royal Tower"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ville -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                    <select name="city" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('city') border-red-500 @enderror">
                        <option value="">Sélectionner une ville</option>
                        <option value="mecca" {{ old('city') == 'mecca' ? 'selected' : '' }}>La Mecque</option>
                        <option value="medina" {{ old('city') == 'medina' ? 'selected' : '' }}>Médine</option>
                    </select>
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Étoiles -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre d'étoiles *</label>
                    <select name="stars" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('stars') border-red-500 @enderror">
                        <option value="">Sélectionner</option>
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ old('stars') == $i ? 'selected' : '' }}>{{ $i }} étoile(s)</option>
                        @endfor
                    </select>
                    @error('stars')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Distance du Haram -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance du Haram (mètres)</label>
                    <input type="number" name="distance_haram" value="{{ old('distance_haram') }}" 
                           step="0.01" min="0" placeholder="Ex: 500"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('distance_haram') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Distance en mètres depuis le Haram (optionnel)</p>
                    @error('distance_haram')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo principale de l'hôtel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo principale de l'hôtel</label>
                    <input type="file" name="main_image" accept="image/jpeg,image/png,image/jpg"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('main_image') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Image principale de l'hôtel (JPG, PNG, max 5MB)</p>
                    @error('main_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Images de chambres -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Images de chambres</label>
                    <input type="file" name="room_images[]" accept="image/jpeg,image/png,image/jpg" multiple
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('room_images.*') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Vous pouvez sélectionner plusieurs images de chambres (JPG, PNG, max 5MB chacune)</p>
                    @error('room_images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('hotels.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-green text-white rounded-md hover:bg-dark-green transition">
                    Créer l'hôtel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

