<?php

namespace App\Http\Controllers;

use App\Models\PilgrimDocument;
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

        $query = PilgrimDocument::with('pilgrim:id,first_name,last_name,email,branch_id')
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

        $documents = $query->orderBy('uploaded_at', 'desc')->paginate(20);

        $pilgrimsForFilter = \App\Models\Pilgrim::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(!$branchId && $agencyId, fn ($q) => $q->where('agence_id', $agencyId))
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('documents.index', compact('documents', 'pilgrimsForFilter'));
    }
}
