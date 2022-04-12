<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use Illuminate\Http\Request;
use App\Http\Resources\BreedResource;


class BreedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $breed= Breed::all();
        return BreedResource::collection($breed);
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
                'cat_id' => 'required|gt:0',
                 
            ]);
            
            
            $new_breed = new Breed();
            
            $new_breed->name = $request->name;
            $new_breed->pet_cat_id = $request->cat_id;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function show(Breed $breed)
    {
        $breed= Breed::where('id',(int)$breed['id'])->orderBy('id','desc')->get();
        // dd($breed['id']);
        return BreedResource::collection($breed);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function edit(Breed $breed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Breed $breed)
    {
        //
        $request->validate([
                'name' => 'required|string|max:255',
                'cat_id' => 'required|gt:0',
                 
            ]);
            
            
            $new_breed = Breed::find((int)$breed->id);
            
            $new_breed->name = $request->name;
            $new_breed->pet_cat_id = $request->cat_id;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Breed $breed)
    {
        //
    }
}
