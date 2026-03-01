<?php

namespace App\Http\Controllers\Ministere;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Pilgrim;
use App\Models\Visa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MinistereDashboardController extends Controller
{
    public function index()
    {
        $totalAgencies = Agency::count();
        $activeAgencies = Agency::where('validated', true)->count();
        $inactiveAgencies = $totalAgencies - $activeAgencies;
        $pilgrimsByAgency = Agency::withCount('pilgrims')->get()->map(fn ($a) => ['agency' => $a->name, 'total' => $a->pilgrims_count]);
        $visasEnAttente = Visa::whereIn('status', ['submitted', 'processing'])->count();
        $agencies = Agency::withCount('pilgrims')->orderBy('created_at', 'desc')->get();

        return view('ministere.dashboard', compact(
            'totalAgencies',
            'activeAgencies',
            'inactiveAgencies',
            'pilgrimsByAgency',
            'visasEnAttente',
            'agencies'
        ));
    }
}
