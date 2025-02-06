<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class MechanicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mechanics = User::whereHas('roles', function ($query) {
            $query->where('name', 'Mecánico');
        })->get();
        return view('mechanic.index', compact('mechanics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    // public function MecanicosDisponibles()
    // {
    //     $mechanics = User::where('status_mechanic', 1)
    //         ->whereHas('roles', function ($query) {
    //             $query->where('name', 'Mecánico');
    //         })->get();

    //     return response()->json($mechanics);
    // }
    public function MecanicosDisponibles()
    {
        $mechanics = User::where('status_mechanic', 1)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Mecánico');
            })
            ->whereHas('services', function ($query) {
                $query->where('status_service', '!=', 2); // Filtrar donde status_service NO sea 2
            })
            ->get();

        return response()->json($mechanics);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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
