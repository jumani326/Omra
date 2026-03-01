<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Pilgrim;
use App\Services\PilgrimService;
use App\Repositories\PilgrimRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    private PilgrimService $pilgrimService;

    public function __construct()
    {
        $this->pilgrimService = new PilgrimService(new PilgrimRepository());
    }

    /**
     * Affiche le formulaire pour choisir un forfait et démarrer la procédure.
     */
    public function choosePackage(Package $package)
    {
        if (!Auth::user()->hasRole('Pèlerin (Client)')) {
            abort(403);
        }
        $existing = Pilgrim::where('email', Auth::user()->email)->first();
        if ($existing) {
            return redirect()->route('dashboard')->with('info', 'Vous avez déjà un dossier pèlerin.');
        }
        if ($package->slots_remaining <= 0) {
            return redirect()->route('dashboard')->with('error', 'Ce forfait n\'a plus de places disponibles.');
        }
        return view('client.choose-package', compact('package'));
    }

    /**
     * Enregistre le choix du forfait et crée le dossier pèlerin.
     */
    public function storeChoosePackage(Request $request, Package $package)
    {
        if (!Auth::user()->hasRole('Pèlerin (Client)')) {
            abort(403);
        }
        $existing = Pilgrim::where('email', Auth::user()->email)->first();
        if ($existing) {
            return redirect()->route('dashboard')->with('info', 'Vous avez déjà un dossier pèlerin.');
        }

        $validated = $request->validate([
            'passport_no' => 'required|string|max:50|unique:pilgrims,passport_no',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'nationality' => 'required|string|max:100',
        ]);

        $validated['email'] = Auth::user()->email;
        $validated['branch_id'] = $package->branch_id;
        $validated['package_id'] = $package->id;
        $validated['agent_id'] = null;
        $validated['status'] = 'registered';

        $pilgrim = $this->pilgrimService->create($validated);

        return redirect()->route('dashboard')->with('success', 'Votre dossier a été créé. Vous pouvez suivre votre procédure depuis le tableau de bord.');
    }
}
