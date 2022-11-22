<?php

namespace App\Http\Controllers;

use App\Models\vendor_ratecard;
use Illuminate\Http\Request;
use DB;

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
        
        $awb_record =DB::table("vendor_ratecards")->where('vendor_ratecards.vid',$request->vendor)->get()->toArray();
        $vendor_data=vendor_ratecard::all();
        if(DB::table('vendor_ratecards')->where('vid',$request->vendor)->exists())
        {
            $request->validate([
                'cod' => 'required',
                'codper' => 'required',
                'above' => 'required',
                'vendor' => 'required',
                'sms' => 'required',
                'courier'=>'required',
                'mjm_charges' => 'required',
            ]);
            
            $wb_data = vendor_ratecard::find((int)$request->vid);

            $vendorrate_data = new vendor_ratecard();
            $vendorrate_data->cod= $request->cod;
            $vendorrate_data->codper= $request->codper;
            $vendorrate_data->courier= $request->courier;
            $vendorrate_data->after500gm= $request->above;
            $vendorrate_data->vid= $request->vendor;
            $vendorrate_data->sms_charges= $request->sms;
            $vendorrate_data->majime_charges= $request->mjm_charges;
    
            $vendorrate_data->save();
            // return response()->json(['error' => false,'data' => $wb_data],200);

        }
        else{
            $request->validate([
                // 'id' => 'required',
                'cod' => 'required',
                'codper' => 'required',
                'above' => 'required',
                'vendor' => 'required',
                'sms' => 'required',
                'courier'=>'required',
                'mjm_charges' => 'required',
             ]);
            $vendorrate_data = new vendor_ratecard();
            $vendorrate_data->cod= $request->cod;
            $vendorrate_data->codper= $request->codper;
            $vendorrate_data->courier= $request->courier;
            $vendorrate_data->after500gm= $request->above;
            $vendorrate_data->vid= $request->vendor;
            $vendorrate_data->sms_charges= $request->sms;
            $vendorrate_data->majime_charges= $request->mjm_charges;
    
            $vendorrate_data->save();
        }

        
    
        
  
       
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
    public function getvedordata(Request $request)
    {
        
         $awb_record =DB::table("vendor_ratecards")->where('vendor_ratecards.vid',$request->vid)->get()->toArray();
         
            if(!empty($awb_record)){
                return $awb_record;
            }else{
               return response()->json(['error' => false, 'msg' => "No Data found.","ErrorCode" => "000"],200);
            }
    }
    
}
