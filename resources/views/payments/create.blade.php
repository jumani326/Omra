@extends('layouts.app')

@section('page-title', 'Nouveau paiement')
@section('page-description', 'Enregistrer un paiement')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau paiement</h1>
            <p class="text-gray-600 mt-1">N° facture : {{ $nextRefNo }}</p>
        </div>
        <a href="{{ route('payments.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pèlerin *</label>
                    <select name="pilgrim_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="">Choisir un pèlerin</option>
                        @foreach($pilgrims as $p)
                        <option value="{{ $p->id }}" {{ old('pilgrim_id', $pilgrim->id ?? null) == $p->id ? 'selected' : '' }}>{{ $p->last_name }} {{ $p->first_name }} — {{ $p->passport_no }}</option>
                        @endforeach
                    </select>
                    @error('pilgrim_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (MAD) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement *</label>
                    <select name="method" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                        <option value="transfer" {{ old('method') == 'transfer' ? 'selected' : '' }}>Virement</option>
                        <option value="tpe" {{ old('method') == 'tpe' ? 'selected' : '' }}>TPE</option>
                        <option value="mobile_money" {{ old('method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                    @error('method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                        <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de paiement *</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('payment_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('payments.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
