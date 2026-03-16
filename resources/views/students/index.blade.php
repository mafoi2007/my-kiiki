@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Gestion des élèves</h3>
    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Retour au menu principal</a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
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
                    <option value="" @selected(old('school_class_id') === null)>Selectionner classe</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected((int) old('school_class_id') === $class->id)>
                            {{ $class->level?->name ? $class->level->name.' - ' : '' }}{{ $class->name }}
                        </option>
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

        <div class="card shadow-sm mt-4"><div class="card-body">
            <h5>Recherche d'élève</h5>
            <form method="get" action="{{ route('students.index') }}" class="mb-3">
                <label class="form-label" for="search">Nom ou matricule national</label>
                <div class="input-group">
                    <input id="search" name="search" class="form-control" placeholder="Ex: KOUADIO ou NAT-001" value="{{ $searchTerm }}">
                    <button class="btn btn-outline-primary">Rechercher</button>
                </div>
            </form>

            @if($searchTerm !== '')
                @if($searchResults->isEmpty())
                    <div class="alert alert-warning mb-0">Aucun élève trouvé pour cette recherche.</div>
                @else
                    <div class="list-group">
                        @foreach($searchResults as $result)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('students.index', ['search' => $searchTerm, 'student_id' => $result->id]) }}">
                                <span>
                                    <strong>{{ $result->full_name }}</strong><br>
                                    <small>{{ $result->matricule }} — {{ $result->schoolClass?->name }}</small>
                                </span>
                                <span class="badge bg-primary">Sélectionner</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif
        </div></div>
    </div>
    
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4"><div class="card-body">
            <h5>Classes contenant des élèves</h5>
            @if($classesWithStudents->isEmpty())
                <div class="alert alert-info mb-0">Aucune classe ne contient encore d'élève.</div>
            @else
                <div class="row g-2">
                    @foreach($classesWithStudents as $class)
                        <div class="col-md-6">
                            <a class="btn {{ $selectedClass?->id === $class->id ? 'btn-primary' : 'btn-outline-primary' }} w-100 text-start" href="{{ route('students.index', ['class_id' => $class->id]) }}">
                                {{ $class->name }}
                                <span class="badge bg-light text-dark ms-2">{{ $class->students_count }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div></div>

        <div class="card shadow-sm mb-4"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Élèves de la classe</h5>
                @if($selectedClass)
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('students.class.pdf', $selectedClass) }}">Exporter PDF</a>
                @endif
            </div>

            @if(! $selectedClass)
                <div class="alert alert-secondary mb-0">Sélectionnez une classe pour afficher ses élèves.</div>
            @elseif($studentsInSelectedClass->isEmpty())
                <div class="alert alert-info mb-0">Cette classe ne contient aucun élève.</div>
            @else
                <table class="table table-sm align-middle">
                    <tr><th>Matricule nat.</th><th>Nom</th><th>Statut</th><th>Sexe</th><th>État</th><th></th></tr>
                    @foreach($studentsInSelectedClass as $student)
                        <tr>
                            <td>{{ $student->matricule }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->status === 'N' ? 'Nouveau' : 'Redoublant' }}</td>
                            <td>{{ $student->sex === 'M' ? 'M' : 'F' }}</td>
                            <td>
                                @if($student->active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('students.index', ['class_id' => $selectedClass->id, 'student_id' => $student->id]) }}">Éditer</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div></div>

        @if($selectedStudent)
            <div class="card shadow-sm mb-4"><div class="card-body">
                <h5>Édition de l'élève sélectionné</h5>
                <form method="post" action="{{ route('students.update', $selectedStudent) }}">
                    @csrf
                    @method('put')
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label required-label" for="edit_full_name">Nom complet</label>
                            <input id="edit_full_name" name="full_name" class="form-control" value="{{ old('full_name', $selectedStudent->full_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-label" for="edit_matricule">Matricule national</label>
                            <input id="edit_matricule" name="matricule" class="form-control" value="{{ old('matricule', $selectedStudent->matricule) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-label" for="edit_birth_date">Date de naissance</label>
                            <input id="edit_birth_date" type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $selectedStudent->birth_date?->format('Y-m-d') ?? $selectedStudent->birth_date) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_birth_place">Lieu de naissance</label>
                            <input id="edit_birth_place" name="birth_place" class="form-control" value="{{ old('birth_place', $selectedStudent->birth_place) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-label" for="edit_status">Statut</label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="N" @selected(old('status', $selectedStudent->status) === 'N')>Nouveau</option>
                                <option value="R" @selected(old('status', $selectedStudent->status) === 'R')>Redoublant</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-label" for="edit_sex">Sexe</label>
                            <select id="edit_sex" name="sex" class="form-select" required>
                                <option value="M" @selected(old('sex', $selectedStudent->sex) === 'M')>Masculin</option>
                                <option value="F" @selected(old('sex', $selectedStudent->sex) === 'F')>Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_father_name">Nom du père</label>
                            <input id="edit_father_name" name="father_name" class="form-control" value="{{ old('father_name', $selectedStudent->father_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-label" for="edit_mother_name">Nom de la mère</label>
                            <input id="edit_mother_name" name="mother_name" class="form-control" value="{{ old('mother_name', $selectedStudent->mother_name) }}" required>
                        </div>
                    </div>

                    <div class="form-check my-3">
                        <input id="edit_active" class="form-check-input" type="checkbox" name="active" value="1" @checked(old('active', $selectedStudent->active ? '1' : '0') === '1')>
                        <label class="form-check-label" for="edit_active">Élève actif</label>
                    </div>

                    <button class="btn btn-primary">Mettre à jour les informations</button>
                </form>
            </div></div>

            <div class="card shadow-sm"><div class="card-body">
                <h5>Changer de classe</h5>
                <form method="post" action="{{ route('students.move-class', $selectedStudent) }}" class="row g-2 align-items-end">
                    @csrf
                    @method('put')
                    <div class="col-md-5">
                        <label class="form-label required-label" for="from_school_class_id">Classe de départ</label>
                        <select id="from_school_class_id" name="from_school_class_id" class="form-select" required>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected((int) old('from_school_class_id', $selectedStudent->school_class_id) === $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label required-label" for="to_school_class_id">Classe but</label>
                        <select id="to_school_class_id" name="to_school_class_id" class="form-select" required>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected((int) old('to_school_class_id') === $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-warning">Transférer</button>
                    </div>
                </form>
            </div></div>
        @endif
    </div>
</div>
@endsection