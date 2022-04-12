<?php

namespace App\Http\Controllers;

use App\Models\ClassificationMonth;
use Illuminate\Http\Request;
use App\Http\Resources\ClassificationMonthResource;

class ClassificationMonthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $classification_month= ClassificationMonth::all();
        return ClassificationMonthResource::collection($classification_month);
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
                'name' => 'required|string|max:255',
                'start_month' => 'required',
                'end_month' => 'required',
                 
            ]);
            
            
            $classification_month = new ClassificationMonth();
            
            $classification_month->name = $request->name;
            $classification_month->start_month = $request->start_month;
            $classification_month->end_month = $request->end_month;
            
            $classification_month->save();
            return response()->json(['error' => false,'data' => $classification_month],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassificationMonth  $classificationMonth
     * @return \Illuminate\Http\Response
     */
    public function show(ClassificationMonth $classificationMonth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClassificationMonth  $classificationMonth
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassificationMonth $classificationMonth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassificationMonth  $classificationMonth
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassificationMonth $classificationmonth)
    {
        $request->validate([
                'name' => 'required|string|max:255',
                'start_month' => 'required',
                'end_month' => 'required',
                 
            ]);
            
            
            $new_breed = ClassificationMonth::find((int)$classificationmonth->id);
            
            $new_breed->name = $request->name;
            $new_breed->start_month = $request->start_month;
            $new_breed->end_month = $request->end_month;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassificationMonth  $classificationMonth
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassificationMonth $classificationMonth)
    {
        //
    }
}
