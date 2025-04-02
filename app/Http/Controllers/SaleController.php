<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DocumentType;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SalePaymentMethod;
use App\Models\SalesItem;
use App\Models\SalesSunat;
use App\Models\Service;
use App\Models\ServiceSale;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\GenerarQR;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::whereIn('name', ['FACTURA', 'BOLETA DE VENTA', 'NOTA DE VENTA'])->get();

        return view('sales.index', compact('documentTypes'));
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
        $fecha_registro =  Carbon::parse($sale->fecha_registro)->format('Y-m-d');

        // GENERAR QR
        $hash = hash('sha1', '' . time(), true);
        $hashBase64 = base64_encode($hash);
        $generarQr = new GenerarQR($sale->companies->ruc, $sale->documentType->sunat_code, $sale->serie, $sale->number, $sale->igv, $sale->total_price, $fecha_registro, $sale->customer_dni, $hashBase64);
        $imagenQr = $generarQr->obtenerQR();
        // Si `sale_items` es null, aseguramos que sea un array vacío para evitar errores
        $sale->sale_items = $sale->sale_items ?? [];
        $pdf = Pdf::loadView('sales.pdf', compact('sale', 'imagenQr', 'hashBase64'));
        return $pdf->stream('venta.pdf');
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

        return view('sales.create', compact('regions', 'warehouses', 'paymentsMethod', 'documentTypes', 'companies', 'paymentsType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response()->json($request);
        try {
            // 1️⃣ Crear la Venta
            $sale = Sale::create([
                'code' => $this->generateCode(),
                'total_price' => $request->total,
                'customer_names_surnames' => $request->customer_names_surnames,
                'customer_address' => $request->customer_address,
                'customer_dni' => $request->customer_dni,
                'igv' => $request->igv,
                'serie' => $this->generateSerie($request->document_type_id),
                'number' => $this->generateNumero($request->document_type_id),
                'document_type_id' => $request->document_type_id,
                'companies_id' => $request->companies_id,
                'payments_id' => $request->payments_id,
                'mechanics_id' => $request->mechanics_id,
                'districts_id' => $request->districts_id,

            ]);
            if (!empty($request->payments)) {
                foreach ($request->payments as $payment) {
                    SalePaymentMethod::create([
                        'sale_id' => $sale->id,
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount' => floatval($payment['amount']),
                        'order' => intval($payment['order'])
                    ]);
                }
            }
            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    $salesItem = SalesItem::create([
                        'sale_id'    => $sale->id,
                        'item_type'  => Product::class,
                        'item_id'    => $product['item_id'],
                        'quantity'   => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                    ]);

                    // Descontar la cantidad del stock
                    if ($salesItem) {
                        $productId = $product['item_id'];
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
                        [
                            'name' => ucfirst(strtolower($service['name'])) // Se busca por 'name'
                        ],
                        [
                            'code_sku' => $this->generateCodeService(), // Se asigna si no existe
                            'default_price' => (float) $service['price'] // Convertir a float
                        ]
                    );

                    SalesItem::create([
                        'sale_id'    => $sale->id,
                        'item_type'  => ServiceSale::class,
                        'item_id'    => $serviceModel->id,
                        'quantity'   => 1,
                        'unit_price' => $service['price'],
                        'product_prices_id' => NULL,
                        'mechanics_id' => $request->mechanics_id,
                    ]);
                }
            }

            //consultando la empresa a emitir el documento
            $company = Company::find($request->companies_id);
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
            // fin de la empresa

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

            // return response()->json($data);
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

        if ($request->filled('document_type_id')) {
            $ventas->where('document_type_id', $request->document_type_id);
        }
        return response()->json($ventas->get());
    }
    public function generateCode()
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
