<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;
use App\Models\Guide;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuideController extends Controller
{
    protected function getAgencyId(): ?int
    {
        $user = auth()->user();
        return $user->agence_id ?? $user->branch?->agency_id;
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Guide::class);
        $agencyId = $this->getAgencyId();
        if (! $agencyId) {
            $guides = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $groups = collect();
            return view('guides.index', compact('guides', 'groups'));
        }

        $query = Guide::with(['user', 'group'])
            ->where('agency_id', $agencyId);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $guides = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $groups = Group::where('agency_id', $agencyId)->orderBy('name')->get();

        return view('guides.index', compact('guides', 'groups'));
    }

    public function create(): View
    {
        Gate::authorize('create', Guide::class);
        $agencyId = $this->getAgencyId();
        $groups = $agencyId
            ? Group::where('agency_id', $agencyId)->orderBy('name')->get()
            : collect();

        return view('guides.create', compact('groups'));
    }

    public function store(StoreGuideRequest $request): RedirectResponse
    {
        $agencyId = $this->getAgencyId();
        if (! $agencyId) {
            return redirect()->route('guides.index')->with('error', 'Agence non identifiée.');
        }

        $data = $request->validated();
        $password = Hash::make($data['password']);
        unset($data['password'], $data['password_confirmation']);

        DB::transaction(function () use ($data, $password, $agencyId) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
                'agence_id' => $agencyId,
                'active' => true,
            ]);
            $user->assignRole('guide');

            Guide::create([
                'user_id' => $user->id,
                'agency_id' => $agencyId,
                'group_id' => $data['group_id'] ?? null,
            ]);
        });

        return redirect()->route('guides.index')->with('success', 'Guide créé avec succès. Il peut se connecter avec son email et son mot de passe.');
    }

    public function show(Guide $guide): View
    {
        Gate::authorize('view', $guide);
        $guide->load(['user', 'group.pilgrims']);
        return view('guides.show', compact('guide'));
    }

    public function edit(Guide $guide): View
    {
        Gate::authorize('update', $guide);
        $agencyId = $this->getAgencyId();
        $groups = $agencyId
            ? Group::where('agency_id', $agencyId)->orderBy('name')->get()
            : collect();

        return view('guides.edit', compact('guide', 'groups'));
    }

    public function update(UpdateGuideRequest $request, Guide $guide): RedirectResponse
    {
        $data = $request->validated();
        if (! empty($data['password'])) {
            $guide->user->update(['password' => Hash::make($data['password'])]);
        }
        unset($data['password'], $data['password_confirmation']);

        $guide->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
        $guide->update([
            'group_id' => $data['group_id'] ?? null,
        ]);

        return redirect()->route('guides.show', $guide)->with('success', 'Guide mis à jour.');
    }

    public function destroy(Guide $guide): RedirectResponse
    {
        Gate::authorize('delete', $guide);
        $user = $guide->user;
        $guide->delete();
        $user->delete();
        return redirect()->route('guides.index')->with('success', 'Guide supprimé.');
    }
}
