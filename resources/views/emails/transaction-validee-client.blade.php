<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement validé</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0F3F2E; color: white; padding: 16px; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; }
        .success { background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 8px; margin: 16px 0; font-weight: bold; }
        .info { margin: 12px 0; }
        .label { font-weight: bold; color: #0F3F2E; }
        .footer { margin-top: 24px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 18px;">Paiement validé</h1>
    </div>
    <div class="content">
        <p>Bonjour {{ $transaction->client_nom ?? ($transaction->pilgrim ? $transaction->pilgrim->first_name : 'Client') }},</p>
        <div class="success">
            Votre agence a validé votre paiement. Les détails de la transaction sont en pièce jointe (fiche PDF).
        </div>
        <p><strong>Récapitulatif :</strong></p>
        <div class="info">
            <span class="label">Montant :</span> {{ number_format($transaction->montant, 2, ',', ' ') }} FDJ
        </div>
        <div class="info">
            <span class="label">Méthode :</span> {{ $transaction->compteMarchand->nom_methode ?? '—' }}
        </div>
        <div class="info">
            <span class="label">Référence :</span> {{ $transaction->reference ?? '—' }}
        </div>
        <div class="info">
            <span class="label">Statut :</span> Validé
        </div>
        <p>Conservez la fiche PDF jointe pour vos archives. Vous pouvez également la télécharger depuis votre espace client.</p>
    </div>
    <div class="footer">
        <p>Cet email a été envoyé automatiquement par {{ config('app.name') }}.</p>
    </div>
</body>
</html>
