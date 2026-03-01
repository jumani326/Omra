<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger vers dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Si déjà connecté, rediriger vers dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $user = Auth::user();

            // Agence : vérifier que l'agence est validée par le ministère
            if ($user->hasRole('agence')) {
                $agency = $user->agency ?? $user->agence_id ? \App\Models\Agency::find($user->agence_id) : null;
                if ($agency && ! $agency->validated) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    throw ValidationException::withMessages([
                        'email' => 'Votre agence n\'a pas encore été validée par le ministère. Vous serez notifié par email.',
                    ]);
                }
            }

            // Pèlerin : vérifier que le compte est activé (lien reçu par email)
            if ($user->hasRole('pelerin') && ! $user->activated_at) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                throw ValidationException::withMessages([
                    'email' => 'Veuillez activer votre compte en cliquant sur le lien reçu par email.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('Les identifiants fournis sont incorrects.'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
