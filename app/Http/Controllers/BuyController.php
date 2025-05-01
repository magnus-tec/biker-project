<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Buy;
use App\Models\DocumentType;
use App\Models\BuyItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPrice;

class BuyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::all();
        $products = Product::where('status', 1)->with('brand', 'unit', 'warehouse', 'prices','stock')->get();
        return view('buy.index', compact('products', 'warehouses'));
    }
    public function search(Request $request)
    {
        if (!$request->filled('fecha_desde') || !$request->filled('fecha_hasta')) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        $user = auth()->user();
        $compras = Buy::with('userRegister')
            ->whereDate('fecha_registro', '>=', $request->fecha_desde)
            ->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        return response()->json($compras->get());
    }
    public function addStock(Request $request)
    {
        //dd($request);
       // Validar los datos del request
    $request->validate([
        'total' => 'required|numeric',
        'customer_names_surnames' => 'required|string',
        'customer_address' => 'required|string',
        'customer_dni' => 'required|string',
        'igv' => 'required|numeric',
        'document_type_id' => 'required|integer',
    ]);

    DB::beginTransaction();

    try {
        // Crear la compra
        $buy = Buy::create([
            'total_price' => $request->total,
            'customer_names_surnames' => $request->customer_names_surnames,
            'customer_address' => $request->customer_address??NULL,
            'customer_dni' => $request->customer_dni,
            'igv' => $request->igv,
            'document_type_id' => $request->document_type_id,
            'serie' => $this->generateSerie($request->document_type_id),
            'number' => $this->generateNumero($request->document_type_id),
        ]);

        // Procesar cada producto
        foreach ($request->products as $productData) {
            // Verificar existencia del producto
            $producto = Product::find($productData['product_id']);
            if (!$producto) {
                // Si el producto no existe, lanzar una excepción
                throw new \Exception("El producto con ID {$productData['producto_id']} no existe.");
            }
            
            // Actualizar o crear el stock
            $productoStock = Stock::firstOrCreate(
                ['product_id' => $producto->id],
                ['quantity' => $productData['quantity']]
            );
            // Actualizar precio solo donde type = 'buy' osea precio compra
            $productPriceBuy = ProductPrice::where('product_id', $productData['product_id'])
                ->where('type', 'buy')
                ->first();

            if ($productPriceBuy) {
                $productPriceBuy->price = $productData['price'];
                $productPriceBuy->save();
            }
            $productoStock->quantity += $productData['quantity'];
            $productoStock->save();

            // Registrar el detalle de la compra
            BuyItem::create([
                'buy_id' => $buy->id,
                'product_id' => $producto->id,
                'quantity' => $productData['quantity'],
                'price' => $productData['price'],
            ]);
        }

        DB::commit();

        return response()->json(['message' => 'Stock y compra registrados correctamente'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error al registrar la compra', 'error' => $e->getMessage()], 500);
    }
    }
    public function detallesBuy($id){
        $buy = Buy::with('buyItems.product','userRegister')->find($id);
        if (!$buy) {
            return abort(404, 'Venta no encontrada');
        }
        return response()->json([
            'buy' => $buy,
        ]);
    }
    public function generatePDF($id)
    {
        $buy = Buy::with('buyItems.product', 'userRegister','documentType')->find($id);
        if (!$buy) {
            return abort(404, 'compra no encontrada');
        }
        
        $buy->buy_items = $buy->buyItems ?? [];
        //dd($buy);
        $pdf = Pdf::loadView('buy.pdf', compact('buy'));
        return $pdf->stream('buy.pdf');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $documentTypes = DocumentType::whereIn('name', ['NOTA DE VENTA'])->get();
        $warehouses = Warehouse::all();

        return view('buy.create',compact('documentTypes','warehouses'));
    }
    private function generateSerie($documentTypeId)
    {
        $documentTypeId = (int) $documentTypeId; // Convertir a entero

        $tipoDocumento = DocumentType::find($documentTypeId);

        if (!$tipoDocumento) {
            throw new \Exception('Tipo de documento no encontrado');
        }

        $prefijos = [
            'NOTA DE VENTA' => 'NC',
        ];

        if (!isset($prefijos[$tipoDocumento->name])) {
            throw new \Exception('Tipo de documento no válido');
        }

        // FACTURA y BOLETA DE VENTA usan tres dígitos (F001, B001), NOTA DE VENTA usa dos (NV01)
        $numeroSerie = ($tipoDocumento->name === 'NOTA DE VENTA') ? '01' : '001';

        return $prefijos[$tipoDocumento->name] . $numeroSerie;
    }

    private function generateNumero($documentTypeId)
    {
        $documentTypeId = (int) $documentTypeId;
        if ($documentTypeId <= 0) {
            throw new \Exception('ID de documento no válido');
        }
        $ultimaVenta = Buy::where('document_type_id', $documentTypeId)
            ->orderByDesc('number')
            ->first();
        $ultimoNumero = $ultimaVenta ? intval($ultimaVenta->number) : 0;
        $nuevoNumero = $ultimoNumero + 1;

        return (string) $nuevoNumero;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
