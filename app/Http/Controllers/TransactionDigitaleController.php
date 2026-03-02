<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionDigitaleRequest;
use App\Mail\TransactionValideeClientMail;
use App\Models\TransactionDigitale;
use App\Models\CompteMarchand;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class TransactionDigitaleController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TransactionDigitale::class);

        $branchId = auth()->user()->branch_id;

        $query = TransactionDigitale::query()
            ->with(['compteMarchand', 'pilgrim'])
            ->whereHas('compteMarchand', fn ($q) => $q->pourBranch($branchId));

        if ($request->filled('methode')) {
            $query->whereHas('compteMarchand', fn ($q) => $q->where('nom_methode', $request->methode));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $transactions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('transaction-digitales.index', compact('transactions'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('viewAny', TransactionDigitale::class);

        $branchId = auth()->user()->branch_id;
        $comptes = CompteMarchand::pourBranch($branchId)->actifs()->orderBy('nom_methode')->get();
        $pilgrims = \App\Models\Pilgrim::when($branchId && !auth()->user()->hasRole('Super Admin Agence'), fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('last_name')->get();

        return view('transaction-digitales.create', compact('comptes', 'pilgrims'));
    }

    public function store(StoreTransactionDigitaleRequest $request): RedirectResponse
    {
        $compte = CompteMarchand::find($request->compte_marchand_id);
        Gate::authorize('view', $compte);

        $data = $request->validated();
        $transaction = TransactionDigitale::create($data);

        if ($request->statut === 'valide' && $compte) {
            $compte->increment('solde', $request->montant);
        }

        return redirect()->route('transaction-digitales.show', $transaction)->with('success', 'Transaction enregistrée.');
    }

    public function show(TransactionDigitale $transaction_digitale): View
    {
        Gate::authorize('view', $transaction_digitale);

        $transaction_digitale->load(['compteMarchand', 'pilgrim']);

        return view('transaction-digitales.show', ['transactionDigitale' => $transaction_digitale]);
    }

    /**
     * L'agence valide la transaction : statut → valide, solde du compte marchand mis à jour.
     */
    public function valider(TransactionDigitale $transaction_digitale): RedirectResponse
    {
        Gate::authorize('view', $transaction_digitale);

        if ($transaction_digitale->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette transaction n\'est plus en attente.');
        }

        $transaction_digitale->update(['statut' => 'valide']);
        $compte = $transaction_digitale->compteMarchand;
        if ($compte) {
            $compte->increment('solde', $transaction_digitale->montant);
        }

        // Envoyer au client un email avec la fiche PDF des détails de la transaction
        $transaction_digitale->load(['compteMarchand', 'pilgrim.user']);
        $clientEmail = $transaction_digitale->pilgrim?->email ?? $transaction_digitale->pilgrim?->user?->email;
        if ($clientEmail) {
            try {
                Mail::to($clientEmail)->send(new TransactionValideeClientMail($transaction_digitale));
                $transaction_digitale->update(['validation_email_sent_at' => now()]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('transaction-digitales.show', $transaction_digitale)
            ->with('success', 'Transaction validée. Le solde du compte marchand a été mis à jour.' . ($clientEmail ? ' Un email avec la fiche PDF a été envoyé au client.' : ''));
    }

    /**
     * L'agence refuse la transaction (optionnel : motif dans notes).
     */
    public function refuser(Request $request, TransactionDigitale $transaction_digitale): RedirectResponse
    {
        Gate::authorize('view', $transaction_digitale);

        if ($transaction_digitale->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette transaction n\'est plus en attente.');
        }

        $notes = $request->validate(['notes' => ['nullable', 'string', 'max:500']])['notes'] ?? null;
        $transaction_digitale->update([
            'statut' => 'refuse',
            'notes' => $notes ?: $transaction_digitale->notes,
        ]);

        return redirect()
            ->route('transaction-digitales.show', $transaction_digitale)
            ->with('success', 'Transaction refusée.');
    }
}
