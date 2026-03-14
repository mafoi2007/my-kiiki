@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Nouveau groupe</h5>
            <form method="post" action="{{ route('groups.store') }}">@csrf
                <input name="name" class="form-control mb-2" placeholder="Ex: Scientifique" required>
                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Liste des groupes</h5>
            <table class="table">
                @forelse($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td class="text-end">
                            <form method="post" action="{{ route('groups.destroy', $group) }}">@csrf @method('delete')
                                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted">Aucun groupe disponible.</td></tr>
                @endforelse
            </table>
        </div></div>
    </div>
</div>
@endsection