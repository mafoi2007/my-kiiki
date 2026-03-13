@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Niveaux</h4>
    <a class="btn btn-outline-secondary" href="{{ route('classes.index') }}">Retour classes</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Nouveau niveau</h5>
            <form method="post" action="{{ route('levels.store') }}">@csrf
                <input name="name" class="form-control mb-2" placeholder="Ex: 6ème" required>
                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Liste des niveaux</h5>
            <table class="table">
                @foreach($levels as $level)
                <tr>
                    <td>{{ $level->name }}</td>
                    <td>{{ $level->classes_count }} classe(s)</td>
                    <td class="text-end">
                        <form method="post" action="{{ route('levels.destroy', $level) }}">@csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" @disabled($level->classes_count > 0)>Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection
