@extends('layouts.app')

@section('content')
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
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Utilisateurs</h5>
                <span class="badge text-bg-secondary">{{ $users->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Nom</th><th>Login</th><th>Contact</th><th>Rôle</th><th class="text-end">Actions</th></tr
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
                                        <form method="post" action="{{ route('users.name.update', $user) }}" class="d-flex gap-2 justify-content-end mb-2">
                                            @csrf
                                            @method('PATCH')
                                            <input name="name" class="form-control form-control-sm" value="{{ $user->name }}" required>
                                            <button class="btn btn-sm btn-outline-primary">Modifier nom</button>
                                        </form>
                                        <form method="post" action="{{ route('users.password.reset', $user) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-warning">Réinitialiser mot de passe</button>
                                        </form>
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
