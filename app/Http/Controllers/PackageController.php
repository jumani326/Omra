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
        
        // KPI limités à la branche ou à l'agence de l'utilisateur
        $user = auth()->user();
        $branchId = $user->hasRole('Super Admin Agence') ? session('current_branch_id') : $user->branch_id;
        $agencyId = $user->agence_id ?? $user->branch?->agency_id;

        $scopePackages = function ($q) use ($branchId, $agencyId) {
            if ($branchId) {
                $q->where('branch_id', $branchId);
            } elseif ($agencyId) {
                $q->whereHas('branch', fn ($b) => $b->where('agency_id', $agencyId));
            }
        };
        $scopePilgrims = function ($q) use ($branchId, $agencyId) {
            if ($branchId) {
                $q->where('branch_id', $branchId);
            } elseif ($agencyId) {
                $q->where('agence_id', $agencyId);
            }
        };
        $scopePayments = function ($q) use ($branchId, $agencyId) {
            if ($branchId) {
                $q->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId));
            } elseif ($agencyId) {
                $q->whereHas('pilgrim', fn ($p) => $p->where('agence_id', $agencyId));
            }
        };

        $totalPackages = \App\Models\Package::when($branchId || $agencyId, $scopePackages)->count();
        $newPackagesThisMonth = \App\Models\Package::when($branchId || $agencyId, $scopePackages)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $activeBookings = \App\Models\Pilgrim::when($branchId || $agencyId, $scopePilgrims)
            ->whereNotNull('package_id')
            ->count();
        $totalSlots = \App\Models\Package::when($branchId || $agencyId, $scopePackages)->sum('slots');
        $fillRate = $totalSlots > 0 ? round(($activeBookings / $totalSlots) * 100, 0) : 0;
        $totalRevenue = \App\Models\Payment::when($branchId || $agencyId, $scopePayments)
            ->where('status', 'completed')
            ->sum('amount');
        
        $agencyId = $user->agence_id ?? $user->branch?->agency_id;
        $hotelsMecca = \App\Models\Hotel::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->where('city', 'mecca')->orderBy('name')->get();
        $hotelsMedina = \App\Models\Hotel::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->where('city', 'medina')->orderBy('name')->get();

        return view('packages.index', compact(
            'packages',
            'totalPackages',
            'newPackagesThisMonth',
            'activeBookings',
            'fillRate',
            'totalRevenue',
            'hotelsMecca',
            'hotelsMedina'
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', \App\Models\Package::class);

        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $hotelsMecca = \App\Models\Hotel::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->where('city', 'mecca')->orderBy('name')->get();
        $hotelsMedina = \App\Models\Hotel::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->where('city', 'medina')->orderBy('name')->get();

        return view('packages.create', compact('hotelsMecca', 'hotelsMedina'));
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

        $package->loadMissing(['pilgrims' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        $pendingApplications = $package->pilgrims->where('status', 'pending');
        $confirmedPilgrims = $package->pilgrims->where('status', '!=', 'pending');
        
        return view('packages.show', compact('package', 'pendingApplications', 'confirmedPilgrims'));
    }

    public function edit(int $id): View
    {
        $package = $this->service->findById($id);
        
        if (!$package) {
            abort(404);
        }
        
        Gate::authorize('update', $package);

        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $hotelsMecca = \App\Models\Hotel::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->where('city', 'mecca')->orderBy('name')->get();
        $hotelsMedina = \App\Models\Hotel::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->where('city', 'medina')->orderBy('name')->get();

        return view('packages.edit', compact('package', 'hotelsMecca', 'hotelsMedina'));
    }

    public function update(UpdatePackageRequest $request, \App\Models\Package $package): RedirectResponse
    {
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

    public function approveApplication(\App\Models\Package $package, \App\Models\Pilgrim $pilgrim): RedirectResponse
    {
        Gate::authorize('update', $package);

        if ($pilgrim->package_id !== $package->id || $pilgrim->status !== 'pending') {
            return redirect()
                ->route('packages.show', $package)
                ->with('error', 'Cette candidature n\'est pas valide pour ce forfait.');
        }

        if ($package->slots_remaining <= 0) {
            return redirect()
                ->route('packages.show', $package)
                ->with('error', 'Aucune place restante pour ce forfait.');
        }

        $pilgrim->status = 'registered';
        $pilgrim->save();

        $package->slots_remaining = max(0, $package->slots_remaining - 1);
        $package->save();

        return redirect()
            ->route('packages.show', $package)
            ->with('success', 'Candidature validée. Le pèlerin est maintenant inscrit sur ce forfait.');
    }
}
