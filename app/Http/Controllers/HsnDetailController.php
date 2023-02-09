<?php

namespace App\Http\Controllers;

use App\Models\Hsn_detail;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Controllers\HsnDetailController;
use Illuminate\Support\Facades\DB;

class HsnDetailController extends Controller
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

        
        $hsn_detail[]=[     
            'hsn_code'=> $request->hsn,
            'slab_1'=>$request->slab1,
            'slab_2'=>$request->slab2,
            'slab_amount'=>$request->slab_amount,
            'description'=>$request->description,
       
        ];      
        Hsn_detail::insert($hsn_detail);
        // return response()->json(['error' => false,'data' => $hsn_detail],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hsn_detail  $hsn_detail
     * @return \Illuminate\Http\Response
     */
    public function show(Hsn_detail $hsn_detail)
    {
        $hsn_data=DB::table('hsn_details')->get();
        return response()->json(['error' => false,'data' => $hsn_data],200);
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hsn_detail  $hsn_detail
     * @return \Illuminate\Http\Response
     */
    public function edit(Hsn_detail $hsn_detail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hsn_detail  $hsn_detail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hsn_detail $hsn_detail)
    {
        $product_id =$request->product_id;
        $hsn=$request->hsn;
        $weight=$request->weight;
        DB::table('products')->where('product_id',$product_id)->update(['hsn_code' => $hsn,'weight'=>$weight]);
        return response()->json(['error' => false, 'msg' => "Answer update successfully", "ErrorCode" => "000"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hsn_detail  $hsn_detail
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hsn_detail $hsn_detail)
    {
        //
    }
    public function getProduct_data(Request $request)
    {
        $product_id=$request->product_id;
        $vid=$request->vid;
        $data=DB::table('products')->where('product_id',$product_id)->where('vid',$vid)->get();
        return $data;
    }
}
