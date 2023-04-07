<?php

namespace App\Http\Controllers;

use App\Models\Billings;
use App\Models\Vendors;
use Illuminate\Http\Request;
use App\Http\Resources\BillingResource;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File; 
use Response;
use App\Exports\product_HsnWeight_export;
use App\Models\OtherUser;
use App\Models\User;
use App\Models\pnToken;
use App\Models\Tower;
use App\Models\Orders;
use App\Models\Project;
use App\Models\BillingProcessed;
use App\Models\login_details;
use App\Models\ProjectCategory;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Resources\OtherUserResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Imports\ProjectCategoriesImport;
use Illuminate\Support\Facades\Log;



class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $billing= Billings::where('order_id','=',$request->id)->get();
        return BillingResource::collection($billing);
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
        // if($request->type == 'get')
        // {
        //     $city = City::where('state_id','=',$request->state_id)->get();
        //     return CityResource::collection($city);
        // }
        // else
        // {

        //     $request->validate([
        //         'name' => 'required',
        //         'state_id' => 'required',  
        //     ]);    
        //     $city = new City();
            
        //     $city->name = $request->name;
        //     $city->state_id = $request->state_id;
            
        //     $city->save();
        //     return response()->json(['error' => false,'data' => $city],200);
        // }    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        //
    }
    // api use billing detail
    public function billing_detail(Request $request)
    {
            $vid = $request->vid;
            ini_set('memory_limit', '-1');
            $data=DB::table('vendors')->where('vendors.id','=',$vid)
            ->join('way_data', 'way_data.vid', '=', 'vendors.id')
            ->leftjoin('orders', function($join) use($vid){
            $join->on('orders.vid','=','way_data.vid')
            ->where('orders.vid', '=', intval($vid));
            })
            ->leftjoin('suborder_details', function($join) use($vid){
                $join->on('orders.oid','=','suborder_details.order_id')
                ->where('suborder_details.vid', '=', intval($vid));
            })
            ->leftjoin('line_items', function($join) use($vid){
                $join->on('line_items.id','=','suborder_details.line_item_id')
                ->where('line_items.vid', '=', intval($vid));
            })
            ->leftjoin('products', function($join) use($vid){
                $join->on('products.product_id','=','line_items.product_id')
                ->where('products.vid', '=', intval($vid));
            })
            ->leftjoin('invoice_infos', function($join) use($vid){
                $join->on('invoice_infos.suborder_id','=','suborder_details.suborder_id')
                ->where('invoice_infos.vid', '=', intval($vid));
            }) 
            ->leftjoin('billings', function($join) use($vid){
                $join->on('billings.order_id','=','orders.oid')
                ->on('billings.vid','=', 'orders.vid') 
                ->where('billings.vid', '=', intval($vid));
            }) 
            ->leftjoin('order_reldates', function($join) use($vid){
                $join->on('order_reldates.oid','=','orders.oid')
                ->where('order_reldates.vid', '=', intval($vid));
            })   
            ->leftjoin('walletprocesseds', function($join) use($vid){
                $join->on('walletprocesseds.oid','=','orders.oid')
                ->where('walletprocesseds.vid', '=', intval($vid));
            })   
            ->leftjoin('waybill', function($join) use($vid){
                $join->on('waybill.order_id','=','walletprocesseds.oid')
                ->where('waybill.vid', '=', intval($vid));
            })   
                ->select( "vendors.name as vendor_name", 
                DB::raw('(CASE WHEN way_data.gateway =0  THEN "Majime Invoicing" WHEN way_data.gateway =1  THEN "Self Invoicing" END) AS invoice_detail'),
                "orders.oid as order_no","suborder_details.suborder_id as suborder_id",
                "suborder_details.invoice_no as invoice_no","suborder_details.created_at as invoice_date","suborder_details.total as invoice_amount"
                ,"line_items.product_id as product_id"
                ,"products.hsn_code as hsn","way_data.state as invoice_state_code_from",
                "billings.state as invoice_state_code_to",
                DB::raw('(CASE WHEN suborder_details.total<=1000 THEN "5%" WHEN suborder_details.total>1000 THEN "12%" END) AS tax_percentage'),
                DB::raw('(CASE WHEN way_data.state=billings.state THEN "0" END) AS CGST'),
                DB::raw('(CASE WHEN way_data.state=billings.state THEN "0" END) AS SGST'),
                "order_reldates.order_deldate as delivery_date",
                "walletprocesseds.date_created as wallet_process_date",
                "waybill.waybill_no as waybill_no",
                "orders.parent_id as parent_no",
                "orders.status as order_status",
                "orders.status as order_status", 
                "orders.date_created as order_date",
                "orders.date_created as order_date",
                "billings.first_name as first_name",
                "billings.last_name as second_name",
                "billings.address_1 as address",
                "billings.city as city",
                "billings.state as state",
                "billings.postcode as postcode",
                "billings.country as country_code",
                "billings.email as email",
                "billings.phone as phone",
                "orders.payment_method_title as payment_method_title",
                "orders.total as order_subtotalo_amount",
                "products.product_id as product_id",
                "products.name as product_name",
                "products.sku as product_sku",
                "products.total_sales as product_qty",
                "products.price as item_cost",
                "products.weight as product_weight",
                DB::raw('(CASE WHEN suborder_details.total<=1000 && way_data.state != billings.state THEN ((suborder_details.total)/(100+5)) WHEN suborder_details.total>1000 && way_data.state != billings.state THEN ((suborder_details.total)/(100+12)) END) AS IGST'),
                "products.weight as product_weight",
                "suborder_details.created_at as sale_return_date",
                "order_reldates.order_dispatchdate as dispatch_date",
                "order_reldates.dto_refunddate as refund_date",
                "orders.oid as parent_order_number",
                "orders.customer_note as parent_order_number",
                "orders.discount_total as cart_discount_amount",
                "orders.discount_total as wallet_used_temporary",
                "suborder_details.sale_return_order_status as sale_return_status",
                "suborder_details.customer_invoice_no as customer_invoice_no",
                DB::raw('((orders.total)-(orders.discount_total)) as order_amount'),
                DB::raw('(((orders.total)-(orders.discount_total))-(orders.discount_total)) as collectable_amount'),
                DB::raw('(CASE WHEN suborder_details.sale_return_order_status="yes" THEN (((orders.total)-(orders.discount_total))-(orders.discount_total)) WHEN suborder_details.sale_return_order_status="no" THEN "0" END) AS order_refund_amount'),
                    //DB::raw('((suborder_details.total)-(CGST+SGST+IGST)) as Taxable_amount'),
                )->get();
                $data = str_replace("null",'"NA"',$data);
                $data = json_decode($data); 
                return response()->json(['data'=>$data ,'msg' => "", "ErrorCode" => "000"], 200);
            }
            public function get_product_info()
            {
                return Excel::download(new product_HsnWeight_export, 'product_weight.xlsx');
            }
            public function product_insert(Request $request){
                $intransit=$request->status;
                // echo $intransit;
                // exit();
                $data=DB::table('orders')->where('orders.status','=',$intransit)->get();
                return $data;
            }
            public function billing_process(Request $request)
            {  
                $oldorderid =0;
                        $dtobook='dtobooked';
                        $intrans='intransit';
                        $dtointrans='dtointransit';
                        $Comple='completed';
                        $rto_del='rto-delivered';
                        $dto_ref='dto-refunded';
                        $clos='closed';
                        $dis='dispatched';
                        $del='deliveredtocust';
                        $dto_del='dtodelivered';
                        $zero='0';
                        $vid = $request->vid;
                        $date_from='2023-04-01 00:00:00';
                        $date_to='2023-04-03 00:00:00';
                        $range = [$date_from, $date_to];
                        $order_id=DB::table('orders')
                            ->leftjoin('line_items', function($join) use($vid){
                            $join->on('line_items.order_id','=','orders.oid')
                            ->where('line_items.vid', '=', intval($vid));
                            })
                            ->whereIn("orders.status",[$dtobook,$intrans,$dtointrans,$Comple,$rto_del,$dto_ref,$clos,$dis,$del,$dto_del])
                            ->whereBetween('orders.date_created_gmt',$range)
                            // ->where('orders.billing_processed','=','0')
                            // ->where('orders.oid','=','12465')
                            ->where('orders.vid','=',$vid)
                            ->orderBy('orders.oid')
                            ->select('orders.oid','line_items.quantity','line_items.line_item_id','line_items.product_id','line_items.total','orders.total as order_amount','orders.status')
                            ->get();
                            // dd($order_id);die();
                            // dd(count($order_id));die();
                            // print_r($order_id);
                        // echo "Before Billing - ".count($order_id);
                        // die();
                        // dd(count($order_id));die();
                        $duplicate = 1;
                        $billing_processdata = array();
                        for($i=0;$i<count($order_id);$i++)
                        {

                            $current_year=date("Y");
                            $orderid=$order_id[$i]->oid;
                            $j="-";
                            $order_id_concat=$orderid.$j;
                            $line_item_id=$order_id[$i]->line_item_id;
                            $line_quantity=$order_id[$i]->quantity;
                            
                            // echo "QTY- ".$line_quantity=$order_id[$i]->quantity;
                            $line_product_id=$order_id[$i]->product_id;
                            $product_detail = DB::table("products")->where('product_id','=',$line_product_id)->get();
                            // dd($product_name);die();
                            $sub_total=$order_id[$i]->total;
                            $way_data =DB::table("way_data")->where('vid','=',$vid)->get();
                            $mytime = Carbon::now()->format('Y-m-d');
                            for($k=1;$k<=$line_quantity;$k++)
                            {
                                    // if(($way_data[0]->gateway)==0)
                                    // {
                                    //     $vendor_name='MAJ';
                                    //     $vendorPrefix=strtoupper(substr($way_data[0]->order_prefix, 0, -1));
                                    //     $ven = $vendor_name.$j.$current_year.$j.$vendorPrefix.$j;    
                                    //     $vendor_invoice = $ven;
                                    // }
                                    // else{
                                        $vendorPrefix="OWR-".strtoupper(substr($way_data[0]->order_prefix, 0, -1));

                                        /* BY HS -  NEED TO CHANGE IT TO DYNAMIC FINANCIAL YEAR USING CARBON */
                                        $ven = $vendorPrefix.$j;    
                                        $vendor_invoice = $ven."23-24";
                                        // $vendor_invoice = $ven.$orderid.$j;
                                    // }
                                    $last2 = DB::table('invoice_infos')->where('vid','=',$vid)->where('invoice_no', 'like', $ven. '%')->orderBy('id', 'DESC') ->first();
                                    // die();
                                    
                                    if (isset($last2)==1){
                                        // $created_att = explode(' ',$last2->created_at);
                                    
                                        // if($created_att[0] == $mytime){
                                        //     $last_invoice = substr($last2->invoice_no,strrpos($last2->invoice_no,"-")+1);
                                        //     $new_invoice = $last_invoice;
                                        // }else{
                                            
                                            $last_invoice = substr($last2->invoice_no,strrpos($last2->invoice_no,"-")+1);
                                            $new_invoice = $last_invoice +1;
                                        // }
                                    }else{
                                        $new_invoice = 1;
                                    }
                                    // echo "--".$new_invoice."--";
                                    $vendor_invoice = $vendor_invoice.$j.$new_invoice;
                                    // die();
                                    
                                    $customer_invoice_no="-";
                                    $customer_invoice_date="-";
                                    if($orderid == $oldorderid){

                                        $lastSO = DB::table('suborder_details')->where('vid','=',$vid)->where('suborder_id', 'like', $order_id_concat. '%')->orderBy('id', 'DESC') ->first();
                                        // print_r($last2);
                                        // die();
                                        
                                        if (isset($lastSO)==1){
                                            // $created_att = explode(' ',$last2->created_at);
                                        
                                            // if($created_att[0] == $mytime){
                                            //     $last_invoice = substr($last2->invoice_no,strrpos($last2->invoice_no,"-")+1);
                                            //     $new_invoice = $last_invoice;
                                            // }else{
                                                $last_subOrder = substr($lastSO->suborder_id,strrpos($lastSO->suborder_id,"-")+1);
                                                $new_subOrder = $last_subOrder +1;
                                            // }
                                        }else{
                                            $new_subOrder = 1;
                                        }
                                        // echo $new_invoice;
                                        $suborder_id=$order_id_concat.$new_subOrder;
                                    }else{
                                        $suborder_id=$order_id_concat.$k;
                                    }
                                    
                                    
                                    DB::table('invoice_infos')->insert(['invoice_no'=>$vendor_invoice,'customer_invoice_no'=>$customer_invoice_no,'customer_invoice_date'=>$customer_invoice_date,'order_id'=>$orderid,'vid'=>$vid,'suborder_id'=>$suborder_id,'line_item_id'=>$line_item_id,'total'=>$sub_total]);
                                    DB::table('suborder_details')->insert(['order_id'=>$orderid,'vid'=>$vid,'suborder_id'=>$suborder_id,'line_item_id'=>$line_item_id,'invoice_no'=>$vendor_invoice,'total'=>$sub_total]);
                            }
                           
                            $line_item_idd = DB::table('suborder_details')->where('vid','=',$vid)->where('suborder_details.suborder_id','=',$suborder_id)->get();
                            $order_lineitem_id=$line_item_idd[0]->line_item_id;
                            $line_item_id = DB::table('line_items')->where('vid','=',$vid)->where('line_item_id', '=', $order_lineitem_id)->get();
                            $quantity=$line_item_id[0]->quantity;
                            $item_cost=$line_item_id[0]->price;
                            $get_order_id= $line_item_id[0]->order_id;
                            $get_product_id= $line_item_id[0]->product_id;
                            // dd($get_product_id);die();
                            $way_data =DB::table("way_data")->where('vid','=',$vid)->get();
// print_r($way_data);die();
                            $order_bill_processed =DB::table('orders')->where('vid','=',$vid)->where('orders.oid','=',$get_order_id)->get();
// print_r($order_bill_processed);
// die();
                            $discount_amount=$order_bill_processed[0]->discount_total;
                            $billing_processed=$order_bill_processed[0]->billing_processed;
                            $parent_order_id=$order_bill_processed[0]->parent_id;
                            $order_status=$order_bill_processed[0]->status;
                            $order_date=$order_bill_processed[0]->date_created;
                            $order_customer_note=$order_bill_processed[0]->customer_note;
                            $payment_method=$order_bill_processed[0]->payment_method_title;
                            $order_subtotal=$order_bill_processed[0]->total;
                            $cart_discount='-';
                            $coupan_discount='-';
                            $order_amount=$order_bill_processed[0]->total;
                            $product_data =DB::table("products")->where('vid','=',$vid)->where('product_id','=',$get_product_id)->get();
                            $product_name= $product_data[0]->name;
                            $product_sku= $product_data[0]->sku;
                            $product_hsn= $product_data[0]->hsn_code;
                            $product_weight= $product_data[0]->weight;
                            $vendor_name=$way_data[0]->name;
                            $hsn_code= $product_data[0]->hsn_code;

                            // dd($line_item_idd);
                            // die();
                            if($billing_processed == '0')
                            {
                                $vendor_nam = DB::table('vendors')->where('id','=',$vid)->get();
                                $vendor_namee=$vendor_nam[0]->name;
                                $way_data_gateway=$way_data[0]->gateway;
                                // if($way_data_gateway==0)
                                // {
                                //     $invoice_type='Billing to Majime';
                                // }
                                // else{
                                    $invoice_type='Billing to Customer';
                                // }



                                $invoice_date=$line_item_idd[0]->created_at;
                                $sub_orderId=$line_item_idd[0]->suborder_id;
                                $invoice_amount=$line_item_idd[0]->total;
                                $get_billing_data =DB::table("billings")->where('vid','=',$vid)->where('order_id','=',$get_order_id)->get();
                                $order_from=$way_data[0]->state;
                                $order_to=$get_billing_data[0]->state;
                                $first_name=$get_billing_data[0]->first_name;
                                $last_name=$get_billing_data[0]->last_name;
                                $address=$get_billing_data[0]->address_1;
                                $city=$get_billing_data[0]->city;
                                $state=$get_billing_data[0]->state;
                                $postcode=$get_billing_data[0]->postcode;
                                $email=$get_billing_data[0]->email;
                                $phone=$get_billing_data[0]->phone;
                                $country=$get_billing_data[0]->country;
                                if($invoice_amount<=1000)
                                {
                                    $tax_percentage="5";
                                }
                                else
                                {
                                    $tax_percentage="12";
                                }
                                if($order_bill_processed[0]->status=='rto-delivered')
                                {
                                    $refund_amount=  $invoice_amount;
                                }
                                if($order_bill_processed[0]->status=='dto-refunded')
                                {
                                    $refund_amount=  $invoice_amount;
                                }
                             
                                // if($order_from==$order_to)
                                // {
                                //     $total_cgst=($invoice_amount)*($cgst/100);
                                //     $total_sgst=($invoice_amount)*($cgst/100);
                                //     $igst='0';
                                //     // $round_sgst=round($total_sgst,2);                                  }
                                // else
                                // {
                                //     $igst=($invoice_amount)/(100+($tax_percentage));
                                //     $total_cgst='0';
                                //     $total_sgst='0';
                                //     // dd($igst);die();
                                // }
                                $sum_qty = DB::table("line_items")->select(DB::raw("(SELECT SUM(quantity) FROM line_items WHERE line_items.order_id = ".intval($get_order_id)." AND line_items.vid = " . intval($vid) . ") as val"))->first();
                                $sum_total = DB::table("line_items")->select(DB::raw("(SELECT SUM(total) FROM line_items WHERE line_items.order_id = ".intval($get_order_id)." AND line_items.vid = " . intval($vid) . ") as val"))->first();

                                    // print_r($sum_qty->val);
                                    // die();

                                $orderTotalAmt = DB::table("line_items")->select(DB::raw("(SELECT SUM(line_items.total) FROM line_items WHERE line_items.order_id = ".intval($get_order_id)." AND line_items.vid = " . intval($vid) . " GROUP BY line_items.order_id) as total_main"))->first();

                                $amtPaid=$order_bill_processed[0]->total;
                                $sale_Amount = $orderTotalAmt->total_main;
                                $walletUsedAmt = $sale_Amount - $amtPaid;
                                

                                $shippingCharges = 0;
                                // here we need to minus actual wallet used from below:
                                $cartDisc = ((int)$walletUsedAmt/(int)$sum_total->val)*(int)$item_cost;
                                $discount_amount = ((int)$discount_amount/(int)$sum_total->val)*(int)$item_cost;
                                $inv_amt = (int)$item_cost - ((int)$cartDisc + (int)$discount_amount) - $shippingCharges;

                                    if($order_from == $order_to)
                                    {
                                        $total_cgst=($inv_amt)/($tax_percentage+100)*($tax_percentage/2);
                                        $total_sgst=($inv_amt)/($tax_percentage+100)*($tax_percentage/2);
                                        $igst='0';
                                    }
                                    else{
                                        $total_cgst=0;
                                        $total_sgst=0;
                                        $igst= (($inv_amt)/($tax_percentage+100))*($tax_percentage/2);
                                    }
                                    // echo $invoice_amount."--".$total_cgst;
                                    
                                


                                $taxable_amount=$inv_amt-($total_cgst+ $total_sgst+$igst);
                                $order_related_dates =DB::table("order_reldates")->where('vid','=',$vid)->where('oid','=',$get_order_id)->get();
                                if($order_related_dates->isEmpty())
                                {
                                    $delivery_date='-';
                                    $dispatch_date='-';
                                    $refund_date='-';
                                    $sale_return_date='-';
                                }
                                else{
                                $delivery_date= $order_related_dates[0]->order_deldate;
                                $dispatch_date= $order_related_dates[0]->order_dispatchdate;
                                $refund_date= $order_related_dates[0]->dto_refunddate;
                                $sale_return_date=$order_related_dates[0]->rto_deldate;;
                                }
                                
                                if($refund_date==null)
                                {
                                    $sale_return_status='No';
                                }
                                else
                                {
                                    $sale_return_status='Yes';
                                }
                                $wallet_processed =DB::table("walletprocesseds")->where('vid','=',$vid)->where('oid','=',$get_order_id)->get();
                                if (count($wallet_processed) >1){
                                    $wallet_processed_date= $wallet_processed[0]->date_created;
                                    $wallet_used= $wallet_processed[0]->Wallet_used;
                                    $collectable_amount=$order_amount-$wallet_used;
                                }else{
                                    $wallet_processed_date= "";
                                    $wallet_used= "";
                                    $collectable_amount=$order_amount;
                                }          
                                $way_bill =DB::table("waybill")->where('vid','=',$vid)->where('order_id','=',$get_order_id)->get();
                                // echo "\n\nCount  - ".count($way_bill);
                                if (count($way_bill)>1){
                                    $Way_bill_no=$way_bill[0]->waybill_no;
                                }else{
                                    $Way_bill_no= "";
                                }
                                // echo $vendor_invoice."|||";
                                $billing_processdata[]=[     
                                    'vendor_name'=>$vendor_namee,
                                    'vid'=>$vid,
                                    'invoicing_type'=>$invoice_type,
                                    'invoice_no'=> $vendor_invoice,
                                    'customer_invice_no'=>'-',
                                    'customer_invoice_date'=>'-',
                                    'sub_order_id'=> $sub_orderId,
                                    'textable_amount'=>$taxable_amount ?? 'NA',
                                    'igst'=>$igst,
                                    'sgst'=>$total_sgst,
                                    'cgst'=>$total_cgst,
                                    'invoice_amount'=>$inv_amt,
                                    'hsn_code'=>$hsn_code,
                                    'text_percentage'=>$tax_percentage,
                                    'dispatch_date'=> $dispatch_date ?? '-',
                                    'order_from'=>$order_from,
                                    'order_to'=>$order_to,
                                    'delivered_date'=> $delivery_date ?? '-',
                                    'dto_booked_date'=>$data[$i]->dto_booked_date ?? '-',
                                    'dto_delivered_to_warhouse_date'=>$data[$i]->dto_delivered_to_warhouse_date ?? '-',
                                    'sale_return_status'=>$sale_return_status,
                                    'sale_return_date'=>'-',
                                    'refund_date'=> $refund_date,
                                    'wallet_procesed_date'=> $wallet_processed_date,
                                    'waybill_no'=>$Way_bill_no,
                                    'parent_order_number'=>$orderid,
                                    'order_status'=>$order_status,
                                    'order_date'=>$order_date,
                                    'customer_note'=>$order_customer_note,
                                    'first_name'=>$first_name,
                                    'last_name'=>$last_name,
                                    'address'=> $address,
                                    'post_code'=>$postcode,
                                    'country_code'=>$country,
                                    'city'=>$city,
                                    'state'=>$state,
                                    'email'=>$email,
                                    'phone'=>$phone,
                                    'pay_method_title'=>$payment_method,
                                    'order_subtotal_amount'=>$order_subtotal,
                                    'status'=>$order_status,
                                    'cart_discount_amount'=>$discount_amount,
                                    'coupon_discount'=>'-',
                                    'order_amount'=>$order_subtotal,
                                    'collectable_amount'=>$collectable_amount,
                                    'orderrefund_amount'=>'-',
                                    'product_id'=>$get_product_id,
                                    'product_name'=>$product_name,
                                    'sku'=>$product_sku,
                                    'product_qty'=>$quantity,
                                    'item_cost'=>$item_cost ,
                                    // 'coupon_code'=>$data[$i]->coupon_code ?? '1',
                                    'product_weight'=>$product_weight,
                                ];    
                                // $oid=$order_id[$i]->oi    
                               
                            }           
                            else{
                                return response()->json(['error' => false,'msg' => "Already Processed", "ErrorCode" => "000"], 200); 
                            }
                            $oldorderid = $orderid;
                            
                        }
                        BillingProcessed::insert($billing_processdata);
                        DB::table('orders')->where('orders.oid','=', $orderid)->update(["billing_processed" => "1"]);
                        return response()->json(['error' => false,'msg' => "Billing Processed Successfully", "ErrorCode" => "000"], 200); 
            }
          
          //return response()->json(['data'=>$data ,'msg' => "", "ErrorCode" => "000"], 200); 
   //   api use return billing process according vid and billing_processeds(table)
    public function return_billing_process(Request $request){
      $vid=$request->vid;
        $retun_billing_processer_data = DB::table('billing_processeds')->where('vid','=',$vid)->get();
        return $retun_billing_processer_data;
    }
    public function update_dispatch_date(Request $request)
    {
     $vid=$request->vid;
     $dtobook='dtobooked';
     $intrans='intransit';
     $dtointrans='dtointransit';
     $Comple='completed';
     $rto_del='rto-delivered';
     $dto_ref='dto-refunded';
     $clos='closed';
     $dis='dispatched';
     $del='deliveredtocust';
     $dto_del='dtodelivered';
     $zero='0';
     $vid = $request->vid;
     $date_from='2023-04-01 00:00:00';
     $date_to='2023-04-03 00:00:00';
     $range = [$date_from, $date_to];
     $order_id=DB::table('orders')
         ->leftjoin('line_items', function($join) use($vid){
         $join->on('line_items.order_id','=','orders.oid')
         ->where('line_items.vid', '=', intval($vid));
         })
         ->whereIn("orders.status",[$dtobook,$intrans,$dtointrans,$Comple,$rto_del,$dto_ref,$clos,$dis,$del,$dto_del])
         ->whereBetween('orders.date_created_gmt',$range)
         // ->where('orders.billing_processed','=','0')
         ->where('orders.vid','=',$vid)
         ->orderBy('orders.oid')
         ->select('orders.oid','line_items.quantity','line_items.line_item_id','line_items.product_id','line_items.total','orders.total as order_amount','orders.status')
         ->pluck('oid')->toArray();
        
     

    }



}
