<?php
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Servicios\ServicioController;
use App\Http\Controllers\Users\ClienteController;
use App\Http\Controllers\Agendas\CitaController;
use Illuminate\Support\Facades\Route;

// Rutas para usuarios
Route::group(['prefix' => 'usuarios', 'middleware' => 'check.role:admin|empleado', 'controller' => UserController::class], function (): void {
    Route::get('listado', 'index')->name('usuarios.index');
    Route::post('crear', 'store')->name('usuarios.store');
    Route::patch('editar/{id}', 'update')->name('usuarios.update');
    Route::delete('eliminar/{id}', 'destroy')->name('usuarios.destroy');
    Route::post('activar/{id}', 'activateUser')->name('usuarios.activate');
})->name('usuarios');

// Rutas para servicios
Route::group(['prefix' => 'servicios', 'middleware' => 'check.role:admin', 'controller' => ServicioController::class], function (): void {
    Route::get('listado', 'index')->name('servicios.index');
    Route::post('crear', 'store')->name('servicios.store');
    Route::patch('editar/{id}', 'update')->name('servicios.update');
    Route::delete('eliminar/{id}', 'destroy')->name('servicios.destroy');
    Route::post('activar/{id}', 'activateServicio')->name('servicios.activate');
})->name('servicios');

    
// Rutas para clientes
Route::group(['prefix' => 'clientes', 'middleware' => 'check.role:admin|empleado', 'controller' => ClienteController::class], function (): void {
    Route::get('listado', 'index')->name('clientes.index');
    Route::patch('editar/{id}', 'update')->name('clientes.update');
    Route::delete('eliminar/{id}', 'destroy')->name('clientes.destroy');
    Route::post('activar/{id}', 'activateCliente')->name('clientes.activate');
})->name('clientes');

Route::group(['prefix' => 'clientes', 'middleware' => 'check.role:admin|empleado|cliente', 'controller' => ClienteController::class], function (): void {
    Route::post('crear', 'store')->name('clientes.store');
})->name('clientes');

Route::group(['prefix'=> 'agendas', 'middleware' => 'check.role:admin|empleado|cliente'], function (): void {
    Route::group(['prefix' => 'citas', 'controller' => CitaController::class], function(): void{
        Route::get('listado', 'index')->name('citas.index')->middleware('check.role:admin|empleado|cliente');
        Route::post('crear', 'store')->name('citas.store')->middleware('check.role:admin|empleado|cliente');
        Route::patch('editar/{id}', 'update')->name('citas.update')->middleware('check.role:admin|empleado');
        Route::delete('eliminar/{id}', 'destroy')->name('citas.destroy')->middleware('check.role:admin|empleado');
    })->name('citas');
})->name('agendas');
