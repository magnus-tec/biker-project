<?php

namespace App\Http\Controllers;

use App\Models\Drive;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('driver.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('driver.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Mensajes personalizados de validación
        $messages = [
            'tipo_doc.required' => 'El tipo de documento es obligatorio.',
            'num_doc.required' => 'El número de documento es obligatorio.',
            'nacionalidad.required' => 'La nacionalidad es obligatoria.',
            'num_doc.unique' => 'El número de documento ya está registrado.',

        ];
        try {
            $request->validate([
                'tipo_doc' => 'required|string',
                'num_doc' => 'required|string|unique:drives,nro_documento',
                'nacionalidad' => 'required|string',
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
        try {
            $driver = Drive::create([
                'tipo_doc' => $request->tipo_doc,
                'nro_documento' => $request->num_doc,
                'nacionalidad' => $request->nacionalidad,
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'nro_licencia' => $request->nro_licencia,
                'categoria_licencia' => $request->licencia_categoria,
                'numUnidad' => $request->nro_unidad,
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
                'photo' => $request->photo,
                'user_register' => auth()->user()->id,
            ]);
            return response()->json([
                'success' => true,
                'message' => '¡El conductor ' . $driver->nombres . ' ha sido registrado con éxito!',
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
