<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\ServiceSale;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('quotation.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        $payments = PaymentMethod::where('status', 1)->get();
        return view('quotation.create', compact('payments', 'warehouses'));
    }
    public function generatePDF($id)
    {
        $quotation = Quotation::with('quotationItems.item', 'userRegister')->find($id);
        if (!$quotation) {
            return abort(404, 'Venta no encontrada');
        }

        // Si `sale_items` es null, aseguramos que sea un array vacío para evitar errores
        $quotation->sale_items = $sale->sale_items ?? [];
        $pdf = Pdf::loadView('quotation.pdf', compact('quotation'));
        return $pdf->stream('cotizacion.pdf');
    }
    public function filtroPorfecha(Request $request)
    {
        if (!$request->filled('fecha_desde') || !$request->filled('fecha_hasta')) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        $user = auth()->user();
        $ventas = Quotation::with('userRegister')
            ->whereDate('fecha_registro', '>=', $request->fecha_desde)
            ->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        return response()->json($ventas->get());
    }
    public function detallesQuotation($id)
    {
        $quotation = Quotation::with('quotationItems.item', 'userRegister')->find($id);

        return response()->json([
            'quotation' => $quotation,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response()->json($request);
        try {
            // 1️⃣ Crear la Venta
            $quotation = Quotation::create([
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
                    $quotationItem = QuotationItem::create([
                        'quotation_id'    => $quotation->id,
                        'item_type'  => Product::class,
                        'item_id'    => $product['product_id'],
                        'quantity'   => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                    ]);
                }
            }
            // Insertar Servicios
            if (!empty($request->services)) {
                foreach ($request->services as $service) {
                    $serviceModel = ServiceSale::firstOrCreate(
                        ['name' => ucfirst(strtolower($service['name']))],
                        ['default_price' => $service['price']]
                    );
                    QuotationItem::create([
                        'quotation_id'    => $quotation->id,
                        'item_type'  => ServiceSale::class,
                        'item_id'    => $serviceModel->id,
                        'quantity'   => 1,
                        'unit_price' => $service['price'],
                    ]);
                }
            }

            if ($quotation) {
                return response()->json(['success' => 'Cotización creada correctamente'], 200);
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
        try {
            $quotation = Quotation::with('quotationItems.item')->find($id);
            if (!$quotation) {
                return response()->json(['error' => 'Cotizacion no encontrada'], 404);
            }
            return response()->json(['success' => "Cotizacion encontrada", 'quotation' => $quotation], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCotizacion($id)
    {
        try {
            $warehouses = Warehouse::all();
            $payments = PaymentMethod::where('status', 1)->get();
            return view('quotation.show', compact('warehouses', 'payments'));
            // return response()->json(['success' => "Cotizacion encontrada", 'quotation' => $quotation], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $quotation = Quotation::find($id);

            if (!$quotation) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }
            $quotation->delete();
            return response()->json(['success' => 'Venta eliminada correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function generateCode()
    {
        $lastCodigo = Quotation::max('code') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
}
