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
        
        return view('pilgrims.index', compact('pilgrims'));
    }

    public function create(): View
    {
        Gate::authorize('create', \App\Models\Pilgrim::class);
        
        return view('pilgrims.create');
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
        
        return view('pilgrims.show', compact('pilgrim'));
    }

    public function edit(int $id): View
    {
        $pilgrim = $this->service->findById($id);
        
        if (!$pilgrim) {
            abort(404);
        }
        
        Gate::authorize('update', $pilgrim);
        
        return view('pilgrims.edit', compact('pilgrim'));
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
}
