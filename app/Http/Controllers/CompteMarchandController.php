<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompteMarchandRequest;
use App\Http\Requests\UpdateCompteMarchandRequest;
use App\Models\CompteMarchand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class CompteMarchandController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CompteMarchand::class);

        $query = CompteMarchand::query()->pourBranch(auth()->user()->branch_id)->orderBy('nom_methode');

        $compteMarchands = $query->paginate(15);

        return view('compte-marchands.index', compact('compteMarchands'));
    }

    public function create(): View
    {
        Gate::authorize('create', CompteMarchand::class);

        $branches = \App\Models\Branch::when(auth()->user()->branch_id, fn ($q) => $q->where('id', auth()->user()->branch_id))
            ->orderBy('name')->get();

        return view('compte-marchands.create', compact('branches'));
    }

    public function store(StoreCompteMarchandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['actif'] = $request->boolean('actif', true);
        $data['branch_id'] = $data['branch_id'] ?? auth()->user()->branch_id;

        CompteMarchand::create($data);

        return redirect()->route('compte-marchands.index')->with('success', 'Compte marchand ajouté avec succès.');
    }

    public function show(CompteMarchand $compte_marchand): View|RedirectResponse
    {
        Gate::authorize('view', $compte_marchand);

        $compte_marchand->load('transactionsDigitales.pilgrim');

        return view('compte-marchands.show', ['compteMarchand' => $compte_marchand]);
    }

    public function edit(CompteMarchand $compte_marchand): View
    {
        Gate::authorize('update', $compte_marchand);

        $branches = \App\Models\Branch::when(auth()->user()->branch_id, fn ($q) => $q->where('id', auth()->user()->branch_id))
            ->orderBy('name')->get();

        return view('compte-marchands.edit', ['compteMarchand' => $compte_marchand, 'branches' => $branches]);
    }

    public function update(UpdateCompteMarchandRequest $request, CompteMarchand $compte_marchand): RedirectResponse
    {
        $data = $request->validated();
        $data['actif'] = $request->boolean('actif', true);

        $compte_marchand->update($data);

        return redirect()->route('compte-marchands.show', $compte_marchand)->with('success', 'Compte marchand mis à jour.');
    }

    public function destroy(CompteMarchand $compte_marchand): RedirectResponse
    {
        Gate::authorize('delete', $compte_marchand);

        if ($compte_marchand->transactionsDigitales()->exists()) {
            return redirect()->back()->with('error', 'Impossible de supprimer : des transactions sont liées à ce compte.');
        }

        $compte_marchand->delete();

        return redirect()->route('compte-marchands.index')->with('success', 'Compte marchand supprimé.');
    }
}
