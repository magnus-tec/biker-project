<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WholesaleController;
use App\Models\Product;
use App\Models\Wholesaler;
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
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:administrador|mecanico|ventas'])
    ->name('dashboard');
Route::group(
    ['middleware' => ['role:administrador|mecanico|ventas']],
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
        Route::get('car/buscarDrive', [App\Http\Controllers\CarController::class, 'searchBuscarDriver'])->name('buscar.Driver');
        Route::get('car/buscarPornroMotor', [App\Http\Controllers\CarController::class, 'searchBuscarVehiculo'])->name('buscar.Vehiculo');
        // PRODUCTOS
        Route::resource('products', App\Http\Controllers\ProductController::class);
        Route::get('product/search', [App\Http\Controllers\ProductController::class, 'search'])->name('products.search');
        Route::get('product/export', [App\Http\Controllers\ProductController::class, 'export'])->name('products.export');
        Route::get('/productos/{id}/imagenes', function ($id) {
            $product = Product::findOrFail($id);
            return response()->json($product->images);
        });
        // Route::get('product/import', [App\Http\Controllers\ProductController::class, 'import'])->name('products.import');
        Route::get('/plantilla-descargar', [App\Http\Controllers\ProductController::class, 'descargarPlantilla'])->name('plantilla.descargar');
        Route::post('/product/import', [App\Http\Controllers\ProductController::class, 'import'])->name('products.import');

        //SERVICIOS
        Route::resource('services', App\Http\Controllers\ServiceController::class);
        Route::get('/service/listado', [ServiceController::class, 'filtroPorfecha'])->name('service.filtroPorfecha');
        Route::post('/service/cambiarEstado', [ServiceController::class, 'cambiarEstado'])->name('service.cambiarEstado');
        Route::get('/service/detalles', [ServiceController::class, 'verDetalles'])->name('service.verDetalles');

        //GARANTIAS
        Route::resource('garantines', App\Http\Controllers\GarantineController::class);
        //TRABAJADORES CON SUS ROLES
        Route::resource('workers', App\Http\Controllers\UserController::class);
        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });
        //VENTAS
        Route::resource('sales',  SaleController::class);
        Route::get('/sale/listado', [SaleController::class, 'filtroPorfecha'])->name('sales.filtroPorfecha');
        Route::get('/sale/detalles/{id}', [SaleController::class, 'detallesVenta'])->name('sale.detallesVenta');
        Route::get('/sale/pdf/{id}', [SaleController::class, 'generatePDF'])->name('sales.pdf');
        Route::post('/sale/enviar-sunat/{id}', [SaleController::class, 'enviarSunat'])->name('sales.enviarSunat');
        //UNIDAD MEDIDA
        Route::resource('units',  App\Http\Controllers\UnitController::class);
        Route::get('/units', [UnitController::class, 'search']);
        // COTIZACIONES
        Route::resource('quotations', QuotationController::class);
        Route::get('/quotation/listado', [QuotationController::class, 'filtroPorfecha'])->name('quotations.filtroPorfecha');
        Route::get('/quotation/detalles/{id}', [QuotationController::class, 'detallesQuotation'])->name('quotations.detallesQuotation');
        Route::get('/quotation/pdf/{id}', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
        Route::post('/quotation/cotizacion/vender/{id}', [QuotationController::class, 'vender'])->name('quotations.vender');
        Route::get('mechanic/MecanicosDisponibles', [App\Http\Controllers\QuotationController::class, 'MecanicosDisponibles'])->name('mecanicosDisponibles');
        //MAYORISTA
        Route::resource('wholesalers', WholesaleController::class);
        Route::get('/wholesaler/listado', [WholesaleController::class, 'filtroPorfecha'])->name('wholesalers.filtroPorfecha');
        Route::get('/wholesaler/detalles/{id}', [WholesaleController::class, 'detallesWholesaler'])->name('wholesalers.detallesWholesaler');
        Route::get('/wholesaler/pdf/{id}', [WholesaleController::class, 'generatePDF'])->name('wholesalers.pdf');
    }
);
require __DIR__ . '/auth.php';
