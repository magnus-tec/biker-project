<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Drive;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Car::with('driver')->where('status', 1)->get();
        return view('car.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('car.datos-vehiculo');
    }
    public function searchBuscarVehiculo(Request $request)
    {
        try {
            $nro_motor = $request->nro_motor;
            $driver = Drive::where('nro_motor', $nro_motor)->select('id', 'nombres', 'apellido_paterno', 'apellido_materno')->first();
            if ($driver) {
                $car = Car::where('drives_id', $driver->id)->where('status', 1)->get();
                if ($car) {
                    return response()->json([
                        'car' => $car,
                        'drive' => $driver
                    ]);
                } else {
                    return response()->json(['error' => 'No esta registrado el cliente con el numero de motor ingresado.']);
                }
            } else {
                return response()->json([
                    'error' => 'No esta registrado el cliente con el numero de motor ingresado.'
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
        $lastCodigo = Car::max('codigo') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
    public  function searchBuscarDriver(Request $request)
    {
        try {
            $nro_motor = $request->nro_motor;
            $drive = Drive::where('nro_motor', $nro_motor)->select('id', 'nombres', 'apellido_paterno', 'apellido_materno')->first();
            if ($drive) {
                return response()->json(['drive' => $drive]);
            } else {
                return response()->json(['error' => 'El número de motor no se encuentra registrado.']);
            }
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Mensajes personalizados de validación
        $messages = [
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'n_placa.unique' => 'La placa ya está registrada.',
            'nro_chasis.required' => 'El numero de chasis es obligatorio.',
            // 'nro_motor.unique' => 'El numero de motor ya esta registrado.',
            'drive_id.required' => 'El numero de documento es obligatorio.',

        ];
        try {
            $request->validate([
                'marca' => 'required|string',
                'modelo' => 'required|string',
                'n_placa' => 'nullable|string|unique:cars,placa',
                // 'nro_motor' => 'unique:cars,nro_motor',
                'drive_id' => 'required|string',
                'nro_chasis' => 'required|string'
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
        try {
            $car = Car::create([
                'codigo' => $this->generateCode(),
                'placa' => $request->n_placa,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'anio' => $request->anio,
                'condicion' => $request->tipo_condicion,
                'nro_chasis' => $request->nro_chasis,
                'fecha_soat' => $request->fecha_soat,
                'fecha_seguro' => $request->fecha_seguro,
                'color' => $request->color,
                'user_register' => auth()->user()->id,
                'drives_id' => $request->drive_id
            ]);
            if ($car->save()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡El vehiculo con la placa ' . $car->placa . ' ha sido registrado con éxito!',
                ]);
            } else {
                return response()->json(['error' => 'No se pudo registrar el vehiculo']);
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
