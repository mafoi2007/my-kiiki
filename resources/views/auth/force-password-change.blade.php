@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning-subtle py-3">
                <h5 class="mb-0">Modification obligatoire du mot de passe</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Pour sécuriser votre compte, vous devez définir un nouveau mot de passe contenant au moins 8 caractères, avec des lettres, des chiffres et des caractères spéciaux.</p>
                <form method="post" action="{{ route('password.change.update') }}" class="vstack gap-3">
                    @csrf
                    <div>
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button class="btn btn-primary">Enregistrer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
