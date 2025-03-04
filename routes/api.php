<?php

use App\Models\Product;
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
Route::get('/product', function (Request $request) {
    $query = $request->input('search');
    $productos = Product::with('brand', 'unit', 'warehouse', 'prices', 'images', 'stock')->where('description', 'like', "%{$query}%")->get();
    return response()->json($productos);
});
