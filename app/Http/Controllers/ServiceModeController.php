<?php

namespace App\Http\Controllers;

use App\Models\ServiceMode;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceModeResource;

class ServiceModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $service_mode= ServiceMode::all();
        return ServiceModeResource::collection($service_mode);
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
                 
            ]);
            
            
            $new_service = new ServiceMode();
            
            $new_service->name = $request->name;
            
            $new_service->save();
            return response()->json(['error' => false,'data' => $new_service],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceMode  $serviceMode
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceMode $serviceMode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceMode  $serviceMode
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceMode $serviceMode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceMode  $serviceMode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceMode $servicemode)
    {
        $request->validate([
                'name' => 'required|string|max:255',
                 
            ]);
            
            
            $new_breed = ServiceMode::find((int)$servicemode->id);
            
            $new_breed->name = $request->name;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceMode  $serviceMode
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceMode $serviceMode)
    {
        //
    }
}
