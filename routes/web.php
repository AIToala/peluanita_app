<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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
    return Inertia::render('Dashboard/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'verified', 'check.role:admin')->group(function () {
    Route::get('/dashboard/admin', function () {
        return Inertia::render('Dashboard/Admin/Admin');
    })->name('dashboard.admin');
    Route::get('/admin/roles', function () {
        return Inertia::render('Admin/Roles');
    })->name('admin.roles');
    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios');
});

Route::middleware('auth', 'verified', 'check.role:empleado')->group(function () {
    // Rutas para empleados
});

Route::middleware('auth', 'verified', 'check.role:cliente')->group(function () {
    // Rutas para clientes
});

Route::middleware('auth', 'verified', 'check.role:empleado|admin')->group(function () {
    Route::get('/dashboard/empleados', function () {
        return Inertia::render('Dashboard/Empleados/GestionarEmpleados');
    })->name('dashboard.empleados');
});

require __DIR__.'/auth.php';
