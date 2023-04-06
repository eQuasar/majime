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
    //  api use save data store function from table hsn_detail
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
    // api use for listing from table hsn_details get all hsn details
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
    // api use according to products,(table)product_id update  hsn_code,weight,cost
    public function update(Request $request, Hsn_detail $hsn_detail)
    {
        $product_id =$request->product_id;
        $hsn=$request->hsn;
        $weight=$request->weight;
        $price=$request->cost;
        DB::table('products')->where('product_id',$product_id)->update(['hsn_code' => $hsn,'weight'=>$weight,'cost'=>$price]);
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
    //api use according to products table get product data
    public function getProduct_data(Request $request)
    {
        $product_id=$request->product_id;
        $vid=$request->vid;
        $data=DB::table('products')->where('product_id',$product_id)->where('vid',$vid)->get();
        return $data;
    }
    //api use  to table orders base pa hencode and weight fill
public function order_hencode_weight(Request $request){

        $order_id=$request->order_id;
        $vid=$request->vid;
        $data=DB::table('line_items')->join('products','products.product_id','=','line_items.product_id')
        ->where('line_items.order_id','=', $order_id)
        ->where('line_items.vid', '=', intval($vid))
        ->where('products.vid', '=', intval($vid))->get();
        for ($i = 0; $i<count($data); $i++) {
            //->where('products.vid', '=', intval($vid))->get();
            $hsn = $data[$i]->hsn_code;
            $weight=$data[$i]->weight;
            // dd($product_id);
            // die();
            if(is_null($hsn && $weight)){
                echo "Please fill hsn code and weight";
            }
            else{
                echo "Aleady have ";
            }
    } 
  
} 
//api get sales invoices from table billing_processeds status wise(rto-delivered,closed,dto-refunded)
public function sale_invoice_wise_detail(Request $request){
    $vid=$request->vid;
    $retun_billing_processer_data = DB::table('billing_processeds')
    ->where('vid', intval($request->vid))
    // check from table billing_processeds status equal rto-delivered
    ->where('billing_processeds.status','=','rto-delivered')
     // check from table billing_processeds status equal closed
    ->orWhere('billing_processeds.status','=','closed')
     // check from table billing_processeds status equal dto-refunded
    ->orWhere('billing_processeds.status','=','dto-refunded')
    ->select('invoice_no','customer_invice_no','igst','sgst','cgst','invoice_amount','hsn_code','text_percentage','product_name','sku','product_qty','sub_order_id','state','status')
    ->get();
    return $retun_billing_processer_data;

  //   $arr1 = array(
  //     0 => array(
  //         'invoice_no' => $retun_billing_processer_data,
  //         'customer_invice_no' => $retun_billing_processer_data,
  //         'igst'=>$retun_billing_processer_data,
  //         'sgst'=>$retun_billing_processer_data,
  //         'cgst'=>$retun_billing_processer_data,
  //         'invoice_amount'=>$retun_billing_processer_data
          
  //     )
  // );
  // $arr = array();
  // foreach($arr1 as $k => $v) {
  //     array_push($arr, array_merge($v, $arr1[$k]));
  // }
  // return $arr1;

}
//api get sales return from table billing_processeds status wise(rto-delivered,dto-refunded)
 public function sale_return_wise_detail(Request $request){
    $vid=$request->vid;
    $sale_return_data = DB::table('billing_processeds')
    ->where('vid', intval($request->vid))
     // check from table billing_processeds status equal dto-refunded
    ->where('billing_processeds.status','=','dto-refunded')
     // check from table billing_processeds status equal rto-delivered
    ->orWhere('billing_processeds.status','=','rto-delivered')
    ->select('refund_amount','hsn_code','text_percentage','product_name','sku','product_qty','sub_order_id','state','status')
    ->get();
    // dd($sale_return_data);die();
    return  $sale_return_data;
 }
 // api use state wise detail from  table billing_processeds 
 public function state_wise_detail(Request $request){
    $rto='rto-delivered';//variable
    $dto='dto-refunded';//variable
    $close='closed';//variable
    // $sale_textable_amount_total="";
    $vid=$request->vid;
    $retun_billing_processer_data = DB::table('billing_processeds')
    //check from table (billing_processeds) order_to equal to PB
    ->where('order_to','=','PB')
    ->where('vid', intval($request->vid))
     //check from table (billing_processeds) status equal to $rto,$dto,$close
    ->whereIn('status',[$rto,$dto,$close])
    // get data from  table (billing_processeds) 'textable_amount','igst','cgst','sgst','invoice_amount','order_to'
    ->select('textable_amount','igst','cgst','sgst','invoice_amount','order_to')->get();
    //sale textable amount total textable_amount
    $sale_textable_amount_total=$retun_billing_processer_data->sum('textable_amount');
    //sale igst total igst
    $sale_igst_total=$retun_billing_processer_data->sum('igst');
    //sale cgst total cgst
    $sale_cgst_total=$retun_billing_processer_data->sum('cgst');
    //sale sgst total sgst
    $sale_sgst_total=$retun_billing_processer_data->sum('sgst');
    //sale invoice_amount  total invoice_amount
    $sale_invoice_amount_total=$retun_billing_processer_data->sum('invoice_amount');


    //sale retun data start
    $sale_return_data = DB::table('billing_processeds')
      //check from table (billing_processeds) order_to equal to PB
    ->where('order_to','=','PB')
    ->where('vid', intval($request->vid))
    //check from table (billing_processeds) status equal to $rto,$dto,
    ->whereIn('status',[$rto,$dto])
     // get data from  table (billing_processeds) 'textable_amount','igst','cgst','sgst','invoice_amount','order_to'
    ->select('textable_amount','igst','cgst','sgst','invoice_amount','order_to')->get();
     // retun sale textable amount total textable_amount
    $retun_sale_textable_amount_total=$retun_billing_processer_data->sum('textable_amount');
       // retun sale igst amount total igst
    $retun_sale_igst_total=$retun_billing_processer_data->sum('igst');
      // retun sale cgst amount total cgst
    $retun_sale_cgst_total=$retun_billing_processer_data->sum('cgst');
      // retun sale sgst amount total sgst
    $retun_sale_sgst_total=$retun_billing_processer_data->sum('sgst');
      // retun sale invoice_amount amount total invoice_amount
    $retun_sale_invoice_amount_total=$retun_billing_processer_data->sum('invoice_amount');
    
    //net sale for sale textable amount_total-return sale textable amount_total
    $net_texable_amount=$sale_textable_amount_total-$retun_sale_textable_amount_total;
      //net sale for sale igst-return sale igst total
    $net_igst=$retun_sale_igst_total-$sale_igst_total;
     //net sale for sale cgst-return sale cgst total
    $net_cgst=$retun_sale_cgst_total-$sale_cgst_total;
   //net sale for sale sgst-return sale sgst total
    $net_sgst=$retun_sale_sgst_total-$sale_sgst_total;
   //net sale for sale invoice_amount-return sale invoice_amount total
    $net_invoice_amount=$retun_sale_invoice_amount_total-$sale_invoice_amount_total;
    //return  array $net_texable_amount,$net_igst, $net_cgst, $net_sgst,
    return
        [
            $net_texable_amount,
            $net_igst,
            $net_cgst,
            $net_sgst,
        ];
        // $a1=array("net_texable_amount","net_igst");

        
    // return $net;
    //.........MH
  // $retun_billing_processer_data_mh = DB::table('billing_processeds')
  //   //check from table (billing_processeds) order_to equal to PB
  //   ->where('order_to','=','MH')
  //   ->where('vid', intval($request->vid))
  //    //check from table (billing_processeds) status equal to $rto,$dto,$close
  //   ->whereIn('status',[$rto,$dto,$close])
  //   // get data from  table (billing_processeds) 'textable_amount','igst','cgst','sgst','invoice_amount','order_to'
  //   ->select('textable_amount','igst','cgst','sgst','invoice_amount','order_to')->get();
  //   // dd($retun_billing_processer_data_mh);die();
  //   //sale textable amount total textable_amount
  //   $sale_textable_amount_total=$retun_billing_processer_data_mh->sum('textable_amount');
  //   //sale igst total igst
  //   $sale_igst_total=$retun_billing_processer_data_mh->sum('igst');
  //   //sale cgst total cgst
  //   $sale_cgst_total=$retun_billing_processer_data_mh->sum('cgst');
  //   //sale sgst total sgst
  //   $sale_sgst_total=$retun_billing_processer_data_mh->sum('sgst');
  //   //sale invoice_amount  total invoice_amount
  //   $sale_invoice_amount_total=$retun_billing_processer_data_mh->sum('invoice_amount');
    //...MH
    // //sale retun data start
    // $sale_return_data = DB::table('billing_processeds')
    //   //check from table (billing_processeds) order_to equal to PB
    // ->where('order_to','=','MH')
    // ->where('vid', intval($request->vid))
    // //check from table (billing_processeds) status equal to $rto,$dto,
    // ->whereIn('status',[$rto,$dto])
    //  // get data from  table (billing_processeds) 'textable_amount','igst','cgst','sgst','invoice_amount','order_to'
    // ->select('textable_amount','igst','cgst','sgst','invoice_amount','order_to')->get();
    //  // retun sale textable amount total textable_amount
    // $retun_sale_textable_amount_total=$retun_billing_processer_data_mh->sum('textable_amount');
    //    // retun sale igst amount total igst
    // $retun_sale_igst_total=$retun_billing_processer_data_mh->sum('igst');
    //   // retun sale cgst amount total cgst
    // $retun_sale_cgst_total=$retun_billing_processer_data_mh->sum('cgst');
    //   // retun sale sgst amount total sgst
    // $retun_sale_sgst_total=$retun_billing_processer_data_mh->sum('sgst');
    //   // retun sale invoice_amount amount total invoice_amount
    // $retun_sale_invoice_amount_total=$retun_billing_processer_data_mh->sum('invoice_amount');
   

        //net sale for sale textable amount_total-return sale textable amount_total
        $net_texable_amount=$sale_textable_amount_total-$retun_sale_textable_amount_total;
        //net sale for sale igst-return sale igst total
      $net_igst=$retun_sale_igst_total-$sale_igst_total;
       //net sale for sale cgst-return sale cgst total
      $net_cgst=$retun_sale_cgst_total-$sale_cgst_total;
     //net sale for sale sgst-return sale sgst total
      $net_sgst=$retun_sale_sgst_total-$sale_sgst_total;
     //net sale for sale invoice_amount-return sale invoice_amount total
      $net_invoice_amount=$retun_sale_invoice_amount_total-$sale_invoice_amount_total;
      //return  array $net_texable_amount,$net_igst, $net_cgst, $net_sgst,
      // $a1=array($net_texable_amount,$net_igst, $net_cgst, $net_sgst);
      // $a2=array($net_texable_amount,$net_igst, $net_cgst, $net_sgst);
      // dd(array_merge($a1,$a2));
      // $firstKey = array_key_first($ab);
      // dd($firstKey);die();

  //     $arr1 = array(
  //       0 => array(
  //           'net_texable_amount' => $net_texable_amount,
  //           'net_igst' => $net_igst,
  //           'net_cgst'=>$net_cgst,
  //           'net_sgst'=>$net_sgst
            
  //       ),
  //       1 => array(
  //         'net_texable_amount' => $net_texable_amount,
  //         'net_igst' => $net_igst,
  //           'net_cgst'=>$net_cgst,
  //           'net_sgst'=>$net_sgst
  //       )
  //   );
  //   $arr2 = array(
  //     0 => array(
  //       'net_texable_amount' => $net_texable_amount,
  //       'net_igst' => $net_igst,
  //       'net_cgst'=>$net_cgst,
  //       'net_sgst'=>$net_sgst
  //     ),
  //     1 => array(
  //       'net_texable_amount' => $net_texable_amount,
  //       'net_igst' => $net_igst,
  //           'net_cgst'=>$net_cgst,
  //           'net_sgst'=>$net_sgst
  //     )
  // );
  //   $arr = array();
  //   foreach($arr1 as $k => $v) {
  //       array_push($arr, array_merge($v, $arr2[$k]));
  //   }
  //   dd($arr);die();
 }
 public function hsn_wise_detail(Request $request){
    $vid=$request->vid;
  $hsn_sale_wise_data = DB::table('billing_processeds')
    ->where('billing_processeds.hsn_code','=','6109')
    ->where('vid', intval($request->vid))
    ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
      // retun sale textable amount total textable_amount
      $sale_textable_amount_total=$hsn_sale_wise_data->sum('textable_amount');
      // retun sale igst amount total igst
   $sale_igst_total=$hsn_sale_wise_data->sum('igst');
     // retun sale cgst amount total cgst
   $sale_cgst_total=$hsn_sale_wise_data->sum('cgst');
     // retun sale sgst amount total sgst
   $sale_sgst_total=$hsn_sale_wise_data->sum('sgst');
     // retun sale invoice_amount amount total invoice_amount
   $sale_invoice_amount_total=$hsn_sale_wise_data->sum('invoice_amount');

   //..6110 sale start
   $hsn_sale_wise_data_10 = DB::table('billing_processeds')
   ->where('billing_processeds.hsn_code','=','6110')
   ->where('vid', intval($request->vid))
   ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
     // retun sale textable amount total textable_amount
     $sale_textable_amount_total_10=$hsn_sale_wise_data_10->sum('textable_amount');
     // retun sale igst amount total igst
  $sale_igst_total_10=$hsn_sale_wise_data_10->sum('igst');
    // retun sale cgst amount total cgst
  $sale_cgst_total_10=$hsn_sale_wise_data_10->sum('cgst');
    // retun sale sgst amount total sgst
  $sale_sgst_total_10=$hsn_sale_wise_data_10->sum('sgst');
    // retun sale invoice_amount amount total invoice_amount
  $sale_invoice_amount_total_10=$hsn_sale_wise_data_10->sum('invoice_amount');
 //..6110 sale end
    //..6111 sale start
    $hsn_sale_wise_data_11 = DB::table('billing_processeds')
    ->where('billing_processeds.hsn_code','=','6111')
    ->where('vid', intval($request->vid))
    ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
      // retun sale textable amount total textable_amount
      $sale_textable_amount_total_11=$hsn_sale_wise_data_11->sum('textable_amount');
      // retun sale igst amount total igst
   $sale_igst_total_11=$hsn_sale_wise_data_11->sum('igst');
     // retun sale cgst amount total cgst
   $sale_cgst_total_11=$hsn_sale_wise_data_11->sum('cgst');
     // retun sale sgst amount total sgst
   $sale_sgst_total_11=$hsn_sale_wise_data_11->sum('sgst');
     // retun sale invoice_amount amount total invoice_amount
   $sale_invoice_amount_total_11=$hsn_sale_wise_data_11->sum('invoice_amount');
  //..6111 sale end
  //..6109 return_sale start
    $hsn_return_sale_wise_data = DB::table('billing_processeds')
    ->where('billing_processeds.hsn_code','=','6109')
    ->where('vid', intval($request->vid))
    ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
    // $hsn = $hsn_return_sale_wise_data('hsn_code');
    $retun_sale_textable_amount_total=$hsn_return_sale_wise_data->sum('textable_amount');
    // retun sale igst amount total igst
 $retun_sale_igst_total=$hsn_return_sale_wise_data->sum('igst');
   // retun sale cgst amount total cgst
 $retun_sale_cgst_total=$hsn_return_sale_wise_data->sum('cgst');
   // retun sale sgst amount total sgst
 $retun_sale_sgst_total=$hsn_return_sale_wise_data->sum('sgst');
   // retun sale invoice_amount amount total invoice_amount
 $retun_sale_invoice_amount_total=$hsn_return_sale_wise_data->sum('invoice_amount');
//  dd($hsn_return_sale_wise_data);die();
//return sale end 6109
//..retuen_sale 6110 start
 $hsn_return_sale_wise_data_10 = DB::table('billing_processeds')
    ->where('billing_processeds.hsn_code','=','6110')
    ->where('vid', intval($request->vid))
    ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
    // $hsn = $hsn_return_sale_wise_data('hsn_code');
    $retun_sale_textable_amount_total_10=$hsn_return_sale_wise_data_10->sum('textable_amount');
    // retun sale igst amount total igst
 $retun_sale_igst_total_10=$hsn_return_sale_wise_data_10->sum('igst');
   // retun sale cgst amount total cgst
 $retun_sale_cgst_total_10=$hsn_return_sale_wise_data_10->sum('cgst');
   // retun sale sgst amount total sgst
 $retun_sale_sgst_total_10=$hsn_return_sale_wise_data_10->sum('sgst');
   // retun sale invoice_amount amount total invoice_amount
 $retun_sale_invoice_amount_total_10=$hsn_return_sale_wise_data_10->sum('invoice_amount');


//  net sale for sale textable amount_total-return sale textable amount_total
 $net_texable_amount=$retun_sale_textable_amount_total-$sale_textable_amount_total;
     //net sale for sale igst-return sale igst total
     $net_igst=$retun_sale_igst_total-$sale_igst_total;
     //net sale for sale cgst-return sale cgst total
    $net_cgst=$retun_sale_cgst_total-$sale_cgst_total;
    $net_sgst=$retun_sale_sgst_total-$sale_sgst_total;
    //net sale for sale invoice_amount-return sale invoice_amount total
     $net_invoice_amount=$retun_sale_invoice_amount_total_10-$sale_invoice_amount_total_10;
     //.......
     $net_texable_amount_10=$retun_sale_textable_amount_total_10-$sale_textable_amount_total_10;
     //net sale for sale igst-return sale igst total
     $net_igst_10=$retun_sale_igst_total_10-$sale_igst_total_10;
     //net sale for sale cgst-return sale cgst total
    $net_cgst_10=$retun_sale_cgst_total_10-$sale_cgst_total_10;
    $net_sgst_10=$retun_sale_sgst_total_10-$sale_sgst_total_10;
    //net sale for sale invoice_amount-return sale invoice_amount total
     $net_invoice_amount_10=$retun_sale_invoice_amount_total_10-$sale_invoice_amount_total_10;
   
   

$arr1 = array(
  0=> array(
    'hsn_code'=>'6109',
    'sale_textable_amount'=> $sale_textable_amount_total,
    'sale_igst'=>$sale_igst_total,
    'sale_cgst'=>$sale_cgst_total,
    'sale_sgst'=>$sale_sgst_total,
    'sale_invoice_amount'=>$sale_invoice_amount_total,
    'return_text_amount'=>$retun_sale_textable_amount_total,
    'return_igst'=>$retun_sale_igst_total,
    'return_cgst'=>$retun_sale_cgst_total,
    'return_sgst'=>$retun_sale_sgst_total,
    'return_invoice_amount'=>$retun_sale_invoice_amount_total,
    'net_textable_amount'=>$net_texable_amount,
    'net_igst'=>$net_igst,
    'net_cgst'=>$net_cgst,
    'net_sgst'=>$net_sgst
  )
  );
  // dd($arr1);die();
  

$arr2 = array(
  0 => array(
    'hsn_code'=>'6110',
    'sale_textable_amount'=> $sale_textable_amount_total_10,
    'sale_igst'=>$sale_igst_total_10,
    'sale_cgst'=>$sale_cgst_total_10,
    'sale_sgst'=>$sale_sgst_total_10,
    'sale_invoice_amount'=>$sale_invoice_amount_total_10,
    'return_text_amount'=>$retun_sale_textable_amount_total_10,
    'return_igst'=>$retun_sale_igst_total_10,
    'return_cgst'=>$retun_sale_cgst_total_10,
    'return_sgst'=>$retun_sale_sgst_total_10,
    'return_invoice_amount'=>$retun_sale_invoice_amount_total_10,
    'net_textable_amount'=>$net_texable_amount_10,
    'net_igst'=>$net_igst_10,
    'net_cgst'=>$net_cgst_10,
    'net_sgst'=>$net_sgst_10
)
  );
 $hsn_wise = (array_merge($arr1,$arr2));
 return  $hsn_wise;

 }
 public function hsn_wise_detail_copy(Request $request){
  $hsn_9='6109';
  $hsn_10='6110';
  $hsn_11='6111';
  $hsn_12='6112';
  $hsn_13='6113';
  $hsn_14='6114';
  $hsn_15='6115';
  $hsn_16='6116';
  $hsn_17='6117';
  $hsn_18='6118';
  $hsn_19='6119';
  $hsn_20='6120';
  $hsn_21='6121';
  $vid=$request->vid;
$hsn_sale_wise_data = DB::table('billing_processeds')
  ->where('vid', intval($request->vid))
  ->whereIn('hsn_code',[$hsn_9,$hsn_10,$hsn_11,$hsn_12,$hsn_13,$hsn_14,$hsn_15,$hsn_16,$hsn_17,$hsn_18,$hsn_19,$hsn_20,$hsn_21])
  ->distinct()
  ->get();
  dd($hsn_sale_wise_data);die();
    // retun sale textable amount total textable_amount
    $sale_textable_amount_total=$hsn_sale_wise_data->sum('textable_amount');
    // retun sale igst amount total igst
 $sale_igst_total=$hsn_sale_wise_data->sum('igst');
   // retun sale cgst amount total cgst
 $sale_cgst_total=$hsn_sale_wise_data->sum('cgst');
   // retun sale sgst amount total sgst
 $sale_sgst_total=$hsn_sale_wise_data->sum('sgst');
   // retun sale invoice_amount amount total invoice_amount
 $sale_invoice_amount_total=$hsn_sale_wise_data->sum('invoice_amount');

 //..6110 sale start
 $hsn_sale_wise_data_10 = DB::table('billing_processeds')
 ->where('billing_processeds.hsn_code','=','6110')
 ->where('vid', intval($request->vid))
 ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
   // retun sale textable amount total textable_amount
   $sale_textable_amount_total_10=$hsn_sale_wise_data_10->sum('textable_amount');
   // retun sale igst amount total igst
$sale_igst_total_10=$hsn_sale_wise_data_10->sum('igst');
  // retun sale cgst amount total cgst
$sale_cgst_total_10=$hsn_sale_wise_data_10->sum('cgst');
  // retun sale sgst amount total sgst
$sale_sgst_total_10=$hsn_sale_wise_data_10->sum('sgst');
  // retun sale invoice_amount amount total invoice_amount
$sale_invoice_amount_total_10=$hsn_sale_wise_data_10->sum('invoice_amount');
//..6110 sale end
  //..6111 sale start
  $hsn_sale_wise_data_11 = DB::table('billing_processeds')
  ->where('billing_processeds.hsn_code','=','6111')
  ->where('vid', intval($request->vid))
  ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
    // retun sale textable amount total textable_amount
    $sale_textable_amount_total_11=$hsn_sale_wise_data_11->sum('textable_amount');
    // retun sale igst amount total igst
 $sale_igst_total_11=$hsn_sale_wise_data_11->sum('igst');
   // retun sale cgst amount total cgst
 $sale_cgst_total_11=$hsn_sale_wise_data_11->sum('cgst');
   // retun sale sgst amount total sgst
 $sale_sgst_total_11=$hsn_sale_wise_data_11->sum('sgst');
   // retun sale invoice_amount amount total invoice_amount
 $sale_invoice_amount_total_11=$hsn_sale_wise_data_11->sum('invoice_amount');
//..6111 sale end
//..6109 return_sale start
  $hsn_return_sale_wise_data = DB::table('billing_processeds')
  ->where('billing_processeds.hsn_code','=','6109')
  ->where('vid', intval($request->vid))
  ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
  // $hsn = $hsn_return_sale_wise_data('hsn_code');
  $retun_sale_textable_amount_total=$hsn_return_sale_wise_data->sum('textable_amount');
  // retun sale igst amount total igst
$retun_sale_igst_total=$hsn_return_sale_wise_data->sum('igst');
 // retun sale cgst amount total cgst
$retun_sale_cgst_total=$hsn_return_sale_wise_data->sum('cgst');
 // retun sale sgst amount total sgst
$retun_sale_sgst_total=$hsn_return_sale_wise_data->sum('sgst');
 // retun sale invoice_amount amount total invoice_amount
$retun_sale_invoice_amount_total=$hsn_return_sale_wise_data->sum('invoice_amount');
//  dd($hsn_return_sale_wise_data);die();
//return sale end 6109
//..retuen_sale 6110 start
$hsn_return_sale_wise_data_10 = DB::table('billing_processeds')
  ->where('billing_processeds.hsn_code','=','6110')
  ->where('vid', intval($request->vid))
  ->select('textable_amount','igst','cgst','sgst','invoice_amount','hsn_code')->get();
  // $hsn = $hsn_return_sale_wise_data('hsn_code');
  $retun_sale_textable_amount_total_10=$hsn_return_sale_wise_data_10->sum('textable_amount');
  // retun sale igst amount total igst
$retun_sale_igst_total_10=$hsn_return_sale_wise_data_10->sum('igst');
 // retun sale cgst amount total cgst
$retun_sale_cgst_total_10=$hsn_return_sale_wise_data_10->sum('cgst');
 // retun sale sgst amount total sgst
$retun_sale_sgst_total_10=$hsn_return_sale_wise_data_10->sum('sgst');
 // retun sale invoice_amount amount total invoice_amount
$retun_sale_invoice_amount_total_10=$hsn_return_sale_wise_data_10->sum('invoice_amount');


//  net sale for sale textable amount_total-return sale textable amount_total
$net_texable_amount=$retun_sale_textable_amount_total-$sale_textable_amount_total;
   //net sale for sale igst-return sale igst total
   $net_igst=$retun_sale_igst_total-$sale_igst_total;
   //net sale for sale cgst-return sale cgst total
  $net_cgst=$retun_sale_cgst_total-$sale_cgst_total;
  $net_sgst=$retun_sale_sgst_total-$sale_sgst_total;
  //net sale for sale invoice_amount-return sale invoice_amount total
   $net_invoice_amount=$retun_sale_invoice_amount_total_10-$sale_invoice_amount_total_10;
   //.......
   $net_texable_amount_10=$retun_sale_textable_amount_total_10-$sale_textable_amount_total_10;
   //net sale for sale igst-return sale igst total
   $net_igst_10=$retun_sale_igst_total_10-$sale_igst_total_10;
   //net sale for sale cgst-return sale cgst total
  $net_cgst_10=$retun_sale_cgst_total_10-$sale_cgst_total_10;
  $net_sgst_10=$retun_sale_sgst_total_10-$sale_sgst_total_10;
  //net sale for sale invoice_amount-return sale invoice_amount total
   $net_invoice_amount_10=$retun_sale_invoice_amount_total_10-$sale_invoice_amount_total_10;
 
 

$arr1 = array(
0=> array(
  'hsn_code'=>'6109',
  'sale_textable_amount'=> $sale_textable_amount_total,
  'sale_igst'=>$sale_igst_total,
  'sale_cgst'=>$sale_cgst_total,
  'sale_sgst'=>$sale_sgst_total,
  'sale_invoice_amount'=>$sale_invoice_amount_total,
  'return_text_amount'=>$retun_sale_textable_amount_total,
  'return_igst'=>$retun_sale_igst_total,
  'return_cgst'=>$retun_sale_cgst_total,
  'return_sgst'=>$retun_sale_sgst_total,
  'return_invoice_amount'=>$retun_sale_invoice_amount_total,
  'net_textable_amount'=>$net_texable_amount,
  'net_igst'=>$net_igst,
  'net_cgst'=>$net_cgst,
  'net_sgst'=>$net_sgst
)
);
// dd($arr1);die();


$arr2 = array(
0 => array(
  'hsn_code'=>'6110',
  'sale_textable_amount'=> $sale_textable_amount_total_10,
  'sale_igst'=>$sale_igst_total_10,
  'sale_cgst'=>$sale_cgst_total_10,
  'sale_sgst'=>$sale_sgst_total_10,
  'sale_invoice_amount'=>$sale_invoice_amount_total_10,
  'return_text_amount'=>$retun_sale_textable_amount_total_10,
  'return_igst'=>$retun_sale_igst_total_10,
  'return_cgst'=>$retun_sale_cgst_total_10,
  'return_sgst'=>$retun_sale_sgst_total_10,
  'return_invoice_amount'=>$retun_sale_invoice_amount_total_10,
  'net_textable_amount'=>$net_texable_amount_10,
  'net_igst'=>$net_igst_10,
  'net_cgst'=>$net_cgst_10,
  'net_sgst'=>$net_sgst_10
)
);
dd(array_merge($arr1,$arr2));

}

}
