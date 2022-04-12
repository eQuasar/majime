<?php

namespace App\Http\Controllers;

use App\Models\ClassificationCharge;
use Illuminate\Http\Request;
use App\Http\Resources\ClassificationChargeResource;

class ClassificationChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $classification_charge= ClassificationCharge::all();
        return ClassificationChargeResource::collection($classification_charge);
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
                'cost' => 'required|string|max:255',
                'class_id' => 'required',
                'service_id' => 'required',
                'slots' => 'required',
                 
            ]);
            
            
            $classification_charge = new ClassificationCharge();
            
            $classification_charge->cost = $request->cost;
            $classification_charge->class_id = $request->class_id;
            $classification_charge->service_id = $request->service_id;
            $classification_charge->slots = $request->slots;
            
            $classification_charge->save();
            return response()->json(['error' => false,'data' => $classification_charge],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassificationCharge  $classificationCharge
     * @return \Illuminate\Http\Response
     */
    public function show(ClassificationCharge $classificationCharge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClassificationCharge  $classificationCharge
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassificationCharge $classificationCharge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassificationCharge  $classificationCharge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassificationCharge $classificationcharge)
    {
        $request->validate([
                'cost' => 'required|string|max:255',
                'class_id' => 'required',
                'service_id' => 'required',
                'slots' => 'required',
                 
            ]);
            
            
            $new_breed = ClassificationCharge::find((int)$classificationcharge->id);
            
            $new_breed->cost = $request->cost;
            $new_breed->class_id = $request->class_id;
            $new_breed->service_id = $request->service_id;
            $new_breed->slots = $request->slots;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassificationCharge  $classificationCharge
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassificationCharge $classificationCharge)
    {
        //
    }
}
