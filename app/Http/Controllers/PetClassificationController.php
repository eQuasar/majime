<?php

namespace App\Http\Controllers;

use App\Models\PetClassification;
use Illuminate\Http\Request;
use App\Http\Resources\PetClassificationResource;

class PetClassificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $pet_classification= PetClassification::all();
        return PetClassificationResource::collection($pet_classification);
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
        $request->validate([
                'pet_class' => 'required',
                'age' => 'required',
                'breed_id' => 'required',
                 
            ]);
            
            
            $pet_classification = new PetClassification();
            
            $pet_classification->pet_class = $request->pet_class;
            $pet_classification->age = $request->age;
            $pet_classification->breed_id = $request->breed_id;
            
            $pet_classification->save();
            return response()->json(['error' => false,'data' => $pet_classification],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PetClassification  $petClassification
     * @return \Illuminate\Http\Response
     */
    public function show(PetClassification $petClassification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PetClassification  $petClassification
     * @return \Illuminate\Http\Response
     */
    public function edit(PetClassification $petClassification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PetClassification  $petClassification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PetClassification $petclassification)
    {
        $request->validate([
                'pet_class' => 'required',
                'age' => 'required',
                'breed_id' => 'required',
                 
            ]);
            
            
            $new_breed = PetClassification::find((int)$petclassification->id);
            
            $new_breed->pet_class = $request->pet_class;
            $new_breed->age = $request->age;
            $new_breed->breed_id = $request->breed_id;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PetClassification  $petClassification
     * @return \Illuminate\Http\Response
     */
    public function destroy(PetClassification $petClassification)
    {
        //
    }
}
