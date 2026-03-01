<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SerinityWelcomeMail;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function choose()
    {
        return view('auth.register.choose');
    }

    public function showAgenceForm()
    {
        return view('auth.register.agence');
    }

    public function showPelerinForm()
    {
        return view('auth.register.pelerin');
    }

    public function registerAgence(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'agency_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $agency = Agency::create([
            'name' => $request->agency_name,
            'license_no' => 'PENDING-' . strtoupper(Str::random(8)),
            'ministry_status' => 'pending',
            'validated' => false,
            'contact' => [
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address ?? '',
            ],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agence_id' => $agency->id,
            'active' => true,
        ])->assignRole('agence');

        return redirect()->route('register.success')
            ->with('message', 'Votre inscription a été enregistrée. Vous pourrez vous connecter après validation de votre agence par le ministère. Un email de confirmation vous a été envoyé.');
    }

    public function registerPelerin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $code = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activation_code' => $code,
            'activation_code_expires_at' => now()->addHours(24),
            'activated_at' => null,
            'active' => true,
        ]);
        $user->assignRole('pelerin');

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new SerinityWelcomeMail($user, $code));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Envoi email activation échoué: ' . $e->getMessage(), [
                'email' => $user->email,
                'exception' => $e,
            ]);
            return redirect()->route('register.verify')
                ->with('pending_activation_email', $request->email)
                ->with('error', 'L\'email avec le code n\'a pas pu être envoyé. Vérifiez la configuration (MAIL_MAILER=smtp et MAIL_PASSWORD dans .env), puis cliquez sur « Renvoyer le code par email ».');
        }

        return redirect()->route('register.verify')
            ->with('pending_activation_email', $request->email)
            ->with('message', 'Inscription réussie. Consultez votre boîte email (et les spams) et saisissez le code à 8 chiffres reçu.');
    }

    public function showVerifyForm(Request $request)
    {
        $email = $request->session()->get('pending_activation_email');
        if (! $email) {
            return redirect()->route('login')->with('error', 'Session expirée. Connectez-vous ou inscrivez-vous à nouveau.');
        }
        return view('auth.register.verify', compact('email'));
    }

    public function resendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;
        $user = User::where('email', $email)->whereNull('activated_at')->first();
        if (! $user || ! $user->activation_code) {
            return back()->with('error', 'Compte introuvable ou déjà activé.');
        }
        $code = $user->activation_code;
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new SerinityWelcomeMail($user, $code));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Renvoy email activation: ' . $e->getMessage());
            return back()->with('error', 'L\'envoi a échoué. Vérifiez MAIL_MAILER=smtp et MAIL_PASSWORD dans .env.');
        }
        return back()->with('message', 'Un nouvel email avec le code a été envoyé. Consultez votre boîte email et les spams.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:8|regex:/^[0-9]+$/',
        ]);

        $user = User::where('email', $request->email)
            ->where('activation_code', $request->code)
            ->whereNull('activated_at')
            ->first();

        if (! $user) {
            return back()->withErrors(['code' => 'Code invalide ou déjà utilisé.'])->withInput();
        }

        if ($user->activation_code_expires_at && $user->activation_code_expires_at->isPast()) {
            return back()->withErrors(['code' => 'Ce code a expiré. Demandez un nouveau code.'])->withInput();
        }

        $user->update([
            'activated_at' => now(),
            'activation_code' => null,
            'activation_code_expires_at' => null,
        ]);

        $request->session()->forget('pending_activation_email');
        return redirect()->route('login')->with('status', 'Votre compte est activé. Vous pouvez vous connecter.');
    }

    public function success(Request $request)
    {
        $message = $request->session()->get('message', 'Votre inscription a été enregistrée.');
        return view('auth.register.success', compact('message'));
    }
}
