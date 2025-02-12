<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicios = [];
        $mechanics = User::where('status_mechanic', 1)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'mecanico');
            })->get();
        return view('service.index', compact('servicios', 'mechanics'));
    }
    public function generateCode()
    {
        $lastCodigo = Service::max('codigo') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
    public function verDetalles(Request $request)
    {
        try {
            $service = Service::findOrFail($request->serviceId);
            return response()->json([
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Servicio no encontrado'], 404);
        }
    }
    public function cambiarEstado(Request $request)
    {
        try {
            $service = Service::findOrFail($request->serviceId);
            $service->status_service = $request->estado;
            $service->user_update = auth()->user()->id;
            $service->detalle_servicio = $request->descripcion;
            $service->save();
            return response()->json(['message' => 'Estado actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Servicio no encontrado'], 404);
        }
    }
    public function filtroPorfecha(Request $request)
    {
        if (!$request->filled('fecha_desde') || !$request->filled('fecha_hasta')) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        $user = auth()->user();

        $servicios = Service::with('drive', 'car', 'registeredBy', 'user')
            ->whereDate('fecha_registro', '>=', $request->fecha_desde)
            ->whereDate('fecha_registro', '<=', $request->fecha_hasta);

        if ($request->filled('estado')) {
            $servicios->where('status_service', $request->estado);
        }

        if ($user->hasRole('administrador') || $user->hasRole('ventas')) {
            if ($request->filled('mechanic') && $request->mechanic !== 'todos') {
                $servicios->where('users_id', $request->mechanic);
            }
        } else {
            $servicios->where('users_id', $user->id);
        }

        return response()->json($servicios->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service.datos-servicio');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'car_id.required' => 'Se quiere que se seleccione un vehiculo.',
            'nro_motor.required' => 'La placa es obligatoria.',
            'drive_id.required' => 'se requiere DATOS del cliente',
            'mechanics_id.required' => 'se requiere DATOS del Mecanico',
            'detalle.required' => 'se requiere detalles del servicio a realizar',
        ];
        try {
            $request->validate([
                'car_id' => 'required|string',
                'nro_motor' => 'required|string',
                'drive_id' => 'required|string|unique:cars,placa',
                'mechanics_id' => 'required|string',
                'detalle' => 'required|string',
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
        try {
            $car = Service::create([
                'drives_id' => $request->drive_id,
                'cars_id' => $request->car_id,
                'descripcion' => $request->detalle,
                'user_register' => auth()->user()->id,
                'codigo' => $this->generateCode(),
                'users_id' => $request->mechanics_id
            ]);
            if ($car) {
                return response()->json([
                    'message' => '¡El Servicio a sido registrado',
                    'success' => true,
                ]);
            } else {
                return response()->json(['error' => 'No se pudo registrar el servicio']);
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
