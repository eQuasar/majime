<?php

namespace App\Http\Controllers;

use App\Models\vendor_ratecard;
use Illuminate\Http\Request;

class VendorRatecardController extends Controller
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
        // dd($request);die();
        $request->validate([
            // 'id' => 'required',
            'cod' => 'required',
            'codper' => 'required',
            'above' => 'required',
            'sms' => 'required',
            
         ]);
        
        
        $vendorrate_data = new vendor_ratecard();
       
        $vendorrate_data->cod= $request->cod;
        $vendorrate_data->codper= $request->codper;
        $vendorrate_data->after500gm= $request->above;
        $vendorrate_data->sms_charges= $request->sms;

        $vendorrate_data->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\vendor_ratecard  $vendor_ratecard
     * @return \Illuminate\Http\Response
     */
    public function show(vendor_ratecard $vendor_ratecard)
    {
        $data = vendor_ratecard::all();
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\vendor_ratecard  $vendor_ratecard
     * @return \Illuminate\Http\Response
     */
    public function edit(vendor_ratecard $vendor_ratecard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\vendor_ratecard  $vendor_ratecard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, vendor_ratecard $vendor_ratecard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vendor_ratecard  $vendor_ratecard
     * @return \Illuminate\Http\Response
     */
    public function destroy(vendor_ratecard $vendor_ratecard)
    {
        //
    }
}
