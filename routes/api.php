<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('status', function () {
    return response()->json(['status' => 'ok']);
});

Route::group(['prefix' => 'auth', 'middleware' => ['auth:sanctum', 'ex.bad_request']], function (): void {
    Route::prefix('dashboard')->group(function (): void {
        require base_path('routes/api/dashboard.php');
    })->name('dashboard:root');
});