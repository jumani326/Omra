<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePilgrimRequest;
use App\Http\Requests\UpdatePilgrimRequest;
use App\Services\PilgrimService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class PilgrimController extends Controller
{
    private PilgrimService $service;

    public function __construct()
    {
        $this->service = new PilgrimService(new \App\Repositories\PilgrimRepository());
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', \App\Models\Pilgrim::class);
        $pilgrims = $this->service->getAll($request->all());
        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $groups = $agencyId ? \App\Models\Group::where('agency_id', $agencyId)->orderBy('name')->get() : collect();
        return view('pilgrims.index', compact('pilgrims', 'groups'));
    }

    public function export(Request $request)
    {
        Gate::authorize('viewAny', \App\Models\Pilgrim::class);
        $pilgrims = $this->service->getAll($request->all(), 50000)->getCollection();
        $filename = 'pelerins-' . date('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($pilgrims) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 pour Excel
            fputcsv($out, ['ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Passeport', 'Nationalité', 'Statut', 'Branche', 'Forfait', 'Date inscription'], ';');
            foreach ($pilgrims as $p) {
                fputcsv($out, [
                    $p->id,
                    $p->first_name,
                    $p->last_name,
                    $p->email ?? '',
                    $p->phone ?? '',
                    $p->passport_no,
                    $p->nationality ?? '',
                    ucfirst(str_replace('_', ' ', $p->status)),
                    $p->branch?->name ?? '—',
                    $p->package?->name ?? '—',
                    $p->created_at?->format('d/m/Y H:i') ?? '',
                ], ';');
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function create(): View
    {
        Gate::authorize('create', \App\Models\Pilgrim::class);
        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $groups = $agencyId ? \App\Models\Group::where('agency_id', $agencyId)->orderBy('name')->get() : collect();

        return view('pilgrims.create', compact('groups'));
    }

    public function store(StorePilgrimRequest $request): RedirectResponse
    {
        $pilgrim = $this->service->create($request->validated());
        
        return redirect()->route('pilgrims.show', $pilgrim)
            ->with('success', 'Pèlerin créé avec succès.');
    }

    public function show(int $id): View
    {
        $pilgrim = $this->service->findById($id);
        if (!$pilgrim) {
            abort(404);
        }
        Gate::authorize('view', $pilgrim);
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('pilgrims.show', compact('pilgrim', 'branches'));
    }

    public function edit(int $id): View
    {
        $pilgrim = $this->service->findById($id);

        if (!$pilgrim) {
            abort(404);
        }

        Gate::authorize('update', $pilgrim);
        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $groups = $agencyId ? \App\Models\Group::where('agency_id', $agencyId)->orderBy('name')->get() : collect();

        return view('pilgrims.edit', compact('pilgrim', 'groups'));
    }

    public function update(UpdatePilgrimRequest $request, int $id): RedirectResponse
    {
        $pilgrim = $this->service->findById($id);
        
        $this->service->update($pilgrim, $request->validated());
        
        return redirect()->route('pilgrims.show', $pilgrim)
            ->with('success', 'Pèlerin mis à jour avec succès.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $pilgrim = $this->service->findById($id);
        
        if (!$pilgrim) {
            abort(404);
        }
        
        Gate::authorize('delete', $pilgrim);
        
        $this->service->delete($pilgrim);
        
        return redirect()->route('pilgrims.index')
            ->with('success', 'Pèlerin supprimé avec succès.');
    }

    public function transfer(Request $request, \App\Models\Pilgrim $pilgrim): RedirectResponse
    {
        if (!auth()->user()->hasRole('Super Admin Agence')) {
            abort(403, 'Seul le Super Admin peut transférer un pèlerin.');
        }
        $request->validate(['branch_id' => 'required|exists:branches,id']);
        if ($this->service->transferToBranch($pilgrim, (int) $request->branch_id)) {
            return redirect()->route('pilgrims.show', $pilgrim)->with('success', 'Pèlerin transféré vers la nouvelle branche.');
        }
        return redirect()->back()->with('info', 'Le pèlerin est déjà dans cette branche.');
    }
}
