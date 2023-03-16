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
use App\Models\Project;
use App\Models\login_details;
use App\Models\ProjectCategory;
use Auth;
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
       ->select( "vendors.name as vendor_name", DB::raw('(CASE WHEN way_data.gateway =0  THEN "Majime Invoicing" WHEN way_data.gateway =1  THEN "Self Invoicing" END) AS invoice_detail'),"orders.oid as order_no","suborder_details.suborder_id as suborder_id",
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
      

       //    DB::raw('((suborder_details.total)-(CGST+SGST+IGST)) as Taxable_amount'),
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
   public function billing_process(Request $request){
    $intransit=$request->status;
    $vid = $request->vid;
    $order_id=DB::table('orders')
                ->where('orders.status','=',$intransit)
                ->where('orders.vid','=',$vid)
                ->get();
                // dd($order_id);
                // exit();
    $orders = array();
        foreach ($order_id as $item) array_push($orders,$item->oid);
        $billing_process=DB::table('orders','orders.oid','=',$orders)->get();
                //  dd($billing_process);
                //  exit();

        // $intransit=$request->status;
        // $data=DB::table('orders')->where('orders.status','=',$intransit)->get();
        // $billing_processed=$request->billing_processed;

        if($billing_process->billing_processed == '0')
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
        });   
           }
           else{
            echo "already data exit";
           }

        // return $data;
    }

    
}
