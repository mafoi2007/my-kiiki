<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:cellule_informatique')->group(function (): void {
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
        Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');
        Route::post('/classes/{class}/matieres', [ClassController::class, 'assignSubject'])->name('classes.subjects.assign');
        Route::delete('/classes/{class}/matieres/{subject}', [ClassController::class, 'detachSubject'])->name('classes.subjects.detach');
        Route::get('/classes/{class}/enseignants/pdf', [ClassController::class, 'teachersPdf'])->name('classes.teachers.pdf');

        Route::get('/niveaux', [LevelController::class, 'index'])->name('levels.index');
        Route::post('/niveaux', [LevelController::class, 'store'])->name('levels.store');
        Route::put('/niveaux/{level}', [LevelController::class, 'update'])->name('levels.update');
        Route::delete('/niveaux/{level}', [LevelController::class, 'destroy'])->name('levels.destroy');
        Route::get('/matieres', [SubjectController::class, 'index'])->name('subjects.index');
        Route::post('/matieres', [SubjectController::class, 'store'])->name('subjects.store');
        Route::delete('/matieres/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');

        Route::get('/eleves', [StudentController::class, 'index'])->name('students.index');
        Route::post('/eleves', [StudentController::class, 'store'])->name('students.store');
    });

    Route::middleware('role:parent')->get('/messages', [MessageController::class, 'index'])->name('messages.index'); 
});