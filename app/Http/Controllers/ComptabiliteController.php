<?php

namespace App\Http\Controllers;

use App\Models\CompteMarchand;
use App\Models\TransactionDigitale;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class ComptabiliteController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CompteMarchand::class);

        $branchId = auth()->user()->branch_id;

        $comptes = CompteMarchand::pourBranch($branchId)->actifs()->get();

        $byMethode = [];
        foreach (CompteMarchand::METHODES as $methode) {
            $comptesMethode = $comptes->where('nom_methode', $methode);
            $solde = $comptesMethode->sum('solde');
            $ids = $comptesMethode->pluck('id')->toArray();
            $totalTransactions = $ids ? TransactionDigitale::whereIn('compte_marchand_id', $ids)->where('statut', 'valide')->sum('montant') : 0;
            $firstCompte = $comptesMethode->first();
            $numero = $firstCompte ? $firstCompte->numero_compte : '—';
            if ($comptesMethode->count() > 1) {
                $numero = $comptesMethode->count() . ' compte(s)';
            }
            $byMethode[$methode] = [
                'numero' => $numero,
                'solde' => (float) $solde,
                'total_transactions' => (float) $totalTransactions,
                'compte' => $firstCompte,
            ];
        }

        return view('comptabilite.index', compact('byMethode'));
    }
}
