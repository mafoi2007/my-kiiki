@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Gestion des niveaux</h4>
        <a class="btn btn-outline-secondary" href="{{ route('classes.index') }}">Retour aux classes</a>
    </div>

    @if($errors->any())
        <div class="col-12">
            <div class="alert alert-danger mb-0" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">Nouveau niveau</h5>
                <form method="post" action="{{ route('levels.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du niveau</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            class="form-control"
                            placeholder="Ex : 6ème"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>
                    <button class="btn btn-primary" type="submit">Créer le niveau</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Liste des niveaux</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Niveau</th>
                                <th scope="col">Nombre de classes</th>
                                <th scope="col" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($levels as $level)
                                <tr>
                                    <td>{{ $level->name }}</td>
                                    <td>{{ $level->classes_count }}</td>
                                    <td class="text-end">
                                        <form method="post" action="{{ route('levels.destroy', $level) }}" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                type="submit"
                                                @disabled($level->classes_count > 0)
                                            >
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted">Aucun niveau disponible.</td>
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