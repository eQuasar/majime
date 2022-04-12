<?php

namespace App\Http\Controllers;

use App\Models\PetCategory;
use Illuminate\Http\Request;
use App\Http\Resources\PetCategoriesResource;

class PetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    $petCategory = PetCategory::all();
        return PetCategoriesResource::collection($petCategory);
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
            
            
            $pet_category = new PetCategory();
            
            $pet_category->name = $request->name;
            
            $pet_category->save();
            return response()->json(['error' => false,'data' => $pet_category],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PetCategory  $petCategory
     * @return \Illuminate\Http\Response
     */
    public function show(PetCategory $petCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PetCategory  $petCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(PetCategory $petCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PetCategory  $petCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PetCategory $petcategory)
    {
        $id = (int)$petcategory->id;
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $new_client = PetCategory::find((int)$id);
        $new_client->name = $request->name;
        $new_client->save();
        return response()->json(['error' => false,'data' => $new_client],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PetCategory  $petCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(PetCategory $petCategory)
    {
        //
    }
}
