<?php

use App\Http\Controllers\LocationController;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('/product', function (Request $request) {
//     $almacen = $request->input('almacen');
//     $query = $request->input('search');
//     if ($almacen !== 'todos') {
//         $productos = Product::with('brand', 'unit', 'warehouse', 'prices', 'stock')->where('description', 'like', "%{$query}%")->where('warehouse_id', $almacen)->get();
//     } else {
//         $productos = Product::with('brand', 'unit', 'warehouse', 'prices', 'stock')->where('description', 'like', "%{$query}%")->get();
//     }
//     return response()->json($productos);
// });
Route::get('/product', function (Request $request) {
    $almacen = $request->input('almacen');
    $query = $request->input('search');

    $productos = Product::with('brand', 'unit', 'warehouse', 'prices', 'stock')
        ->where(function ($q) use ($query) {
            $q->where('code_sku', 'like', "%{$query}%")
                ->orWhere('code_bar', 'like', "%{$query}%");
        });

    if ($almacen !== 'todos') {
        $productos->where('warehouse_id', $almacen);
    }

    return response()->json($productos->get());
});
Route::get('/brands', function (Request $request) {
    $query = $request->input('query');
    $brands = Brand::where('name', 'LIKE', "%$query%")->limit(5)->get(['name']);
    return response()->json($brands);
});

Route::get('/services', function (Request $request) {
    $query = $request->input('query');
    $services = ServiceSale::where('name', 'LIKE', "%$query%")->limit(5)->get(['id', 'name', 'default_price']);
    return response()->json($services);
});
Route::get('/provinces/{regionId}', [LocationController::class, 'getProvinces']);
Route::get('/districts/{provinceId}', [LocationController::class, 'getDistricts']);
