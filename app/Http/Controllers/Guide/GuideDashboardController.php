<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Pilgrim;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuideDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guide = $user->guide;
        if (! $guide || ! $guide->group_id) {
            return view('guide.dashboard', [
                'group' => null,
                'pilgrims' => collect(),
                'checkinsToday' => collect(),
            ]);
        }

        $group = $guide->group;
        $pilgrims = Pilgrim::where('group_id', $guide->group_id)->with('visa')->get();
        $today = now()->toDateString();
        $checkinsToday = Checkin::where('guide_id', $guide->id)
            ->whereDate('created_at', $today)
            ->with('pilgrim')
            ->orderBy('created_at')
            ->get();

        return view('guide.dashboard', compact('group', 'pilgrims', 'checkinsToday'));
    }
}
