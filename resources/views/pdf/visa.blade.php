<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Visa Omra - {{ $visa->pilgrim->first_name }} {{ $visa->pilgrim->last_name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; color: #0F3F2E; }
        .section { margin-bottom: 16px; }
        .section h2 { font-size: 14px; margin: 0 0 8px 0; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .label { font-weight: bold; }
        .small { font-size: 10px; color: #777; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td { padding: 4px 2px; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visa Omra</h1>
        <p class="small">Document récapitulatif généré par Serinity</p>
    </div>

    <div class="section">
        <h2>Informations pèlerin</h2>
        <table>
            <tr>
                <td class="label">Nom complet :</td>
                <td>{{ $visa->pilgrim->first_name }} {{ $visa->pilgrim->last_name }}</td>
            </tr>
            <tr>
                <td class="label">Passeport :</td>
                <td>{{ $visa->pilgrim->passport_no }}</td>
            </tr>
            <tr>
                <td class="label">Nationalité :</td>
                <td>{{ $visa->pilgrim->nationality ?? '—' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Dossier visa</h2>
        <table>
            <tr>
                <td class="label">Référence :</td>
                <td>{{ $visa->reference_no ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Statut :</td>
                <td>{{ ucfirst(str_replace('_', ' ', $visa->status)) }}</td>
            </tr>
            <tr>
                <td class="label">Soumis le :</td>
                <td>{{ $visa->submitted_at ? $visa->submitted_at->format('d/m/Y H:i') : '—' }}</td>
            </tr>
            <tr>
                <td class="label">Décision le :</td>
                <td>{{ $visa->decision_at ? $visa->decision_at->format('d/m/Y H:i') : '—' }}</td>
            </tr>
            <tr>
                <td class="label">Date d'expiration :</td>
                <td>{{ $visa->expiry_date ? $visa->expiry_date->format('d/m/Y') : '—' }}</td>
            </tr>
        </table>
    </div>

    @if($visa->pilgrim->package)
        <div class="section">
            <h2>Forfait Omra</h2>
            <table>
                <tr>
                    <td class="label">Nom du forfait :</td>
                    <td>{{ $visa->pilgrim->package->name }}</td>
                </tr>
                <tr>
                    <td class="label">Dates :</td>
                    <td>
                        @if($visa->pilgrim->package->departure_date && $visa->pilgrim->package->return_date)
                            Du {{ $visa->pilgrim->package->departure_date->format('d/m/Y') }}
                            au {{ $visa->pilgrim->package->return_date->format('d/m/Y') }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <p class="small">
        Ce document ne remplace pas le visa officiel délivré par les autorités compétentes mais reprend les informations
        enregistrées dans votre dossier Serinity.
    </p>
</body>
</html>

