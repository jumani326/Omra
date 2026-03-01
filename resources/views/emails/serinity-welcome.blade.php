<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0F3F2E; margin: 0; font-size: 28px;">🌟 Serinity</h1>
            <p style="color: #7f8c8d; margin: 5px 0 0 0; font-size: 16px;">Plateforme de gestion Omra</p>
        </div>

        <h2 style="color: #2c3e50; margin-bottom: 20px;">Salut {{ explode(' ', $user->name)[0] ?? $user->name }} ! 👋</h2>

        <p style="color: #34495e; line-height: 1.6; font-size: 16px; margin-bottom: 20px;">
            Bienvenue sur Serinity ! Vous venez de créer votre compte pèlerin.
            Nous sommes ravis de vous accompagner pour votre Omra. 🕋
        </p>

        <p style="color: #34495e; line-height: 1.6; font-size: 16px; margin-bottom: 25px;">
            Pour activer votre compte et accéder à votre espace personnel (votre dossier, visa, paiements, guide),
            saisissez le code de vérification ci-dessous sur la page d'activation :
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <div style="background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%);
               color: white; padding: 20px 40px; border-radius: 15px;
               font-weight: bold; font-size: 32px; display: inline-block;
               box-shadow: 0 4px 15px rgba(15, 63, 46, 0.4); letter-spacing: 8px;">
                {{ $codeVerification }}
            </div>
        </div>

        <div style="background-color: #e8f5e9; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #0F3F2E;">
            <p style="color: #2c3e50; margin: 0; font-size: 14px;">
                <strong>💡 Astuce :</strong> Ce code est valide pendant 24 heures. Si vous ne l'utilisez pas à temps,
                vous pourrez demander un nouveau code depuis la page de connexion.
            </p>
        </div>

        <p style="color: #7f8c8d; line-height: 1.6; font-size: 14px; margin-top: 30px;">
            Une fois votre compte activé, vous pourrez :
        </p>
        <ul style="color: #34495e; line-height: 1.8; font-size: 14px; margin: 15px 0;">
            <li>📋 Consulter votre dossier pèlerin</li>
            <li>🛂 Suivre le statut de votre visa</li>
            <li>💳 Voir l'état de vos paiements</li>
            <li>👥 Connaître votre groupe et votre guide</li>
        </ul>

        <hr style="border: none; border-top: 1px solid #ecf0f1; margin: 30px 0;">

        <p style="color: #7f8c8d; font-size: 12px; text-align: center; margin: 0;">
            Si vous n'avez pas créé de compte sur Serinity, vous pouvez ignorer cet email.
        </p>
    </div>
</div>
