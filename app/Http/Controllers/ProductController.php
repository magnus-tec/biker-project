<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('status', 1)->with('brand', 'unit', 'warehouse')->get();
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        $units = Unit::all();
        $warehouses = Warehouse::all();
        return view('product.create', compact('brands', 'units', 'warehouses'));
    }
    public function generateCode()
    {
        $lastCodigo = Product::max('code') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'amount' => 'nullable|integer',
            'model' => 'nullable|string',
            'location' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'prices' => 'nullable|array',
            'prices.*' => 'nullable|numeric|min:0',
        ]);
        $validated['code'] = $this->generateCode();

        $product = Product::create($validated);

        if ($product) {
            $prices = $validated['prices'] ?? [];
            $priceData = [];
            foreach ($prices as $type => $price) {
                if (!is_null($price)) {
                    $priceData[] = [
                        'product_id' => $product->id,
                        'type' => $type,
                        'price' => $price,
                    ];
                }
            }
            Stock::create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'minimum_stock' => $request->minimum_stock,
            ]);
            if (!empty($priceData)) {
                ProductPrice::insert($priceData);
            }
            return response()->json([
                'success' => true,
                'message' => '¡El producto ha sido registrado con éxito!',
                'product' => $product
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al registrar el producto',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $product = Product::with('brand', 'unit', 'warehouse', 'prices')->findOrFail($id);
            $brands = Brand::all();
            $units = Unit::all();
            $warehouses = Warehouse::all();
            $productStock = Stock::where('product_id', $product->id)->first();
            return view('product.edit', compact('product', 'brands', 'units', 'warehouses', 'productStock'));
        } catch (\Throwable $th) {
            return redirect()->route('product.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validar los datos recibidos
            $validated = $request->validate([
                'description' => 'nullable|string',
                'amount' => 'nullable|integer',
                'model' => 'nullable|string',
                'location' => 'nullable|string',
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'brand_id' => 'nullable|exists:brands,id',
                'unit_id' => 'nullable|exists:units,id',
                'prices' => 'nullable|array',
                'prices.*' => 'nullable|numeric|min:0',
            ]);

            // Buscar el producto existente
            $product = Product::findOrFail($id);

            // Actualizar los datos del producto
            $product->update($validated);

            // Manejar los precios
            $prices = $validated['prices'] ?? [];
            $product->prices()->delete(); // Elimina precios anteriores

            $priceData = [];
            foreach ($prices as $type => $price) {
                if (!is_null($price)) {
                    $priceData[] = [
                        'product_id' => $product->id,
                        'type' => $type,
                        'price' => $price,
                    ];
                }
            }

            // Actualizar el stock
            $stock = Stock::where('product_id', $product->id)->first();
            if ($stock) {
                $stock->update([
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'minimum_stock' => $request->minimum_stock,
                ]);
            }

            // Insertar los nuevos precios si hay datos
            if (!empty($priceData)) {
                ProductPrice::insert($priceData);
            }

            return response()->json([
                'success' => true,
                'message' => '¡El producto ha sido actualizado con éxito!',
                'product' => $product
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->status = 0;
            $product->save();
            return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Error al eliminar el producto.');
        }
    }
}
