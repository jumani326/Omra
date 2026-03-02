<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de paiement - {{ $transaction->client_nom ?? $transaction->pilgrim?->first_name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 2px solid #0F3F2E; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #0F3F2E; }
        .ref { font-size: 14px; font-weight: bold; color: #0F3F2E; }
        .section { margin-bottom: 16px; }
        .section h2 { font-size: 13px; margin: 0 0 8px 0; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f5f5; font-weight: bold; width: 40%; }
        .total { font-size: 16px; font-weight: bold; margin-top: 20px; color: #0F3F2E; }
        .statut { display: inline-block; padding: 4px 10px; border-radius: 4px; font-weight: bold; }
        .statut-en_attente { background: #FEF3C7; color: #92400E; }
        .statut-valide { background: #D1FAE5; color: #065F46; }
        .statut-refuse { background: #FEE2E2; color: #991B1B; }
        .footer { margin-top: 40px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fiche détaillée de paiement</h1>
        <p class="ref">Transaction n° {{ $transaction->id }} — {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Client (pèlerin)</h2>
        <table>
            <tr>
                <th>Nom complet</th>
                <td>{{ $transaction->client_nom ?? ($transaction->pilgrim ? $transaction->pilgrim->first_name . ' ' . $transaction->pilgrim->last_name : '—') }}</td>
            </tr>
            @if($transaction->pilgrim)
            <tr>
                <th>Email</th>
                <td>{{ $transaction->pilgrim->email ?? '—' }}</td>
            </tr>
            <tr>
                <th>Téléphone</th>
                <td>{{ $transaction->pilgrim->phone ?? '—' }}</td>
            </tr>
            <tr>
                <th>Passeport</th>
                <td>{{ $transaction->pilgrim->passport_no ?? '—' }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <h2>Informations de la transaction</h2>
        <table>
            <tr>
                <th>Agence</th>
                <td>{{ $transaction->compteMarchand->nom_agence ?? '—' }}</td>
            </tr>
            <tr>
                <th>Méthode de paiement</th>
                <td>{{ $transaction->compteMarchand->nom_methode ?? '—' }}</td>
            </tr>
            <tr>
                <th>Numéro du compte marchand</th>
                <td>{{ $transaction->compteMarchand->numero_compte ?? '—' }}</td>
            </tr>
            <tr>
                <th>Montant (FDJ)</th>
                <td><strong>{{ number_format($transaction->montant, 2, ',', ' ') }}</strong></td>
            </tr>
            <tr>
                <th>Référence</th>
                <td>{{ $transaction->reference ?? '—' }}</td>
            </tr>
            <tr>
                <th>Statut</th>
                <td><span class="statut statut-{{ $transaction->statut }}">{{ ucfirst(str_replace('_', ' ', $transaction->statut)) }}</span></td>
            </tr>
        </table>
    </div>

    <p class="total">Total : {{ number_format($transaction->montant, 2, ',', ' ') }} FDJ</p>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y H:i') }} — {{ config('app.name') }}</p>
        <p>Cette fiche confirme la déclaration de paiement du client. L'agence validera la réception des fonds.</p>
    </div>
</body>
</html>
