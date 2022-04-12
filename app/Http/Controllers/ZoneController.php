<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\ZoneArea;
use Illuminate\Http\Request;
use App\Http\Resources\ZoneResource;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $zone= Zone::with('areas_tab.area')->get();
        return ZoneResource::collection($zone);
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
                'areas' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
                 
            ]);
            
            
            $new_zone = new Zone();
            
            $new_zone->name = $request->name;
            // $new_zone->areas = $request->areas;
            $new_zone->city_id = $request->city_id;
            $new_zone->state_id = $request->state_id;
            $new_zone->country_id = $request->country_id;
            
            $new_zone->save();
            $area = explode(',',$request->areas);
            if(count($area) >0)
            {
                foreach ($area as $key => $value) {
                    $new_area = new ZoneArea();
                    $new_area->zone_id = $new_zone->id;
                    $new_area->area_id= (int)$value;
                    $new_area->save();
                }
            }
            return response()->json(['error' => false,'data' => $new_zone],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function show(Zone $zone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function edit(Zone $zone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Zone $zone)
    {
        //
        $request->validate([
                'name' => 'required|string|max:255',
                'areas' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
                 
            ]);
            
            $id = (int)$zone->id;
            $new_zone = Zone::find($id);
            
            $new_zone->name = $request->name;
            // $new_zone->areas = $request->areas;
            $new_zone->city_id = $request->city_id;
            $new_zone->state_id = $request->state_id;
            $new_zone->country_id = $request->country_id;
            
            $new_zone->save();
            $area = explode(',',$request->areas);
            $delete = ZoneArea::where('zone_id', '=',$new_zone->id)->whereNotIn('id', $area)->delete();
            if(count($area) >0)
            {
                foreach ($area as $key => $value) {
                    $check = ZoneArea::where('zone_id','=',$new_zone->id)->where('area_id','=',(int)$value)->get()->first();
                    if($check == null)
                    {
                        $new_area = new ZoneArea();
                        $new_area->zone_id = $new_zone->id;
                        $new_area->area_id= (int)$value;
                        $new_area->save();
                    }
                    else
                    {
                        $check->area_id = (int)$value;
                        $check->save();
                    }
                    
                }
            }
            return response()->json(['error' => false,'data' => $new_zone],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Zone $zone)
    {
        //
    }
}
