<?php

namespace App\Http\Controllers;

use App\Models\PetAggresiveLevel;
use Illuminate\Http\Request;
use App\Http\Resources\PetAggresiveLevelResource;

class PetAggresiveLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $pet_aggresive = PetAggresiveLevel::all();
        return PetAggresiveLevelResource::collection($pet_aggresive);
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
            
        $pet_category = new PetAggresiveLevel();
        $pet_category->name = $request->name;
        
        $pet_category->save();
        return response()->json(['error' => false,'data' => $pet_category],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PetAggresiveLevel  $petAggresiveLevel
     * @return \Illuminate\Http\Response
     */
    public function show(PetAggresiveLevel $petAggresiveLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PetAggresiveLevel  $petAggresiveLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(PetAggresiveLevel $petAggresiveLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PetAggresiveLevel  $petAggresiveLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PetAggresiveLevel $petaggresive)
    {
        $id = (int)$petaggresive->id;
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $new_client = PetAggresiveLevel::find((int)$id);
        $new_client->name = $request->name;
        $new_client->save();
        return response()->json(['error' => false,'data' => $new_client],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PetAggresiveLevel  $petAggresiveLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(PetAggresiveLevel $petAggresiveLevel)
    {
        //
    }
}
