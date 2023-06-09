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
public function sale_invoice_wise_detail(Request $request)
{
    $date_from=$request->date_from;
    $date_to=$request->date_to;
    $range = [$date_from,$date_to];
        if(empty($date_from)||empty($date_to)){
            $da_from='2023-04-01';
            $da_to=date('Y-m-d H:i:s');
            $rang = [$da_from,$da_to];
            $completed='completed';
            $intransit='intransit';
            $packed='packed';
            $deliveredtocust='deliveredtocust';
            $rto='rto-delivered';
            $clos='closed';
            $dto='dto-refunded';
            $dtoint='dtointransit';
            $dtodel='dtodelivered';
            $dtoboo='dtobooked';
                $vid=$request->vid;
                $retun_billing_processer_data = DB::table('billing_processeds')
                ->where('vid', intval($request->vid))->whereBetween('billing_processeds.created_at',$rang)
                ->whereIn('status',[$completed,$intransit,$packed,$deliveredtocust,$rto,$clos,$dto,$dtoint,$dtodel,$dtoboo])
                ->select('invoice_no','created_at','textable_amount','igst','sgst','cgst','invoice_amount','hsn_code','text_percentage','product_name','sku','product_qty','sub_order_id','state','status')
                ->get();
                return  $retun_billing_processer_data;
        }
    else{
        $completed='completed';
        $intransit='intransit';
        $packed='picked';
        $deliveredtocust='deliveredtocust';
        $pickedup='pickedup';
            $vid=$request->vid;
            $retun_billing_processer_data = DB::table('billing_processeds')
            ->where('vid', intval($request->vid))
            ->whereBetween('billing_processeds.created_at',$range)
            ->whereIn('status',[$completed,$intransit,$packed,$deliveredtocust,$rto,$clos,$dto,$dtoint,$dtodel,$dtoboo])
            ->select('invoice_no','created_at','igst','sgst','cgst','invoice_amount','hsn_code','text_percentage','product_name','sku','product_qty','sub_order_id','state','status')
            ->get();
            return  $retun_billing_processer_data;
            }
}
 public function sale_return_wise_detail(Request $request){

     $date_from=$request->date_from;
    $date_to=$request->date_to;
    $range = [$date_from,$date_to];
    if(empty($date_from)||empty($date_to)){
        $da_from='2023-04-01'.' 00:00:00';
        $da_to=date('Y-m-d H:i:s');
        $rang = [$da_from,$da_to];
        $dto='dto-refunded';//variable
            $dtoBooked='dto-booked';
            $dispatched='dispatched';
            $rtdOnline='rtd-online';
            $rtdCod='rtd-cod';
            $dtodel2warehouse='dtodel2warehouse';
            $dtointransit='dtointransit';
            $rto='rto-delivered';
            $vid=$request->vid;
            $sale_return_data = DB::table('billing_processeds')
            ->where('vid', intval($request->vid))
            ->whereIn('status',[$dto, $rto])->whereBetween('billing_processeds.created_at',$rang)
            ->select('invoice_no','sale_return_date','textable_amount','igst','sgst','cgst','refund_amount','hsn_code','text_percentage','product_name','product_qty','sub_order_id','parent_order_number','state','status',)
            ->get();
            // dd($sale_return_data);die();
            return  $sale_return_data;
    }
    else{
            
       $dto='dto-refunded';//variable
       $dtoBooked='dto-booked';
       $dispatched='dispatched';
       $rtdOnline='rtd-online';
       $rtdCod='rtd-cod';
       $dtodel2warehouse='dtodel2warehouse';
       $dtointransit='dtointransit';
       $rto='rto-delivered';
       $vid=$request->vid;
       $sale_return_data = DB::table('billing_processeds')
       ->where('vid', intval($request->vid))
       ->whereBetween('billing_processeds.created_at',$range)
       ->whereIn('status',[$dto, $rto])
       ->select('invoice_no','sale_return_date','textable_amount','igst','sgst','cgst','refund_amount','hsn_code','text_percentage','product_name','product_qty','sub_order_id','parent_order_number','state','status',)
       ->get();
       // dd($sale_return_data);die();
       return  $sale_return_data;
        }
 }
 
 public function state_wise_detail(Request $request){
    $date_from=$request->date_from;
    $dt_from=$date_from.' 00:00:00';
    $date_to=$request->date_to;
    $dt_to=$date_to.' 00:00:00';
    $range = [$dt_from,$dt_to];

    if(empty($dt_from)||empty($date_to)){

        $ldate = date('Y-m-d');
        $date_fro='2023-04-01';
        $ran=[$date_fro,$ldate];
        $dto='dto-refunded';//variable
        $dtoBooked='dto-booked';
        $dispatched='dispatched';
        $rtdOnline='rtd-online';
        $rtdCod='rtd-cod';
        $dtodel2warehouse='dtodel2warehouse';
        $dtointransit='dtointransit';
        $completed='completed';
        $intransit='intransit';
        $packed='picked';
        $deliveredtocust='deliveredtocust';
        $pickedup='pickedup';
        $closed='closed';
        $rto='rto-delivered';
        $vid=$request->vid;
    
    $all_state = DB::table('billing_processeds')
    ->where('vid', intval($request->vid))->distinct()->pluck('order_to')->toArray();

    // ->whereIn('status',[$dto,$closed,$dtodel2warehouse,$dtointransit,$completed,$intransit,$packed,$deliveredtocust,$pickedup])
        $state_sale_wise_data = DB::table('billing_processeds')->where('vid', intval($request->vid))->whereBetween('billing_processeds.created_at',$ran)->distinct('order_to')->pluck('order_to')->toArray();
        
       
      
    // echo "dffdf";print_r($state_sale_wise_data);die();
        $state_wise_detail_sale=array();
        for($i=0;$i<count($state_sale_wise_data);$i++)
        {
            $order_to=$state_sale_wise_data[$i];
            //    dd($order_to);die();
            $state_wise_detail_sale[] = DB::table('billing_processeds')->where('vid', intval($request->vid))->where('order_to',$order_to)->whereBetween('billing_processeds.created_at',$ran)
            ->select(["order_to",DB::raw("SUM(textable_amount) as sale_texable_amount"),DB::raw("Round(SUM(igst),2) as sale_igst"),DB::raw("Round(SUM(cgst),2) as sale_cgst"),DB::raw("Round(SUM(sgst),2) as sale_sgst"),DB::raw("SUM(invoice_amount) as sale_invoice_amount")])
            ->groupby('order_to')
            ->get();
        }
        
        $state_sale_return_wise_data = DB::table('billing_processeds')->where('vid', intval($request->vid))->whereBetween('billing_processeds.created_at',$ran)
        ->whereIn('status',[$dto,$rto])->distinct('order_to')->pluck('order_to')->toArray();
    
        $state_returnwise_detail_sale=array();
       
        for($i=0;$i<count($state_sale_return_wise_data);$i++)
        {
            $order_to=$state_sale_return_wise_data[$i];
            
            $state_returnwise_detail_sale[] = DB::table('billing_processeds')->where('vid', intval($request->vid))->where('order_to',$order_to)
            ->whereIn('status',[$dto,$rto])->whereBetween('billing_processeds.created_at',$ran)
            ->select([DB::raw("SUM(textable_amount) as return_texable_amount"),
                    DB::raw("SUM(igst) as return_igst"),
                    DB::raw("SUM(cgst) as return_cgst"),
                    DB::raw("SUM(sgst) as return_sgst"),
                    DB::raw("SUM(invoice_amount) as return_invoice_amount"),'order_to'])
                    ->groupby('order_to')
                    ->get();
        }
        $newarray = array();
        $inc = 0;
        foreach ($state_wise_detail_sale as $item){
            // print($item);die();
            $check =1;
            $returnAmt = '0';
            $return_igst = '0';
            $return_cgst = '0';
            $return_sgst = '0';
            $return_invoice_amount = 0;
            $return_texable_amount = 0;


            foreach($state_returnwise_detail_sale as $retItem){
                
                if($item[0]->order_to == $retItem[0]->order_to){
                    
                        // add all object here
                        $return_texable_amount += $retItem[0]->return_texable_amount;
                        $returnAmt += $retItem[0]->return_texable_amount;
                        $return_igst += $retItem[0]->return_igst;
                        $return_cgst += $retItem[0]->return_cgst;
                        $return_sgst += $retItem[0]->return_sgst;
                        $return_invoice_amount += $retItem[0]->return_invoice_amount;
                        $check =0;
                    
                }
            }
            // echo ($return_igst)."  ==  ";
            // if($check = 1){
                
            //     $return_texable_amount = '0';
            //     $return_igst = '0';
            //     $return_cgst = '0';
            //     $return_sgst = '0';
            //     $return_invoice_amount = '0';
                
            // }
            // }
            $item[0]->return_texable_amount = $return_texable_amount;
            // $item[0]->returnAmt = $returnAmt;
            $item[0]->return_igst = $return_igst;
            $item[0]->return_cgst = $return_cgst;
            $item[0]->return_sgst = $return_sgst;
            $item[0]->return_invoice_amount = $return_invoice_amount;

            $item[0]->net_texable_amount = ($item[0]->sale_texable_amount)-($returnAmt);
            $item[0]->net_igst = ($item[0]->sale_igst)-($return_igst);
            $item[0]->net_cgst =($item[0]->sale_cgst)- ($return_cgst);
            $item[0]->net_sgst = ($item[0]->sale_sgst)-($return_sgst);
            $item[0]->net_invoice_amount = ($item[0]->sale_invoice_amount)-($return_invoice_amount);
            
//             print_r($item[0]);
// exit();
            $newarray[$inc++] = $item[0];
        
        }
        return $newarray;  
    }
    else{
        
    $dto='dto-refunded';//variable
        $dtoBooked='dto-booked';
        $dispatched='dispatched';
        $rtdOnline='rtd-online';
        $rtdCod='rtd-cod';
        $dtodel2warehouse='dtodel2warehouse';
        $dtointransit='dtointransit';
        $completed='completed';
        $intransit='intransit';
        $packed='picked';
        $deliveredtocust='deliveredtocust';
        $pickedup='pickedup';
        $rto='rto-delivered';
        $vid=$request->vid;
        // ->whereIn('status',[$dtodel2warehouse,$dtointransit,$completed,$intransit,$packed,$deliveredtocust,$pickedup])
    $all_state = DB::table('billing_processeds')
    ->where('vid', intval($request->vid))->whereBetween('billing_processeds.created_at',$range)
    ->distinct()->pluck('order_to')->toArray();

        $state_sale_wise_data = DB::table('billing_processeds')->where('vid', intval($request->vid))
        ->whereBetween('billing_processeds.created_at',$range)->distinct('order_to')->pluck('order_to')->toArray();
       

        $state_sale_return_wise_data = DB::table('billing_processeds')->where('vid', intval($request->vid))
        ->whereBetween('billing_processeds.created_at',$range)
        ->whereIn('status',[$dto,$rto])->distinct('order_to')->pluck('order_to')->toArray();
       
        $state_wise_detail_sale=array();
        for($i=0;$i<count($state_sale_wise_data);$i++)
        {
        $order_to=$state_sale_wise_data[$i];
       
        //    dd($order_to);die();
        $state_wise_detail_sale[] = DB::table('billing_processeds')->where('order_to',$order_to)->where('vid', intval($request->vid))->whereBetween('billing_processeds.created_at',$range)
        ->select(["order_to",DB::raw("SUM(textable_amount) as sale_texable_amount"),DB::raw("Round(SUM(igst),2) as sale_igst"),DB::raw("Round(SUM(cgst),2) as sale_cgst"),DB::raw("Round(SUM(sgst),2) as sale_sgst"),DB::raw("SUM(invoice_amount) as sale_invoice_amount")])
        ->groupby('order_to')
        ->get();
        }
        
        //this sale wise total text_amount
        $state_returnwise_detail_sale=array();
        for($i=0;$i<count($state_sale_return_wise_data);$i++)
        {
            $order_to=$state_sale_return_wise_data[$i];
            $state_returnwise_detail_sale[] = DB::table('billing_processeds')->where('vid', intval($request->vid))->where('order_to',$order_to)
            ->whereIn('status',[$dto,$rto])->whereBetween('billing_processeds.created_at',$range)
            ->select(['created_at',DB::raw("SUM(textable_amount) as return_texable_amount"),
                    DB::raw("SUM(igst) as return_igst"),
                    DB::raw("SUM(cgst) as return_cgst"),
                    DB::raw("SUM(sgst) as return_sgst"),
                    DB::raw("SUM(invoice_amount) as return_invoice_amount"),'order_to'])
                    ->groupby('order_to')
                    ->get();
        } 
        $newarray = array();
        $inc = 0;
        foreach ($state_wise_detail_sale as $item){
            // print($item);die();
            $check =1;
            $returnAmt = '0';
            $return_igst = '0';
            $return_cgst = '0';
            $return_sgst = '0';
            $return_invoice_amount = 0;
            $return_texable_amount = 0;

            foreach($state_returnwise_detail_sale as $retItem){
                
                if($item[0]->order_to == $retItem[0]->order_to){
                    
                        // add all object here
                        $return_texable_amount += $retItem[0]->return_texable_amount;
                        $returnAmt += $retItem[0]->return_texable_amount;
                        $return_igst += $retItem[0]->return_igst;
                        $return_cgst += $retItem[0]->return_cgst;
                        $return_sgst += $retItem[0]->return_sgst;
                        $return_invoice_amount += $retItem[0]->return_invoice_amount;
                        $check =0;
                    
                }
            }
            // // echo ($return_igst);
            // if($check = 1){
                
            //     $return_texable_amount = '0';
            //     $return_igst = '0';
            //     $return_cgst = '0';
            //     $return_sgst = '0';
            //     $return_invoice_amount = '0';
                
            // }
            // }
            $item[0]->return_texable_amount = $return_texable_amount;
            // $item[0]->returnAmt = $returnAmt;
            $item[0]->return_igst = $return_igst;
            $item[0]->return_cgst = $return_cgst;
            $item[0]->return_sgst = $return_sgst;
            $item[0]->return_invoice_amount = $return_invoice_amount;
            $item[0]->net_texable_amount = ($item[0]->sale_texable_amount)-($returnAmt);
            $item[0]->net_igst = ($item[0]->sale_igst)-($return_igst);
            $item[0]->net_cgst =($item[0]->sale_cgst)- ($return_cgst);
            $item[0]->net_sgst = ($item[0]->sale_sgst)-($return_sgst);
            $item[0]->net_invoice_amount = ($item[0]->sale_invoice_amount)-($return_invoice_amount);
            
//             print_r($item[0]);
// exit();
            $newarray[$inc++] = $item[0];
        
        }
        return $newarray;  
    }
}

}
