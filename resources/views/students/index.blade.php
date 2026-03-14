@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Créer un élève</h5>
            <form method="post" action="{{ route('students.store') }}" enctype="multipart/form-data">@csrf
                <label class="form-label required-label" for="matricule">Matricule national</label>
                <input id="matricule" name="matricule" class="form-control mb-2" placeholder="Matricule national" value="{{ old('matricule') }}" required>

                <label class="form-label required-label" for="full_name">Nom complet</label>
                <input id="full_name" name="full_name" class="form-control mb-2" placeholder="Nom complet" value="{{ old('full_name') }}" required>

                <label class="form-label required-label" for="birth_date">Date de naissance</label>
                <input id="birth_date" type="date" name="birth_date" class="form-control mb-2" value="{{ old('birth_date') }}" required>

                <label class="form-label" for="birth_place">Lieu de naissance</label>
                <input id="birth_place" name="birth_place" class="form-control mb-2" placeholder="Lieu de naissance" value="{{ old('birth_place') }}">

                <label class="form-label required-label" for="school_class_id">Classe</label>
                <select id="school_class_id" name="school_class_id" class="form-select mb-2" required>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected((int) old('school_class_id') === $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>

                <label class="form-label required-label" for="status">Statut</label>
                <select id="status" name="status" class="form-select mb-2" required>
                    <option value="N" @selected(old('status', 'N') === 'N')>Nouveau</option>
                    <option value="R" @selected(old('status') === 'R')>Redoublant</option>
                </select>

                <label class="form-label required-label" for="sex">Sexe</label>
                <select id="sex" name="sex" class="form-select mb-2" required>
                    <option value="M" @selected(old('sex', 'M') === 'M')>Masculin</option>
                    <option value="F" @selected(old('sex') === 'F')>Féminin</option>
                </select>

                <label class="form-label" for="father_name">Nom du père</label>
                <input id="father_name" name="father_name" class="form-control mb-2" placeholder="Nom du père" value="{{ old('father_name') }}">

                <label class="form-label required-label" for="mother_name">Nom de la mère</label>
                <input id="mother_name" name="mother_name" class="form-control mb-2" placeholder="Nom de la mère" value="{{ old('mother_name') }}" required>

                <label class="form-label" for="photo">Photo</label>
                <input id="photo" type="file" name="photo" class="form-control mb-2" accept="image/*">

                <div class="form-check mb-3">
                    <input id="active" class="form-check-input" type="checkbox" name="active" value="1" @checked(old('active', '1') === '1')>
                    <label class="form-check-label" for="active">Élève actif</label>
                </div>

                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Élèves inscrits</h5>
            <table class="table table-sm">
                <tr><th>Mat. national</th><th>Mat. établissement</th><th>Nom</th><th>Classe</th><th>État</th><th></th></tr>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->matricule }}</td>
                        <td>{{ $student->school_matricule }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->schoolClass->name }}</td>
                        <td>
                            @if($student->active)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-secondary">Supprimé</span>
                            @endif
                        </td>
                        <td>
                            @if($student->active)
                                <form method="post" action="{{ route('students.destroy', $student) }}">@csrf @method('delete')
                                    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection