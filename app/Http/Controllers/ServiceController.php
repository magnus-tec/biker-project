<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use App\Models\Service;
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
        $mechanics = Mechanic::where('status', 1)->get();
        return view('service.index', compact('servicios', 'mechanics'));
    }
    public function generateCode()
    {
        $lastCodigo = Service::max('codigo') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
    public function filtroPorfecha(Request $request)
    {
        if (!$request->has(['fecha_desde', 'fecha_hasta']) || empty($request->fecha_desde) || empty($request->fecha_hasta)) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        $user = auth()->user();
        $servicios = Service::with('drive', 'car', 'registeredBy', 'mechanic')
            ->whereDate('fecha_registro', '>=', $request->fecha_desde)
            ->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        if ($request->filled('estado')) {
            $servicios->where('status_service', $request->estado);
        }
        if ($user->hasRole('admin')) {
            if ($request->mechanic === 'todos' || empty($request->mechanic)) {
                return response()->json($servicios->get());
            } else {
                $servicios->where('user_register', $request->trabajador);
            }
        } else {
            $servicios->where('user_register', $user->id);
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
            'n_placa.required' => 'La placa es obligatoria.',
            'id_drive.required' => 'se requiere DATOS del cliente',
        ];
        try {
            $request->validate([
                'car_id' => 'required|string',
                'n_placa' => 'required|string',
                'id_drive' => 'required|string|unique:cars,placa',
            ], $messages);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 500);
        }
        try {
            $car = Service::create([
                'drives_id' => $request->id_drive,
                'cars_id' => $request->car_id,
                'descripcion' => $request->detalle,
                'user_register' => auth()->user()->id,
                'codigo' => $this->generateCode(),
                'mechanics_id' => $request->mechanics_id
            ]);
            return response()->json([
                'success' => true,
                'message' => '¡El Servicio a sido registrado',
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
