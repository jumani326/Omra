<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AgenceProfileController extends Controller
{
    /**
     * Affiche le profil de l'agence et du compte connecté.
     */
    public function show()
    {
        $user = Auth::user();
        $agency = $user->agency;
        $branch = $user->branch;

        if (! $agency) {
            return view('agence.profile', [
                'agency' => null,
                'user' => $user,
                'branch' => null,
            ]);
        }

        $agency->load('branches');

        return view('agence.profile', compact('agency', 'user', 'branch'));
    }

    /**
     * Met à jour les informations du profil (compte utilisateur et informations liées à l'agence).
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $agency = $user->agency;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ];

        if ($agency) {
            $rules['agency_name'] = ['required', 'string', 'max:255'];
            $rules['contact_phone'] = ['nullable', 'string', 'max:100'];
            $rules['contact_email'] = ['nullable', 'email', 'max:255'];
            $rules['contact_address'] = ['nullable', 'string', 'max:500'];
            $rules['logo'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        if ($agency) {
            $agency->name = $validated['agency_name'];
            $contact = $agency->contact ?? [];
            $contact['phone'] = $validated['contact_phone'] ?? null;
            $contact['email'] = $validated['contact_email'] ?? null;
            $contact['address'] = $validated['contact_address'] ?? null;
            $agency->contact = $contact;

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                if ($agency->logo) {
                    Storage::disk('public')->delete($agency->logo);
                }
                $agency->logo = $request->file('logo')->store('agencies/logos', 'public');
            }
            $agency->save();
        }

        return redirect()
            ->route('agence.profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
