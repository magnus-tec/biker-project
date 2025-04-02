<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\District;
use App\Models\DocumentType;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Province;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationPaymentMethod;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\SalesSunat;
use App\Models\Service;
use App\Models\ServiceSale;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dom\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use mysqli;
use PDO;
use PDOException;

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
        $paymentsMethod = PaymentMethod::where('status', 1)->get();
        $paymentsType = Payment::all();
        $documentTypes = DocumentType::whereIn('name', ['FACTURA', 'BOLETA DE VENTA', 'NOTA DE VENTA'])->get();
        $companies = Company::all();
        $regions = Region::all();
        return view('quotation.create', compact('paymentsMethod', 'paymentsType', 'warehouses', 'documentTypes', 'companies', 'regions'));
    }
    public function MecanicosDisponibles()
    {
        $mechanics = User::whereHas('roles', function ($query) {
            $query->where('name', 'mecanico');
        })
            ->get();

        return response()->json($mechanics);
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
        $ventas = Quotation::with('userRegister', 'mechanic')
            ->whereDate('fecha_registro', '>=', $request->fecha_desde)
            ->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        return response()->json($ventas->get());
    }
    public function detallesQuotation($id)
    {
        $quotation = Quotation::with('quotationItems.item', 'userRegister', 'quotationPaymentMethod', 'district.province.region', 'mechanic')->find($id);
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
                'customer_address' => $request->customer_address,
                'customer_dni' => $request->customer_dni,
                'igv' => $request->igv,
                'document_type_id' => $request->document_type_id,
                'districts_id' => $request->districts_id,
                // 'payment_method_id' => $request->payment_method_id,
                'companies_id' => $request->companies_id,
                'payments_id' => $request->payments_id,
                'mechanics_id' => $request->mechanics_id,
            ]);
            if (!empty($request->payments)) {
                foreach ($request->payments as $payment) {
                    QuotationPaymentMethod::create([
                        'quotation_id' => $quotation->id,
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount' => floatval($payment['amount']),
                        'order' => intval($payment['order'])
                    ]);
                }
            }
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
                        [
                            'name' => ucfirst(strtolower($service['name'])) // Se busca por 'name'
                        ],
                        [
                            'code_sku' => $this->generateCodeService(), // Se asigna si no existe
                            'default_price' => (float) $service['price'] // Convertir a float
                        ]
                    );

                    QuotationItem::create([
                        'quotation_id'    => $quotation->id,
                        'item_type'  => ServiceSale::class,
                        'item_id'    => $serviceModel->id,
                        'quantity'   => 1,
                        'unit_price' => $service['price'],
                        'product_prices_id' => NULL,
                        'mechanics_id' => $request->mechanics_id,
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
                'customer_address' => $cotizacion->customer_address,
                'customer_dni' => $cotizacion->customer_dni,
                'igv' => $cotizacion->igv,
                'quotation_id' => $cotizacion->id,
                'serie' => $this->generateSerie($cotizacion->document_type_id),
                'number' => $this->generateNumero($cotizacion->document_type_id),
                'payment_method_id' => $cotizacion->payment_method_id,
                'document_type_id' => $cotizacion->document_type_id,
                'companies_id' => $cotizacion->companies_id,
                'payments_id' => $cotizacion->payments_id,

            ]);
            if (!empty($cotizacion->quotationItems)) {
                foreach ($cotizacion->quotationItems as $item) {
                    if ($item->item_type === Product::class) {
                        $salesItem = SalesItem::create([
                            'sale_id'    => $sale->id,
                            'item_type'  => $item->item_type,
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
                            [
                                'name' => ucfirst(strtolower($item->item->name))
                            ],
                            [
                                'default_price' => $item->item->default_price,
                                'code_sku' => $this->generateCodeService()
                            ]

                        );
                        SalesItem::create([
                            'sale_id'    => $sale->id,
                            'item_type'  => $item->item_type,
                            'item_id'    => $serviceModel->id,
                            'quantity'   => 1,
                            'unit_price' => $item->unit_price,
                        ]);
                    }
                }
                //consultando la empresa a emitir el documento
                $company = Company::find($sale->companies_id);
                $empresa = [
                    "ruc" => $company->ruc,
                    "usuario" => $company->sol_user,
                    "clave" => $company->sol_pass,
                    "razon_social" => $company->razon_social,
                    "direccion" => $company->direccion,
                    "ubigeo" => $company->ubigeo,
                    "distrito" => $company->distrito,
                    "provincia" => $company->provincia,
                    "departamento" => $company->departamento
                ];
                //datos cliente
                $cliente = [
                    "num_doc" => $sale->customer_dni,
                    "rzn_social" => $sale->customer_names_surnames,
                    "direccion" => !empty($sale->customer_address) ? $sale->customer_address : ""
                ];
                //datos productos
                $products = [];
                $saleItems = SalesItem::where('sale_id', $sale->id)->with('item')->get();

                foreach ($saleItems as $product) {
                    $products[] = [
                        "cod_producto" => $product->item->code_sku, // preguntar si es code_sku o code_bar o code interno
                        "cod_sunat" => "",
                        "unidad" => "NIU",
                        "precio" => $product->unit_price,
                        "cantidad" => $product->quantity,
                        "descripcion" => !empty($product->item->description) ? $product->item->description : $product->item->name
                    ];
                }
                $data = [
                    "total" => $sale->total_price,
                    "endpoint" => "beta",
                    "fecha_emision" => Carbon::parse($sale->fecha_registro)->format('Y-m-d'),
                    "fecha_vencimiento" => Carbon::parse($sale->fecha_registro)->addDay()->format('Y-m-d'),
                    "documento" => strtolower(strtok($sale->documentType->name, ' ')),
                    "serie" => $sale->serie,
                    "numero" => $sale->number,
                    "forma_pago" => $sale->payments->name,
                    "moneda" => "PEN",
                    "empresa" => $empresa,
                    "cliente" => $cliente,
                    "detalles" => $products
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://magustechnologies.com/apisunat/api/generar/comprobante/electronico");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desactiva verificación SSL (solo para pruebas)
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json",
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $response = ltrim($response, "\u{00A9}");
                $response = ltrim($response, "©");
                // Decodificar la respuesta JSON
                $body = json_decode($response, true);
                // return $body;
                $xmlContent = $body['data']['contenido_xml']; // Extraer el XML de la respuesta
                $nombre_archivo = $body['data']['nombre_archivo'];
                $hash = $body['data']['hash'];
                $qr_info = $body['data']['qr_info'];
                // Guardar el XML en storage/app/xmls/archivo.xml
                Storage::put("xmls/$nombre_archivo.xml", $xmlContent);
                $saleSunat = SalesSunat::create([
                    'sale_id' => $sale->id,
                    'hash' => $hash,
                    'qr_info' => $qr_info,
                    'name_xml' => $nombre_archivo
                ]);
                if ($saleSunat) {
                    return response()->json(['success' => 'Venta guardada correctamente'], 200);
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
        $quotation = Quotation::with('quotationItems', 'quotationPaymentMethod')->find($id);
        $warehouses = Warehouse::all();
        $paymentsMethod = PaymentMethod::where('status', 1)->get();
        $paymentsType = Payment::all();
        $documentTypes = DocumentType::whereIn('name', ['FACTURA', 'BOLETA DE VENTA', 'NOTA DE VENTA'])->get();
        $companies = Company::all();
        $regions = Region::all();
        return view('quotation.edit', compact('quotation', 'warehouses', 'paymentsType', 'documentTypes', 'companies', 'paymentsMethod', 'regions'));
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
                'customer_address' => $request->customer_address,
                'igv' => $request->igv,
                'document_type_id' => $request->document_type,
                // 'payment_method_id' => $request->payment_method_id,
                'fecha_actualizacion' => now()->setTimezone('America/Lima'),
                'user_update' => auth()->user()->id,
                'companies_id' => $request->companies_id,
                'payments_id' => $request->payments_id,
                'mechanics_id' => $request->mechanics_id,
                'districts_id' => $request->districts_id,
            ]);

            QuotationPaymentMethod::where('quotation_id', $quotation->id)->delete();
            if (!empty($request->payments)) {
                foreach ($request->payments as $payment) {
                    QuotationPaymentMethod::create([
                        'quotation_id' => $quotation->id,
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount' => floatval($payment['amount']),
                        'order' => intval($payment['order'])
                    ]);
                }
            }

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
                        [
                            'name' => ucfirst(strtolower($service['name'])) // Se busca por 'name'
                        ],
                        [
                            'code_sku' => $this->generateCodeService(), // Se asigna si no existe
                            'default_price' => (float) $service['price'] // Convertir a float
                        ]
                    );
                    QuotationItem::create([
                        'quotation_id'    => $quotation->id,
                        'item_type'  => ServiceSale::class,
                        'item_id'    => $serviceModel->id,
                        'quantity'   => 1,
                        'unit_price' => $service['price'],
                        'product_prices_id' => NULL,
                        'mechanics_id' => $request->mechanics_id,

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
    public function generateCodeService()
    {
        $lastCodigo = ServiceSale::max('code_sku') ?? '0000000';
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
        $documentTypeId = (int) $documentTypeId;
        if ($documentTypeId <= 0) {
            throw new \Exception('ID de documento no válido');
        }
        $ultimaVenta = Sale::where('document_type_id', $documentTypeId)
            ->orderByDesc('number')
            ->first();
        $ultimoNumero = $ultimaVenta ? intval($ultimaVenta->number) : 0;
        $nuevoNumero = $ultimoNumero + 1;

        return (string) $nuevoNumero;
    }
}
