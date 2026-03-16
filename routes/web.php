<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherGradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'force_password_change'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/mot-de-passe/modifier', [AuthController::class, 'showPasswordChangeForm'])->name('password.change.form');
    Route::post('/mot-de-passe/modifier', [AuthController::class, 'changePassword'])->name('password.change.update');

    Route::middleware('role:cellule_informatique')->group(function (): void {
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
        Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');
        Route::post('/classes/{class}/matieres', [ClassController::class, 'assignSubject'])->name('classes.subjects.assign');
        Route::delete('/classes/{class}/matieres/{subject}', [ClassController::class, 'detachSubject'])->name('classes.subjects.detach');
        Route::post('/classes/{class}/enseignants', [ClassController::class, 'assignTeacher'])->name('classes.teachers.assign');
        Route::get('/classes/{class}/enseignants/pdf', [ClassController::class, 'teachersPdf'])->name('classes.teachers.pdf');

        Route::get('/niveaux', [LevelController::class, 'index'])->name('levels.index');
        Route::get('/niveaux/pdf', [LevelController::class, 'pdf'])->name('levels.pdf');
        Route::post('/niveaux', [LevelController::class, 'store'])->name('levels.store');
        Route::put('/niveaux/{level}', [LevelController::class, 'update'])->name('levels.update');
        Route::delete('/niveaux/{level}', [LevelController::class, 'destroy'])->name('levels.destroy');
        Route::get('/sequences', [EvaluationController::class, 'index'])->name('evaluations.index');
        Route::post('/sequences', [EvaluationController::class, 'store'])->name('evaluations.store');
        Route::put('/sequences/{evaluation}', [EvaluationController::class, 'update'])->name('evaluations.update');
        Route::get('/matieres', [SubjectController::class, 'index'])->name('subjects.index');
        Route::post('/matieres', [SubjectController::class, 'store'])->name('subjects.store');
        Route::put('/matieres/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/matieres/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

        Route::get('/groupes', [GroupController::class, 'index'])->name('groups.index');
        Route::post('/groupes', [GroupController::class, 'store'])->name('groups.store');
        Route::put('/groupes/{group}', [GroupController::class, 'update'])->name('groups.update');
        Route::delete('/groupes/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::get('/utilisateurs/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/utilisateurs/{user}/editer', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/utilisateurs/{user}/reinitialiser-mot-de-passe', [UserController::class, 'resetPassword'])->name('users.password.reset');
        
        Route::get('/eleves', [StudentController::class, 'index'])->name('students.index');
        Route::post('/eleves', [StudentController::class, 'store'])->name('students.store');
        Route::put('/eleves/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::put('/eleves/{student}/changer-classe', [StudentController::class, 'moveClass'])->name('students.move-class');
        Route::get('/eleves/classes/{class}/pdf', [StudentController::class, 'classStudentsPdf'])->name('students.class.pdf');
        Route::delete('/eleves/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    Route::middleware('role:enseignant')->group(function (): void {
        Route::get('/enseignant/notes', [TeacherGradeController::class, 'index'])->name('teacher.grades.index');
        Route::post('/enseignant/notes', [TeacherGradeController::class, 'store'])->name('teacher.grades.store');
        Route::put('/enseignant/notes/{grade}', [TeacherGradeController::class, 'update'])->name('teacher.grades.update');
        Route::delete('/enseignant/notes/{grade}', [TeacherGradeController::class, 'destroy'])->name('teacher.grades.destroy');
    });

    Route::middleware('role:parent')->get('/messages', [MessageController::class, 'index'])->name('messages.index'); 
});