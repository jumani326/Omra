<?php

namespace App\Http\Controllers;

use App\Models\Pilgrim;
use App\Models\PilgrimDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Liste des documents des pèlerins de l'agence (branche courante).
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', \App\Models\Pilgrim::class);

        $branchId = auth()->user()->hasRole('Super Admin Agence')
            ? session('current_branch_id')
            : auth()->user()->branch_id;

        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;

        $query = PilgrimDocument::with('pilgrim:id,first_name,last_name,email,branch_id,status')
            ->whereHas('pilgrim', function ($q) use ($branchId, $agencyId) {
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                } elseif ($agencyId) {
                    $q->where('agence_id', $agencyId);
                }
            });

        if ($request->filled('pilgrim_id')) {
            $query->where('pilgrim_id', $request->pilgrim_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut du dossier (complet/validé vs non validé)
        if ($request->filled('dossier_status')) {
            $validatedStatuses = ['dossier_complete', 'visa_submitted', 'visa_approved', 'departed', 'returned'];
            $query->whereHas('pilgrim', function ($q) use ($request, $validatedStatuses) {
                if ($request->dossier_status === 'complete_validated') {
                    $q->whereIn('status', $validatedStatuses);
                } elseif ($request->dossier_status === 'not_validated') {
                    $q->where(function ($q2) use ($validatedStatuses) {
                        $q2->whereNull('status')
                            ->orWhereNotIn('status', $validatedStatuses);
                    });
                }
            });
        }

        $documents = $query->orderBy('uploaded_at', 'desc')->paginate(20);

        $pilgrimsForFilter = \App\Models\Pilgrim::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(!$branchId && $agencyId, fn ($q) => $q->where('agence_id', $agencyId))
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('documents.index', compact('documents', 'pilgrimsForFilter'));
    }

    /**
     * Valider le dossier d'un pèlerin (passer le statut à « dossier complet »).
     */
    public function validateDossier(Pilgrim $pilgrim): RedirectResponse
    {
        Gate::authorize('update', $pilgrim);

        $validatedStatuses = ['dossier_complete', 'visa_submitted', 'visa_approved', 'departed', 'returned'];
        if (in_array($pilgrim->status, $validatedStatuses)) {
            return redirect()->route('documents.index')->with('info', 'Le dossier de ce pèlerin est déjà validé.');
        }

        $pilgrim->update(['status' => 'dossier_complete']);

        return redirect()->route('documents.index', request()->only(['pilgrim_id', 'type', 'dossier_status']))
            ->with('success', 'Dossier de ' . $pilgrim->first_name . ' ' . $pilgrim->last_name . ' validé avec succès.');
    }
}
