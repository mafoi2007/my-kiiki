@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Éditer un utilisateur</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Retour au menu principal</a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Retour aux utilisateurs</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="{{ route('users.update', $user) }}" class="vstack gap-3">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">Nom complet</label>
                <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label class="form-label">Login (non modifiable)</label>
                <input class="form-control" value="{{ $user->login }}" disabled readonly>
            </div>

            <div>
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select" required>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $role }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
            </div>

            <div>
                <label class="form-label">Contact téléphonique</label>
                <input name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="must_change_password" value="1" id="must_change_password" @checked(old('must_change_password', $user->must_change_password))>
                <label class="form-check-label" for="must_change_password">
                    Forcer le changement de mot de passe à la prochaine connexion
                </label>
            </div>

            <div class="alert alert-secondary mb-0">
                Le login et le mot de passe ne sont pas modifiables depuis l'écran d'édition.
            </div>

            <button class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>
</div>
@endsection
