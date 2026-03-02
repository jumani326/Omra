<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des pèlerins – {{ $group->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; color: #333; max-width: 700px; margin: 0 auto; padding: 20px; }
        h1 { color: #0F3F2E; font-size: 1.5rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #0F3F2E; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .meta { color: #666; font-size: 0.9rem; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <p>Bonjour,</p>
    <p>Voici la liste des pèlerins du groupe <strong>{{ $group->name }}</strong>.</p>
    <p>Cette liste est également jointe en <strong>PDF</strong> et en <strong>fichier Excel (.xlsx)</strong> stylisé (colonnes, en-têtes colorés) pour impression et ouverture dans Excel.</p>

    @if($group->pilgrims->isEmpty())
        <p>Aucun pèlerin n’est actuellement dans ce groupe.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Passeport</th>
                    <th>Nationalité</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group->pilgrims as $p)
                <tr>
                    <td>{{ $p->first_name }}</td>
                    <td>{{ $p->last_name }}</td>
                    <td>{{ $p->email ?? '—' }}</td>
                    <td>{{ $p->phone ?? '—' }}</td>
                    <td>{{ $p->passport_no ?? '—' }}</td>
                    <td>{{ $p->nationality ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p class="meta">Total : {{ $group->pilgrims->count() }} pèlerin(s).</p>
    @endif

    <p class="meta">Cet email a été envoyé depuis {{ config('app.name') }}.</p>
</body>
</html>
