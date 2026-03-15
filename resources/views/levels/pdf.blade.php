<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Liste des niveaux</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <p style="margin:0 0 10px 0;">Utilisez la commande <strong>Imprimer</strong> de votre navigateur puis <strong>Enregistrer au format PDF</strong>.</p>
    <h2>Liste des niveaux</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Niveau</th>
                <th>Nombre de classes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($levels as $level)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $level->name }}</td>
                    <td>{{ $level->classes_count }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Aucun niveau disponible.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
