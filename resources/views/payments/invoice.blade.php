<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $payment->ref_no }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 2px solid #0F3F2E; padding-bottom: 10px; margin-bottom: 20px; }
        .ref { font-size: 18px; font-weight: bold; color: #0F3F2E; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .total { font-size: 16px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 40px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UMS — Facture</h1>
        <p class="ref">N° {{ $payment->ref_no }}</p>
        <p>Date : {{ $payment->payment_date->format('d/m/Y') }}</p>
    </div>

    <table>
        <tr><th>Client (Pèlerin)</th><td>{{ $payment->pilgrim->first_name }} {{ $payment->pilgrim->last_name }}</td></tr>
        <tr><th>Passeport</th><td>{{ $payment->pilgrim->passport_no }}</td></tr>
        <tr><th>Email</th><td>{{ $payment->pilgrim->email ?? '—' }}</td></tr>
    </table>

    <table>
        <tr><th>Description</th><th>Montant (MAD)</th></tr>
        <tr><td>Paiement — {{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td><td>{{ number_format($payment->amount, 2, ',', ' ') }}</td></tr>
    </table>

    <p class="total">Total : {{ number_format($payment->amount, 2, ',', ' ') }} MAD</p>
    <p>Statut : {{ $payment->status === 'completed' ? 'Payé' : ucfirst($payment->status) }}</p>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y H:i') }} — Umrah Management System</p>
    </div>
</body>
</html>
