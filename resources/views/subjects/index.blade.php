@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Gestion des matières</h4>
    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Retour au menu principal</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Nouvelle matière</h5>
            <form method="post" action="{{ route('subjects.store') }}">@csrf
                <input name="name" class="form-control mb-2" placeholder="Ex: Mathématiques" required>
                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Liste des matières</h5>
            <table class="table">
                @foreach($subjects as $subject)
                <tr>
                    <td>
                        <form method="post" action="{{ route('subjects.update', $subject) }}" class="d-flex gap-2">
                            @csrf
                            @method('put')
                            <input name="name" class="form-control form-control-sm" value="{{ $subject->name }}" required>
                            <button class="btn btn-sm btn-outline-primary">Modifier</button>
                        </form>
                    </td>
                    <td class="text-end">
                        <form method="post" action="{{ route('subjects.destroy', $subject) }}">@csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" @disabled($subject->classes_count > 0)>Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection
