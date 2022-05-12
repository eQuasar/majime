<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Orders;
use App\Models\billings;
use App\Models\shippings;
use App\Models\meta_data;
use App\Models\LineItemsMeta;
use App\Models\LineItems;
use App\Models\tax_lines;
use App\Models\shipping_lines;
use App\Models\Order_fee_lines;
use App\Models\Order_Coupan_lines;
use App\Models\Order_Refunds;
use App\Models\Order_links;
use App\Http\Resources\OrderResource;
use App\Http\Resources\BillingResource;
use App\Http\Resources\LineItemsResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function OrderDetail(Request $request)
	{
		$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
		->join('products', 'orders.id', '=', 'products.id')
		->where('orders.vid','=',$request->vid)
          ->select("orders.*","billings.*","products.*",
          DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

                                WHERE line_items.order_id = orders.oid

                                GROUP BY line_items.order_id) as quantity"))

          ->get();
        return $orders;
	}

	public function getOrderDetails(Request $request)
	{
		$vendor = $request->vid;

		if($vendor != null){
			$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
	          ->where('orders.vid','=',intval($vendor))
	          ->where('billings.vid','=',intval($vendor))
	          ->select("orders.*","orders.status as orderstatus","billings.*",

	                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vendor)." GROUP BY line_items.order_id) as quantity"))
	          ->get();
	    }else{
	    	$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')

	          ->select("orders.*","orders.status as orderstatus","billings.*",

	                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

	                                WHERE line_items.order_id = orders.oid

	                                GROUP BY line_items.order_id) as quantity"))

	          ->get();
	    }
        return $orders;
	}

	public function Order_Search(Request $request)
	{
		$range =[$request->date_from,$request->date_to];
       	// $order=orders::whereBetween('date_created_gmt',$range)->get();
       	$order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
	          ->whereBetween('date_created_gmt',$range)
	          ->select("orders.*","billings.*",

	                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

	                                WHERE line_items.order_id = orders.oid

	                                GROUP BY line_items.order_id) as quantity"))
	          ->get();

        return $order;
       
    }

   public function order_Profile($oid)
    {

        // $order =DB::table("orders")->where('orders.oid','=',$oid)
        // ->join('billings','orders.oid','=','billings.order_id')
        // ->select("orders.*","billings.*",
        // 				DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

	       //                          WHERE line_items.order_id = orders.oid

	       //                          GROUP BY line_items.order_id) as quantity"))

        //  ->get();
         
        $order =DB::table("orders")->join('billings','orders.oid','=','billings.order_id')
      	->where('orders.oid','=',$oid)
        ->where('orders.vid','=',intval($_REQUEST['vid']))
      	->where('billings.vid','=',intval($_REQUEST['vid']))
        ->select("orders.*","billings.*",
        				DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($_REQUEST['vid'])." GROUP BY line_items.order_id) as quantity"))

         ->get();
         
       	return $order;
    }
    public function order_items($oid){
        $orderItems =DB::table("line_items")->where('order_id','=',$oid)->where('vid','=',$_REQUEST['vid'])->get();
       	return $orderItems;
    }
  public function getPackdetail($vid)
  {
        $orderItems =DB::table("line_items")->where('line_items.vid',$vid)

        ->join('orders','orders.oid','=','line_items.order_id')
        	->select("line_items.sku as SKU","line_items.name as Name","line_items.quantity as Qty","line_items.parent_name as Parent","line_items.order_id as OrderID","orders.date_created as Date") 
        // 				DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

	       //                          WHERE line_items.order_id = orders.oid

	       //                          GROUP BY line_items.order_id) as quantity"))

         ->get();
         
       return $orderItems;
    }

	public function getOrderOnStatus($vid,$status){
		
		// echo "string"; die;

		  $orderItems =DB::table("orders")
		  	->join('billings','orders.oid','=','billings.order_id')	
		  	->where('orders.vid',$vid)
			->where('orders.status',$status)
  
		  // ->join('billings','orders.oid','=','billings.order_id')
		  // ->select("orders.*","billings.*",
		  // 				DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
  
			 //                          WHERE line_items.order_id = orders.oid
  
			 //                          GROUP BY line_items.order_id) as quantity"))
  
		   ->get();
		   
		 return $orderItems;
  	}

  	public function assignAWB(Request $request){
  		$orders =DB::table("orders")
		  	->join('billings','orders.oid','=','billings.order_id')	
		  	->where('orders.vid',$request->vid)
			->where('orders.status','confirmed')->get();

		foreach($orders as $order){
			// var_dump($order);die();
			$order_id = $order->oid;
			$product_name = "";
			$order_items =DB::table("line_items")
			->where('line_items.vid',$request->vid)
			->where('line_items.order_id',$order_id)->get();
			foreach( $order_items as $product ) {
				$product_name = $product_name." | ".$product->name." - ".$product->quantity;
			}
			$curl2 = curl_init();
			curl_setopt_array($curl2, array(
			  CURLOPT_URL => 'https://staging-express.delhivery.com/c/api/pin-codes/json/?filter_codes='.$order->postcode,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
				'Authorization: Token 5235172eea087eda74de0cf82149fa8a419d5122',
				'Content-Type: application/json'
			  ),
			));

			$response2 = curl_exec($curl2);
			curl_close($curl2);
			$new_val2 = json_decode($response2, true);
			if(count($new_val2["delivery_codes"]) > 0){
				$curl = curl_init();
				if($order->payment_method == "cod"){
					curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://staging-express.delhivery.com/api/cmu/create.json',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS =>'format=json&data={
					  "shipments": [
						{
						  "add": "'.$order->address_1.', '.$order->address_2.'",
						  "phone": '.$order->phone.',
						  "payment_mode": "COD",
						  "name": "'.$order->first_name.' '.$order->last_name.'",
						  "pin": '.$order->postcode.',
						  "cod_amount":'.$order->total.',
						  "order": "blah_'.$order->id.'",
						  "shipping_mode" : "Surface",
						  "products_desc": "'.$product_name.'"
						}
					  ],
					  "pickup_location": 
						{
						  "city": "Ludhiana",
						  "name": "Hemkunt Industries",
						  "pin": "141010",
						  "country": "India",
						  "phone": "8146959656",
						  "add": "E-207, Phase-IV A, Focal Point, Dhandari Kalan"
						}
					}',
					  CURLOPT_HTTPHEADER => array(
						'Authorization: Token 5235172eea087eda74de0cf82149fa8a419d5122',
						'Content-Type: application/json',
						'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'
					  ),
					));
				}else{
					curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://staging-express.delhivery.com/api/cmu/create.json',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS =>'format=json&data={
					  "shipments": [
						{
						  "add": "'.$order->address_1.', '.$order->address_2.'",
						  "phone": '.$order->phone.',
						  "payment_mode": "COD",
						  "name": "'.$order->first_name.' '.$order->last_name.'",
						  "pin": '.$order->postcode.',
						  "cod_amount":'.$order->total.',
						  "order": "blah_'.$order->id.'",
						  "shipping_mode" : "Surface",
						  "products_desc": "'.$product_name.'"
						}
					  ],
					  "pickup_location": 
						{
						  "city": "Ludhiana",
						  "name": "Hemkunt Industries",
						  "pin": "141010",
						  "country": "India",
						  "phone": "8146959656",
						  "add": "E-207, Phase-IV A, Focal Point, Dhandari Kalan"
						}
					}',
					  CURLOPT_HTTPHEADER => array(
						'Authorization: Token 5235172eea087eda74de0cf82149fa8a419d5122',
						'Content-Type: application/json',
						'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'
					  ),
					));
				}

				$response = curl_exec($curl);

				curl_close($curl);
				$new_val = json_decode($response, true);

				if(isset($new_val["packages"])){
					$wbill = $new_val["packages"][0]["waybill"];
					$o_id = $order_id;

					$order_items =DB::table("waybill")
						->where('waybill.vid',$request->vid)
						->where('waybill.order_id',$order_id)->get();
					if(empty($results)){
						DB::table('waybill')->insert(
						    ['vid' => $request->vid, 'order_id' => $order_id, 'waybill_no' => $wbill, 'date_created' => date("Y-m-d h:i:sa")]
						);
						DB::table('orders')
			              ->where('oid', $order_id)
			              ->where('vid', $request->vid)
			              ->update(['status' => "intransit"]);
						return response()->json(['error' => false, 'msg' => "WayBill successfully added.","ErrorCode" => "000"],200);
					}else{
						return response()->json(['error' => true, 'msg' => "Something went wrong.","ErrorCode" => -2],200);
					}
				}else{
					return response()->json(['error' => true, 'msg' => $new_val['detail'],"ErrorCode" => -2],200);
				}
			}else{
				DB::table('orders')
		              ->where('oid', $order_id)
		              ->where('vid', $request->vid)
		              ->update(['status' => "on-hold"]);
		        return response()->json(['error' => false, 'msg' => "No WayBill generate so status set to on-hold.","ErrorCode" => "000"],200);
			}
		}
  	}

  	function changeStatus(Request $request){
  		DB::table('orders')
          ->where('oid', $request->oid)
          ->where('vid', $request->vid)
          ->update(['status' => $request->status_assign]);

        return response()->json(['error' => false, 'msg' => "Order Status successfully updated.","ErrorCode" => "000"],200);
  	}

	public function delete($id)
	{
    	$Orders=Orders::find($id);
    	if(empty($orders))
		{
    		return;
    	}
    	$Orders->delete();
    }

}
