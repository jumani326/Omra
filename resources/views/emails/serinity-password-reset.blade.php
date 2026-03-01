<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0F3F2E; margin: 0; font-size: 28px;">🔐 Serinity</h1>
            <p style="color: #7f8c8d; margin: 5px 0 0 0; font-size: 16px;">Récupération de compte</p>
        </div>

        <h2 style="color: #2c3e50; margin-bottom: 20px;">Réinitialisation de votre mot de passe</h2>

        <p style="color: #34495e; line-height: 1.6; font-size: 16px; margin-bottom: 20px;">
            Vous avez demandé à réinitialiser le mot de passe de votre compte Serinity associé à <strong>{{ $email }}</strong>.
        </p>

        <p style="color: #34495e; line-height: 1.6; font-size: 16px; margin-bottom: 25px;">
            Utilisez le code à 8 chiffres ci-dessous sur la page de réinitialisation pour définir un nouveau mot de passe :
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <div style="background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%);
               color: white; padding: 20px 40px; border-radius: 15px;
               font-weight: bold; font-size: 32px; display: inline-block;
               box-shadow: 0 4px 15px rgba(15, 63, 46, 0.4); letter-spacing: 8px;">
                {{ $codeVerification }}
            </div>
        </div>

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $resetUrl }}" style="display: inline-block; background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%); color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold;">
                Aller à la page de réinitialisation
            </a>
        </div>

        <div style="background-color: #fff3e0; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #C9A227;">
            <p style="color: #2c3e50; margin: 0; font-size: 14px;">
                <strong>⏱ Ce code est valide 1 heure.</strong> Si vous n'êtes pas à l'origine de cette demande, ignorez cet email et votre mot de passe restera inchangé.
            </p>
        </div>

        <hr style="border: none; border-top: 1px solid #ecf0f1; margin: 30px 0;">

        <p style="color: #7f8c8d; font-size: 12px; text-align: center; margin: 0;">
            Serinity – Plateforme de gestion Omra
        </p>
    </div>
</div>
