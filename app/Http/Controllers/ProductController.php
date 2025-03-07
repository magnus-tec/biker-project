<?php

namespace App\Http\Controllers;

use App\Exports\ImportTemplateExport;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Imports\ProductsImport;
use App\Models\UnitType;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::all();
        $products = Product::where('status', 1)->with('brand', 'unit', 'warehouse', 'prices')->get();
        return view('product.index', compact('products', 'warehouses'));
    }
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
        $query = Product::where('status', 1)
            ->with('brand', 'unit', 'warehouse', 'images', 'prices');

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('description', 'like', "%{$buscar}%")
                    ->orWhere('code', 'like', "%{$buscar}%");
            });
        }
        if ($request->has('almacen') && $request->almacen !== 'todos') {
            $query->where('warehouse_id', $request->almacen);
        }

        $products = $query->get();

        // Convertir la ruta de cada imagen para que incluya la URL completa
        $products->each(function ($product) {
            $product->images->transform(function ($img) {
                $img->image_path = asset($img->image_path);
                return $img;
            });
        });

        return response()->json($products);
    }
    public function descargarPlantilla()
    {
        return Excel::download(new ImportTemplateExport, 'Plantilla_Importacion.xlsx');
    }
    public function import(Request $request)
    {
        // dd($request->file('importFile'));
        // Validar que el archivo sea correcto
        $request->validate([
            'importFile' => 'required|mimes:xlsx,csv',
        ]);

        try {
            // return response()->json($request);
            Excel::import(new ProductsImport(auth()->id()), $request->file('importFile'));

            return response()->json([
                'success' => true,
                'message' => 'Productos importados correctamente.'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->failures()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
        $messages = [
            'description.string' => 'La descripción debe ser un texto.',
            'model.required' => 'El modelo es obligatorio.',
            'warehouse_id.required' => 'El almacén es obligatorio.',
            'warehouse_id.exists' => 'El almacén seleccionado no es válido.',
            'brand.required' => 'La marca es obligatoria.',
            'unit_name.required' => 'La unidad es obligatoria.',
            'code_sku.required' => 'El código es obligatorio.',
            'code_sku.unique' => 'El código ya está registrado.',
            'code_bar.required' => 'El código de barra es obligatorio.',
            'code_bar.unique' => 'El código de barra ya está registrado.',
            'prices.array' => 'Los precios deben ser una lista.',
            'prices.*.numeric' => 'Cada precio debe ser un número válido.',
            'prices.*.min' => 'Cada precio debe ser mayor o igual a 0.',

        ];

        try {
            $validated = $request->validate([
                'description' => 'nullable|string',
                'amount' => 'nullable|integer',
                'model' => 'required|string',
                'location' => 'nullable|string',
                'warehouse_id' => 'required|exists:warehouses,id',
                'brand' => 'required|string|max:255',
                'unit_name' => 'required|string|max:255',
                'prices' => 'nullable|array',
                'prices.*' => 'nullable|numeric|min:0',
                'code_sku' => 'required|string|unique:products,code_sku',
                'code_bar' => 'required|string|unique:products,code_bar',
            ], $messages);

            $validated['code'] = $this->generateCode();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Normaliza la unidad y la crea si no existe
            $unitName = ucfirst(strtolower(trim($request->unit_name)));
            $unitTypeId = $this->determineUnitType($unitName);
            $unit = Unit::firstOrCreate([
                'name' => $unitName,
            ], [
                'unit_type_id' => $unitTypeId
            ]);
            $validated['unit_id'] = $unit->id;


            // Normaliza la marca y la crea si no existe
            $brandName = ucfirst(strtolower($request->brand));
            $brand = Brand::firstOrCreate(['name' => $brandName]);

            // Agregar el brand_id a los datos validados
            $validated['brand_id'] = $brand->id;

            $product = Product::create($validated);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images/products'), $imageName);

                    $product->images()->create([
                        'image_path' => 'images/products/' . $imageName,
                    ]);
                }
            }
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

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => '¡El producto ha sido registrado con éxito!',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    private function determineUnitType($unitName)
    {
        $unitTypeMappings = [
            'Peso' => ['kilogramos', 'gramos', 'toneladas'],
            'Volumen' => ['litros', 'mililitros', 'galones'],
            'Cantidad' => ['unidades', 'piezas', 'cajas'],
        ];

        foreach ($unitTypeMappings as $type => $keywords) {
            if (in_array(strtolower($unitName), $keywords)) {
                // Busca el ID del tipo, si no existe, lo crea
                return UnitType::firstOrCreate(['name' => $type])->id;
            }
        }

        // Si no se encuentra un tipo, se crea "Otro" y devuelve su ID
        return UnitType::firstOrCreate(['name' => 'Otro'])->id;
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $product = Product::with('brand', 'unit', 'warehouse', 'prices')->findOrFail($id);
            // $brands = Brand::all();
            $units = Unit::all();
            $warehouses = Warehouse::all();
            $productStock = Stock::where('product_id', $product->id)->first();
            return view('product.edit', compact('product', 'units', 'warehouses', 'productStock'));
        } catch (\Throwable $th) {
            return redirect()->route('product.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // return response()->json($request);
        $messages = [
            'description.string' => 'La descripción debe ser un texto.',
            'model.required' => 'El modelo es obligatorio.',
            'warehouse_id.required' => 'El almacén es obligatorio.',
            'warehouse_id.exists' => 'El almacén seleccionado no es válido.',
            'brand.required' => 'La marca es obligatoria.',
            'unit_name.required' => 'La unidad es obligatoria.',
            'code_sku.required' => 'El código  es obligatorio.',
            'code_sku.unique' => 'El código  ya está registrado.',
            'prices.array' => 'Los precios deben ser una lista.',
            'prices.*.numeric' => 'Cada precio debe ser un número válido.',
            'prices.*.min' => 'Cada precio debe ser mayor o igual a 0.',
        ];
        try {
            // Validar los datos recibidos
            $validated = $request->validate([
                'description' => 'nullable|string',
                'amount' => 'nullable|integer',
                'model' => 'required|string',
                'location' => 'nullable|string',
                'warehouse_id' => 'required|exists:warehouses,id',
                'brand' => 'required|string|max:255',
                'unit_name' => 'required|string|max:255',
                'prices' => 'nullable|array',
                'prices.*' => 'nullable|numeric|min:0',
                'code_sku' => 'required|string|unique:products,code_sku,' . $id,
                'code_bar' => 'required|string|unique:products,code_bar,' . $id,
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
        try {
            // Buscar el producto existente
            $product = Product::findOrFail($id);

            if ($request->hasFile('new_images')) {
                $newImages = $request->file('new_images');

                foreach ($newImages as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images/products'), $imageName);

                    $product->images()->create([
                        'image_path' => 'images/products/' . $imageName,
                    ]);
                }
            }

            // Asegurar que deleted_images es un array válido
            $deletedImages = $request->input('deleted_images', []);
            $deletedImages = is_array($deletedImages) ? $deletedImages : json_decode($deletedImages, true);

            if (!empty($deletedImages)) {
                foreach ($deletedImages as $imageId) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        $imagePath = public_path($image->image_path);
                        if (file_exists($imagePath)) {
                            unlink($imagePath); // Eliminar archivo físico
                        }
                        $image->delete(); // Eliminar registro en la base de datos
                    }
                }
            }
            $unitName = ucfirst(strtolower(trim($request->unit_name)));
            $unitTypeId = $this->determineUnitType($unitName);
            $unit = Unit::firstOrCreate([
                'name' => $unitName,
            ], [
                'unit_type_id' => $unitTypeId
            ]);
            $validated['unit_id'] = $unit->id;
            $brandName = ucfirst(strtolower($request->brand));
            $brand = Brand::firstOrCreate(['name' => $brandName]);
            $validated['brand_id'] = $brand->id;
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
