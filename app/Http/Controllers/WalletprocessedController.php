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
        //if single value passed 
        if ($request->allSelected == "false")
        {
            $orders[]=$request->oid;
        }
        // multiple value passed through checkbox
        else
        {
            $orders=explode(',', $request->allSelected);
            
        }
        
        $vendor = $request->vid;
        //using for loop for more than one order 
        for($y = 0;$y < count($orders);$y++) 
        {
            $order_table=DB::table("orders")->where('orders.oid','=',intval($orders[$y]))->where('orders.vid','=',$vendor)->get();
            //assign order status 
            if($order_table[0]->status== 'completed' || $order_table[0]->status== 'closed')
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
            $origin_pin=141001;
            //get API through Zone 
            $curl = curl_init();
            curl_setopt_array($curl, array(
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
            //Get Rate List according to Zone no 
            $zone_rate=DB::table("zoneratecards")->where('zoneratecards.zoneno','like',$zone)->where('zoneratecards.vid','=',$order_table[0]->vid)->get();
            //Get Line Items through order id 
            $line_items=DB::table("line_items")->where('line_items.order_id','=',intval($orders[$y]))->where('line_items.vid','=',$order_table[0]->vid)->get();
            //Get quantity from line items
            $line_items_qty=DB::table("line_items")->select(DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = $orders[$y] AND line_items.vid = " . intval($order_table[0]->vid) . " GROUP BY line_items.order_id) as quantity"))->get(); 
            //Get Product Weight through product id 
            $product_weight=DB::table("products")->where('products.product_id','=',$line_items[0]->product_id)->get();  
            //Get rate card according to the Vendor 
            $vendor_rate=DB::table("vendor_ratecards")->where('vendor_ratecards.vid','=',$order_table[0]->vid)->get();
            //SMS Charges according to the vendor 
            $sms_cost=$vendor_rate[0]->sms_charges;
            //Majime Cost according to the vendor
            $mjm_cost=$vendor_rate[0]->majime_charges; 
            //if weight is above 500g charges accordingly 
            $above_value=$vendor_rate[0]->after500gm;
            $oc_balance=DB::table("opening_closing_tables")->where('opening_closing_tables.vid','=',$order_table[0]->vid)->orderBy('id','DESC')->limit(1)->get();
            
            // Value total of all line items clubbed together to make order total
            $orderTotalAmt = DB::table("line_items")->select(DB::raw("(SELECT SUM(line_items.total) FROM line_items WHERE line_items.order_id = ".intval($orders[$y])." AND line_items.vid = " . intval($order_table[0]->vid) . " GROUP BY line_items.order_id) as total_main"))->first();

            $amtPaid=$order_table[0]->total;
            $sale_Amount = $orderTotalAmt->total_main;
            $walletUsedAmt = $sale_Amount - $amtPaid;
            
            //calculate majime cost 
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
            if($mystatus== 'Delivered')
            {
                $zone_price=$zone_rate[0]->fwd;
            }
            elseif($mystatus== 'DTO')
            {
                $zone_price=$zone_rate[0]->fwd;
                $zone_price=$zone_price+$zone_rate[0]->dto;
            }
            elseif($mystatus== 'RTO')
            {
                $zone_price=$zone_rate[0]->fwd;
                $zone_price=$zone_price+$zone_rate[0]->rto;
            }

            $zone_price = $zone_price*1.18;
            // if order status is dto-refunded or completed
            if($order_table[0]->status != 'rto-delivered')
            {   
                    //caluclate logistic cost if payment method id COD
                if($order_table[0]->payment_method == 'cod')
                {   
                    $Vcodper = $vendor_rate[0]->codper;
                    $getval = $sale_Amount*($Vcodper/100);
                    $cod_charges = $vendor_rate[0]->cod;
                    if($getval > $vendor_rate[0]->cod){
                        $cod_charges = $getval;
                    }
                    $pw=$product_weight[0]->weight;
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
                        $pp_amount=($cod_cost*($above_value/100))*$percent_price;
                        $logistic_cost=$cod_cost+$pp_amount+$cod_charges;
                    }
                    else{
                        $cod_cost=$zone_price;
                        $logistic_cost=$cod_cost+$cod_charges;
                    }                        
                        $net_amount=$sale_Amount-($walletUsedAmt+$logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                        $closing_bal=$opening_balance+$net_amount;
                }
                //caluclate logistic cost if payment method is prepaid
                else{
                    $cod_charges = 0;
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
                        $cod_cost=$zone_price;
                        $logistic_cost=$cod_cost+$cod_charges;
                    }
                    $net_amount=$sale_Amount-($walletUsedAmt+$logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                    $closing_bal=$opening_balance+$net_amount;
                   
                }
            }else{
                //order status is rto-delivered
                $logistic_cost=$zone_price;
                $majime_cost=0;
                $net_amount=0-($walletUsedAmt+$logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                $closing_bal=$opening_balance+$net_amount;
                
            }
            $wallet=$request->walletvalue;
            //insert values into Wallet Processed table into database
            $Wallet_order_data[]=[     
                'date_created'=>$order_table[0]->date_created,
                'transaction_id'=>"N/A",
                'oid'=>$order_table[0]->oid,
                'vid'=>$request->vid,
                'payment_mode'=>$order_table[0]->payment_method,
                'status'=>$order_table[0]->status,
                'sale_amount'=>$sale_Amount,
                'Wallet_used'=>$walletUsedAmt,
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
            // insert values into opening closing table into database  
            DB::table('opening_closing_tables')->insert(
                ['vid' => $request->vid, 'opening_bal' => $opening_balance, 'closing_bal' => $closing_bal, 'created_at' => date('Y-m-d h:m:s'), 'updated_at' => date('Y-m-d h:m:s')]
            );
            // update order table with wallet processed 
            DB::table('orders')->where('orders.oid', intval($orders[$y]))->where('vid', intval($request->vid))->update(['wallet_processed' => $wallet]);
        }   
        return response()->json(['error' => false, 'msg' => "Wallet Processed Successfully", "ErrorCode" => "000"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\walletprocessed  $walletprocessed
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //get wallet processed table
        $order = DB::table("walletprocesseds")->where('walletprocesseds.vid', intval($request->vid))->select("walletprocesseds.*","walletprocesseds.oid as orderno")->get();
        //get last element of array 
        $Clos = $order->last();
        //calculate closing values
        $Closing_balance=$Clos->current_wallet_bal;
        $open=$order[0]->current_wallet_bal;
        //get opening balance 
        $opening_data = DB::table("opening_closing_tables")->where('opening_closing_tables.vid', intval($request->vid))->where('opening_closing_tables.closing_bal','like', $open)->get();
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
