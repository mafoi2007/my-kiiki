@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Classe {{ $class->name }} ({{ $class->code }}) - {{ $class->level?->name }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('classes.index') }}">Retour</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Affecter une matière à la classe</h5>
            <form method="post" action="{{ route('classes.subjects.assign', $class) }}">@csrf
                <select name="subject_id" class="form-select mb-2" required>
                    <option value="">Choisir une matière</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                <select name="group_id" class="form-select mb-2" required>
                    <option value="">Choisir un groupe</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
                <input type="number" min="1" max="20" name="coefficient" class="form-control mb-2" placeholder="Coefficient" required>
                <button class="btn btn-primary">Affecter</button>
            </form>
        </div></div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Matières de la classe</h5>
            <table class="table">
                @forelse($class->subjects as $subject)
                    <tr>
                        <td>{{ $subject->name }}</td>
                        <td>Groupe {{ optional($groups->firstWhere('id', $subject->pivot->group_id))->name }}</td>
                        <td>Coef. {{ $subject->pivot->coefficient }}</td>
                        <td class="text-end">
                            <form method="post" action="{{ route('classes.subjects.detach', [$class, $subject]) }}">@csrf @method('delete')
                                <button class="btn btn-sm btn-outline-danger">Retirer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Aucune matière affectée.</td></tr>
                @endforelse
            </table>
        </div></div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Enseignants intervenant dans cette classe</h5>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('classes.teachers.pdf', $class) }}">Exporter PDF</a>
            </div>
            <form method="post" action="{{ route('classes.teachers.assign', $class) }}" class="row g-2 mt-2">@csrf
                <div class="col-md-5">
                    <select name="teacher_id" class="form-select" required>
                        <option value="">Choisir un enseignant</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="subject_id" class="form-select" required>
                        <option value="">Choisir une matière de la classe</option>
                        @foreach($class->subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">Assigner</button>
                </div>
            </form>
            <table class="table mt-3">
                <tr><th>Enseignant</th><th>Matière</th></tr>
                @forelse($class->teacherAssignments as $assignment)
                    <tr><td>{{ $assignment->teacher->name }}</td><td>{{ $assignment->subject->name }}</td></tr>
                @empty
                    <tr><td colspan="2" class="text-muted">Aucun enseignant assigné.</td></tr>
                @endforelse
            </table>
        </div></div>
    </div>
</div>
@endsection
