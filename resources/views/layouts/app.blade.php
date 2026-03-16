<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>{{ config('app.name') }} - Gestion des notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .required-label::after {
            content: ' *';
            color: #dc3545;
        }

        .required-asterisk {
            color: #dc3545;
            margin-left: 0.35rem;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-body-tertiary min-vh-100 d-flex flex-column">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>
        <div class="ms-auto d-flex gap-2 align-items-center text-white">
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light">Déconnexion</button>
            </form>
        </div>
    </div>
</nav>
<main class="container py-4 flex-grow-1">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @yield('content')
</main>
@include('components.footer')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-auto-dismiss]').forEach((alertElement) => {
            const timeout = Number(alertElement.dataset.autoDismiss) || 5000;

            window.setTimeout(() => {
                alertElement.remove();
            }, timeout);
        });

        document.querySelectorAll('form :is(input, select, textarea)[required]').forEach((field) => {
            if (['hidden', 'submit', 'button', 'reset', 'checkbox', 'radio'].includes(field.type)) {
                return;
            }

            const previousElement = field.previousElementSibling;
            if (previousElement?.matches('label')) {
                previousElement.classList.add('required-label');
                return;
            }

            if (field.nextElementSibling?.classList.contains('required-asterisk')) {
                return;
            }

            const marker = document.createElement('span');
            marker.className = 'required-asterisk';
            marker.setAttribute('aria-hidden', 'true');
            marker.textContent = '*';
            field.insertAdjacentElement('afterend', marker);
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>