<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Secondaire Notes</a>
        <div class="ms-auto d-flex gap-2 align-items-center text-white">
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light">Déconnexion</button>
            </form>
        </div>
    </div>
</nav>
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
</div>
</body>
</html>
