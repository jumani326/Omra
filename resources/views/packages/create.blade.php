@extends('layouts.app')

@section('page-title', 'Nouveau Forfait')
@section('page-description', 'Créer un nouveau forfait Omra')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Informations du Forfait</h2>

        <form action="{{ route('packages.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du forfait *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('type') border-red-500 @enderror">
                        <option value="">Sélectionner</option>
                        <option value="economic" {{ old('type') == 'economic' ? 'selected' : '' }}>Économique</option>
                        <option value="standard" {{ old('type') == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="premium" {{ old('type') == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="vip" {{ old('type') == 'vip' ? 'selected' : '' }}>VIP</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre de places -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de places *</label>
                    <input type="number" name="slots" value="{{ old('slots') }}" min="1" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('slots') border-red-500 @enderror">
                    @error('slots')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prix -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix de vente (FDJ) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Coût -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Coût (FDJ) *</label>
                    <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" min="0" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('cost') border-red-500 @enderror">
                    @error('cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de départ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de départ *</label>
                    <input type="date" name="departure_date" value="{{ old('departure_date') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('departure_date') border-red-500 @enderror">
                    @error('departure_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de retour -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de retour *</label>
                    <input type="date" name="return_date" value="{{ old('return_date') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green @error('return_date') border-red-500 @enderror">
                    @error('return_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hôtel La Mecque -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hôtel La Mecque</label>
                    <select name="hotel_mecca_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="">Sélectionner</option>
                        @foreach(\App\Models\Hotel::where('city', 'mecca')->get() as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_mecca_id') == $hotel->id ? 'selected' : '' }}>
                                {{ $hotel->name }} ({{ $hotel->stars }}⭐)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nuits La Mecque -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuits La Mecque *</label>
                    <input type="number" name="nights_mecca" value="{{ old('nights_mecca') }}" min="1" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                </div>

                <!-- Hôtel Médine -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hôtel Médine</label>
                    <select name="hotel_medina_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="">Sélectionner</option>
                        @foreach(\App\Models\Hotel::where('city', 'medina')->get() as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_medina_id') == $hotel->id ? 'selected' : '' }}>
                                {{ $hotel->name }} ({{ $hotel->stars }}⭐)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nuits Médine -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuits Médine *</label>
                    <input type="number" name="nights_medina" value="{{ old('nights_medina', 0) }}" min="0" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('packages.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-green text-white rounded-md hover:bg-dark-green transition">
                    Créer le forfait
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

