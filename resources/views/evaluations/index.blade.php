@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Gestion des séquences</h4>
    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Retour au menu principal</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label" for="school_class_id">Classe</label>
                <select id="school_class_id" name="school_class_id" class="form-select" onchange="this.form.submit()" required>
                    <option value="">Choisir une classe</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected($selectedClassId === $class->id)>
                            {{ $class->level?->name }} - {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($selectedClassId > 0)
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Année scolaire : 6 séquences / 3 trimestres</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Séquence</th>
                        <th>Trimestre</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Ouverte à la saisie</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($availableSequences as $sequence)
                        @php($evaluation = $evaluations->get($sequence))
                        <tr>
                            <td>Séquence {{ $sequence }}</td>
                            <td>Trimestre {{ intdiv($sequence + 1, 2) }}</td>
                            <td colspan="4">
                                <form method="post" action="{{ $evaluation ? route('evaluations.update', $evaluation) : route('evaluations.store') }}" class="row g-2 align-items-center">
                                    @csrf
                                    @if($evaluation)
                                        @method('put')
                                    @else
                                        <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                                        <input type="hidden" name="sequence_number" value="{{ $sequence }}">
                                    @endif

                                    <div class="col-md-3">
                                        <input type="date" class="form-control" name="starts_at" value="{{ old('starts_at', optional($evaluation?->starts_at)->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control" name="ends_at" value="{{ old('ends_at', optional($evaluation?->ends_at)->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_open" value="1" id="is_open_{{ $sequence }}" @checked((bool) old('is_open', $evaluation?->is_open))>
                                            <label class="form-check-label" for="is_open_{{ $sequence }}">Activer la saisie</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <button class="btn btn-primary btn-sm">{{ $evaluation ? 'Mettre à jour' : 'Créer' }}</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
