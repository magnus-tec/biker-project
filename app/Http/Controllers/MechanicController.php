<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class MechanicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mechanics = Mechanic::with('userRegistered')->get();
        return view('mechanic.index', compact('mechanics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mechanic.create');
    }
    public function MecanicosDisponibles()
    {
        $mechanics = Mechanic::where('status_mechanic', 0)->get();
        return response()->json($mechanics);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Mensajes personalizados de validación
        $messages = [
            'num_doc.required' => 'El número de documento es obligatorio.',
            'num_doc.unique' => 'El número de documento ya está registrado.',

        ];
        try {
            $request->validate([
                'num_doc' => 'required|string|unique:mechanics,nro_documento',
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
        try {
            $driver = Mechanic::create([
                'codigo' => $this->generateCode(),
                'tipo_doc' => $request->tipo_doc,
                'nro_documento' => $request->num_doc,
                'nacionalidad' => $request->nacionalidad,
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'departamento' => $request->departamento,
                'provincia' => $request->provincia,
                'distrito' => $request->distrito,
                'direccion_detalle' => $request->direccion_domicilio,
                'nombres_contacto' => $request->nombre_contacto,
                'telefono_contacto' => $request->telefono_contacto,
                'parentesco_contacto' => $request->parentesco_contacto,
                // 'photo' => $request->photo,
                'user_register' => auth()->user()->id,
            ]);
            if ($driver) {
                return response()->json([
                    'success' => true,
                    'message' => '¡El Mecanico ' . $driver->nombres . ' ha sido registrado con éxito!',
                ]);
            } else {
                return response()->json([
                    'error' => 'El Mecanico no pudo ser registrado',
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
        $lastCodigo = Mechanic::max('codigo') ?? '0000000';
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
