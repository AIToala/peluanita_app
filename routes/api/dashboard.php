<?php
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

// Rutas para usuarios
Route::group(['prefix' => 'usuarios', 'middleware' => 'check.role:admin|empleado', 'controller' => UserController::class], function (): void {
    Route::get('listado', 'index')->name('usuarios.index');
});