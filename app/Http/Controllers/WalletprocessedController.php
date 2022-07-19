<?php

namespace App\Http\Controllers;

use App\Models\walletprocessed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\line_items;
use App\Models\AddTransaction;
use App\Models\zonedetails;
use App\Models\billings;



class WalletprocessedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if ($request->allSelected) {
        //     $listImp = explode(',', $request->allSelected);
        //     $vid = intval($request->vid);
        //     $orders[] = DB::table("orders")->join('line_items', 'orders.oid', '=', 'line_items.order_id')->select("orders.oid as OrderID", "orders.date_created as OrderDate", "line_items.product_id as Item ID", "line_items.name as Item Name", "line_items.sku as SKU", "line_items.quantity as Qty")->where('orders.vid', $vid)->where('line_items.vid', $vid)->orderBy('oid', 'DESC')->get();
        // } else {
        //     $orders[] = DB::table("orders")->join('line_items', 'orders.oid', '=', 'line_items.order_id')->select("orders.oid as OrderID", "orders.date_created as OrderDate", "line_items.product_id as Item ID", "line_items.name as Item Name", "line_items.sku as SKU", "line_items.quantity as Qty")->where('orders.vid', intval($request->vid))->where('orders.status', "confirmed")->where('line_items.vid', intval($request->vid))->get();
        // }
        // return $orders;
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
        if ($request->allSelected == "false")
        {
            $orders[]=$request->oid;
        }
        else
        {
            $orders=explode(',', $request->allSelected);
        }
         //echo count($orders);
        $vendor = $request->vid;
        
        // $orders = DB::table("walletprocesseds")->get();
        for($y = 0;$y < count($orders);$y++) 
        {
            $order_table=DB::table("orders")->where('orders.oid','=',intval($orders[$y]))->get();
            $data=DB::table("billings")->where('billings.order_id','=',intval($orders[$y]))->get();
            $zone=DB::table("zonedetails")->where('zonedetails.pincode','=',$data[0]->postcode)->get();
            if(count($zone)>=1)
            {
            $zone_rate=DB::table("zoneratecards")->where('zoneratecards.zoneno','=',$zone[0]->zoneno)->get(); 
            }
            $line_items=DB::table("line_items")->where('line_items.order_id','=',intval($orders[$y]))->get();  
            $product_weight=DB::table("products")->where('products.product_id','=',$line_items[0]->product_id)->get();    
            $vendor_rate=DB::table("vendor_ratecards")->get();
            $oc_balance=DB::table("opening_closing_tables")->where('opening_closing_tables.vid','=',$order_table[0]->vid)->orderBy('id','DESC')->limit(1)->get();
            $sms_cost=2;
            $sale_Amount=$order_table[0]->total;
            $majime_cost=(($order_table[0]->total)*5/100);
           

            if($order_table[0]->payment_method == 'cod')
            {
                $payment_gateway=0;
            }else{
                $payment_gateway=(($order_table[0]->total)*2/100);
            }
             
            if(count($oc_balance) >= 1){
                $opening_balance=$oc_balance[0]->closing_bal;
            }else{
                $opening_balance=0;
            }
            //  echo $opening_balance; 
            //  die;
            if($order_table[0]->status=='completed')
            {
                if(count($zone)>=1){
                $zone_price=$zone_rate[0]->fwd;
                }
                else{
                    $zone_price=0;
                }
            }
            elseif($order_table[0]->status== 'dto-refunded')
            {
                if(count($zone)>=1){
                $zone_price=$zone_rate[0]->dto;
                }
                else{
                    $zone_price=0;
                }

            }
            elseif($order_table[0]->status== 'rtodelivered')
            {
                if(count($zone)>=1){
                $zone_price=$zone_rate[0]->rto;
                }
                else{
                    $zone_price=0;
                }
            }
            // if($order_table[0]->payment_method == 'cod'){
            //     // If wallet is in COD order you can use here
            //     $payment_gateway=0;
               
            // }else if($order_table[0]->payment_method == 'cashfree' || $order_table[0]->payment_method == 'prepaid'){
            //     // If wallet is in Prepaid order you can use here
            //         $payment_gateway=(($order_table[0]->total)*2/100);
            //         $cod_charges = 0;
            // }
          
            if($order_table[0]->status != 'rtodelivered')
            {
                if($order_table[0]->payment_method == 'cod')
                {   
                    $Vcodper = $vendor_rate[0]->codper;
                    $getval = ceil($sale_Amount*($Vcodper/100));
                    if($getval > $zone_price){
                        $cod_charges = $getval;
                    }
                    $pw=$product_weight[0]->weight;
                    if($pw==0)
                    {   
                        $pw=250;
                    }
                    $qty=$line_items[0]->quantity; 
                    $total_weight=$pw*$qty;
                    if($total_weight>500)
                    {
                        // $cod_cost=$vendor_rate[0]->cod;
                        $cod_cost=$zone_price;
                        $percent_price=(int)($total_weight/500);
                        $pp_amount=($cod_cost*85/100)*$percent_price;
                        $logistic_cost=$cod_cost+$pp_amount+$cod_charges;
                    }
                    else{
                        $logistic_cost=$vendor_rate[0]->cod+($vendor_rate[0]->cod*2/100);
                    }
                    if($order_table[0]->status== 'dto-refunded'){
                       
                        $net_amount=$logistic_cost+$majime_cost+$sms_cost+$payment_gateway;
                        $closing_bal=$opening_balance+$net_amount;
                    }else{
                        
                         $net_amount=$sale_Amount-($logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                        $closing_bal=$net_amount+ $opening_balance;
                    }
                }
                else{
                   
                    $Vcodper = $vendor_rate[0]->codper;
                    $getval = ceil($sale_Amount*($Vcodper/100));;
                    if($getval > $zone_price){
                        $cod_charges = $getval;
                    }
                    $pw=$product_weight[0]->weight;
                    if($pw==0)
                    {   
                        $pw=250;
                    }
                    $qty=$line_items[0]->quantity;
                     
                    $total_weight=$pw*$qty;
                    if($total_weight>500)
                    {
                        $cod_cost=$zone_price;
                       
                        $percent_price=(int)($total_weight/500);
                        $pp_amount=($cod_cost*85/100)*$percent_price;
                        $logistic_cost=$cod_cost+$pp_amount+$cod_charges;
                    }
                    else{
                        $logistic_cost=$vendor_rate[0]->cod+($vendor_rate[0]->cod*2/100);
                    }
                    if($order_table[0]->status== 'dto-refunded'){
                        $net_amount=$logistic_cost+$majime_cost+$sms_cost+$payment_gateway;
                        $closing_bal=$opening_balance+$net_amount;
                    }else{
                        $net_amount=$sale_Amount-($logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                        $closing_bal=$net_amount+ $opening_balance;
                    }
                }
            }else{
                $logistic_cost=$zone_price*2;
                $sms_cost=2*2;
                $net_amount=$logistic_cost+$sms_cost;
                $closing_bal=$opening_balance+ $net_amount;
                $majime_cost=0;
            }
            // echo $cod_cost;
            // echo $pp_amount;
            // echo $logistic_cost;
            // echo $percent_price;

            $wallet=$request->walletvalue;
            DB::table('orders')->where('orders.oid', intval($orders[$y]))->where('vid', intval($request->vid))->update(['wallet_processed' => $wallet]);
            $Wallet_order_data[]=[     
                'date_created'=>$order_table[0]->date_created,
                'oid'=>$order_table[0]->oid,
                'vid'=>$request->vid,
                'payment_mode'=>$order_table[0]->payment_method,
                'status'=>$order_table[0]->status,
                'sale_amount'=>$sale_Amount,
                'Wallet_used'=>0,
                'logistic_cost'=>$logistic_cost,
                'payment_gateway_charges'=>$payment_gateway,
                'sms_cost'=>$sms_cost,
                'majime_charges'=>$majime_cost,
                'net_amount'=>$net_amount,
                'current_wallet_bal'=>$closing_bal,
                'order_count'=> $line_items[0]->quantity,
                'zone_amt'=> $zone_price
            ];    
            walletprocessed::insert($Wallet_order_data);   

            DB::table('opening_closing_tables')->insert(
                ['vid' => $request->vid, 'opening_bal' => $opening_balance, 'closing_bal' => $closing_bal, 'created_at' => date('Y-m-d h:m:s'), 'updated_at' => date('Y-m-d h:m:s')]
            );
        }   
        // if ($request->allSelected == "false")
        // {
        //     $wallet=$request->walletvalue;
        //       $order_data=DB::table('orders')->where('orders.oid', intval($request->oid))->where('vid', intval($request->vid))
        //       ->select("orders.*","orders.oid as orderno",
        //        DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
        //              WHERE line_items.order_id = orders.oid 
        //              GROUP BY line_items.order_id) as Quantity"),
        //            (DB::raw('(total*(5/100)) AS m_cost')),(DB::raw('(total*(5/100)) AS v_cost')),
        //            (DB::raw('(total*(2/100)) AS gateway_cost')),(DB::raw('(total*(2/100)) AS s_cost')),
        //            (DB::raw('(total*(93/100)) AS net_amount'))
        //            )->get();
                   
        //         foreach($order_data as $orderdata)
        //         {
        //              $Wallet_order_data[]=[     
        //                 'date_created'=>$orderdata->date_created,
        //                 'oid'=>$orderdata->oid,
        //                 'vid'=>$orderdata->vid,
        //                 'payment_mode'=>$orderdata->payment_method,
        //                 'status'=>$orderdata->status,
        //                 'sale_amount'=>$orderdata->total,
        //                 'Wallet_used'=>$orderdata->total,
        //                 'logistic_cost'=>$orderdata->total,
        //                 'payment_gateway_charges'=>$orderdata->gateway_cost,
        //                 'sms_cost'=>$orderdata->s_cost,
        //                 'majime_charges'=>$orderdata->s_cost,
        //                 'net_amount'=>$orderdata->net_amount,
        //                 'current_wallet_bal'=>$orderdata->total,
        //                 'order_count'=> $orderdata->Quantity,
        //                 'created_at'=> $orderdata->created_at,
        //                 'updated_at'=> $orderdata->updated_at,
        //              ];    
        //              walletprocessed::insert($Wallet_order_data);       
        //       }            
           
        //     DB::table('orders')->where('orders.oid', intval($request->oid))->where('vid', intval($request->vid))->update(['wallet_processed' => $wallet]);
        //     return response()->json(['error' => true, 'msg' => " Wallet Data Processed", "ErrorCode" => "000"], 200);
        // }
        // else
        // {
        //     $main = explode(',', $request->allSelected);
        //     $wallet=$request->walletvalue;
        //     for ($i = 0;$i < count($main);$i++) 
        //      {
        //       $order_data=DB::table('orders')->where('orders.oid', intval($main[$i]))->where('vid', intval($request->vid))
        //       ->select("orders.*","orders.oid as orderno",
        //        DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
        //              WHERE line_items.order_id = orders.oid 
        //              GROUP BY line_items.order_id) as Quantity"),
        //            (DB::raw('(total*(5/100)) AS m_cost')),(DB::raw('(total*(5/100)) AS v_cost')),
        //            (DB::raw('(total*(2/100)) AS gateway_cost')),(DB::raw('(total*(2/100)) AS s_cost')),
        //            (DB::raw('(total*(93/100)) AS net_amount'))
        //            )->get();
        //         foreach($order_data as $orderdata)
        //         {
        //              $Wallet_order_data[]=[
        //                 'date_created'=>$orderdata->date_created,
        //                 'oid'=>$orderdata->oid,
        //                 'vid'=>$orderdata->vid,
        //                 'payment_mode'=>$orderdata->payment_method,
        //                 'status'=>$orderdata->status,
        //                 'sale_amount'=>$orderdata->total,
        //                 'Wallet_used'=>$orderdata->total,
        //                 'logistic_cost'=>$orderdata->total,
        //                 'payment_gateway_charges'=>$orderdata->gateway_cost,
        //                 'sms_cost'=>$orderdata->s_cost,
        //                 'majime_charges'=>$orderdata->s_cost,
        //                 'net_amount'=>$orderdata->net_amount,
        //                 'current_wallet_bal'=>$orderdata->total,
        //                 'order_count'=> $orderdata->Quantity,
        //              ];           
        //       }
        //     DB::table('orders')->where('orders.oid', intval($main[$i]))->where('vid', intval($request->vid))->update(['wallet_processed' => $wallet]);
        //     }
        //     walletprocessed::insert($Wallet_order_data);
        //     return response()->json(['error' => true, 'msg' => " Wallet Data Processed", "ErrorCode" => "000"], 200);
        // }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\walletprocessed  $walletprocessed
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
     
        $vendor = $request->vid;
        $orders = DB::table("walletprocesseds")->where('walletprocesseds.vid', intval($request->vid))
        ->select("walletprocesseds.*","walletprocesseds.oid as orderno")->get();
        
        return $orders;
         
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\walletprocessed  $walletprocessed
     * @return \Illuminate\Http\Response
     */
    public function edit(walletprocessed $walletprocessed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\walletprocessed  $walletprocessed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, walletprocessed $walletprocessed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\walletprocessed  $walletprocessed
     * @return \Illuminate\Http\Response
     */
    public function destroy(walletprocessed $walletprocessed)
    {
        //
    }
}
