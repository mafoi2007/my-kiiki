<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary min-vh-100 d-flex flex-column">
<main class="container py-5 flex-grow-1">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('images/richapp-logo.svg') }}" alt="Logo RichApp" width="44" height="44" class="rounded-3 bg-white p-1 shadow-sm">
                        <div>
                            <strong class="d-block fs-5">RichApp</strong>
                            <small class="opacity-75">Gestion scolaire - Secondaire Notes</small>
                        </div>
                    </div>
                    <h4 class="mb-0">Connexion à la plateforme</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <form method="post" action="{{ route('login.attempt') }}" class="vstack gap-3">
                        @csrf
                        <div>
                            <label class="form-label">Login</label>
                             <input type="text" class="form-control form-control-lg" name="login" value="{{ old('login') }}" required>
                            @error('login') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                         <div>
                            <label class="form-label">Mot de passe</label>
                             <input type="password" class="form-control form-control-lg" name="password" required>
                        </div>
                        </div>
                        <button class="btn btn-primary btn-lg w-100">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@include('components.footer')
</body>
</html>
