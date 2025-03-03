<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Users\UserController;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware('web');

// Rutas que no requieren JWT
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware('auth', 'verified', 'check.role:admin|empleado|cliente')->group(function () {
    Route::prefix('dashboard')->group(function (): void {
        Route::prefix('clientes')->group(function (): void {
            Route::get('crear', function () {
                return Inertia::render('Dashboard/Clientes/CrearCliente');
            })->name('dashboard.clientes.crear');
        });
    });
});

Route::middleware('auth', 'verified', 'check.role:admin|empleado')->group(function () {
    Route::prefix('dashboard')->group(function (): void {
        Route::prefix('clientes')->group(function (): void {
            Route::get('', function () {
                return Inertia::render('Dashboard/Clientes/GestionarClientes');
            })->name('dashboard.clientes');
            Route::get('{id}', function () {
                return Inertia::render('Dashboard/Clientes/EditarCliente');
            })->name('dashboard.clientes.editar');
            Route::get('{id}/eliminar', function () {
                return Inertia::render('Dashboard/Clientes/EliminarCliente');
            })->name('dashboard.clientes.eliminar');
        });
    });
});



Route::middleware('auth', 'verified', 'check.role:admin')->group(function () {
    Route::prefix('dashboard')->group(function (): void {
        Route::prefix('empleados')->group(function (): void {
            Route::get('', function () {
                return Inertia::render('Dashboard/Empleados/GestionarEmpleados');
            })->name('dashboard.empleados');
            Route::get('crear', function () {
                return Inertia::render('Dashboard/Empleados/CrearEmpleado');
            })->name('dashboard.empleados.crear');
            Route::get('{id}', function () {
                return Inertia::render('Dashboard/Empleados/EditarEmpleado');
            })->name('dashboard.empleados.editar');
            Route::get('{id}/eliminar', function () {
                return Inertia::render('Dashboard/Empleados/EliminarEmpleado');
            })->name('dashboard.empleados.eliminar');
        });
        Route::prefix('servicios')->group(function (): void {
            Route::get('', function () {
                return Inertia::render('Dashboard/Servicios/GestionarServicios');
            })->name('dashboard.servicios');
            Route::get('crear', function () {
                return Inertia::render('Dashboard/Servicios/CrearServicio');
            })->name('dashboard.servicios.crear');
            Route::get('{id}', function () {
                return Inertia::render('Dashboard/Servicios/EditarServicio');
            })->name('dashboard.servicios.editar');
            Route::get('{id}/eliminar', function () {
                return Inertia::render('Dashboard/Servicios/EliminarServicio');
            })->name('dashboard.servicios.eliminar');
        });
    });
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Dashboard', [
        'token' => session('api_token'),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/{any}', [ApplicationController::class, 'index'])->where('any', '^(?!aplicaciones|telescope)(.*)')->name('inicio');

//Inicio de sesion
Route::fallback(function () {
    return redirect()->route('/', ['any' => 'login']);
});
