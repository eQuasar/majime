<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Orders;
use App\Models\billings;
use App\Models\shippings;
use App\Models\meta_data;
use App\Models\line_items;
use App\Models\line_items_meta;
use App\Models\tax_lines;
use App\Models\shipping_lines;
use App\Models\Order_fee_lines;
use App\Models\Order_Coupan_lines;
use App\Models\Order_Refunds;
use App\Models\Order_links;
use App\Models\meta_data_value;
use App\Http\Resources\OrdersResource;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
   
	public function Report_detail()
	{
        $obj=orders::join('billings','orders.oid','=','billings.order_id')
       ->join('categories','categories.id','=', 'orders.id')
       //->join('line_items_metas','line_items_metas.product_id','=','line_items.product_id')->get(['orders.oid', 'billings.city','billings.first_name','orders.status','orders.payment_method']); 
       ->get(['orders.oid', 'billings.city','billings.first_name','orders.status','orders.payment_method','categories.name','orders.date_created_gmt']); 
		return $obj ;
	}

 // public function status_details(Request $request)
 // {
 // 	$status = $request->status;
	// 	if($status != null){
	// 		$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
	// 		  ->join('line_items','line_items.order_id','=', 'orders.oid')
	// 		  ->join('line_items_metas','line_items_metas.product_id','=','line_items.product_id')
	//           ->where('orders.status','=',$status)
	//           ->select("orders.*","billings.*","line_items.*")

	//           return $orders; 
	                    
	//     }else{
	//     	$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')

	//           ->select("orders.*","billings.*",

	//                     DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

	//                                 WHERE line_items.order_id = orders.oid

	//                                 GROUP BY line_items.order_id) as quantity"))

	//           ->get();
	//     }




    // }   //
    
        // dd($client);
     //  return $order;
 
           



		
	}










