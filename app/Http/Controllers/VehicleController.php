<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleZone;
use Illuminate\Http\Request;
use App\Http\Resources\VehicleResource;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicle = Vehicle::with('groomer.user','zone.zone')->get();
        return VehicleResource::collection($vehicle);
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
                'vehicle_number' => 'required',
                'vehicle_type' => 'required',
                'zone' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
                'groomer_id' => 'required',
                 
            ]);
            
            
            $new_vehicle = new Vehicle();
            
            $new_vehicle->name = $request->name;
            $new_vehicle->vehicle_number = $request->vehicle_number;
            $new_vehicle->vehicle_type = $request->vehicle_type;
            // $new_vehicle->zone = $request->zone;
            $new_vehicle->city_id = $request->city_id;
            $new_vehicle->country_id = $request->country_id;
            $new_vehicle->state_id = $request->state_id;
            $new_vehicle->groomer_id = $request->groomer_id;
            
            $new_vehicle->save();
            $area = explode(',',$request->zone);
            if(count($area) >0)
            {
                foreach ($area as $key => $value) {
                    $new_area = new VehicleZone();
                    $new_area->vehicle_id = $new_vehicle->id;
                    $new_area->zone_id = (int)$value;
                    $new_area->save();
                }
            }
            return response()->json(['error' => false,'data' => $new_vehicle],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        //
        $request->validate([
                'name' => 'required|string|max:255',
                'vehicle_number' => 'required',
                'vehicle_type' => 'required',
                'zone' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
                'groomer_id' => 'required',
                 
            ]);
            
            $id = (int)$vehicle->id;
            $new_vehicle = Vehicle::find($id);
            
            $new_vehicle->name = $request->name;
            $new_vehicle->vehicle_number = $request->vehicle_number;
            $new_vehicle->vehicle_type = $request->vehicle_type;
            // $new_vehicle->zone = $request->zone;
            $new_vehicle->city_id = $request->city_id;
            $new_vehicle->country_id = $request->country_id;
            $new_vehicle->state_id = $request->state_id;
            $new_vehicle->groomer_id = $request->groomer_id;
            
            $new_vehicle->save();
            $area = explode(',',$request->zone);
            $delete = VehicleZone::where('vehicle_id', '=',$new_vehicle->id)->whereNotIn('id', $area)->delete();
            if(count($area) >0)
            {
                foreach ($area as $key => $value) {
                    $check = VehicleZone::where('vehicle_id','=',$new_vehicle->id)->where('zone_id','=',(int)$value)->get()->first();
                    if($check == null)
                    {
                        $new_area = new VehicleZone();
                        $new_area->vehicle_id = $new_vehicle->id;
                        $new_area->zone_id = (int)$value;
                        $new_area->save();
                    }
                    else
                    {
                        $check->vehicle_id = $new_vehicle->id;
                        $check->zone_id = (int)$value;
                        $check->save();
                    }
                    
                }
            }
            return response()->json(['error' => false,'data' => $new_vehicle],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        //
    }
}
