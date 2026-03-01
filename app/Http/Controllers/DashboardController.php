<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pilgrim;
use App\Models\Package;
use App\Models\Visa;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Dashboard client (Pèlerin)
        if ($user->hasRole('Pèlerin (Client)')) {
            return $this->clientDashboard();
        }
        $isSupervision = $user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National']);
        $branchId = $isSupervision ? null : ($user->hasRole('Super Admin Agence') ? session('current_branch_id') : $user->branch_id);

        $scopeBranch = function ($query, $column = 'branch_id') use ($branchId) {
            if ($branchId) {
                if ($column === 'branch_id') {
                    $query->where('branch_id', $branchId);
                } else {
                    $query->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId));
                }
            }
        };

        // Total Pilgrims
        $totalPilgrims = Pilgrim::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->count();

        // Monthly Revenue
        $monthlyRevenue = Payment::when($branchId, fn ($q) => $q->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId)))
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Visa Acceptance Rate
        $totalVisas = Visa::when($branchId, fn ($q) => $q->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId)))->count();
        $approvedVisas = Visa::when($branchId, fn ($q) => $q->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId)))
            ->where('status', 'approved')
            ->count();
        $visaAcceptanceRate = $totalVisas > 0 ? ($approvedVisas / $totalVisas) * 100 : 0;

        // Active Groups
        $activeGroups = Package::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereHas('pilgrims')
            ->where('departure_date', '>=', now())
            ->count();

        // Recent Activities
        $recentActivities = ActivityLog::when($branchId, fn ($q) => $q->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId)))
            ->with(['user', 'pilgrim.package'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'description' => $log->description,
                'time' => $log->created_at->diffForHumans(),
                'group' => $log->pilgrim && $log->pilgrim->package ? $log->pilgrim->package->name : 'N/A',
            ])
            ->toArray();

        // Active Pilgrims
        $activePilgrims = Pilgrim::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['registered', 'dossier_complete', 'visa_approved'])
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Données pour graphiques (12 derniers mois revenus, distribution visas)
        $revenueByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueByMonth[] = [
                'label' => $date->translatedFormat('M Y'),
                'value' => (int) Payment::when($branchId, fn ($q) => $q->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId)))
                    ->where('status', 'completed')
                    ->whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->sum('amount'),
            ];
        }
        $visaDistribution = Visa::when($branchId, fn ($q) => $q->whereHas('pilgrim', fn ($p) => $p->where('branch_id', $branchId)))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Pèlerins par branche (vue consolidée : Super Admin "toutes" ou rôles supervision)
        $pilgrimsByBranch = [];
        if ($branchId === null) {
            $pilgrimsByBranch = Branch::withCount('pilgrims')
                ->orderBy('pilgrims_count', 'desc')
                ->get()
                ->map(fn ($b) => ['label' => $b->name, 'value' => $b->pilgrims_count])
                ->toArray();
        }

        return view('dashboard.index', compact(
            'totalPilgrims',
            'monthlyRevenue',
            'visaAcceptanceRate',
            'activeGroups',
            'recentActivities',
            'activePilgrims',
            'revenueByMonth',
            'visaDistribution',
            'pilgrimsByBranch',
            'isSupervision'
        ));
    }

    /**
     * Dashboard pour le rôle Pèlerin (Client) : forfaits, procédure, chatbot.
     */
    private function clientDashboard()
    {
        $user = Auth::user();
        $pilgrim = Pilgrim::where('email', $user->email)->with(['package', 'visa', 'payments'])->first();
        $packages = Package::available()
            ->with(['hotelMecca', 'hotelMedina', 'branch'])
            ->where('departure_date', '>=', now())
            ->orderBy('departure_date')
            ->get();

        $procedureSteps = [
            ['id' => 1, 'label' => 'Choisir un forfait', 'key' => 'package', 'done' => (bool) $pilgrim?->package_id],
            ['id' => 2, 'label' => 'Compléter le dossier', 'key' => 'dossier', 'done' => $pilgrim && in_array($pilgrim->status, ['dossier_complete', 'visa_submitted', 'visa_approved', 'departed', 'returned'])],
            ['id' => 3, 'label' => 'Dépôt visa', 'key' => 'visa', 'done' => $pilgrim && in_array($pilgrim->status, ['visa_submitted', 'visa_approved', 'departed', 'returned'])],
            ['id' => 4, 'label' => 'Paiement', 'key' => 'payment', 'done' => $pilgrim && $pilgrim->payments()->where('status', 'completed')->exists()],
            ['id' => 5, 'label' => 'Départ', 'key' => 'departure', 'done' => $pilgrim && in_array($pilgrim->status, ['departed', 'returned'])],
        ];

        return view('dashboard.client', compact('packages', 'pilgrim', 'procedureSteps'));
    }
}
