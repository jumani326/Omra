@extends('layouts.app')

@section('page-title', 'Postuler à un forfait')
@section('page-description', 'Complétez vos informations pour envoyer votre demande à l\'agence.')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-2">Forfait sélectionné</h2>
        <p class="text-xl font-semibold text-primary-green">{{ $package->name }}</p>
        <p class="text-gray-600">{{ number_format($package->price, 0) }} FDJ · {{ $package->departure_date?->translatedFormat('d M Y') }} → {{ $package->return_date?->translatedFormat('d M Y') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Postuler auprès de l'agence</h2>
        <p class="text-sm text-gray-600 mb-6">Renseignez vos informations. Votre demande sera envoyée à l'agence <strong>{{ $package->branch->agency->name ?? '—' }}</strong>. Une fois validée, vous pourrez poursuivre la procédure. Votre email ({{ auth()->user()->email }}) sera utilisé pour vous identifier.</p>

        <form action="{{ route('client.package.store', $package) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom *</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', auth()->user()->name) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-green focus:ring-primary-green">
                @error('first_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-green focus:ring-primary-green">
                @error('last_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="passport_no" class="block text-sm font-medium text-gray-700">Numéro de passeport *</label>
                <input type="text" name="passport_no" id="passport_no" value="{{ old('passport_no') }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-green focus:ring-primary-green">
                @error('passport_no')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-green focus:ring-primary-green">
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nationality" class="block text-sm font-medium text-gray-700">Nationalité *</label>
                <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-green focus:ring-primary-green" placeholder="ex. Maroc">
                @error('nationality')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-primary-green text-white px-4 py-3 rounded-lg hover:bg-dark-green transition font-medium">
                    Envoyer ma demande
                </button>
                <a href="{{ route('client.packages.index') }}" class="px-4 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
