<?php

namespace App\Http\Controllers;

use App\Models\ServiceCost;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceCostResource;
use App\Http\Resources\ServiceResource;

class ServiceCostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $servicecost= ServiceCost::all();
        return ServiceCostResource::collection($servicecost);
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
                'service_id' => 'required',
                'service_mode' => 'required',
                'slots' => 'required',
                 
            ]);
            $new_service = new ServiceCost();
            $new_service->cost = $request->cost;
            $new_service->slots = $request->slots;
            $new_service->service_id = $request->service_id;
            $new_service->service_mode = $request->service_mode;
            
            $new_service->save();
            return response()->json(['error' => false,'data' => $new_service],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceCost  $serviceCost
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceCost $serviceCost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceCost  $serviceCost
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceCost $serviceCost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceCost  $serviceCost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceCost $servicecost)
    {
        $request->validate([
                'cost' => 'required|string|max:255',
                'service_id' => 'required',
                'service_mode' => 'required',
                'slots' => 'required',
                 
            ]);
            
            
            $new_breed = ServiceCost::find((int)$servicecost->id);
            
            $new_breed->cost = $request->cost;
            $new_breed->service_id = $request->service_id;
            $new_breed->service_mode = $request->service_mode;
            $new_breed->slots = $request->slots;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceCost  $serviceCost
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceCost $serviceCost)
    {
        //
    }
    public function getcost(Request $request)
    {
            
            $cost = ServiceCost::where('service_mode','=',(int)$request->vehicle)->where('service_id','=',(int)$request->service_id)->get()->first();
            // dd($cost);
            $array = explode(',', $request->additional_service);
            // dd($array);
            $additional_cost = ServiceCost::where('service_mode','=',(int)$request->vehicle)->whereIn('service_id',$array)->sum('cost');

            $bath_service = ServiceCost::where('service_mode','=',(int)$request->vehicle)
            ->join('services', 'services.id','=', 'service_costs.service_id')
            ->where('service_id','=',(int)$request->service_id)->get()->toArray();

            $additional_service_main = ServiceCost::where('service_mode','=',(int)$request->vehicle)
            ->join('services', 'services.id','=', 'service_costs.service_id')
            ->whereIn('service_id',$array)->get()->toArray();

            $myservice = array_merge($bath_service,$additional_service_main);
            // dd($myservice);
            
            if($cost != null)
            {
                $total = $cost->cost+$additional_cost;
            }
            else
            {
                $total = $additional_cost;
            }
            
            // $new_service->save();
            return response()->json(['error' => false,'data' => $total,'myservice' => $myservice],200);
    }
}
