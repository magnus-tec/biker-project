<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin|user|mechanic'])
    ->name('dashboard');
Route::group(
    ['middleware' => ['role:admin|user|mechanic']],
    function () {
        Route::resource('permissions', App\Http\Controllers\PermissionController::class);
        Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);

        Route::resource('roles', App\Http\Controllers\RoleController::class);
        Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);
        Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'addPermissionToRole']);
        Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'givePermissionToRole']);

        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy']);
        //CLIENTES
        Route::resource('drives', App\Http\Controllers\CustomerController::class);
        //MECANICOS
        Route::resource('mechanics', App\Http\Controllers\MechanicController::class);
        Route::get('mechanic/MecanicosDisponibles', [App\Http\Controllers\MechanicController::class, 'MecanicosDisponibles'])->name('obtener.MecanicosDisponibles');
        //VEHICULOS
        Route::resource('cars', App\Http\Controllers\CarController::class);
        Route::get('car/buscarPorPlaca', [App\Http\Controllers\CarController::class, 'searchDriverPorPlaca'])->name('buscar.DriverPorPlaca');
        Route::get('car/buscarPornumeroDoc', [App\Http\Controllers\CarController::class, 'searchBuscarDriver'])->name('buscar.Driver');

        //SERVICIOS
        Route::resource('services', App\Http\Controllers\ServiceController::class);
        Route::get('/service/listado', [ServiceController::class, 'filtroPorfecha'])->name('service.filtroPorfecha');

        //GARANTIAS
        Route::resource('garantines', App\Http\Controllers\GarantineController::class);
        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });
    }
);
require __DIR__ . '/auth.php';
