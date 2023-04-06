<?php

namespace App\Http\Controllers;

use App\Models\zonedetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ZonedetailController extends Controller
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
        //use for validation check zoneno,pincode(required)
        $request->validate([
            // 'id' => 'required',
            'zoneno' => 'required',
            'pincode' => 'required',
         
         ]);
        
        $zone_data = new zonedetail();
        $zone_data->zoneno= $request->zoneno;
        $zone_data->pincode= $request->pincode;
        $zone_data->save();//save zone_data into database table zonedetails
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\zonedetail  $zonedetail
     * @return \Illuminate\Http\Response
     */
    //listing  from table zonedetail get zoneno
    public function show(zonedetail $zonedetail)
    {
        $data=DB::table('zonedetails')->distinct()->select('zonedetails.zoneno')->get();
        // $order = DB::table("orders")->distinct()->select('orders.status')->where('orders.vid', $request->vid)->get();
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\zonedetail  $zonedetail
     * @return \Illuminate\Http\Response
     */
    public function edit(zonedetail $zonedetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\zonedetail  $zonedetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, zonedetail $zonedetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\zonedetail  $zonedetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(zonedetail $zonedetail)
    {
        //
    }
}
