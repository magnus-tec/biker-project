<?php

namespace App\Http\Controllers;

use App\Models\Garantine;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class GarantineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $garantias = Garantine::where('status', 1)->get();
        return view('garantine.index', compact('garantias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('garantine.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'n_documento.required' => 'El numero de documento es obligatorio.',
            'datos_cliente.required' => 'Los datos del cliente son  obligatorio.',
            'nro_motor.unique' => 'El numero de motor ya a sido registrado como vendido.',
        ];
        try {
            $request->validate([
                'n_documento' => 'required|string',
                'datos_cliente' => 'required|string',
                'nro_motor' => 'required|unique:garantines,nro_motor',
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
        try {
            $garantine = Garantine::create([
                'codigo' => $this->generateCode(),
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'anio' => $request->anio,
                'nro_chasis' => $request->nro_chasis,
                'nro_motor' => $request->nro_motor,
                'color' => $request->color,
                'user_register' => auth()->user()->id,
                'nro_documento' => $request->n_documento,
                'nombres_apellidos' => $request->datos_cliente
            ]);
            if ($garantine) {
                return response()->json([
                    'success' => true,
                    'message' => '¡La Garantianza  ha sido registrado con éxito!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar la Garantianza',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function generateCode()
    {
        $lastCodigo = Garantine::max('codigo') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
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
