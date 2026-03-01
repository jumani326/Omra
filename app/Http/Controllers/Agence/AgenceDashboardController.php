<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Pilgrim;
use App\Models\Guide;
use App\Models\Group;
use App\Models\Visa;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgenceDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $agencyId = $user->agence_id ?? $user->branch?->agency_id ?? null;
        if (! $agencyId) {
            return view('agence.dashboard', [
                'totalPilgrims' => 0,
                'totalGuides' => 0,
                'totalGroups' => 0,
                'visasEnCours' => [],
                'recentPayments' => [],
            ]);
        }

        $totalPilgrims = Pilgrim::where('agence_id', $agencyId)->count();
        $totalGuides = Guide::where('agency_id', $agencyId)->count();
        $totalGroups = Group::where('agency_id', $agencyId)->count();
        $visasEnCours = Visa::whereHas('pilgrim', fn ($q) => $q->where('agence_id', $agencyId))
            ->whereIn('status', ['submitted', 'processing'])
            ->with('pilgrim')
            ->limit(10)
            ->get();
        $recentPayments = Payment::whereHas('pilgrim', fn ($q) => $q->where('agence_id', $agencyId))
            ->where('status', 'completed')
            ->with('pilgrim')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        return view('agence.dashboard', compact(
            'totalPilgrims',
            'totalGuides',
            'totalGroups',
            'visasEnCours',
            'recentPayments'
        ));
    }
}
