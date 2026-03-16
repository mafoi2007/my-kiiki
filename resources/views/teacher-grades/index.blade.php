@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Saisie des notes</h4>
    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Retour au menu principal</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="sequence">Séquence ouverte</label>
                <select id="sequence" name="sequence" class="form-select" onchange="this.form.submit()" required>
                    <option value="">Choisir une séquence</option>
                    @foreach($sequences as $sequence)
                        <option value="{{ $sequence }}" @selected($selectedSequence === (int) $sequence)>Séquence {{ $sequence }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="school_class_id">Classe attribuée</label>
                <select id="school_class_id" name="school_class_id" class="form-select" onchange="this.form.submit()" @disabled($selectedSequence <= 0) required>
                    <option value="">Choisir une classe</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected($selectedClassId === $class->id)>{{ $class->level?->name }} - {{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="subject_id">Matière</label>
                <select id="subject_id" name="subject_id" class="form-select" onchange="this.form.submit()" @disabled($selectedClassId <= 0) required>
                    <option value="">Choisir une matière</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected($selectedSubjectId === $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($evaluation && $selectedSubjectId > 0)
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-2">Formulaire de saisie - Séquence {{ $evaluation->sequence_number }}</h5>
            <p class="text-muted small mb-3">La note trimestrielle est calculée par matière avec la formule : (note séquence 1 + note séquence 2) / 2.</p>
            @if($students->isEmpty())
                <p class="text-muted mb-0">Aucun élève trouvé pour cette classe.</p>
            @else
                <table class="table align-middle">
                    <tr>
                        <th>Élève</th>
                        <th style="width: 220px;">Note / 20</th>
                         <th>Note trimestrielle / 20</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    @foreach($students as $student)
                        @php($grade = $grades->get($student->id))
                        <tr>
                            <td>{{ $student->full_name }}</td>
                            <td>
                                <form method="post" action="{{ $grade ? route('teacher.grades.update', $grade) : route('teacher.grades.store') }}" class="d-flex gap-2">
                                    @csrf
                                    @if($grade)
                                        @method('put')
                                    @else
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                        <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">
                                        <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                                    @endif
                                    <input type="number" name="score" min="0" max="20" step="0.01" class="form-control" value="{{ old('score', $grade?->score) }}" required>
                                    <button class="btn btn-sm btn-primary">{{ $grade ? 'Modifier' : 'Enregistrer' }}</button>
                                </form>
                            </td>
                            <td>
                                @php($trimesterAverage = data_get($trimesterAverageByStudent->get($student->id), 'trimester_average'))
                                {{ $trimesterAverage !== null ? number_format((float) $trimesterAverage, 2) : '—' }}
                            </td>

                            <td class="text-end">
                                @if($grade)
                                    <form method="post" action="{{ route('teacher.grades.destroy', $grade) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
@endif
@endsection