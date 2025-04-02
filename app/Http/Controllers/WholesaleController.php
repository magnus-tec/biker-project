<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Region;
use App\Models\ServiceSale;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\Wholesaler;
use App\Models\WholesalerItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WholesaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('wholesaler.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        $regions = Region::all();
        return view('wholesaler.create', compact('warehouses', 'regions'));
    }
    public function detallesWholesaler($id)
    {
        $mayorista = Wholesaler::with('wholesalerItems.item', 'userRegister', 'district.province.region', 'mechanic')->find($id);
        if (!$mayorista) {
            return abort(404, 'Venta no encontrada');
        }

        foreach ($mayorista->wholesalerItems as $wholesalerItem) {

            $id = $wholesalerItem->item_id;
            if ($wholesalerItem->item_type == Product::class) {
                $wholesalerItem->item = Product::find($id);

                $wholesalerItem->stock = Stock::where('product_id', $id)->first();
                // Obtener todos los precios del producto en el almacén de la cotización
                $productPrices = ProductPrice::where('product_id', $id)
                    ->get();

                // Formatear los precios en la estructura deseada
                $wholesalerItem->prices = $productPrices->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'product_id' => $price->product_id,
                        'type' => $price->type,
                        'price' => $price->price,

                    ];
                });
            } elseif ($wholesalerItem->item_type == ServiceSale::class) {
                $wholesalerItem->item = ServiceSale::find($id);
            }
        }
        $mayorista->stock = Stock::where('product_id', $id)->first();
        return response()->json([
            'mayorista' => $mayorista,
        ]);
    }
    public function filtroPorfecha(Request $request)
    {
        if (!$request->filled('fecha_desde') || !$request->filled('fecha_hasta')) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        $mayorista = Wholesaler::with('userRegister', 'mechanic')
            ->whereDate('fecha_registro', '>=', $request->fecha_desde)
            ->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        return response()->json($mayorista->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1️⃣ Crear la Venta
            $wholesaler = Wholesaler::create([
                'code' => $this->generateCodeWholesaler(),
                'total_price' => $request->total,
                'customer_names_surnames' => $request->customer_names_surnames,
                'customer_address' => $request->customer_address,
                'customer_dni' => $request->customer_dni,
                'igv' => $request->igv,
                'mechanics_id' => $request->mechanics_id,
                'districts_id' => $request->districts_id,
            ]);

            // Insertar Productos
            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    $wholesalerItem = WholesalerItem::create([
                        'wholesaler_id'    => $wholesaler->id,
                        'item_type'  => Product::class,
                        'item_id'    => $product['item_id'],
                        'quantity'   => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                        'product_prices_id' => $product['priceId']
                    ]);
                }
            }
            // Insertar Servicios
            if (!empty($request->services)) {
                foreach ($request->services as $service) {
                    $serviceModel = ServiceSale::firstOrCreate(
                        [
                            'name' => ucfirst(strtolower($service['name']))
                        ],
                        [
                            'code_sku' => $this->generateCodeService(),
                            'default_price' => (float) $service['price']
                        ]
                    );

                    WholesalerItem::create([
                        'wholesaler_id'    => $wholesaler->id,
                        'item_type'  => ServiceSale::class,
                        'item_id'    => $serviceModel->id,
                        'quantity'   => 1,
                        'unit_price' => $service['price'],
                        'product_prices_id' => NULL,
                        'mechanics_id' => $request->mechanics_id,
                    ]);
                }
            }

            if ($wholesaler) {
                return response()->json(['success' => 'Registro de mayorista creada correctamente'], 200);
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
        $mayorista = Wholesaler::with('wholesalerItems')->find($id);
        $warehouses = Warehouse::all();
        $regions = Region::all();
        return view('wholesaler.edit', compact('mayorista', 'warehouses', 'regions'));
    }
    public function generatePDF($id)
    {
        $mayorista = Wholesaler::with('wholesalerItems.item', 'userRegister')->find($id);
        if (!$mayorista) {
            return abort(404, 'Venta no encontrada');
        }

        // Si `sale_items` es null, aseguramos que sea un array vacío para evitar errores
        $mayorista->wholesaler_items = $mayorista->wholesaler_items ?? [];
        $pdf = Pdf::loadView('wholesaler.pdf', compact('mayorista'));
        return $pdf->stream('wholesaler.pdf');
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
            $mayorista = Wholesaler::find($id);

            if (!$mayorista) {
                return response()->json(['error' => 'Mayorista no encontrada'], 404);
            }
            $mayorista->delete();
            return response()->json(['success' => 'Registro eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function generateCodeWholesaler()
    {
        $lastCodigo = Wholesaler::max('code') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
    public function generateCodeService()
    {
        $lastCodigo = ServiceSale::max('code_sku') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
}
