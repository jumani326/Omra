<?php

namespace App\Http\Controllers\Pelerin;

use App\Http\Controllers\Controller;
use App\Models\Pilgrim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelerinDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pilgrim = Pilgrim::where('user_id', $user->id)->orWhere('email', $user->email)
            ->with(['package', 'visa', 'payments', 'group', 'guide.user'])
            ->first();

        return view('pelerin.dashboard', compact('pilgrim'));
    }

    public function profile()
    {
        $user = Auth::user();
        $pilgrim = Pilgrim::where('user_id', $user->id)->orWhere('email', $user->email)
            ->with(['package', 'visa', 'payments', 'group', 'guide.user'])
            ->first();

        return view('pelerin.profile', compact('pilgrim'));
    }
}
