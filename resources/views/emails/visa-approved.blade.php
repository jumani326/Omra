<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0F3F2E; margin: 0; font-size: 28px;">🕋 Serinity</h1>
            <p style="color: #7f8c8d; margin: 5px 0 0 0; font-size: 16px;">Votre visa Omra est approuvé</p>
        </div>

        <h2 style="color: #2c3e50; margin-bottom: 20px;">
            Alhamdulillah {{ explode(' ', $pilgrim->first_name)[0] ?? $pilgrim->first_name }} !
        </h2>

        <p style="color: #34495e; line-height: 1.6; font-size: 16px; margin-bottom: 20px;">
            Votre visa pour le forfait
            <strong>{{ $pilgrim->package?->name ?? 'Omra' }}</strong>
            a été <strong>approuvé</strong>.
        </p>

        <p style="color: #34495e; line-height: 1.6; font-size: 16px; margin-bottom: 20px;">
            Vous trouverez en pièce jointe votre visa (ou les documents associés). Nous vous invitons à
            vérifier toutes les informations (nom, numéro de passeport, dates, etc.).
        </p>

        <div style="background-color: #e8f5e9; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #0F3F2E;">
            <p style="color: #2c3e50; margin: 0; font-size: 14px;">
                <strong>Étape suivante :</strong> connectez-vous à votre espace pèlerin pour
                consulter votre dossier et procéder au paiement du solde de votre forfait.
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('pelerin.dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%); color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold;">
                Accéder à mon espace pèlerin
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #ecf0f1; margin: 30px 0;">

        <p style="color: #7f8c8d; font-size: 12px; text-align: center; margin: 0;">
            Serinity – Plateforme de gestion Omra
        </p>
    </div>
</div>

