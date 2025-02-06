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
        $garantias = Garantine::with('drive', 'userRegistered')->get();
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
        // Mensajes personalizados de validación
        $messages = [
            'marca.required' => 'La marca es obligatorio.',
            'modelo.required' => 'El modelo es obligatorio.',
            'nro_chasis.required' => 'El numero de chasis es obligatorio.',
            'drive_id.required' => 'El numero de documento es obligatorio.',
        ];
        try {
            $request->validate([
                'marca' => 'required|string',
                'modelo' => 'required|string',
                'nro_chasis' => 'required|string',
                'drive_id' => 'required|string'
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
                'drives_id' => $request->drive_id
            ]);
            return response()->json([
                'success' => true,
                'message' => '¡La Garantianza  ha sido registrado con éxito!',
            ]);
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
