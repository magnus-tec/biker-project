<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function getProvinces($regionId)
    {
        $provinces = Province::where('regions_id', $regionId)->get();
        return response()->json(['provinces' => $provinces]);
    }
    public function getDistricts($provinceId)
    {
        $districts = District::where('provinces_id', $provinceId)->get();
        return response()->json(['districts' => $districts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
