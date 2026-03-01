<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ActivateAccountController extends Controller
{
    public function show(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        if (! $email || ! $token) {
            return redirect()->route('login')->with('error', 'Lien d\'activation invalide.');
        }

        $user = User::where('email', $email)
            ->where('activation_code', $token)
            ->whereNull('activated_at')
            ->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Lien d\'activation invalide ou déjà utilisé.');
        }

        if ($user->activation_code_expires_at && $user->activation_code_expires_at->isPast()) {
            return redirect()->route('login')->with('error', 'Ce lien d\'activation a expiré. Demandez un nouveau lien.');
        }

        $user->update([
            'activated_at' => now(),
            'activation_code' => null,
            'activation_code_expires_at' => null,
        ]);

        return redirect()->route('login')->with('status', 'Votre compte est activé. Vous pouvez vous connecter.');
    }
}
