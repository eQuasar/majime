<?php

namespace App\Http\Controllers;

use App\Models\PetCoatLevel;
use Illuminate\Http\Request;
use App\Http\Resources\PetCoatLevelResource;

class PetCoatLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $petcoat_level = PetCoatLevel::all();
        return PetCoatLevelResource::collection($petcoat_level);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
             
        ]);
            
        $pet_category = new PetCoatLevel();
        $pet_category->name = $request->name;
        
        $pet_category->save();
        return response()->json(['error' => false,'data' => $pet_category],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PetCoatLevel  $petcoat
     * @return \Illuminate\Http\Response
     */
    public function show(PetCoatLevel $petcoat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PetCoatLevel  $petcoat
     * @return \Illuminate\Http\Response
     */
    public function edit(PetCoatLevel $petcoat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PetCoatLevel  $petcoat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PetCoatLevel $petcoat)
    {
        // dd($petcoat->id);
        $id = (int)$petcoat->id;
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $new_client = PetCoatLevel::find((int)$id);
        $new_client->name = $request->name;
        $new_client->save();
        return response()->json(['error' => false,'data' => $new_client],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PetCoatLevel  $petcoat
     * @return \Illuminate\Http\Response
     */
    public function destroy(PetCoatLevel $petcoat)
    {
        //
    }
}
