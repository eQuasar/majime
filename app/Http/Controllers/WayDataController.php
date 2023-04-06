<?php

namespace App\Http\Controllers;

use App\Models\WayData;
use Illuminate\Http\Request;
use DB;



class WayDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //listing data from table waydata
    public function index()
    {
        $data = WayData::all();
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //save data
    public function store(Request $request)
    {
        // use for validation check requried
        $request->validate([
                'user_id' => 'required',
                'vid' => 'required',
                'city' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'pin' => 'required',
                'country' => 'required',
                'phone' => 'required',
                'add' => 'required',
                'token' => 'required',
                'order_prefix' => 'required',
            ]);

        // dd($request); die;
            
            
            $wb_data = new WayData();
            
            $wb_data->user_id = $request->user_id;
            $wb_data->vid = $request->vid;
            $wb_data->city = $request->city;
            $wb_data->name = $request->name;
            $wb_data->pin = $request->pin;
            $wb_data->country = $request->country;
            $wb_data->phone = $request->phone;
            $wb_data->gateway = $request->gateway;
            $wb_data->add = $request->add;
            $wb_data->token = $request->token;
            $wb_data->order_prefix = $request->order_prefix;
            
            $wb_data->save();//save into database table WayData
            return response()->json(['error' => false,'data' => $wb_data],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WayData  $wayData
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // dd($request->vid);die();
        // $awb_record =DB::table("way_data")->where('way_data.vid',$request->vid)->get()->toArray();
        //     if(!empty($awb_record)){
        //         return $awb_record;
        //     }else{
        //        return response()->json(['error' => false, 'msg' => "No Data found.","ErrorCode" => "000"],200);
        //     }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WayData  $wayData
     * @return \Illuminate\Http\Response
     */
    public function edit(WayData $wayData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WayData  $wayData
     * @return \Illuminate\Http\Response
     */
    // function update from table way_data
    public function update(Request $request, WayData $wayData)
    {
        //if condition check from table waydata api use(vendor) exists(yes/no)
        if(DB::table('way_data')->where('vid',$request->vendor)->exists())
        {
            //if exist
        $request->validate([
                'user_id' => 'required',
                'vid' => 'required',
                'city' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'pin' => 'required',
                'country' => 'required',
                'phone' => 'required',
                'add' => 'required',
                'token' => 'required',
                'order_prefix' => 'required',
            ]);
            
            
            $wb_data = WayData::find((int)$request->id);
            $wb_data->user_id = $request->user_id;
            $wb_data->vid = $request->vid;
            $wb_data->city = $request->city;
            $wb_data->name = $request->name;
            $wb_data->pin = $request->pin;
            $wb_data->country = $request->country;
            $wb_data->phone = $request->phone;
            $wb_data->add = $request->add;
            $wb_data->token = $request->token;
            $wb_data->order_prefix = $request->order_prefix;
            
            $wb_data->save();
            return response()->json(['error' => false,'data' => $wb_data],200);
        }
    }
    //api use updatedata
    public function updatedata(Request $request)
    {
         //if condition check from table waydata vid exists(yes/no)
        if(DB::table('way_data')->where('vid',$request->vid)->exists())
        {
        $request->validate([
                'user_id' => 'required',
                'vid' => 'required',
                'city' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'pin' => 'required',
                'country' => 'required',
                'phone' => 'required',
                'add' => 'required',
                'token' => 'required',
                'order_prefix' => 'required',
            ]);
            
            
            $wb_data = WayData::find((int)$request->id);
            $wb_data->user_id = $request->user_id;
            $wb_data->vid = $request->vid;
            $wb_data->city = $request->city;
            $wb_data->name = $request->name;
            $wb_data->pin = $request->pin;
            $wb_data->country = $request->country;
            $wb_data->phone = $request->phone;
            $wb_data->add = $request->add;
            $wb_data->token = $request->token;
            $wb_data->order_prefix = $request->order_prefix;
            
            $wb_data->save();
            return response()->json(['error' => false,'data' => $wb_data],200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WayData  $wayData
     * @return \Illuminate\Http\Response
     */
    public function destroy(WayData $wayData)
    {
        //
    }
    //api get awbl location form table way_data
    public function getAWBLocation(Request $request)
    {
        $awb_record =DB::table("way_data")
            ->where('vid',$request->vid)
            // ->where('user_id',$request->user_id)
            ->get()->toArray();

        if(!empty($awb_record)){
            return $awb_record;
        }else{
           return response()->json(['error' => false, 'msg' => "No Data found.","ErrorCode" => "000"],200);
        }
    }
}
