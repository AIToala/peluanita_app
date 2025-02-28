<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', function () {
            return Inertia::render('Admin');
        })->name('admin');
        Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios');
    });

    Route::middleware('role:empleado')->group(function () {
        Route::get('/empleado', function () {
            return Inertia::render('Empleado');
        })->name('empleado');
    });

    Route::middleware('role:cliente')->group(function () {
        Route::get('/cliente', function () {
            return Inertia::render('ClienteCitas');
        })->name('cliente');
    });
});

require __DIR__.'/auth.php';
