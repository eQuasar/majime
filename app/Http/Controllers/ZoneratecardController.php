<?php

namespace App\Http\Controllers;

use App\Models\zoneratecard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vendors;

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
    // according to  Zone No  and status of the product save chrarges
    public function store(Request $request)
    {
        //if condition check the exist of zoneno and vid from table zoneratecards use(vendor,zone)
        if(DB::table('zoneratecards')
        ->where('vid',$request->vendor)
        ->where('zoneno',$request->zone)->exists())
        {
            //zoneno exist
            //use for validation requried
        $request->validate([
            'zone' => 'required',
            'fwd' => 'required',
            'dto' => 'required',
            'rto' => 'required',
         ]);
         $zonerate_data = zoneratecard::where('zoneratecards.vid',$request->vendor)->where('zoneno',$request->zone)->first();//method will help us to return the first record found from the database
        $zonerate_data->zoneno= $request->zone;
        $zonerate_data->courier= $request->courier;
        $zonerate_data->vid= $request->vendor;
        $zonerate_data->fwd= $request->fwd;
        $zonerate_data->dto= $request->dto;
        $zonerate_data->rto= $request->rto;
        $zonerate_data->save();//save zonerate_data(database)
    }
    else
    {
        //if zoenno and vid doesnot exist then save with new entry 
        $request->validate([
            // 'id' => 'required',
            'zone' => 'required',
            // 'courier' => 'required',

            // 'vendor' => 'required',
            'fwd' => 'required',
            'dto' => 'required',
            'rto' => 'required',
         
         ]);
        $zonerate_data = new zoneratecard();
        $zonerate_data->zoneno= $request->zone;
        $zonerate_data->courier= $request->courier;
        $zonerate_data->vid= $request->vendor;
        $zonerate_data->fwd= $request->fwd;
        $zonerate_data->dto= $request->dto;
        $zonerate_data->rto= $request->rto;
        $zonerate_data->save();
    }
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\zoneratecard  $zoneratecard
     * @return \Illuminate\Http\Response
     */
    //show all record of the zone rate card into table
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
    //api use get show all zone no for using dropdown.
    public function showzonedetail (Request  $request)
    {
     
        $awb_record =DB::table("zoneratecards")
        ->where('zoneratecards.vid',$request->vendor)
        ->where('zoneno',$request->selectzone)->get()->toArray();
   
            if(!empty($awb_record)){
                return $awb_record;
            }else{
               return response()->json(['error' => false, 'msg' => "No Data found.","ErrorCode" => "000"],200);
            }

     
    }
}
