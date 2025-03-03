<?php
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

// Rutas para usuarios
Route::group(['prefix' => 'usuarios', 'middleware' => 'check.role:admin|empleado', 'controller' => UserController::class], function (): void {
    Route::get('listado', 'index')->name('usuarios.index');
    Route::post('crear', 'store')->name('usuarios.store');
    Route::patch('editar/{id}', 'update')->name('usuarios.update');
    Route::delete('eliminar/{id}', 'destroy')->name('usuarios.destroy');
    Route::post('activar/{id}', 'activateUser')->name('usuarios.activate');
})->name('usuarios');