<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\ServiceSale;
use App\Models\Stock;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Dom\Document;
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
        $documentTypes = DocumentType::whereIn('name', ['FACTURA', 'BOLETA DE VENTA', 'NOTA DE VENTA'])->get();

        return view('quotation.create', compact('payments', 'warehouses', 'documentTypes'));
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
        if (!$quotation) {
            return abort(404, 'Venta no encontrada');
        }

        foreach ($quotation->quotationItems as $quotationItem) {

            $id = $quotationItem->item_id;
            if ($quotationItem->item_type == Product::class) {
                $quotationItem->item = Product::find($id);

                $quotationItem->stock = Stock::where('product_id', $id)->first();
                // Obtener todos los precios del producto en el almacén de la cotización
                $productPrices = ProductPrice::where('product_id', $id)
                    ->get();

                // Formatear los precios en la estructura deseada
                $quotationItem->prices = $productPrices->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'product_id' => $price->product_id,
                        'type' => $price->type,
                        'price' => $price->price,

                    ];
                });
            } elseif ($quotationItem->item_type == ServiceSale::class) {
                $quotationItem->item = ServiceSale::find($id);
            }
        }
        $quotation->stock = Stock::where('product_id', $id)->first();
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
                'code' => $this->generateCodeQuotation(),
                'total_price' => $request->total,
                'customer_names_surnames' => $request->customer_names_surnames,
                'customer_dni' => $request->customer_dni,
                'igv' => $request->igv,
                'document_type_id' => $request->document_type,
                'payment_method_id' => $request->payment_method_id,
            ]);

            // 2️⃣ Insertar Productos
            // Insertar Productos
            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    $quotationItem = QuotationItem::create([
                        'quotation_id'    => $quotation->id,
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
                        ['name' => ucfirst(strtolower($service['name']))],
                        ['default_price' => $service['price']]
                    );
                    QuotationItem::create([
                        'quotation_id'    => $quotation->id,
                        'item_type'  => ServiceSale::class,
                        'item_id'    => $serviceModel->id,
                        'quantity'   => 1,
                        'unit_price' => $service['price'],
                        'product_prices_id' => NULL
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
    // public function show(string $id)
    // {
    //     $quotation = Quotation::with('quotationItems')->find($id);
    //     if (!$quotation) {
    //         return response()->json(['error' => 'Cotización no encontrada'], 404);
    //     }

    //     return response()->json($quotation);
    // }
    public function vender(string $id)
    {
        $cotizacion = Quotation::with('quotationItems')->find($id);
        if (!$cotizacion) {
            return response()->json(['error' => 'Cotización no encontrada'], 404);
        }

        $cotizacion->status_sale = '1';
        $cotizacion->save();
        try {
            // 1️⃣ Crear la Venta
            $sale = Sale::create([
                'code' => $this->generateCodeSale(),
                'total_price' => $cotizacion->total_price,
                'customer_names_surnames' => $cotizacion->customer_names_surnames,
                'customer_dni' => $cotizacion->customer_dni,
                'igv' => $cotizacion->igv,
                'quotation_id' => $cotizacion->id,
                'serie' => $this->generateSerie($cotizacion->document_type_id),
                'number' => $this->generateNumero($cotizacion->document_type_id),
            ]);
            if (!empty($cotizacion->quotationItems)) {
                foreach ($cotizacion->quotationItems as $item) {
                    if ($item->item_type === Product::class) {
                        $salesItem = SalesItem::create([
                            'sale_id'    => $sale->id,
                            'item_type'  => Product::class,
                            'item_id'    => $item->item_id,
                            'quantity'   => $item->quantity,
                            'unit_price' => $item->unit_price,
                        ]);
                        if ($salesItem) {
                            $stock = Stock::where('product_id', $item->item_id)->first();
                            if ($stock) {
                                $stock->quantity -= $item->quantity;
                                $stock->save();
                            }
                        }
                    } elseif ($item->item_type === ServiceSale::class) {
                        // return $item;
                        $serviceModel = ServiceSale::firstOrCreate(
                            ['name' => ucfirst(strtolower($item->item->name))],
                            ['default_price' => $item->item->default_price]
                        );
                        SalesItem::create([
                            'sale_id'    => $sale->id,
                            'item_type'  => ServiceSale::class,
                            'item_id'    => $serviceModel->id,
                            'quantity'   => 1,
                            'unit_price' => $item->unit_price,
                        ]);
                    }
                }
            } else {
                return response()->json(['error' => 'La cotización no tiene ítems'], 400);
            }

            return response()->json(['success' => 'Venta creada correctamente'], 200);
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
    public function edit($id)
    {
        $quotation = Quotation::with('quotationItems')->find($id);
        $warehouses = Warehouse::all();
        $payments = PaymentMethod::where('status', 1)->get();
        $documentTypes = DocumentType::whereIn('name', ['FACTURA', 'BOLETA DE VENTA', 'NOTA DE VENTA'])->get();

        return view('quotation.edit', compact('quotation', 'warehouses', 'payments', 'documentTypes'));
        // return response()->json(['success' => "Cotizacion encontrada", 'quotation' => $quotation], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // 1️⃣ Buscar la cotización
            $quotation = Quotation::find($id);
            if (!$quotation) {
                return response()->json(['error' => 'Cotización no encontrada'], 404);
            }

            // 2️⃣ Actualizar datos generales de la cotización
            $quotation->update([
                'total_price' => $request->total,
                'customer_names_surnames' => $request->customer_names_surnames,
                'customer_dni' => $request->customer_dni,
                'igv' => $request->igv,
                'document_type_id' => $request->document_type,
                'payment_method_id' => $request->payment_method_id,

            ]);

            // 3️⃣ Eliminar productos y servicios actuales (para una actualización limpia)
            QuotationItem::where('quotation_id', $quotation->id)->delete();

            // 4️⃣ Insertar nuevos productos
            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    QuotationItem::create([
                        'quotation_id'    => $quotation->id,
                        'item_type'  => Product::class,
                        'item_id'    => $product['item_id'],
                        'quantity'   => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                        'product_prices_id' => $product['priceId']
                    ]);
                }
            }

            // 5️⃣ Insertar nuevos servicios
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
                        'product_prices_id' => NULL
                    ]);
                }
            }

            return response()->json(['success' => 'Cotización actualizada correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
    public function generateCodeSale()
    {
        $lastCodigo = Sale::max('code') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
    public function generateCodeQuotation()
    {
        $lastCodigo = Quotation::max('code') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
    private function generateSerie($documentTypeId)
    {
        $documentTypeId = (int) $documentTypeId; // Convertir a entero

        $tipoDocumento = DocumentType::find($documentTypeId);

        if (!$tipoDocumento) {
            throw new \Exception('Tipo de documento no encontrado');
        }

        $prefijos = [
            'FACTURA' => 'F',
            'BOLETA DE VENTA' => 'B',
            'NOTA DE VENTA' => 'NV',
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
        $documentTypeId = (int) $documentTypeId; // Convertir a entero

        if ($documentTypeId <= 0) {
            throw new \Exception('ID de documento no válido');
        }

        // Buscar la última venta con este tipo de documento
        $ultimaVenta = Sale::whereHas('quotation', function ($query) use ($documentTypeId) {
            $query->where('document_type_id', $documentTypeId);
        })
            ->latest('number')
            ->first();

        // Generar el nuevo número
        $nuevoNumero = $ultimaVenta ? (int) $ultimaVenta->number + 1 : 1;

        return str_pad((string) $nuevoNumero, 4, '0', STR_PAD_LEFT); // 0001, 0002, 0003...
    }
}
