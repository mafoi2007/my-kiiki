@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Gestion des utilisateurs</h4>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">← Retour au menu principal</a>
</div>

<div class="row g-4 align-items-start">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary-subtle border-0 py-3">
                <h5 class="mb-0">Créer un utilisateur</h5>
                <small class="text-muted">Si aucun mot de passe n'est saisi, le système assigne <strong>login@1234</strong>.</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('users.store') }}" class="vstack gap-3">
                    @csrf
                    <div>
                        <label class="form-label">Nom complet</label>
                        <input name="name" class="form-control" placeholder="Nom" required>
                    </div>
                    <div>
                        <label class="form-label">E-mail (optionnel)</label>
                        <input type="email" name="email" class="form-control" placeholder="nom@ecole.com">
                    </div>
                    <div>
                        <label class="form-label">Contact téléphonique (optionnel)</label>
                        <input name="phone" class="form-control" placeholder="+2250700000000">
                    </div>
                    <div>
                        <label class="form-label">Login</label>
                        <input name="login" class="form-control" placeholder="login" required>
                    </div>
                    <div>
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Mot de passe (optionnel)</label>
                        <input type="password" name="password" class="form-control" placeholder="Laisser vide pour générer automatiquement">
                        <small class="text-muted">Le mot de passe personnalisé doit avoir au moins 8 caractères.</small>
                    </div>
                    <button class="btn btn-primary">Créer</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center gap-3">
                <h5 class="mb-0">Utilisateurs</h5>
                <span class="badge text-bg-secondary">{{ $users->count() }}</span>
            </div>
            <div class="card-body border-bottom">
                <form method="get" action="{{ route('users.index') }}" class="row g-2 align-items-center">
                    <div class="col-sm-9">
                        <input
                            type="text"
                            class="form-control"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Recherche par nom ou login"
                        >
                    </div>
                    <div class="col-sm-3 d-grid">
                        <button class="btn btn-outline-primary">Rechercher</button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                             <tr>
                                <th>Nom</th>
                                <th>Login</th>
                                <th>Contact</th>
                                <th>Rôle</th>
                                <th class="text-end">Menu utilisateurs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td><code>{{ $user->login }}</code></td>
                                    <td>
                                        <div>{{ $user->email ?? '—' }}</div>
                                        <small class="text-muted">{{ $user->phone ?? '—' }}</small>
                                    </td>
                                    <td><span class="badge rounded-pill text-bg-info">{{ $user->role }}</span></td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('users.show', $user) }}">Visualiser</a></li>
                                                <li><a class="dropdown-item" href="{{ route('users.edit', $user) }}">Éditer</a></li>
                                                <li>
                                                    <form method="post" action="{{ route('users.password.reset', $user) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">Réinitialiser le mot de passe</button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="post" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Supprimer</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucun utilisateur enregistré.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
