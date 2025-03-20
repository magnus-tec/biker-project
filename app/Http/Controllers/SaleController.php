<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Service;
use App\Models\ServiceSale;
use App\Models\Stock;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sales.index');
    }
    public function detallesVenta($id)
    {
        $sale = Sale::with('saleItems.item', 'userRegister')->find($id);

        return response()->json([
            'sale' => $sale,
        ]);
    }
    public function generatePDF($id)
    {
        $sale = Sale::with('saleItems.item', 'userRegister')->find($id);
        if (!$sale) {
            return abort(404, 'Venta no encontrada');
        }

        // Si `sale_items` es null, aseguramos que sea un array vacío para evitar errores
        $sale->sale_items = $sale->sale_items ?? [];
        $pdf = Pdf::loadView('sales.pdf', compact('sale'));
        return $pdf->stream('venta.pdf');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1️⃣ Crear la Venta
            $sale = Sale::create([
                'code' => $this->generateCode(),
                'total_price' => $request->total,
                'customer_names_surnames' => $request->customer_names_surnames,
                'customer_dni' => $request->customer_dni,
                'igv' => $request->igv,
            ]);

            // 2️⃣ Insertar Productos
            // Insertar Productos
            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    $salesItem = SalesItem::create([
                        'sale_id'    => $sale->id,
                        'item_type'  => Product::class,
                        'item_id'    => $product['product_id'],
                        'quantity'   => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                    ]);

                    // Descontar la cantidad del stock
                    if ($salesItem) {
                        $productId = $product['product_id'];
                        $quantity = $product['quantity'];

                        // Actualizar el stock
                        $stock = Stock::where('product_id', $productId)->first();
                        if ($stock) {
                            $stock->quantity -= $quantity;
                            $stock->save();
                        }
                    }
                }
            }

            // Insertar Servicios
            if (!empty($request->services)) {
                foreach ($request->services as $service) {
                    $serviceModel = ServiceSale::firstOrCreate(
                        ['name' => ucfirst(strtolower($service['name']))],
                        ['default_price' => $service['price']]
                    );
                    SalesItem::create([
                        'sale_id'    => $sale->id,
                        'item_type'  => ServiceSale::class, // 🔹 Verifica que ServiceSale::class devuelve el FQCN correcto
                        'item_id'    => $serviceModel->id,
                        'quantity'   => 1,
                        'unit_price' => $service['price'],
                    ]);
                }
            }

            if ($sale) {
                return response()->json(['success' => 'Venta creada correctamente'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }

            // Obtener los productos asociados a la venta
            $salesItems = SalesItem::where('sale_id', $sale->id)->get();

            foreach ($salesItems as $salesItem) {
                if ($salesItem->item_type === Product::class) {
                    $stock = Stock::where('product_id', $salesItem->item_id)->first();
                    if ($stock) {
                        $stock->quantity += $salesItem->quantity; // Revertir la reducción del stock
                        $stock->save();
                    }
                }
            }
            $sale->delete();
            return response()->json(['success' => 'Venta eliminada correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function filtroPorfecha(Request $request)
    {
        if (!$request->filled('fecha_desde') || !$request->filled('fecha_hasta')) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        $user = auth()->user();
        $ventas = Sale::with('userRegister')
            ->whereDate('fecha_registro', '>=', $request->fecha_desde)
            ->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        return response()->json($ventas->get());
    }
    public function generateCode()
    {
        $lastCodigo = Sale::max('code') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
}
