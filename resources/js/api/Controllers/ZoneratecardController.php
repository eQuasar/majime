<?php

namespace App\Http\Controllers;

use App\Models\zoneratecard;
use Illuminate\Http\Request;

class ZoneratecardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            // 'id' => 'required',
            'zone' => 'required',
            'vendor' => 'required',
            'fwd' => 'required',
            'dto' => 'required',
            'rto' => 'required',
         
         ]);
        
        
        $zonerate_data = new zoneratecard();
        
        $zonerate_data->zoneno= $request->zone;
        $zonerate_data->vid= $request->vendor;
        $zonerate_data->fwd= $request->fwd;
        $zonerate_data->dto= $request->dto;
        $zonerate_data->rto= $request->rto;
        $zonerate_data->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\zoneratecard  $zoneratecard
     * @return \Illuminate\Http\Response
     */
    public function show(zoneratecard $zoneratecard)
    {
        $data = zoneratecard::all();
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\zoneratecard  $zoneratecard
     * @return \Illuminate\Http\Response
     */
    public function edit(zoneratecard $zoneratecard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\zoneratecard  $zoneratecard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, zoneratecard $zoneratecard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\zoneratecard  $zoneratecard
     * @return \Illuminate\Http\Response
     */
    public function destroy(zoneratecard $zoneratecard)
    {
        //
    }
}
