@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Visualiser un utilisateur</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Retour au menu principal</a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Retour aux utilisateurs</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-light">
        <strong>{{ $user->name }}</strong>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Nom complet</dt>
            <dd class="col-sm-9">{{ $user->name }}</dd>

            <dt class="col-sm-3">Login</dt>
            <dd class="col-sm-9"><code>{{ $user->login }}</code></dd>

            <dt class="col-sm-3">Rôle</dt>
            <dd class="col-sm-9">{{ $user->role }}</dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9">{{ $user->email ?? '—' }}</dd>

            <dt class="col-sm-3">Téléphone</dt>
            <dd class="col-sm-9">{{ $user->phone ?? '—' }}</dd>

            <dt class="col-sm-3">Doit changer son mot de passe</dt>
            <dd class="col-sm-9">{{ $user->must_change_password ? 'Oui' : 'Non' }}</dd>

            <dt class="col-sm-3">Créé le</dt>
            <dd class="col-sm-9">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>

            <dt class="col-sm-3">Dernière mise à jour</dt>
            <dd class="col-sm-9">{{ $user->updated_at?->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection
