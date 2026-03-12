<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
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
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');

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
