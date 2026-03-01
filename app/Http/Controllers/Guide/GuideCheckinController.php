<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Checkin;
use App\Models\Pilgrim;
use Illuminate\Http\Request;

class GuideCheckinController extends Controller
{
    public function checkin(Pilgrim $pilgrim)
    {
        $guide = auth()->user()->guide;
        if (! $guide || $pilgrim->guide_id != $guide->id) {
            abort(403, 'Ce pèlerin n\'est pas dans votre groupe.');
        }
        Checkin::create([
            'pilgrim_id' => $pilgrim->id,
            'guide_id' => $guide->id,
            'type' => 'checkin',
        ]);
        return redirect()->back()->with('success', 'Check-in enregistré.');
    }

    public function checkout(Pilgrim $pilgrim)
    {
        $guide = auth()->user()->guide;
        if (! $guide || $pilgrim->guide_id != $guide->id) {
            abort(403, 'Ce pèlerin n\'est pas dans votre groupe.');
        }
        Checkin::create([
            'pilgrim_id' => $pilgrim->id,
            'guide_id' => $guide->id,
            'type' => 'checkout',
        ]);
        return redirect()->back()->with('success', 'Check-out enregistré.');
    }
}
