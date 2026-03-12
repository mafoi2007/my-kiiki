@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Créer un utilisateur</h5>
            <form method="post" action="{{ route('users.store') }}">@csrf
                <input name="name" class="form-control mb-2" placeholder="Nom" required>
                <input name="login" class="form-control mb-2" placeholder="login" required>
                <select name="role" class="form-select mb-2" required>
                    @foreach($roles as $role)
                        <option value="{{ $role }}">{{ $role }}</option>
                    @endforeach
                </select>
                <input type="password" name="password" class="form-control mb-2" placeholder="Mot de passe" required>
                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Utilisateurs</h5>
            <table class="table table-sm">
                <tr><th>Nom</th><th>Login</th><th>Rôle</th></tr>
                @foreach($users as $user)
                    <tr><td>{{ $user->name }}</td><td>{{ $user->login }}</td><td>{{ $user->role }}</td></tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection
