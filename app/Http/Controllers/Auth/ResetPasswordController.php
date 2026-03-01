<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /**
     * Étape 1 : afficher la vue de saisie du code (email + code à 8 chiffres).
     */
    public function showResetForm(Request $request)
    {
        return view('auth.verify-reset-code', [
            'email' => $request->query('email', old('email')),
        ]);
    }

    /**
     * Vérifier le code : si correct, enregistrer l'email en session et rediriger vers l'étape 2.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:8|regex:/^[0-9]+$/',
        ]);

        $key = 'password_reset:' . $request->email;
        $cached = Cache::get($key);

        if (! $cached || ($cached['code'] ?? '') !== $request->code) {
            return back()->withErrors(['code' => 'Code invalide ou expiré. Demandez un nouveau code depuis « Mot de passe oublié ».'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Aucun compte associé à cet email.'])->withInput();
        }

        session(['password_reset_email' => $request->email]);

        return redirect()->route('password.new');
    }

    /**
     * Étape 2 : afficher la vue de saisie du nouveau mot de passe (uniquement si le code a été validé).
     */
    public function showNewPasswordForm(Request $request)
    {
        $email = session('password_reset_email');
        if (! $email) {
            return redirect()->route('password.reset')->with('error', 'Session expirée. Saisissez à nouveau votre email et le code reçu.');
        }

        return view('auth.new-password', ['email' => $email]);
    }

    /**
     * Enregistrer le nouveau mot de passe (email pris depuis la session).
     */
    public function reset(Request $request)
    {
        $email = session('password_reset_email');
        if (! $email) {
            return redirect()->route('password.reset')->with('error', 'Session expirée. Recommencez la procédure.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();
        if (! $user) {
            session()->forget('password_reset_email');
            return redirect()->route('password.reset')->with('error', 'Compte introuvable.');
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        Cache::forget('password_reset:' . $email);
        session()->forget('password_reset_email');

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.');
    }
}
