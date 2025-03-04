<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Sale;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $payments = PaymentMethod::where('status', 1)->get();
        return view('sales.create', compact('payments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $sale = Sale::create([
                'code' => $this->generateCode(),
                'total_price' => $request->total,
                'fecha_registro' => $request->order_date,
                'customer_names_surnames' => $request->customer_names_surnames,
                'customer_dni' => $request->customer_dni,
            ]);
            foreach ($request->products as $product) {
                $sale->products()->create([
                    'product_id' => $product['product_id'],
                    'quantity'   => $product['quantity'],
                    'unit_price' => $product['unit_price'],
                ]);
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
        //
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
