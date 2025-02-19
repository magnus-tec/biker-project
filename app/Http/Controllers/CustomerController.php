<?php

namespace App\Http\Controllers;

use App\Models\Drive;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drives = Drive::where('status', 1)->get();
        return view('driver.index', compact('drives'));
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
        $messages = [
            'num_doc.unique' => 'El número de documento ya está registrado.',
            'nro_motor.unique' => 'El número de motor ya está registrado.',
            'nro_licencia.unique' => 'La licencia ya está registrada.',
        ];
        try {
            $request->validate([
                'nro_motor' => 'unique:drives,nro_motor',
                'num_doc' => 'nullable|unique:drives,nro_documento',
                'nro_licencia' => 'nullable|unique:drives,nro_licencia',
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        try {
            $driver = Drive::create([
                'codigo' => $this->generateCode(),
                'tipo_doc' => $request->tipo_doc,
                'nro_documento' => $request->num_doc,
                'nacionalidad' => $request->nacionalidad,
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'nro_licencia' => $request->nro_licencia,
                'categoria_licencia' => $request->licencia_categoria,
                'nro_motor' => $request->nro_motor,
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
            if ($driver) {
                return response()->json([
                    'success' => true,
                    'message' => '¡El conductor ' . $driver->nombres . ' ha sido registrado con éxito!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el conductor',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function generateCode()
    {
        $lastCodigo = Drive::max('codigo') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
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
        $driver = Drive::find($id);
        if (!$driver) {
            return redirect()->route('driver.index');
        }
        return view('driver.edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $messages = [
            'nro_documento.unique' => 'El número de documento ya está registrado.',
            'nro_motor.unique' => 'El número de motor ya está registrado.',
            'nro_licencia.unique' => 'La licencia ya está registrada.',
        ];
        try {
            $request->validate([
                'nro_documento' => [
                    'nullable',
                    Rule::unique('drives', 'nro_documento')->ignore($id),
                ],
                'nro_motor' => [
                    'nullable',
                    Rule::unique('drives', 'nro_motor')->ignore($id),
                ],
                'nro_licencia' => [
                    'nullable',
                    Rule::unique('drives', 'nro_licencia')->ignore($id),
                ],
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        $driver = Drive::find($id);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Conductor no encontrado'
            ], 404);
        }
        try {
            $driver->update($request->except(['_method', '_token']));

            return response()->json([
                'success' => true,
                'message' => '¡El conductor ' . $driver->nombres . ' ha sido actualizado con éxito!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el conductor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
