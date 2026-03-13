@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Gestion des classes</h4>
    <a class="btn btn-outline-primary" href="{{ route('levels.index') }}">Gérer les niveaux</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Nouvelle classe</h5>
            <form method="post" action="{{ route('classes.store') }}">@csrf
                <input name="name" class="form-control mb-2" placeholder="Nom de classe" required>
                <input name="code" class="form-control mb-2" placeholder="Code de classe" required>
                <select name="level_id" class="form-select mb-2" required>
                    <option value="">Choisir un niveau</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Liste des classes</h5>
             <table class="table align-middle">
                <tr><th>Nom</th><th>Code</th><th>Niveau</th><th>Élèves</th><th>Actions</th></tr>
                @foreach($classes as $class)
                <tr>
                    <td>{{ $class->name }}</td>
                    <td>{{ $class->code }}</td>
                    <td>{{ $class->level?->name }}</td>
                    <td>{{ $class->students->count() }}</td>
                    <td class="text-end d-flex gap-2 justify-content-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('classes.show', $class) }}">Gérer</a>
                        <form method="post" action="{{ route('classes.destroy', $class) }}">@csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" @disabled($class->students->isNotEmpty())>Supprimer</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <form method="post" action="{{ route('classes.update', $class) }}" class="row g-2">@csrf @method('put')
                            <div class="col-md-4"><input name="name" class="form-control form-control-sm" value="{{ $class->name }}" required></div>
                            <div class="col-md-3"><input name="code" class="form-control form-control-sm" value="{{ $class->code }}" required></div>
                            <div class="col-md-3">
                                <select name="level_id" class="form-select form-select-sm" required>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" @selected($class->level_id === $level->id)>{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 text-end"><button class="btn btn-sm btn-outline-primary">Modifier</button></div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection
