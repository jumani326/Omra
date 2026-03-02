@extends('layouts.app')

@section('page-title', 'Modifier le paiement')
@section('page-description', 'Modifier le paiement')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier le paiement {{ $payment->ref_no }}</h1>
            <p class="text-gray-600 mt-1">{{ $payment->pilgrim->first_name }} {{ $payment->pilgrim->last_name }}</p>
        </div>
        <a href="{{ route('payments.show', $payment) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Retour</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('payments.update', $payment) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° Facture</label>
                    <p class="text-gray-900 font-mono">{{ $payment->ref_no }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FDJ) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount', $payment->amount) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement *</label>
                    <select name="method" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        @foreach(['cash' => 'Espèces', 'cash_espece' => 'Cash espèce', 'transfer' => 'Virement', 'tpe' => 'TPE', 'mobile_money' => 'Mobile Money'] as $m => $label)
                        <option value="{{ $m }}" {{ old('method', $payment->method) == $m ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                        @foreach(['pending','completed','refunded'] as $s)
                        <option value="{{ $s }}" {{ old('status', $payment->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de paiement *</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    @error('payment_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('payments.show', $payment) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
