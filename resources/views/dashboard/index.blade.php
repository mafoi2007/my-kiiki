@extends('layouts.app')

@section('content')
<h3 class="mb-3">Tableau de bord</h3>
<div class="row g-3 mb-4">
    @foreach($stats as $label => $value)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">{{ $label }}</div>
                    <div class="fs-3 fw-bold">{{ $value }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">Menu métier</h5>
        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user()->isRole('cellule_informatique'))
                <a class="btn btn-primary" href="{{ route('classes.index') }}">Classes</a>
                <a class="btn btn-primary" href="{{ route('levels.index') }}">Niveaux</a>
                <a class="btn btn-primary" href="{{ route('subjects.index') }}">Matières</a>
                <a class="btn btn-primary" href="{{ route('evaluations.index') }}">Séquences</a>
                 <a class="btn btn-primary" href="{{ route('groups.index') }}">Groupes</a>
                <a class="btn btn-primary" href="{{ route('users.index') }}">Utilisateurs</a>
                <a class="btn btn-primary" href="{{ route('students.index') }}">Élèves</a>
            @endif

            @if(auth()->user()->isRole('chef_etablissement'))
                <span class="badge bg-info fs-6">Consultation des inscrits, paiements et bulletins.</span>
            @endif

            @if(auth()->user()->isRole('censeur'))
                <span class="badge bg-warning text-dark fs-6">Statistiques d'évaluations & notes par matière.</span>
            @endif

            @if(auth()->user()->isRole('surveillant_general'))
                <span class="badge bg-secondary fs-6">Saisie des absences (période ouverte).</span>
            @endif

            @if(auth()->user()->isRole('econome'))
                <span class="badge bg-success fs-6">Paiements, solvables et insolvables.</span>
            @endif

            @if(auth()->user()->isRole('enseignant'))
                <a class="btn btn-dark" href="{{ route('teacher.grades.index') }}">Saisie des notes</a>
            @endif

            @if(auth()->user()->isRole('parent'))
                <a class="btn btn-outline-primary" href="{{ route('messages.index') }}">Messages</a>
                <span class="badge bg-light text-dark fs-6">Bulletins séquentiels, trimestriels et annuels de vos enfants.</span>
            @endif
        </div>
    </div>
</div>
@endsection
