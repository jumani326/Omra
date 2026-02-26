<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pilgrim;
use App\Models\Package;
use App\Models\Visa;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Filtre par branche si nécessaire
        $branchFilter = function($query) use ($user) {
            if ($user->branch_id && !$user->hasRole('Super Admin Agence')) {
                $query->where('branch_id', $user->branch_id);
            }
        };

        // Total Pilgrims
        $totalPilgrims = Pilgrim::when($user->branch_id && !$user->hasRole('Super Admin Agence'), 
            fn($q) => $q->where('branch_id', $user->branch_id)
        )->count();

        // Monthly Revenue (somme des paiements du mois en cours)
        $monthlyRevenue = Payment::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->whereHas('pilgrim', fn($p) => $p->where('branch_id', $user->branch_id))
        )
        ->where('status', 'completed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');

        // Visa Acceptance Rate
        $totalVisas = Visa::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->whereHas('pilgrim', fn($p) => $p->where('branch_id', $user->branch_id))
        )->count();
        
        $approvedVisas = Visa::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->whereHas('pilgrim', fn($p) => $p->where('branch_id', $user->branch_id))
        )
        ->where('status', 'approved')
        ->count();
        
        $visaAcceptanceRate = $totalVisas > 0 ? ($approvedVisas / $totalVisas) * 100 : 0;

        // Active Groups (packages avec des pèlerins)
        $activeGroups = Package::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->where('branch_id', $user->branch_id)
        )
        ->whereHas('pilgrims')
        ->where('departure_date', '>=', now())
        ->count();

        // Recent Activities
        $recentActivities = ActivityLog::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->whereHas('pilgrim', fn($p) => $p->where('branch_id', $user->branch_id))
        )
        ->with(['user', 'pilgrim.package'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function($log) {
            return [
                'description' => $log->description,
                'time' => $log->created_at->diffForHumans(),
                'group' => $log->pilgrim && $log->pilgrim->package ? $log->pilgrim->package->name : 'N/A',
            ];
        })
        ->toArray();

        // Active Pilgrims (derniers pèlerins avec statut actif)
        $activePilgrims = Pilgrim::when($user->branch_id && !$user->hasRole('Super Admin Agence'),
            fn($q) => $q->where('branch_id', $user->branch_id)
        )
        ->whereIn('status', ['registered', 'dossier_complete', 'visa_approved'])
        ->with('package')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        return view('dashboard.index', compact(
            'totalPilgrims',
            'monthlyRevenue',
            'visaAcceptanceRate',
            'activeGroups',
            'recentActivities',
            'activePilgrims'
        ));
    }
}
