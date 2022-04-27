<?php

namespace App\Http\Controllers;

use App\Models\Billings;
use Illuminate\Http\Request;
use App\Http\Resources\BillingResource;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $billing= Billings::all();
        // $billing= billing::where('state_id','=',32)->get();
        return BillingResource::collection($billing);
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
        // if($request->type == 'get')
        // {
        //     $city = City::where('state_id','=',$request->state_id)->get();
        //     return CityResource::collection($city);
        // }
        // else
        // {

        //     $request->validate([
        //         'name' => 'required',
        //         'state_id' => 'required',  
        //     ]);    
        //     $city = new City();
            
        //     $city->name = $request->name;
        //     $city->state_id = $request->state_id;
            
        //     $city->save();
        //     return response()->json(['error' => false,'data' => $city],200);
        // }    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        //
    }
}
