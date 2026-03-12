@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Créer un élève</h5>
            <form method="post" action="{{ route('students.store') }}">@csrf
                <input name="matricule" class="form-control mb-2" placeholder="Matricule" required>
                <input name="full_name" class="form-control mb-2" placeholder="Nom complet" required>
                <select name="school_class_id" class="form-select mb-2" required>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary">Créer</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Élèves inscrits</h5>
            <table class="table table-sm">
                <tr><th>Matricule</th><th>Nom</th><th>Classe</th></tr>
                @foreach($students as $student)
                    <tr><td>{{ $student->matricule }}</td><td>{{ $student->full_name }}</td><td>{{ $student->schoolClass->name }}</td></tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
