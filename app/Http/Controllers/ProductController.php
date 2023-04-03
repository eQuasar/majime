<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Billings;
use App\Models\LineItems;
use App\Models\line_items_metas;
use App\Models\product_variation;
use App\Models\Orders;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;
use Input;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller {

    public function productDetail(Request $request)
     {
        $vendor = $request->vid;
        $order=DB::table('products')->where('vid','=',$vendor)->get();
        // $order = DB::table('line_items')->join('orders', function($join) use ($vendor)
        // {
        //     $join->on('orders.oid', '=', 'line_items.order_id')
        //          ->where('orders.vid', '=', intval($vendor));
        // })
        // // ->distinct()
        // // 					->select('line_items.*',
        // // 							DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = 6726 AND line_items.vid = ".intval($vendor)." GROUP BY line_items.order_id) as quantity"))
        // ->select(DB::raw('
		// 					name,
		// 					variation_id,
		// 					product_id,
		// 					SUM(quantity) as quantity,
		// 					date_created_gmt,
		// 					order_id
		// 				') //,
        // // DB::raw("(SELECT categories FROM products WHERE products.product_id = line_items.product_id)")
        // )
        // // ->leftJoin('products','products.product_id','=','line_items.product_id')
        // ->where('line_items.vid', '=', intval($vendor))->whereNotIn('line_items.variation_id', [0])->groupBy('line_items.product_id')->groupBy('line_items.variation_id')->groupBy('line_items.sku')->groupBy('line_items.name')->groupBy('orders.date_created_gmt')->groupBy('line_items.order_id')->orderBy('line_items.name')->get();
        // // $order =	DB::table('line_items as s')
        // //   ->leftJoin ('products as e', 'e.product_id', '=' , 's.product_id')
        // //   ->select('s.name as name','s.variation_id as variation_id','s.product_id as product_id',
        // //               'e.categories as categories')
        // //   ->get();
        return $order;
    }
    public function status_data(Request $request) {
        $order = DB::table('products')->distinct()->select('products.status')->get();
        return $order;
    }
    public function product_data(Request $request) {
       
        $order = DB::table('products')->where('products.vid','=',$request->vid)->select('products.name')->orderBy('products.name')->get();
        return $order;
    }
    public function product_Order_Search(Request $request) {
        $vendor = $request->vid;
        $range = [$request->date_from, $request->date_to];
        // $order=orders::whereBetween('date_created_gmt',$range)->get();
        $order = DB::table('line_items')->join('orders', function($join) use ($vendor)
        {
            $join->on('orders.oid', '=', 'line_items.order_id')
                 ->where('orders.vid', '=', intval($vendor));
        })->select(DB::raw('
							name,
							variation_id,
							product_id,
							SUM(quantity) as quantity,
							date_created_gmt
						') //,
        // DB::raw("(SELECT categories FROM products WHERE products.product_id = line_items.product_id)")
        )
        // ->where('line_items.vid','=',intVal($vendor))
        ->whereNotIn('line_items.variation_id', [0])->groupBy('line_items.product_id')->groupBy('line_items.variation_id')->groupBy('line_items.sku')->groupBy('line_items.name')->whereBetween('orders.date_created_gmt', $range)->groupBy('orders.date_created_gmt')->orderBy('orders.date_created_gmt', 'ASC')->get();
        return $order;
    }
    public function category_Search(Request $request) {
        // $order=orders::whereBetween('date_created_gmt',$range)->get();
        $order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')->Where('category', 'like', '%' . $request->category . '%')->get();
        // ->select("orders.*","billings.*","line_items.*",
        //           DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
        //                       WHERE line_items.order_id = orders.oid
        //                       GROUP BY line_items.order_id) as quantity"))
        // ->get();
        return $order;
    }
    public function product_search(Request $request) {
        if ($request->name == "allproducts") {
            // $order = DB::table("orders")->join('line_items', 'orders.oid', '=', 'line_items.order_id')->where('orders.vid', $request->vid)->get();
            $order = DB::table("products")->where('vid', $request->vid)->get();
            return $order;
        } else {
            // $order = DB::table("orders")->join('line_items', 'orders.oid', '=', 'line_items.order_id')->Where('line_items.name', 'like', '%' . $request->name . '%')->where('orders.vid', $request->vid)->get();
            $order = DB::table("products")->Where('categories', 'like', '%' . $request->name . '%')->where('vid', $request->vid)->get();

            return $order;
        }
    }
    public function product_Profile($pro_id) {
        $orderItems = DB::table("orders")->join("line_items", 'line_items.order_id', '=', 'orders.oid')->where('orders.vid', '=', intval($_REQUEST['vid']))->where('line_items.variation_id', '=', $pro_id)->where('line_items.vid', '=', $_REQUEST['vid'])->get();
        return $orderItems;
    }
    public function product_items($pro_id) {

         $orderItems = DB::table("product_variations")->join('product_variation_images','product_variation_images.variation_id','=','product_variations.variation_id')
   ->join('product_variation_attributes','product_variation_attributes.variation_id','=','product_variations.variation_id')
        ->where('product_variations.product_id', '=', $pro_id)
        ->where('product_variation_images.product_id', '=', $pro_id)
        ->where('product_variations.vid', '=', intval($_REQUEST['vid']))
        ->where('product_variation_images.vid', '=', intval($_REQUEST['vid']))
        // ->select('product_variations.product_id','product_variations.vid','product_variation_images.src','product_variations.variation_id','product_variations.sku','product_variations.stock_quantity','product_variations.stock_status','product_variations.price','product_variations.tax_status')
        ->distinct()
        ->get();
        return $orderItems;
    }
    public function product_detail($pro_id) {
        $orderItems = DB::table("products")->where('vid', '=', intval($_REQUEST['vid']))->where('product_id', '=', $pro_id)->get();
        return $orderItems;
    }
    public function color_Search(Request $request) {
        // $order=orders::whereBetween('date_created_gmt',$range)->get();
        $order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')->Where('color', 'like', '%' . $request->color . '%')->get();
        // ->select("orders.*","billings.*","line_items.*",
        //           DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
        //                       WHERE line_items.order_id = orders.oid
        //                       GROUP BY line_items.order_id) as quantity"))
        // ->get();
        return $order;
    }
    public function getDelivery_Details(Request $request) {
        $vid = $request->vid;
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->join('waybill', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'waybill.order_id')
                 ->where('waybill.vid', '=', intval($vid));
        })
        ->where('orders.vid', $request->vid)->where('orders.status', "dispatched")->select("orders.*", "billings.*","waybill.waybill_no", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($request->vid) . " GROUP BY line_items.order_id) as quantity"), DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($request->vid) . " limit 1) as name"), DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($request->vid) . " limit 1) as sku"))->orderBy('oid', 'DESC')->get();
        return $orders;
    }
    function product_Sheet_download(Request $request) {
        if ($request->selectall) {
            $listImp = explode(',', $request->selectall);
            $orderItems[] = DB::table("line_items")->whereIn('line_items.variation_id', $listImp)->select("line_items.sku as SKU", "line_items.name as Name", "line_items.quantity as Qty", "line_items.parent_name as Parent", "line_items.variation_id as VariationID")->where('vid', intval($request->vid))->get();
        } else {
            $orderItems[] = DB::table("line_items")
            // ->whereIn('line_items.variation_id',$listImp)
            ->where('vid', intval($request->vid))->get();
        }
        return $orderItems;
    }
    function stat($vid) {
        
        $dtobook='dtobooked';
        $intrans='intransit';
        $dtointrans='dtointransit';
        $Comple='completed';
        $rto_del='rto-delivered';
        $dto_ref='dto-refunded';
        $clos='closed';
        $process='processing';
        $confirm='confirmed';
        $pack='packed';
        $hold='on-hold';
        $dis='dispatched';
        $del='deliveredtocust';
        $dto_del='dtodelivered';
        $int_check = 0;
        $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dtobook,$intrans,$dtointrans,$dis])->get();
        $intransit_saleAmount=$orders->sum('total');
        $order_count=count($orders);
        $unprocessed_orders=DB::table("orders")->where('orders.vid', $vid)->where('orders.wallet_processed', $int_check)->whereIn('orders.status',[$dto_del,$del])->get();
        $unprocessed_saleAmount=$unprocessed_orders->sum('total');
        $unprocess_count=count($unprocessed_orders);
        $due_amount=DB::table("opening_closing_tables")->where('opening_closing_tables.vid','=',$vid)->orderBy('id', 'DESC')->first();
        $due_closing_bal=$due_amount->closing_bal;
        $tomorrow = date("Y-m-d", strtotime('tomorrow'));
        $pending_disptach=DB::table("orders")->where('orders.vid', $vid)->whereIn('orders.status',[$process,$confirm,$pack,$hold])->get();
        $pending_saleAmount=$pending_disptach->sum('total');
        $pending_count=count($pending_disptach);
        $stats['inTransitCount'] = $order_count;
        $stats['inTransitSaleAmount'] = $intransit_saleAmount;
        $stats['unProcessedCount'] =  $unprocess_count;
        $stats['unProcessedSaleAmount'] =  $unprocessed_saleAmount;
        $stats['nextDate'] = $tomorrow;
        $stats['dueAmount'] =$due_closing_bal;
        $stats['pendingDispatch'] = $pending_count;
        $stats['pendingDispatchAmount'] =$pending_saleAmount;
        return $stats;
        
    }

    function dispatched_order_toclose($vid) 
    {
        $del='deliveredtocust';
        $clos='closed';
        //get all orders with deltocustomer status
        $orders=DB::table("orders")->where('orders.vid','=',$vid)->where("orders.status",'=',$del)->get();
        //using for loop for more than 1 orders
        for($x=0;$x<count($orders);$x++) 
        {   
            //get current date
            $current_date=Carbon::parse(date('Y-m-d')); 
            //get dispatched date 
            $order_date=DB::table("order_reldates")->where('order_reldates.oid','=',intval($orders[$x]->oid))->where('order_reldates.vid','=',$vid)->get(); 
            $order_confirm_date=$order_date[0]->order_dispatchdate;
            //calculate days from current date to dispatched date
            $days=$current_date->diffInDays($order_confirm_date);
            //using if days above 15 days close all orders
                if($days>15)
                {
                    //above 15 days update status delivertocust to closed
                DB::table('orders')->where('orders.oid', intval($orders[$x]->oid))->where('orders.vid', $vid)->update(['status' => $clos]);
                }
        }
        //print message 
        return response()->json([ 'msg' => "orders closed Successfully"]);
    }
    public function update_product_detail(Request $request)
    {
        $data=DB::table('products')->where('product_id', intval($request->product_id))->where('products.vid', $request->vid)
        ->update(['name' => $request->product_name,'hsn_code'=>$request->hsn,'weight'=>$request->weight,'slug'=>$request->slug,'sku'=>$request->sku,'price'=>$request->price,'categories'=>$request->categories]);
          return response()->json([ 'msg' => "Update Successfully"]);
    }
    public function getProductVariation_detail(Request $request)
    {
        $data=DB::table('product_variations')
        ->join('product_variation_attributes','product_variations.variation_id','=','product_variation_attributes.variation_id')
        ->where('product_variations.product_id', intval($request->product_id))->where('product_variations.variation_id', intval($request->variation_id))->where('product_variations.vid', $request->vid)->get();
         return response()->json(['data'=>$data]);

    }
    public function update_productVariation_detail(Request $request)
    {
        $data=DB::table('product_variations')->where('product_id', intval($request->product_id))->where('product_variations.vid', $request->vid)->where('product_variations.variation_id', $request->variation_id)
        ->update(['sku' => $request->sku,'stock_status'=>$request->stock_status,'price'=>$request->price,'tax_status'=>$request->tax_status,'description'=>$request->description]);
          return response()->json([ 'msg' => "Update Successfully"]);
    }
    public function get_category(Request $request)
    {
        $data=DB::table('products')->where('vid', intval($request->vid))->distinct()->select('products.categories')->get();
        return $data;
    }
    public function import_product_info(Request $request)
    {
        $data_file=$request->file;
        //     $uploaded_files = $data_file->store("Users/eq-mini/Documents");
        // print_r($uploaded_files);
        // exit();
     //Excel::import(new ProductsImport,$request->file('file'));
     //Excel::import(new ProductsImport, $request->file('select_users_file'));
         Excel::import(new ProductsImport,$data_file);
         return response()->json(['msg' => "File Upload Successfully", "ErrorCode" => "000"], 200); 
        //  return redirect()->back();
    
    }
   
    //14.3.2023
    public function category(Request $request){
        $category = DB::table("categories")->where('vid', intval($request->vid))->get();
        return $category; 

    }
    public function get_import_data(Request $request){
       
        $import_data = DB::table("products")->where('vid', intval($request->vid))->select('product_id','cost','name','hsn_code','weight','categories','vid')->get();
        return $import_data; 

    }
}
