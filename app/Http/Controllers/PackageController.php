<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class PackageController extends Controller
{
    private PackageService $service;

    public function __construct()
    {
        $this->service = new PackageService(new \App\Repositories\PackageRepository());
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', \App\Models\Package::class);
        
        $packages = $this->service->getAll($request->all());
        
        // Calculate KPI metrics
        $user = auth()->user();
        $totalPackages = \App\Models\Package::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->where('branch_id', $user->branch_id)
        )->count();
        
        $newPackagesThisMonth = \App\Models\Package::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->where('branch_id', $user->branch_id)
        )
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
        
        $activeBookings = \App\Models\Pilgrim::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->where('branch_id', $user->branch_id)
        )
        ->whereNotNull('package_id')
        ->count();
        
        $totalSlots = \App\Models\Package::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->where('branch_id', $user->branch_id)
        )->sum('slots');
        
        $fillRate = $totalSlots > 0 ? round(($activeBookings / $totalSlots) * 100, 0) : 0;
        
        $totalRevenue = \App\Models\Payment::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->whereHas('pilgrim', fn($p) => $p->where('branch_id', $user->branch_id))
        )
        ->where('status', 'completed')
        ->sum('amount');
        
        return view('packages.index', compact(
            'packages',
            'totalPackages',
            'newPackagesThisMonth',
            'activeBookings',
            'fillRate',
            'totalRevenue'
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', \App\Models\Package::class);
        
        return view('packages.create');
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        $package = $this->service->create($request->validated());
        
        return redirect()->route('packages.show', $package)
            ->with('success', 'Forfait créé avec succès.');
    }

    public function show(int $id): View
    {
        $package = $this->service->findById($id);
        
        if (!$package) {
            abort(404);
        }
        
        Gate::authorize('view', $package);
        
        return view('packages.show', compact('package'));
    }

    public function edit(int $id): View
    {
        $package = $this->service->findById($id);
        
        if (!$package) {
            abort(404);
        }
        
        Gate::authorize('update', $package);
        
        return view('packages.edit', compact('package'));
    }

    public function update(UpdatePackageRequest $request, int $id): RedirectResponse
    {
        $package = $this->service->findById($id);
        
        $this->service->update($package, $request->validated());
        
        return redirect()->route('packages.show', $package)
            ->with('success', 'Forfait mis à jour avec succès.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $package = $this->service->findById($id);
        
        if (!$package) {
            abort(404);
        }
        
        Gate::authorize('delete', $package);
        
        $this->service->delete($package);
        
        return redirect()->route('packages.index')
            ->with('success', 'Forfait supprimé avec succès.');
    }

    public function clone(int $id): RedirectResponse
    {
        $package = $this->service->findById($id);
        
        if (!$package) {
            abort(404);
        }
        
        Gate::authorize('create', \App\Models\Package::class);
        
        $newPackage = $this->service->clone($package);
        
        return redirect()->route('packages.edit', $newPackage)
            ->with('success', 'Forfait cloné avec succès. Modifiez les dates et détails.');
    }
}
