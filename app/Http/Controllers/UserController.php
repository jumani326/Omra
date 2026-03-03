<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $query = User::with(['branch', 'roles']);

        $currentUser = auth()->user();
        if ($currentUser->branch_id && !$currentUser->hasRole('Super Admin Agence')) {
            $query->where('branch_id', $currentUser->branch_id);
        } else {
            // Sans branche : limiter aux utilisateurs de la même agence
            $agencyId = $currentUser->agence_id ?? $currentUser->branch?->agency_id;
            if ($agencyId) {
                $branchIds = Branch::where('agency_id', $agencyId)->pluck('id');
                $query->where(function ($q) use ($agencyId, $branchIds) {
                    $q->where('agence_id', $agencyId)->orWhereIn('branch_id', $branchIds);
                });
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->orderBy('name')->paginate(15);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name', 'name');

        return view('users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);
        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $branches = $agencyId
            ? Branch::where('agency_id', $agencyId)->orderBy('name')->get()
            : Branch::orderBy('name')->get();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        return view('users.create', compact('branches', 'roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        unset($data['role']);
        $data['active'] = $data['active'] ?? true;

        $user = User::create($data);
        $user->assignRole($request->validated('role'));

        return redirect()->route('users.show', $user)->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user): View
    {
        Gate::authorize('view', $user);
        $user->load(['branch', 'roles']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);
        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $branches = $agencyId
            ? Branch::where('agency_id', $agencyId)->orderBy('name')->get()
            : Branch::orderBy('name')->get();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['password_confirmation'], $data['role']);
        $data['active'] = $data['active'] ?? true;

        $user->update($data);
        $user->syncRoles([$request->validated('role')]);

        return redirect()->route('users.show', $user)->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }
}
