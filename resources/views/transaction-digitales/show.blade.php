@extends('layouts.app')

@section('page-title', 'Transaction #' . $transactionDigitale->id)
@section('page-description', $transactionDigitale->compteMarchand->nom_methode . ' - ' . number_format($transactionDigitale->montant, 0, ',', ' ') . ' FDJ')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <dl class="grid grid-cols-1 gap-4">
            <div>
                <dt class="text-sm text-gray-500">ID</dt>
                <dd class="font-mono font-medium">#{{ $transactionDigitale->id }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Méthode</dt>
                <dd class="font-medium">{{ $transactionDigitale->compteMarchand->nom_methode }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Numéro du compte</dt>
                <dd class="font-mono">{{ $transactionDigitale->compteMarchand->numero_compte }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Montant</dt>
                <dd class="text-xl font-bold text-primary-green">{{ number_format($transactionDigitale->montant, 0, ',', ' ') }} FDJ</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Client</dt>
                <dd>{{ $transactionDigitale->client_display }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Date</dt>
                <dd>{{ $transactionDigitale->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Statut</dt>
                <dd>
                    @php
                        $statutClass = match($transactionDigitale->statut) {
                            'valide' => 'bg-green-100 text-green-800',
                            'refuse' => 'bg-red-100 text-red-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp
                    <span class="px-2 py-1 text-sm font-semibold rounded-full {{ $statutClass }}">{{ ucfirst(str_replace('_', ' ', $transactionDigitale->statut)) }}</span>
                </dd>
            </div>
            @if($transactionDigitale->reference)
            <div>
                <dt class="text-sm text-gray-500">Référence</dt>
                <dd class="font-mono text-sm">{{ $transactionDigitale->reference }}</dd>
            </div>
            @endif
            @if($transactionDigitale->notes)
            <div>
                <dt class="text-sm text-gray-500">Notes</dt>
                <dd class="text-sm text-gray-700">{{ $transactionDigitale->notes }}</dd>
            </div>
            @endif
        </dl>
        @if($transactionDigitale->statut === 'en_attente')
        <div class="mt-6 pt-4 border-t border-gray-200 flex flex-wrap items-center gap-3">
            <form action="{{ route('transaction-digitales.valider', $transactionDigitale) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la validation ? Le montant sera ajouté au solde du compte marchand.');">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition">✓ Valider la transaction</button>
            </form>
            <form action="{{ route('transaction-digitales.refuser', $transactionDigitale) }}" method="POST" class="inline" id="form-refuser">
                @csrf
                <input type="hidden" name="notes" id="refus-notes" value="">
                <button type="button" onclick="promptRefus()" class="px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition">✕ Refuser la transaction</button>
            </form>
        </div>
        <script>
        function promptRefus() {
            var motif = prompt('Motif du refus (facultatif) :');
            if (motif !== null) {
                document.getElementById('refus-notes').value = motif;
                if (confirm('Confirmer le refus de cette transaction ?')) document.getElementById('form-refuser').submit();
            }
        }
        </script>
        @endif

        <div class="mt-6 pt-4 border-t border-gray-200">
            <a href="{{ route('compte-marchands.show', $transactionDigitale->compteMarchand) }}" class="text-primary-green hover:underline text-sm mr-4">Voir le compte marchand</a>
            <a href="{{ route('transaction-digitales.index') }}" class="text-gray-600 hover:underline text-sm">← Liste des transactions</a>
        </div>
    </div>
</div>
@endsection
