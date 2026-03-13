<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Enseignants - {{ $class->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <p style="margin:0 0 10px 0;">Utilisez la commande <strong>Imprimer</strong> de votre navigateur puis <strong>Enregistrer au format PDF</strong>.</p>
    <h2>Enseignants intervenant - Classe {{ $class->name }} ({{ $class->code }})</h2>
    <p>Niveau : {{ $class->level?->name }}</p>

    <table>
        <thead>
            <tr>
                <th>Enseignant</th>
                <th>Matière</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->teacher->name }}</td>
                    <td>{{ $assignment->subject->name }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Aucun enseignant assigné.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
