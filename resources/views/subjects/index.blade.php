@extends('layouts.app')

@section('content')
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
                <tr><td>{{ $subject->name }}</td><td class="text-end">
                    <form method="post" action="{{ route('subjects.destroy', $subject) }}">@csrf @method('delete')
                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                </td></tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection
