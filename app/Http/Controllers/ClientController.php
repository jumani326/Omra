<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\Notification;
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
     * Catalogue de tous les forfaits créés par les agences (visibles au client pour postuler).
     */
    public function packagesIndex(Request $request)
    {
        if (!Auth::user()->hasRole('pelerin')) {
            abort(403);
        }

        // Tous les forfaits créés par les agences (visibles pour postuler ; ceux sans place affichent "Complet")
        $packages = Package::query()
            ->with(['branch.agency', 'hotelMecca', 'hotelMedina'])
            ->orderBy('departure_date', 'desc')
            ->paginate(12);

        $pilgrim = Pilgrim::where('user_id', Auth::id())
            ->orWhere('email', Auth::user()->email)
            ->first();

        return view('client.packages.index', compact('packages', 'pilgrim'));
    }

    /**
     * Détail d'un forfait pour le client (voir plus).
     */
    public function packageShow(Package $package)
    {
        if (!Auth::user()->hasRole('pelerin')) {
            abort(403);
        }

        $package->load(['branch.agency', 'hotelMecca', 'hotelMedina']);
        $pilgrim = Pilgrim::where('user_id', Auth::id())
            ->orWhere('email', Auth::user()->email)
            ->first();

        return view('client.packages.show', compact('package', 'pilgrim'));
    }

    /**
     * Affiche le formulaire pour postuler à un forfait (création demande en attente).
     */
    public function choosePackage(Package $package)
    {
        if (!Auth::user()->hasRole('pelerin')) {
            abort(403);
        }
        $existing = Pilgrim::where('email', Auth::user()->email)
            ->orWhere('user_id', Auth::id())
            ->first();
        if ($existing) {
            $msg = $existing->status === 'pending'
                ? 'Votre demande est en attente de validation par l\'agence.'
                : 'Vous avez déjà un dossier pèlerin.';
            return redirect()->route('pelerin.dashboard')->with('info', $msg);
        }
        if ($package->slots_remaining <= 0) {
            return redirect()->route('client.packages.index')->with('error', 'Ce forfait n\'a plus de places disponibles.');
        }
        $package->load(['branch.agency']);
        return view('client.choose-package', compact('package'));
    }

    /**
     * Enregistre la candidature : crée le dossier pèlerin avec statut "pending" (en attente de validation par l'agence).
     */
    public function storeChoosePackage(Request $request, Package $package)
    {
        if (!Auth::user()->hasRole('pelerin')) {
            abort(403);
        }
        $existing = Pilgrim::where('email', Auth::user()->email)
            ->orWhere('user_id', Auth::id())
            ->first();
        if ($existing) {
            return redirect()->route('pelerin.dashboard')->with('info', 'Vous avez déjà une demande ou un dossier pèlerin.');
        }

        $validated = $request->validate([
            'passport_no' => 'required|string|max:50|unique:pilgrims,passport_no',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'nationality' => 'required|string|max:100',
        ]);

        $validated['email'] = Auth::user()->email;
        $validated['user_id'] = Auth::id();
        $validated['branch_id'] = $package->branch_id;
        $validated['agence_id'] = $package->branch->agency_id;
        $validated['package_id'] = $package->id;
        $validated['agent_id'] = null;
        $validated['status'] = 'pending';

        $pilgrim = $this->pilgrimService->create($validated);

        // Créer une notification pour tous les utilisateurs de l'agence concernée
        $package->loadMissing('branch.agency.users');
        $agency = $package->branch?->agency;
        if ($agency) {
            foreach ($agency->users as $agencyUser) {
                Notification::create([
                    'user_id' => $agencyUser->id,
                    'type' => 'agency',
                    'channel' => 'in_app',
                    'content' => sprintf(
                        'Nouvelle candidature pour le forfait "%s" : %s %s.',
                        $package->name,
                        $pilgrim->first_name,
                        $pilgrim->last_name
                    ),
                    'sent_at' => now(),
                ]);
            }
        }

        return redirect()->route('pelerin.dashboard')->with('success', 'Votre demande a bien été envoyée. L\'agence la validera sous peu. Vous pourrez alors poursuivre la procédure.');
    }
}
