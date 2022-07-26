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
           

        $pw=0;
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
            $order_table=DB::table("orders")->where('orders.oid','=',intval($orders[$y]))->where('orders.vid','=',$vendor)->get();
            if($order_table[0]->status== 'completed')
            {
                $mystatus= 'Delivered';
            }
            elseif($order_table[0]->status== 'rto-delivered')
            {
                $mystatus= 'RTO';
            }
            elseif($order_table[0]->status== 'dto-refunded')
            {
                $mystatus= 'DTO';
            }
            else
            {
                $mystatus= 'Delivered';
            }
            $data=DB::table("billings")->where('billings.order_id','=',intval($orders[$y]))->where('billings.vid','=',$order_table[0]->vid)->get();
            // $zone=DB::table("zonedetails")->where('zonedetails.pincode','=',$data[0]->postcode)->get();
            $origin_pin=141001;
            $curl = curl_init();
            curl_setopt_array($curl, array(
            // CURLOPT_URL => 'https://track.delhivery.com/api/kinko/v1/invoice/charges/.json?md=S&cgm=250&o_pin=141001&d_pin=110011&ss=Delivered',
            CURLOPT_URL => 'https://track.delhivery.com/api/kinko/v1/invoice/charges/.json?md=S&cgm=250&o_pin=141001&d_pin=' .$data[0]->postcode. '&ss=' .$mystatus,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token ed99803a18868406584c6d724f71ebccc80a89f9'
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $jsonResp = json_decode($response);
            $zone=$jsonResp[0]->zone;
            $zone=substr($zone, 0, 1);
		// var_dump($jsonResp[0]->zone);die();

            //     curl_close($curl);
            // $jsonResp = json_decode($response);
            // foreach ($jsonResp as $jp) {
            //     // $curl_data[] = $jp->id;
            //     $get_zone=$jp->zone;
            // }
            $zone_rate=DB::table("zoneratecards")->where('zoneratecards.zoneno','like',$zone)->get();
            $line_items=DB::table("line_items")->where('line_items.order_id','=',intval($orders[$y]))->where('line_items.vid','=',$order_table[0]->vid)->get();
            $line_items_qty=DB::table("line_items")->select(DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = $orders[$y] AND line_items.vid = " . intval($order_table[0]->vid) . " GROUP BY line_items.order_id) as quantity"))->get(); 
            $product_weight=DB::table("products")->where('products.product_id','=',$line_items[0]->product_id)->get();  
            $vendor_rate=DB::table("vendor_ratecards")->where('vendor_ratecards.vid','=',$order_table[0]->vid)->get();
            $sms_cost=$vendor_rate[0]->sms_charges;
            $mjm_cost=$vendor_rate[0]->majime_charges; 
            $above_value=$vendor_rate[0]->after500gm;
            $oc_balance=DB::table("opening_closing_tables")->where('opening_closing_tables.vid','=',$order_table[0]->vid)->orderBy('id','DESC')->limit(1)->get();
            $sale_Amount=$order_table[0]->total;
            $majime_cost=(($order_table[0]->total)*($mjm_cost/100));
            if($order_table[0]->payment_method == 'cod')
            {
                $payment_gateway=0;
            }else{
                $payment_gateway=(($order_table[0]->total)*2.36/100);
            }
             
            if(count($oc_balance) >= 1){
                $opening_balance=$oc_balance[0]->closing_bal;
            }else{
                $opening_balance=0;
            }
//              print_r($zone); 
//              die;
            if($mystatus= 'Delivered')
            {
                $zone_price=$zone_rate[0]->fwd;
            }
            elseif($mystatus= 'DTO')
            {
                $zone_price=$zone_rate[0]->dto;
            }
            elseif($mystatus= 'RTO')
            {
                $zone_price=$zone_rate[0]->rto;
            }
            // if($order_table[0]->payment_method == 'cod'){
            //     // If wallet is in COD order you can use here
            //     $payment_gateway=0;
               
            // }else if($order_table[0]->payment_method == 'cashfree' || $order_table[0]->payment_method == 'prepaid'){
            //     // If wallet is in Prepaid order you can use here
            //         $payment_gateway=(($order_table[0]->total)*2/100);
            //         $cod_charges = 0;
            // }
          
            if($order_table[0]->status != 'rto-delivered')
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
                    //  $qty=$line_items[0]->quantity; 
                    $qty=$line_items_qty[0]->quantity;
                    $total_weight=($pw)*($qty);
                    if($total_weight>500)
                    {
                        // $cod_cost=$vendor_rate[0]->cod;
                        $cod_cost=$zone_price;
                        $percent_price=(int)($total_weight/500);
                        $pp_amount=($cod_cost*($above_value/100))*$percent_price;
                        $logistic_cost=$cod_cost+$pp_amount+$cod_charges;
                    }
                    else{
                        $logistic_cost=$vendor_rate[0]->cod+($vendor_rate[0]->cod*2/100);
                    }
                    // if($order_table[0]->status== 'dto-refunded' || $order_table[0]->status== 'dto-refunded'){
                       
                    //     $net_amount=$sale_Amount-($logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                    //     $closing_bal=$opening_balance+$net_amount;
                    // }else{
                        
                        $net_amount=$sale_Amount-($logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                        $closing_bal=$opening_balance+$net_amount;
                    // }
                }
                else{
                   
                    $Vcodper = $vendor_rate[0]->codper;
                    $getval = ceil($sale_Amount*($Vcodper/100));;
                    if($getval > $zone_price){
                        $cod_charges = $getval;
                    }
                    // $pw=$product_weight[0]->weight;
                    if($pw==0)
                    {   
                        $pw=250;
                    }
                    $qty=$line_items_qty[0]->quantity;
                    $total_weight=($pw)*($qty);
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
                    $net_amount=$sale_Amount-($logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                    $closing_bal=$opening_balance+$net_amount;
                    // if($order_table[0]->status== 'dto-refunded'){
                    //     $net_amount=$logistic_cost+$majime_cost+$sms_cost+$payment_gateway;
                    //     $closing_bal=$opening_balance-$net_amount;
                    // }else{
                    //     $net_amount=$sale_Amount-($logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                    //     $closing_bal=$opening_balance-$net_amount;
                    // }
                }
            }else{
                $logistic_cost=$zone_price;
                // $sms_cost=2*2;       
                // $net_amount=$logistic_cost+$sms_cost;
                $majime_cost=0;
                $net_amount=0-($logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                $closing_bal=$opening_balance+$net_amount;
                
            }
            // echo $cod_cost;
            // echo $pp_amount;
            // echo $logistic_cost;
            // echo $percent_price;

            $wallet=$request->walletvalue;
            DB::table('orders')->where('orders.oid', intval($orders[$y]))->where('vid', intval($request->vid))->update(['wallet_processed' => $wallet]);
            $Wallet_order_data[]=[     
                'date_created'=>$order_table[0]->date_created,
                'transaction_id'=>"N/A",
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
        return response()->json(['error' => false, 'msg' => "Wallet Processed Successfully", "ErrorCode" => "000"], 200);
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
        $order = DB::table("walletprocesseds")->where('walletprocesseds.vid', intval($request->vid))->select("walletprocesseds.*","walletprocesseds.oid as orderno")->get();
        $Clos = $order->last();
        $Closing_balance=$Clos->current_wallet_bal;
        $open=$order[0]->current_wallet_bal;
        $opening_data = DB::table("opening_closing_tables")->where('opening_closing_tables.closing_bal','=', $open)->get();
        $opening_balance=$opening_data[0]->opening_bal;
    
        return response()->json([ 'order'=> $order,'closing_bal'=> $Closing_balance,'opening_bal'=> $opening_balance], 200);
         
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
