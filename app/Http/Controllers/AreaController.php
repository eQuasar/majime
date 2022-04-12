<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Resources\AreaResource;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $area= Area::all();
        return AreaResource::collection($area);
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
        if($request->type == 'get')
        {
            $area = Area::where('city_id','=',$request->city_id)->get();
            return AreaResource::collection($area);
        }
        else
        {
            $request->validate([
                'name' => 'required',
                'area_code' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
                 
            ]);
            $area = new Area();
            $area->name = $request->name;
            $area->area_code = $request->area_code;
            $area->city_id = $request->city_id;
            $area->state_id = $request->state_id;
            $area->country_id = $request->country_id;
            
            $area->save();
            return response()->json(['error' => false,'data' => $area],200);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        //
        $request->validate([
            'name' => 'required',
            'area_code' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'country_id' => 'required',
        ]);
        $area = Area::find((int)$area->id);
        $area->name = $request->name;
        $area->area_code = $request->area_code;
        $area->city_id = $request->city_id;
        $area->state_id = $request->state_id;
        $area->country_id = $request->country_id;
        
        $area->save();
        return response()->json(['error' => false,'data' => $area],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        //
    }
}
