@extends('layouts.app')

@section('page-title', 'Modifier Hôtel')
@section('page-description', 'Modifier les informations de l\'hôtel')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Modifier les Informations</h2>

        <form action="{{ route('hotels.update', $hotel) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'hôtel *</label>
                    <input type="text" name="name" value="{{ old('name', $hotel->name) }}" required
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
                        <option value="mecca" {{ old('city', $hotel->city) == 'mecca' ? 'selected' : '' }}>La Mecque</option>
                        <option value="medina" {{ old('city', $hotel->city) == 'medina' ? 'selected' : '' }}>Médine</option>
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
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ old('stars', $hotel->stars) == $i ? 'selected' : '' }}>{{ $i }} étoile(s)</option>
                        @endfor
                    </select>
                    @error('stars')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Distance du Haram -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distance du Haram (mètres)</label>
                    <input type="number" name="distance_haram" value="{{ old('distance_haram', $hotel->distance_haram) }}" 
                           step="0.01" min="0"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('distance_haram') border-red-500 @enderror">
                    @error('distance_haram')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo principale actuelle -->
                @if($hotel->main_image)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo principale actuelle</label>
                    <div class="mb-2">
                        <img src="{{ Storage::url($hotel->main_image) }}" alt="{{ $hotel->name }}" 
                             class="w-full h-48 object-cover rounded-lg border border-gray-300">
                    </div>
                </div>
                @endif

                <!-- Nouvelle photo principale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $hotel->main_image ? 'Remplacer la photo principale' : 'Photo principale de l\'hôtel' }}
                    </label>
                    <input type="file" name="main_image" accept="image/jpeg,image/png,image/jpg"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('main_image') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Image principale de l'hôtel (JPG, PNG, max 10 Mo)</p>
                    @error('main_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Images de chambres existantes -->
                @if($hotel->room_images && count($hotel->room_images) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Images de chambres existantes</label>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        @foreach($hotel->room_images as $index => $image)
                        <div class="relative">
                            <img src="{{ Storage::url($image) }}" alt="Chambre {{ $index + 1 }}" 
                                 class="w-full h-32 object-cover rounded-lg border border-gray-300">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Ajouter de nouvelles images de chambres -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ajouter des images de chambres</label>
                    <input type="file" name="room_images[]" accept="image/jpeg,image/png,image/jpg" multiple
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('room_images.*') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Vous pouvez sélectionner plusieurs images de chambres (JPG, PNG, max 10 Mo chacune)</p>
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
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

