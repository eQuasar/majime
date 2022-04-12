<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $service= Service::all();
        return ServiceResource::collection($service);
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
            
            
            $new_service = new Service();
            
            $new_service->name = $request->name;
            $new_service->type = $request->service_type;
            
            $new_service->save();
            return response()->json(['error' => false,'data' => $new_service],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
                'name' => 'required|string|max:255',
                 
            ]);
            
            
            $new_breed = Service::find((int)$service->id);
            
            $new_breed->name = $request->name;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //
    }

    public function getmyservices(Request $request)
    {
        // $mainservice[] = $request->main_service_id;
        $addservice = explode(",", $request->additional_service_ids);
        // $myarr = array_merge($mainservice, $addservice);
        // var_dump($myarr); 
        if ($request->main_service_id != '') {
            $service = Service::whereIn('id', $addservice)->get();
            return ServiceResource::collection($service);
        }else{
            return '';
        }
    }
}
