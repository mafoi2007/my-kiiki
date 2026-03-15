<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Liste des élèves - {{ $class->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .muted { color: #666; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f3f3f3; }
    </style>
</head>
<body>
    <h1>Classe {{ $class->name }}</h1>
    <div class="muted">Liste des élèves inscrits</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Matricule national</th>
                <th>Matricule établissement</th>
                <th>Nom complet</th>
                <th>Sexe</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->matricule }}</td>
                    <td>{{ $student->school_matricule }}</td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->sex === 'M' ? 'M' : 'F' }}</td>
                    <td>{{ $student->status === 'N' ? 'Nouveau' : 'Redoublant' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucun élève dans cette classe.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
