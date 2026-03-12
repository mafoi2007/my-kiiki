@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Nouvelle classe</h5>
            <form method="post" action="{{ route('classes.store') }}">@csrf
                <input name="name" class="form-control mb-2" placeholder="Ex: 3e A" required>
                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Liste des classes</h5>
            <table class="table">
                @foreach($classes as $class)
                <tr><td>{{ $class->name }}</td><td class="text-end">
                    <form method="post" action="{{ route('classes.destroy', $class) }}">@csrf @method('delete')
                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                </td></tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection
