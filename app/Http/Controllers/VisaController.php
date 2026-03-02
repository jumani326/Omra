<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisaRequest;
use App\Http\Requests\UpdateVisaRequest;
use App\Mail\VisaApprovedMail;
use App\Models\Visa;
use App\Services\VisaService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class VisaController extends Controller
{
    public function __construct(
        private VisaService $service
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', \App\Models\Visa::class);
        $visas = $this->service->getAll($request->all());
        return view('visas.index', compact('visas'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', \App\Models\Visa::class);
        $pilgrimId = $request->get('pilgrim_id');
        $pilgrim = $pilgrimId ? \App\Models\Pilgrim::find($pilgrimId) : null;
        $pilgrims = \App\Models\Pilgrim::when(auth()->user()->branch_id && !auth()->user()->hasRole('Super Admin Agence'), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('last_name')->get();
        return view('visas.create', compact('pilgrims', 'pilgrim'));
    }

    public function store(StoreVisaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['documents_upload'] = $request->file('documents_upload', []);
        $visa = $this->service->create($data);
        return redirect()->route('visas.show', $visa)->with('success', 'Visa enregistré avec succès.');
    }

    public function show(int $id): View|RedirectResponse
    {
        $visa = $this->service->findById($id);
        if (!$visa) {
            abort(404);
        }
        Gate::authorize('view', $visa);
        return view('visas.show', compact('visa'));
    }

    public function edit(int $id): View
    {
        $visa = $this->service->findById($id);
        if (!$visa) {
            abort(404);
        }
        Gate::authorize('update', $visa);
        return view('visas.edit', compact('visa'));
    }

    public function update(UpdateVisaRequest $request, int $id): RedirectResponse
    {
        $visa = $this->service->findById($id);
        if (!$visa) {
            abort(404);
        }
        $data = $request->validated();
        $data['documents_upload'] = $request->file('documents_upload', []);
        $this->service->update($visa, $data);
        return redirect()->route('visas.show', $visa)->with('success', 'Visa mis à jour avec succès.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $visa = $this->service->findById($id);
        if (!$visa) {
            abort(404);
        }
        Gate::authorize('delete', $visa);
        $this->service->delete($visa);
        return redirect()->route('visas.index')->with('success', 'Visa supprimé.');
    }

    /**
     * Envoyer le dossier visa au client par email (PDF + documents).
     */
    public function sendEmail(Visa $visa): RedirectResponse
    {
        Gate::authorize('update', $visa);

        $visa->loadMissing(['pilgrim.user', 'pilgrim.package']);
        $pilgrim = $visa->pilgrim;
        if (!$pilgrim) {
            return redirect()->back()->with('error', 'Aucun pèlerin associé à ce visa.');
        }

        $email = $pilgrim->email ?: $pilgrim->user?->email;
        if (!$email) {
            return redirect()->back()->with('error', 'Aucune adresse email pour ce pèlerin.');
        }

        Mail::to($email)->send(new VisaApprovedMail($pilgrim, $visa));

        return redirect()->back()->with('success', 'Le visa a été envoyé par email (PDF) à ' . $email . '.');
    }
}
