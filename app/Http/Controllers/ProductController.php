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
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

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
    // public function export(Request $request)
    // {
    //     $filter = $request->get('filter', 'estado');
    //     $query = Product::query()->with('brand', 'unit', 'warehouse');
    //     if ($filter === 'productos') {
    //         $query->where('status', 1);
    //     } elseif ($filter === 'stock_minimo') {
    //         $query->whereColumn('stock', '<=', 'stock_min');
    //     } elseif ($filter === 'precio') {
    //         $query->orderBy('price');
    //     }
    //     return Excel::download(new class($query) implements FromQuery, WithHeadings, WithMapping {
    //         protected $query;

    //         public function __construct($query)
    //         {
    //             $this->query = $query;
    //         }

    //         public function query()
    //         {
    //             return $this->query->select('id', 'code', 'description', 'model', 'location', 'warehouse_id', 'brand_id', 'unit_id', 'status');
    //         }

    //         public function headings(): array
    //         {
    //             return [
    //                 'ID',
    //                 'Código',
    //                 'Descripción',
    //                 'Modelo',
    //                 'Localización',
    //                 'Almacén',
    //                 'Marca',
    //                 'Unidad',
    //                 'Estado',
    //             ];
    //         }

    //         public function map($product): array
    //         {
    //             return [
    //                 $product->id,
    //                 $product->code,
    //                 $product->description,
    //                 $product->model,
    //                 $product->location,
    //                 $product->warehouse->name ?? '',
    //                 $product->brand->name ?? '',
    //                 $product->unit->name ?? '',
    //                 $product->status,
    //             ];
    //         }
    //     }, 'products.xlsx');
    // }
    public function export(Request $request)
    {
        // Obtén el parámetro 'filter' enviado desde la vista
        $filter = $request->get('filter', 'productos');

        if ($filter === 'productos') {
            // Opción "Exportar por Productos": Exporta todos los productos (con relaciones)
            $query = Product::query()->with('brand', 'unit', 'warehouse');

            return Excel::download(new class($query) implements FromQuery, WithHeadings, WithMapping {
                protected $query;

                public function __construct($query)
                {
                    $this->query = $query;
                }

                public function query()
                {
                    // Selecciona los campos de la tabla products
                    return $this->query->select(
                        'id',
                        'code',
                        'description',
                        'model',
                        'location',
                        'warehouse_id',
                        'brand_id',
                        'unit_id',
                        'status'
                    );
                }

                public function headings(): array
                {
                    return [
                        'ID',
                        'Código',
                        'Descripción',
                        'Modelo',
                        'Localización',
                        'Almacén',
                        'Marca',
                        'Unidad',
                        'Estado',
                    ];
                }

                public function map($product): array
                {
                    return [
                        $product->id,
                        $product->code,
                        $product->description,
                        $product->model,
                        $product->location,
                        $product->warehouse->name ?? '',
                        $product->brand->name ?? '',
                        $product->unit->name ?? '',
                        $product->status,
                    ];
                }
            }, 'productos.xlsx');
        } elseif ($filter === 'stock_minimo') {
            // Opción "Exportar por Stock Mínimo": Exporta solo aquellos stocks donde la cantidad es igual al stock mínimo
            // Se asume que existe un modelo Stock con la relación 'product'
            $query = Stock::query()->with('product')->whereColumn('quantity', 'minimum_stock');

            return Excel::download(new class($query) implements FromQuery, WithHeadings, WithMapping {
                protected $query;

                public function __construct($query)
                {
                    $this->query = $query;
                }

                public function query()
                {
                    return $this->query->select(
                        'product_id',
                        'quantity',
                        'minimum_stock'
                    );
                }

                public function headings(): array
                {
                    return [
                        'Producto',
                        'Cantidad',
                        'Stock Mínimo',
                    ];
                }

                public function map($stock): array
                {
                    return [
                        $stock->product->description ?? '', // o product code, según lo que necesites
                        $stock->quantity,
                        $stock->minimum_stock,
                    ];
                }
            }, 'stock_minimo.xlsx');
        } elseif (
            $filter === 'precio'
        ) {
            $query = ProductPrice::with('product')
                ->whereHas('product', function ($q) {
                    $q->where('status', 1);
                });

            return Excel::download(new class($query) implements FromQuery, WithHeadings, WithMapping {
                protected $query;
                public function __construct($query)
                {
                    $this->query = $query;
                }
                public function query()
                {
                    // Seleccionamos los campos de la tabla product_prices
                    return $this->query->select('id', 'product_id', 'price');
                }
                public function headings(): array
                {
                    return [
                        'Producto',
                        'Precio',
                    ];
                }
                public function map($row): array
                {
                    return [
                        // Utiliza la relación definida en ProductPrice para obtener la descripción del producto
                        $row->product->description ?? '',
                        $row->price,
                    ];
                }
            }, 'precio.xlsx');
        }
    }
    public function search(Request $request)
    {
        $buscar = $request->buscar;
        $query = Product::where('status', 1)->with('brand', 'unit', 'warehouse');

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('description', 'like', "%{$buscar}%")
                    ->orWhere('code', 'like', "%{$buscar}%");
            });
        }

        $products = $query->get();

        // Retornamos los productos en formato JSON
        return response()->json($products);
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
        // Manejo de la imagen
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $product->bar_code = 'images/products/' . $imageName;
            $product->save();
        }
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

            if ($request->hasFile('bar_code_update')) {
                // Eliminar imagen anterior si existe
                if (!empty($product->bar_code) && file_exists(public_path($product->bar_code))) {
                    unlink(public_path($product->bar_code));
                }

                // Guardar la nueva imagen
                $image = $request->file('bar_code_update');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $product->bar_code = 'images/products/' . $imageName;
            }

            // Si se envía un nuevo código de barras, actualizarlo
            if ($request->filled('bar_code_update')) {
                $product->bar_code = $request->bar_code_update;
            }

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
