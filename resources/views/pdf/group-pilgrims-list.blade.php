<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des pèlerins – {{ $group->name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #0F3F2E; }
        .header h1 { margin: 0; font-size: 18px; color: #0F3F2E; }
        .header .sub { font-size: 10px; color: #666; margin-top: 4px; }
        .meta { margin-bottom: 15px; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #0F3F2E; color: white; font-size: 10px; font-weight: bold; }
        tr:nth-child(even) { background: #f5f5f5; }
        .footer { margin-top: 20px; font-size: 9px; color: #777; text-align: center; }
        .total { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des pèlerins – {{ $group->name }}</h1>
        <p class="sub">{{ config('app.name') }} – Document à l’usage du guide</p>
    </div>

    <div class="meta">
        Généré le {{ now()->format('d/m/Y à H:i') }} · Groupe : {{ $group->name }}
    </div>

    @if($group->pilgrims->isEmpty())
        <p>Aucun pèlerin dans ce groupe.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>N° Passeport</th>
                    <th>Nationalité</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group->pilgrims as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->first_name }}</td>
                    <td>{{ $p->last_name }}</td>
                    <td>{{ $p->email ?? '—' }}</td>
                    <td>{{ $p->phone ?? '—' }}</td>
                    <td>{{ $p->passport_no ?? '—' }}</td>
                    <td>{{ $p->nationality ?? '—' }}</td>
                    <td>{{ $p->status ? ucfirst(str_replace('_', ' ', $p->status)) : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p class="total">Total : {{ $group->pilgrims->count() }} pèlerin(s)</p>
    @endif

    <div class="footer">
        Ce document a été généré automatiquement. {{ config('app.name') }}.
    </div>
</body>
</html>
