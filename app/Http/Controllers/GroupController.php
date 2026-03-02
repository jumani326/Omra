<?php

namespace App\Http\Controllers;

use App\Mail\GroupPilgrimsListMail;
use App\Models\Group;
use App\Models\Pilgrim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class GroupController extends Controller
{
    protected function getAgencyId(): ?int
    {
        $user = auth()->user();
        return $user->agence_id ?? $user->branch?->agency_id;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Group::class);
        $agencyId = $this->getAgencyId();
        $groups = $agencyId
            ? Group::withCount('pilgrims')->where('agency_id', $agencyId)->orderBy('name')->get()
            : collect();

        return view('groups.index', compact('groups'));
    }

    public function create(): View
    {
        Gate::authorize('create', Group::class);
        return view('groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Group::class);
        $agencyId = $this->getAgencyId();
        if (! $agencyId) {
            return redirect()->route('groups.index')->with('error', 'Agence non identifiée.');
        }

        $request->validate(['name' => ['required', 'string', 'max:255']]);

        Group::create([
            'agency_id' => $agencyId,
            'name' => $request->name,
        ]);

        return redirect()->route('groups.index')->with('success', 'Groupe créé. Vous pouvez l\'assigner à un guide.');
    }

    public function show(Group $group): View|RedirectResponse
    {
        Gate::authorize('view', $group);
        $group->load(['pilgrims', 'guide.user']);
        $agencyId = $this->getAgencyId();
        // Pèlerins de l'agence qui ne sont pas dans ce groupe (créés par l'agence ou inscrits sur la plateforme)
        $pilgrimsNotInGroup = $agencyId
            ? Pilgrim::with('group')
                ->where('agence_id', $agencyId)
                ->where(function ($q) use ($group) {
                    $q->whereNull('group_id')->orWhere('group_id', '!=', $group->id);
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
            : collect();

        return view('groups.show', compact('group', 'pilgrimsNotInGroup'));
    }

    public function addPilgrims(Request $request, Group $group): RedirectResponse
    {
        Gate::authorize('update', $group);
        $agencyId = $this->getAgencyId();
        if (! $agencyId) {
            return redirect()->route('groups.show', $group)->with('error', 'Agence non identifiée.');
        }

        $request->validate([
            'pilgrim_ids' => ['required', 'array'],
            'pilgrim_ids.*' => ['integer', 'exists:pilgrims,id'],
        ]);

        $pilgrimIds = $request->input('pilgrim_ids', []);
        $updated = Pilgrim::where('agence_id', $agencyId)
            ->whereIn('id', $pilgrimIds)
            ->update(['group_id' => $group->id]);

        $message = $updated > 0
            ? $updated . ' pèlerin(s) ajouté(s) au groupe.'
            : 'Aucun pèlerin ajouté (vérifiez qu\'ils appartiennent à votre agence).';

        return redirect()->route('groups.show', $group)->with('success', $message);
    }

    public function removePilgrim(Request $request, Group $group): RedirectResponse
    {
        Gate::authorize('update', $group);
        $request->validate(['pilgrim_id' => ['required', 'integer', 'exists:pilgrims,id']]);
        $pilgrim = Pilgrim::findOrFail($request->pilgrim_id);
        if ($pilgrim->group_id != $group->id) {
            return redirect()->route('groups.show', $group)->with('error', 'Ce pèlerin n\'est pas dans ce groupe.');
        }
        $pilgrim->update(['group_id' => null]);
        return redirect()->route('groups.show', $group)->with('success', 'Pèlerin retiré du groupe.');
    }

    public function sendListToGuide(Group $group): RedirectResponse
    {
        Gate::authorize('view', $group);
        $group->load(['pilgrims', 'guide.user']);
        $guide = $group->guide;
        if (! $guide || ! $guide->user) {
            return redirect()->route('groups.show', $group)->with('error', 'Aucun guide assigné à ce groupe. Assignez un guide au groupe pour pouvoir lui envoyer la liste.');
        }
        Mail::to($guide->user->email)->send(new GroupPilgrimsListMail($group));
        return redirect()->route('groups.show', $group)->with('success', 'La liste des pèlerins a été envoyée par email au guide (' . $guide->user->email . ').');
    }
}
