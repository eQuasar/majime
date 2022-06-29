<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Billings;
use App\Models\LineItems;
use App\Models\line_items_metas;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Support\Facades\DB;
use PDF;
class ProductController extends Controller {
    // public function productDetail()
    // {
    // 	//return billings::all();
    // $obj=Products::join('categories','products.id','=','categories.id')->get(['products.product_id','products.name','products.price','categories.name']);
    //     //->join('billings','billings.order_id','=','orders.oid')-
    //        return $obj;
    // }
    public function productDetail(Request $request) {
        // echo $request->vid; die;
        $vendor = $request->vid;
        // $Order=DB::table("line_items")->where('line_items.vid','=',intVal($vendor))
        // // ->get();
        $order = DB::table('line_items')->join('orders', 'orders.oid', '=', 'line_items.order_id')
        // ->distinct()
        // 					->select('line_items.*',
        // 							DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = 6726 AND line_items.vid = ".intval($vendor)." GROUP BY line_items.order_id) as quantity"))
        ->select(DB::raw('
							name,
							variation_id,
							product_id,
							SUM(quantity) as quantity,
							date_created_gmt,
							order_id
						') //,
        // DB::raw("(SELECT categories FROM products WHERE products.product_id = line_items.product_id)")
        )
        // ->leftJoin('products','products.product_id','=','line_items.product_id')
        ->where('line_items.vid', '=', intVal($vendor))->whereNotIn('line_items.variation_id', [0])->groupBy('line_items.product_id')->groupBy('line_items.variation_id')->groupBy('line_items.sku')->groupBy('line_items.name')->groupBy('orders.date_created_gmt')->groupBy('line_items.order_id')->orderBy('line_items.name')->get();
        // $order =	DB::table('line_items as s')
        //   ->leftJoin ('products as e', 'e.product_id', '=' , 's.product_id')
        //   ->select('s.name as name','s.variation_id as variation_id','s.product_id as product_id',
        //               'e.categories as categories')
        //   ->get();
        return $order;
        /*
        $users = DB::table('users')
        ->selectRaw('count(*) as user_count, status')
        ->where('status', '<>', 1)
        ->groupBy('status')
        ->get();
        */
        // $Order=DB::table("line_items")
        // 	// ->join('products','products.product_id','=','line_items.product_id')
        // 	->where('line_items.vid','=',intVal($vendor))
        // 	// ->where('products.vid','=',intVal($vendor))
        // 	// ->where('products.vid','=',)
        // 	->select("line_items.*")
        //   	     ->distinct()
        //   	     ->select('line_items.name')
        //   	     ->orderBy('line_items.name')
        // 		   // DB::raw("(select products.categories from products)"))
        // 	->get();
        // return $Order;
        // DB::table("line_items")
        // 		 ->join('products','products.product_id','=','line_items.product_id')
        // 		->where('line_items.vid','=',intVal($vendor))
        // 		->select("line_items.*","products.*")
        // 			   DB::raw("(select products.categories from products)"))
        //           ->get();
        // $results = DB::select("(SELECT products.categories FROM products WHERE products.product_id = 6699)");
        // 	var_dump($results);
        // 		$orders = DB::table('line_items')
        //         ->select("line_items.*")
        //         ->get();
        //          ->groupBy('variation_id')
        //                     DB::raw("(SELECT products.categories FROM products
        //                                 WHERE products.product_id = line_items.product_id
        //                                 GROUP BY products.id) as cat_name"))
        //          ->get();
        // 		$obj=Products::all();
        //        return $collection;
        
    }
    public function status_data(Request $request) {
        $order = DB::table('products')->distinct()->select('products.status')->get();
        return $order;
    }
    public function product_data(Request $request) {
        $order = DB::table('line_items')->distinct()->select('line_items.name')->orderBy('line_items.name')->get();
        return $order;
    }
    public function product_Order_Search(Request $request) {
        $vendor = $request->vid;
        $range = [$request->date_from, $request->date_to];
        // $order=orders::whereBetween('date_created_gmt',$range)->get();
        $order = DB::table('line_items')->join('orders', 'orders.oid', '=', 'line_items.order_id')->select(DB::raw('
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
            $order = DB::table("orders")->join('line_items', 'orders.oid', '=', 'line_items.order_id')->where('orders.vid', $request->vid)->get();
            return $order;
        } else {
            $order = DB::table("orders")->join('line_items', 'orders.oid', '=', 'line_items.order_id')->Where('line_items.name', 'like', '%' . $request->name . '%')->where('orders.vid', $request->vid)->get();
            return $order;
        }
    }
    public function product_Profile($variation_id) {
        $orderItems = DB::table("orders")->join("line_items", 'line_items.order_id', '=', 'orders.oid')->where('orders.vid', '=', intval($_REQUEST['vid']))->where('line_items.variation_id', '=', $variation_id)->where('line_items.vid', '=', $_REQUEST['vid'])->get();
        return $orderItems;
    }
    public function product_items($variation_id) {
        $orderItems = DB::table("orders")->join("line_items", 'line_items.order_id', '=', 'orders.oid')->where('orders.vid', '=', intval($_REQUEST['vid']))->where('line_items.variation_id', '=', $variation_id)->where('line_items.vid', '=', $_REQUEST['vid'])->get();
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
        $orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')->where('orders.vid', $request->vid)->where('orders.status', "dispatched")->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($request->vid) . " GROUP BY line_items.order_id) as quantity"), DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($request->vid) . " limit 1) as name"), DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($request->vid) . " limit 1) as sku"))->orderBy('oid', 'DESC')->get();
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
}
