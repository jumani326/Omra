<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SerinityPasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            $code = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
            Cache::put('password_reset:' . $email, ['code' => $code], now()->addHour());

            try {
                Mail::to($email)->send(new SerinityPasswordResetMail(
                    $email,
                    $code,
                    route('password.reset', ['email' => $email])
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Envoi email réinitialisation: ' . $e->getMessage());
                return redirect()->route('password.reset', ['email' => $email])
                    ->with('error', 'L\'envoi de l\'email a échoué. Vérifiez la configuration mail puis réessayez depuis « Mot de passe oublié ».');
            }
        }

        return redirect()->route('password.reset', ['email' => $email])
            ->with('status', $user ? 'Un code à 8 chiffres vous a été envoyé par email. Saisissez-le ci-dessous.' : 'Si ce compte existe, un code vous a été envoyé. Saisissez votre email et le code ci-dessous.');
    }
}
