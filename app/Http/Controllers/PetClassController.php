<?php

namespace App\Http\Controllers;

use App\Models\PetClass;
use Illuminate\Http\Request;
use App\Http\Resources\PetClassResource;

class PetClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $pet_class= PetClass::all();
        return PetClassResource::collection($pet_class);
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
                'name' => 'required|string|max:255'
                 
            ]);
            
            
            $pet_class = new PetClass();
            
            $pet_class->name = $request->name;
            
            $pet_class->save();
            return response()->json(['error' => false,'data' => $pet_class],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PetClass  $petClass
     * @return \Illuminate\Http\Response
     */
    public function show(PetClass $petClass)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PetClass  $petClass
     * @return \Illuminate\Http\Response
     */
    public function edit(PetClass $petClass)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PetClass  $petClass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PetClass $petclass)
    {
        $id = (int)$petclass->id;
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $new_client = PetClass::find((int)$id);
        $new_client->name = $request->name;
        $new_client->save();
        return response()->json(['error' => false,'data' => $new_client],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PetClass  $petClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(PetClass $petClass)
    {
        //
    }
}
