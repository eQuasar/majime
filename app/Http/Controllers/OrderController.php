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
use Elibyy\TCPDF\Facades\TCPDF;
use PDF;
use \Milon\Barcode\DNS1D;

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
	    public function getOrderDetails(Request $request)
		{
			$vendor = $request->vid;
			if($vendor != null){
				$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
				   // ->join('waybill','orders.oid','=','waybill.order_id')
		          ->where('orders.vid','=',intval($vendor))
		          ->where('billings.vid','=',intval($vendor))
		          ->orderBy('oid','DESC')
		           // ->select("orders.*","waybill.waybill_no","orders.status as orderstatus","billings.*",
		          ->select("orders.*","orders.status as orderstatus","billings.*",
		                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = ".intval($vendor)." GROUP BY line_items.order_id) as quantity"))
		          	          ->get();
		       
		    }else{
		    	$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
		    	->orderBy('oid', 'DESC')
		          ->select("orders.*","orders.status as orderstatus","billings.*",
		                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
		                                WHERE line_items.order_id = orders.oid
		                                GROUP BY line_items.order_id) as quantity"))    
		          ->get();
		        }
	        return $orders;
		}
	    public function getPackdetail($vid)
		   {
		        $orderItems =DB::table("orders")
		         ->join('billings','orders.oid','=','billings.order_id')
		        // ->join('line_items','orders.oid','=','line_items.order_id')
		       ->where('orders.vid',$vid)
		       ->where('orders.status',"packed")
		       ->select("orders.*","billings.*",
		        		DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." GROUP BY line_items.order_id) as quantity"),
		        		DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as name"),
		        		DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as sku"))
		        	// ->select("line_items.sku as SKU","line_items.name as Name","line_items.quantity as Qty") 
		        				// DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
			           //                      WHERE line_items.order_id = orders.oid
			           //                      GROUP BY line_items.order_id) as quantity")
		        ->orderBy('oid','DESC')
		        ->get();
		            return $orderItems;
        	}
	 	public function get_packdetail_Refund($vid)
	    	{
	  		    $orders =DB::table("orders")
	  		       ->join('billings','orders.oid','=','billings.order_id')
			  		->where('orders.vid',$vid)
		       ->where('orders.status',"dtodel2warehouse")
		       ->select("orders.*","billings.*",
		        		DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." GROUP BY line_items.order_id) as quantity"),
		        		DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as name"),
		        		DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as sku"))
		       ->orderBy('oid','DESC')
		        ->get();
		        
				return $orders;
	  		}
		public function getOrderOnStatus($vid,$status)
			{
					// echo "string"; die;
			  $orderItems =DB::table("orders")
			  	->join('billings','orders.oid','=','billings.order_id')	
			  	->where('orders.vid',$vid)
			  	// ->where('billings.vid',$vid)
				->where('orders.status',$status)->get();
			   	return $orderItems;
	  		}

  		public function return_order(){
		  		$oid = $_REQUEST['order_id'];
		  		$vid = $_REQUEST['vid'];
		  		$my_data =DB::table("way_data")
				->where('vid',$_REQUEST['vid'])->get();
				$city = $my_data[0]->city;
				$name = $my_data[0]->name;
				$pin = $my_data[0]->pin;
				$country = $my_data[0]->country;
				$phone = $my_data[0]->phone;
				$add = $my_data[0]->add;
				$token = $my_data[0]->token;
		  		$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
				->join('products', 'orders.id', '=', 'products.id')
				->where('orders.vid','=',$vid)
				->where('orders.oid','=',$oid)
		          ->select("orders.*","billings.*","products.*",
		          DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
		                                WHERE line_items.order_id = orders.oid
		                                GROUP BY line_items.order_id) as quantity"))
		          ->get();
  				$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://track.delhivery.com/api/cmu/create.json',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'format=json&data={
						  "shipments": [
							{
							  "add": "'.$orders[0]->address_1.', '.$orders[0]->address_2.'",
							  "phone": '.$orders[0]->phone.',
							  "payment_mode": "Pickup",
							  "name": "'.$orders[0]->first_name.'",
							  "pin": '.$orders[0]->postcode.',
							  "order": "BBB_'.$oid.'",
							  "return_state": "'.$city.'",
								"return_city": "'.$city.'",
								"return_phone": "'.$phone.'",
								"return_add": "'.$add.'",
								"return_pin": "'.$pin.'",
								"extra_parameters": { 
								  "return_reason": "Return Order"
								},
								"return_name": "'.$name.'"
							}
						  ],
						  "pickup_location": 
							{
							  "city": "'.$city.'",
							  "name": "'.$name.'",
							  "pin": "'.$pin.'",
							  "country": "'.$country.'",
							  "phone": "'.$phone.'",
							  "add": "'.$add.'"
							}
						}',
					CURLOPT_HTTPHEADER => array(
						'Authorization: Token '.$token,
						'Content-Type: application/json',
						'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'
					),
				));
					$response = curl_exec($curl);
					$new_val = json_decode($response, true);
					$wbill = $new_val["packages"][0]["waybill"];
					$order_items =DB::table("waybill")
					->where('waybill.vid',$vid)
					->where('waybill.order_id',$oid)->get()->toArray();
					// var_dump($order_items); die;
					if(!empty($order_items)){
						DB::table('waybill')
			              ->where('order_id', $_REQUEST['order_id'])
			              ->where('vid', $_REQUEST['vid'])
			              ->update(['return_waybill_no' => $wbill]);
			              DB::table('orders')
			              ->where('oid', $_REQUEST['order_id'])
			              ->where('vid', $_REQUEST['vid'])
			              ->update(['status' => "dtobooked"]);
    //           $curl = curl_init();
    //           $vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
    //           var_dump($vendor[0]->url); die;
			 //    curl_setopt_array($curl, array(
			 //    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$_REQUEST['order_id'].'?status=dtobooked',
			 //    CURLOPT_RETURNTRANSFER => true,
			 //    CURLOPT_ENCODING => '',
			 //    CURLOPT_MAXREDIRS => 10,
			 //    CURLOPT_TIMEOUT => 0,
			 //    CURLOPT_FOLLOWLOCATION => true,
			 //    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			 //    CURLOPT_CUSTOMREQUEST => 'PUT',
			 //    CURLOPT_HTTPHEADER => array(

			 //        'Authorization: Basic '.$vendor[0]->token
			 //      ),
			 //    ));

			 //    $response = curl_exec($curl);
			 //    curl_close($curl);
			 //    $jsonResp = json_decode($response);


				// $curl2 = curl_init();

				// curl_setopt_array($curl2, array(
				//   CURLOPT_URL => $vendor[0]->url.'/wp-json/waybill/waybill_data?order_id='.$order_id.'&return=1&waybill_no='.$new_val["packages"][0]["waybill"],
				//   CURLOPT_RETURNTRANSFER => true,
				//   CURLOPT_ENCODING => '',
				//   CURLOPT_MAXREDIRS => 10,
				//   CURLOPT_TIMEOUT => 0,
				//   CURLOPT_FOLLOWLOCATION => true,
				//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				//   CURLOPT_CUSTOMREQUEST => 'GET',
				// ));

			 //    $response2 = curl_exec($curl2);
			 //    curl_close($curl2);
			 //    $jsonResp2 = json_decode($response2);

              return response()->json(['error' => false, 'abn_no' => $wbill,"ErrorCode" => "000"],200);
	      	}
	  	}

  	public function assignAWB(Request $request){
  		$main = explode(',', $request->allSelected);
  		// var_dump($request);
				// echo "strong"; die;
  		for ($i=0; $i < count($main); $i++) { 
	  		$orders =DB::table("orders")
			  	->join('billings','orders.oid','=','billings.order_id')	
			  	->where('orders.oid', intval($main[$i]))
			  	->where('orders.vid',$request->vid)
				->where('orders.status','confirmed')->get();
			$my_data =DB::table("way_data")
			->where('vid',$request->vid)->get();

			$city = $my_data[0]->city;
			$name = $my_data[0]->name;
			$pin = $my_data[0]->pin;
			$country = $my_data[0]->country;
			$phone = $my_data[0]->phone;
			$add = $my_data[0]->add;
			$token = $my_data[0]->token;
			$order_prefix = $my_data[0]->order_prefix;

			foreach($orders as $order){

				// var_dump($order);die();
				$order_id = $order->oid;
				$product_name = "";
				$order_items =DB::table("line_items")
				->where('line_items.vid',$request->vid)
				->where('line_items.order_id',$order_id)->get();

				if($request->vid == 1){
					$curlopt_url = "https://staging-express.delhivery.com/api/cmu/create.json";
					$del_url = "https://staging-express.delhivery.com/c/api/pin-codes/json/";
				}else{
					$curlopt_url = "https://track.delhivery.com/api/cmu/create.json";
					$del_url = "https://track.delhivery.com/c/api/pin-codes/json/";
				}
				// var_dump($order->postcode);
				foreach( $order_items as $product ) {

					$product_name = $product_name." | ".$product->name." - ".$product->quantity;
				}
				$curl2 = curl_init();
				curl_setopt_array($curl2, array(
				  CURLOPT_URL => $del_url.'?filter_codes='.$order->postcode,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				  CURLOPT_HTTPHEADER => array(
					'Authorization: Token '.$token,
					'Content-Type: application/json'
				  ),
				));

				$response2 = curl_exec($curl2);
				curl_close($curl2);
				$new_val2 = json_decode($response2, true);
				// var_dump($new_val2); die;
				if(count($new_val2["delivery_codes"]) > 0){
					$curl = curl_init();
					if($order->payment_method == "cod"){
						curl_setopt_array($curl, array(
						  CURLOPT_URL => $curlopt_url,
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
							  "order": "'.$order_prefix.$order->oid.'",
							  "shipping_mode" : "Surface",
							  "products_desc": "'.$product_name.'"
							}
						  ],
						  "pickup_location": 
							{
							  "city": "'.$city.'",
							  "name": "'.$name.'",
							  "pin": "'.$pin.'",
							  "country": "'.$country.'",
							  "phone": "'.$phone.'",
							  "add": "'.$add.'"
							}
						}',
						  CURLOPT_HTTPHEADER => array(
							'Authorization: Token '.$token,
							'Content-Type: application/json',
							'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'
						  ),
						));
					}else{
						curl_setopt_array($curl, array(
						  CURLOPT_URL => $curlopt_url,
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
							  "order": "'.$order_prefix.$order->oid.'",
							  "shipping_mode" : "Surface",
							  "products_desc": "'.$product_name.'"
							}
						  ],
						  "pickup_location": 
							{
							  "city": "'.$city.'",
							  "name": "'.$name.'",
							  "pin": "'.$pin.'",
							  "country": "'.$country.'",
							  "phone": "'.$phone.'",
							  "add": "'.$add.'"
							}
						}',
						  CURLOPT_HTTPHEADER => array(
							'Authorization: Token '.$token,
							'Content-Type: application/json',
							'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'
						  ),
						));
					}

					$response = curl_exec($curl);
				
					curl_close($curl);
					$new_val = json_decode($response, true);

					if(isset($new_val["packages"])){
						if(!empty($new_val["packages"])){
							$wbill = $new_val["packages"][0]["waybill"];
							$o_id = $order_id;

							$order_items =DB::table("waybill")
								->where('waybill.vid',$request->vid)
								->where('waybill.order_id',$order_id)->get()->toArray();
							if(empty($order_items)){
								DB::table('waybill')->insert(
								    ['vid' => $request->vid, 'order_id' => $order_id, 'waybill_no' => $wbill, 'date_created' => date("Y-m-d h:i:s")]
								);
								DB::table('orders')
					              ->where('oid', $order_id)
					              ->where('vid', $request->vid)
					              ->update(['status' => "packed"]);

					              $curl = curl_init();

					              $vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();

								    curl_setopt_array($curl, array(

								    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$order_id.'?status=packed',
								    CURLOPT_RETURNTRANSFER => true,
								    CURLOPT_ENCODING => '',
								    CURLOPT_MAXREDIRS => 10,
								    CURLOPT_TIMEOUT => 0,
								    CURLOPT_FOLLOWLOCATION => true,
								    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								    CURLOPT_CUSTOMREQUEST => 'PUT',
								    CURLOPT_HTTPHEADER => array(

								        'Authorization: Basic '.$vendor[0]->token
								      ),
								    ));

								    $response = curl_exec($curl);
								    curl_close($curl);
								    $jsonResp = json_decode($response);


									$curl2 = curl_init();

									curl_setopt_array($curl2, array(
									  CURLOPT_URL => $vendor[0]->url.'/wp-json/waybill/waybill_data?order_id='.$order_id.'&waybill_no='.$new_val["packages"][0]["waybill"],
									  CURLOPT_RETURNTRANSFER => true,
									  CURLOPT_ENCODING => '',
									  CURLOPT_MAXREDIRS => 10,
									  CURLOPT_TIMEOUT => 0,
									  CURLOPT_FOLLOWLOCATION => true,
									  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
									  CURLOPT_CUSTOMREQUEST => 'GET',
									));

								    $response2 = curl_exec($curl2);
								    curl_close($curl2);
								    $jsonResp2 = json_decode($response2);

								    
								$error = false;
								$msg = "WayBill successfully added.";
								// return response()->json(['error' => false, 'msg' => "WayBill successfully added.","ErrorCode" => "000"],200);
							}else{
								DB::table('orders')
					              ->where('oid', $order_id)
					              ->where('vid', $request->vid)
					              ->update(['status' => "packed"]);

								$error = false;
								$msg = "Already Assign AWB No.";
								// return response()->json(['error' => true, 'msg' => "Already Assign AWB No.","ErrorCode" => -2],200);
							}
						}else{
							$error = true;
							$msg = $new_val['rmk'];
							// return response()->json(['error' => true, 'msg' => $new_val['rmk'],"ErrorCode" => -2],200);
						}
					}else{

						$error = true;
						$msg = $new_val['detail'];
						// return response()->json(['error' => true, 'msg' => $new_val['detail'],"ErrorCode" => -2],200);
					}
				}else{
					DB::table('orders')
			              ->where('oid', $order_id)
			              ->where('vid', $request->vid)
			              ->update(['status' => "on-hold"]);

			              $curl = curl_init();

			              $vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();

						    curl_setopt_array($curl, array(

						    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$order_id.'?status=on-hold',
						    CURLOPT_RETURNTRANSFER => true,
						    CURLOPT_ENCODING => '',
						    CURLOPT_MAXREDIRS => 10,
						    CURLOPT_TIMEOUT => 0,
						    CURLOPT_FOLLOWLOCATION => true,
						    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						    CURLOPT_CUSTOMREQUEST => 'PUT',
						    CURLOPT_HTTPHEADER => array(

						        'Authorization: Basic '.$vendor[0]->token
						      ),
						    ));

						    $response = curl_exec($curl);
						    curl_close($curl);
						    $jsonResp = json_decode($response);

					$error = false;
					$msg = "No WayBill generate so status set to on-hold.";
			        // return response()->json(['error' => false, 'msg' => "No WayBill generate so status set to on-hold.","ErrorCode" => "000"],200);
				}
			}
		}
		if($error == false){
			return response()->json(['error' => false, 'msg' => $msg,"ErrorCode" => "000"],200);
		}else{
			return response()->json(['error' => true, 'msg' => $msg,"ErrorCode" => -2],200);
		}
  	}

  	function assignAWBOrder(Request $request){
  		// dd($request); die;
   			$orders =DB::table("orders")
			  	->join('billings','orders.oid','=','billings.order_id')	
			  	->where('orders.vid',intval($request->vid))
			  	->where('orders.oid',intval($request->oid))->get();
			  	// var_dump($orders); die;
			$my_data =DB::table("way_data")
				->where('vid', intval($request->vid))->get();
			// var_dump($my_data); die;
				$city = $my_data[0]->city;
				$name = $my_data[0]->name;
				$pin = $my_data[0]->pin;
				$country = $my_data[0]->country;
				$phone = $my_data[0]->phone;
				$add = $my_data[0]->add;
				$token = $my_data[0]->token;
				$order_prefix = $my_data[0]->order_prefix;

				if($request->vid == 1){
					$curlopt_url = "https://staging-express.delhivery.com/api/cmu/create.json";
					$del_url = "https://staging-express.delhivery.com/c/api/pin-codes/json/";
				}else{
					$curlopt_url = "https://track.delhivery.com/api/cmu/create.json";
					$del_url = "https://track.delhivery.com/c/api/pin-codes/json/";
				}

				// $curlopt_url = "https://track.delhivery.com/api/cmu/create.json";
					foreach($orders as $order){
				// var_dump($order);die();
					$order_id = $order->oid;
					$product_name = "";
					$order_items =DB::table("line_items")
					->where('line_items.vid',intval($request->vid))
					->where('line_items.order_id',intval($order_id))->get();
						foreach( $order_items as $product ) {
							$product_name = $product_name." | ".$product->name." - ".$product->quantity;

					}
					// echo $order->postcode; die;
					$curl2 = curl_init();
					curl_setopt_array($curl2, array(
					  CURLOPT_URL => $del_url.'?filter_codes='.$order->postcode,
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'GET',
					  CURLOPT_HTTPHEADER => array(
						'Authorization: Token '.$token,
						'Content-Type: application/json'
					  ),
					));
			$response2 = curl_exec($curl2);
			curl_close($curl2);
			$new_val2 = json_decode($response2, true);
			if($new_val2 != NULL){
				$curl = curl_init();
				if($order->payment_method == "cod"){
					$payment_mode = "COD";
				}else{
					$payment_mode = "Prepaid";
				}

				$postfields = 'format=json&data={
					  "shipments": [
						{
						  "add": "'.$order->address_1.', '.$order->address_2.'",
						  "phone": '.$order->phone.',
						  "payment_mode": "'.$payment_mode.'",
						  "name": "'.$order->first_name.' '.$order->last_name.'",
						  "pin": '.$order->postcode.',
						  "cod_amount":'.$order->total.',
						  "order": "'.$order_prefix.$order->oid.'",
						  "shipping_mode" : "Surface",
						  "products_desc": "'.$product_name.'"
						}
					  ],
					  "pickup_location": 
						{
						  "city": "'.$city.'",
						  "name": "'.$name.'",
						  "pin": "'.$pin.'",
						  "country": "'.$country.'",
						  "phone": "'.$phone.'",
						  "add": "'.$add.'"
						}
					}';
				
				// echo $postfields;
				
					curl_setopt_array($curl, array(
					  CURLOPT_URL => $curlopt_url,
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS => $postfields,
					  CURLOPT_HTTPHEADER => array(
						'Authorization: Token '.$token,
						'Content-Type: application/json',
						'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'
					  ),
					));
					$response = curl_exec($curl);
					curl_close($curl);
				$new_val = json_decode($response, true);
				// var_dump($response); die;

				// if(isset($new_val["packages"])){
					if(!empty($new_val["packages"])){
						$wbill = $new_val["packages"][0]["waybill"];
						$o_id = $order_id;
						$order_items =DB::table("waybill")
							->where('waybill.vid',$request->vid)
							->where('waybill.order_id',$order_id)->get()->toArray();
						if(empty($order_items)){
							DB::table('waybill')->insert(
							    ['vid' => $request->vid, 'order_id' => $order_id, 'waybill_no' => $wbill, 'date_created' => date("Y-m-d h:i:s")]
							);
							DB::table('orders')
				              ->where('oid', $order_id)
				              ->where('vid', $request->vid)
				              ->update(['status' => "packed"]);
				              $curl = curl_init();
				              $vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();
							    curl_setopt_array($curl, array(
							    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$order_id.'?status=packed',
							    CURLOPT_RETURNTRANSFER => true,
							    CURLOPT_ENCODING => '',
							    CURLOPT_MAXREDIRS => 10,
							    CURLOPT_TIMEOUT => 0,
							    CURLOPT_FOLLOWLOCATION => true,
							    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							    CURLOPT_CUSTOMREQUEST => 'PUT',
							    CURLOPT_HTTPHEADER => array(

							        'Authorization: Basic '.$vendor[0]->token
							      ),
							    ));

							    $response = curl_exec($curl);
							    curl_close($curl);
							    $jsonResp = json_decode($response);
							$curl2 = curl_init();
							curl_setopt_array($curl2, array(
							  CURLOPT_URL => $vendor[0]->url.'/wp-json/waybill/waybill_data?order_id='.$order_id.'&waybill_no='.$new_val["packages"][0]["waybill"],
							  CURLOPT_RETURNTRANSFER => true,
							  CURLOPT_ENCODING => '',
							  CURLOPT_MAXREDIRS => 10,
							  CURLOPT_TIMEOUT => 0,
							  CURLOPT_FOLLOWLOCATION => true,
							  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST => 'GET',
							));

						    $response2 = curl_exec($curl2);
						    curl_close($curl2);
						    $jsonResp2 = json_decode($response2);
							return response()->json(['error' => false, 'msg' => "WayBill successfully added.","ErrorCode" => "000"],200);
						}else{
							DB::table('orders')
				              ->where('oid', $order_id)
				              ->where('vid', $request->vid)
				              ->update(['status' => "packed"]);
							return response()->json(['error' => true, 'msg' => "Already Assign AWB No.","ErrorCode" => -2],200);
						}
					}else{
						return response()->json(['error' => true, 'msg' => $new_val['rmk'],"ErrorCode" => -2],200);
					}
				// }else{
				// 	return response()->json(['error' => true, 'msg' => $new_val['detail'],"ErrorCode" => -2],200);
				// }
			}else{
				DB::table('orders')
		              ->where('oid', $order_id)
		              ->where('vid', $request->vid)
		              ->update(['status' => "on-hold"]);
		              $curl = curl_init();
		              $vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();
					    curl_setopt_array($curl, array(
					    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$order_id.'?status=on-hold',
					    CURLOPT_RETURNTRANSFER => true,
					    CURLOPT_ENCODING => '',
					    CURLOPT_MAXREDIRS => 10,
					    CURLOPT_TIMEOUT => 0,
					    CURLOPT_FOLLOWLOCATION => true,
					    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					    CURLOPT_CUSTOMREQUEST => 'PUT',
					    CURLOPT_HTTPHEADER => array(
					        'Authorization: Basic '.$vendor[0]->token
					      ),
					    ));
					    $response = curl_exec($curl);
					    curl_close($curl);
					    $jsonResp = json_decode($response);
		        return response()->json(['error' => false, 'msg' => "No WayBill generate so status set to on-hold.","ErrorCode" => "000"],200);
			}
		}
  	}
	  	function changeStatus(Request $request)
	  	{
	  		if($request->status_assign=='cancelled')
	  		{	
	  			// echo $request->status; die;
	  			if($request->status == 'processing' || $request->status == 'on-hold')
		  		{
					DB::table('orders')
			          ->where('oid', intval($request->selectall))
			          ->where('vid', intval($request->vid))
			          ->update(['status' => $request->status_assign]);
					// print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
					// https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
					$vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();
					          $curl = curl_init();
				    curl_setopt_array($curl, array(
				    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.intval($request->selectall).'?status='.$request->status_assign,
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_ENCODING => '',
				    CURLOPT_MAXREDIRS => 10,
				    CURLOPT_TIMEOUT => 0,
				    CURLOPT_FOLLOWLOCATION => true,
				    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				    CURLOPT_CUSTOMREQUEST => 'PUT',
				    CURLOPT_HTTPHEADER => array(

				        'Authorization: Basic '.$vendor[0]->token
				      ),
				    ));

				    $response = curl_exec($curl);
				    curl_close($curl);
				    $jsonResp = json_decode($response);
							// var_dump($jsonResp);
			  		return response()->json(['error' => false, 'msg' => "Order Status successfully updated.","ErrorCode" => "000"],200);
				}
				else
				{
					return response()->json(['error' => false, 'msg' => "Please Note :  Order Can Not Be Cancel.","ErrorCode" => "000"],200);
				}
  			}else{
  					DB::table('orders')
		          ->where('oid', intval($request->selectall))
		          ->where('vid', intval($request->vid))
		          ->update(['status' => $request->status_assign]);
				// print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
				// https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
				$vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();
				          $curl = curl_init();
			    curl_setopt_array($curl, array(
			    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.intval($request->selectall).'?status='.$request->status_assign,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_ENCODING => '',
			    CURLOPT_MAXREDIRS => 10,
			    CURLOPT_TIMEOUT => 0,
			    CURLOPT_FOLLOWLOCATION => true,
			    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			    CURLOPT_CUSTOMREQUEST => 'PUT',
			    CURLOPT_HTTPHEADER => array(

			        'Authorization: Basic '.$vendor[0]->token
			      ),
			    ));

			    $response = curl_exec($curl);
			    curl_close($curl);
			    $jsonResp = json_decode($response);
						// var_dump($jsonResp);
		  		return response()->json(['error' => false, 'msg' => "Order Status successfully updated.","ErrorCode" => "000"],200);
		  	}
	  	}

	  	public function changeOrderStatus($vid,$oid,$status){
	  		// var_dump($vid);
	  		// var_dump($oid);
	  		// var_dump($status); die;
  				DB::table('orders')
			          ->where('oid', intval($oid))
			          ->where('vid', intval($vid))
			          ->update(['status' => $status]);

					// print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
					// https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
				$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
					          $curl = curl_init();
				    curl_setopt_array($curl, array(
				    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.intval($oid).'?status='.$status,
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_ENCODING => '',
				    CURLOPT_MAXREDIRS => 10,
				    CURLOPT_TIMEOUT => 0,
				    CURLOPT_FOLLOWLOCATION => true,
				    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				    CURLOPT_CUSTOMREQUEST => 'PUT',
				    CURLOPT_HTTPHEADER => array(

				        'Authorization: Basic '.$vendor[0]->token
				      ),
				    ));

				    $response = curl_exec($curl);
				    curl_close($curl);
				    $jsonResp = json_decode($response);
	  		return response()->json(['error' => false, 'msg' => "Order Status successfully updated.","ErrorCode" => "000"],200);
	  	}
  	function printSlip(Request $request){
  		$listImp = explode(',', $request->allSelected);
  		$d = new DNS1D();
  		// create new PDF document
    	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        	// set document information
    	$pdf::SetTitle('Slip');
        	// set default monospaced font
    	$pdf::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    	$pdf::SetPrintHeader(false);
		$pdf::SetPrintFooter(false);
        	// set auto page breaks
    	$pdf::SetAutoPageBreak(TRUE, 0);
        	// set image scale factor
    	$pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);
    	    	$pdf::SetFont('helvetica', '', 10);
        // define barcode style
        $style = array(
            'position' => 'C',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '0',
            'vpadding' => '0',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
    
		$orders =DB::table("orders")
		  	->join('billings','orders.oid','=','billings.order_id')	
		  	->where('orders.vid',$request->vid)
	  	   	->whereIn('orders.oid', $listImp)
			->where('orders.status','packed')->get();
		// dd($orders); die;
	    $i=1;
		foreach($orders as $order){
			$oid = $order->oid;
			$results2 =DB::table("waybill")	
			  	->where('vid',$request->vid)
			  	->where('order_id',$oid)->get()->toArray();
			if(!empty($results2)){
				$wbill = $results2[0]->waybill_no;

				$img_base64_encoded = 'data:image/png;base64,'. $d->getBarcodePNG($wbill, 'C128', 3,33,array(1,1,1));
		  		$params = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '">';

		  		$img_base64_encoded2 = 'data:image/png;base64,'. $d->getBarcodePNG($order->oid, 'C128', 3,33,array(1,1,1));
		  		$params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
			}else{
				$wbill = "";
				$params = "";

		  		$img_base64_encoded2 = 'data:image/png;base64,'. $d->getBarcodePNG($order->oid, 'C128', 3,33,array(1,1,1));
		  		$params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
			}
	// 	    $pdf::Cell(0, 0, 'CODABAR', 0, 1);
	//         $params = $pdf::write1DBarcode($order->id, 'CODABAR', '', '', '', 18, 0.4, $style, 'N');
			// $params_d = $pdf::serializeTCPDFtagParameters(array($wbill, 'C128', '', '', '', 18, 1.0, $style, 'N'));
			// $params2_d = $pdf::serializeTCPDFtagParameters(array($order->oid, 'C128', '', '', '', 18, 1.0, $style, 'N'));
			

	  		// echo $params2; die;
			
	    	// define some HTML content with style
	$html2 = '<style type="text/css">
	@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap");
	table, th, td, p, div, h1, h2, h3, h4, h5, h6 {font-family: "Poppins", sans-serif;}
	table, th, td {  border:1px solid black; border-collapse: collapse; margin: 0 auto;}
	div, table{
	    padding:0;
	    margin:0;   
	}
	h1, h2, h3, h4, h5, h6 {
	    margin: 0;padding:0;
	}
	.tax_invoice_text{font-size:16pt;font-weight:bold;}
	.parent_table p{margin:0;padding:0}
	.parent_table{border:none;}
	.company_info td{font-size: 8pt;}
	.product_info th{font-size: 9pt;font-weight:bold;}
	.product_info td{font-size: 9pt;}
	.currency_text{font-size: 13pt;font-weight:bold;}
	.tax_invoice_val{font-size: 11pt;font-weight:bold;}
	.tax_value p{font-size: 8pt;margin:0;padding:0;}
	.customer_info p, .customer_info td{font-size: 8pt;}
	.customer_info h2{width:100%;}
	td.bottom_barcode{font-size: 11pt;font-weight:bold;}
	.c_name{font-size: 12pt;font-weight:bold;margin-bottom:5px;}
	.cus_name{font-size: 13pt;font-weight:bold;}
	.awb_text{font-size: 13pt;font-weight:bold;}
	</style>
	<div class="parent_table">
	            <table width="100%" cellpadding="5" class="child_table company_info">
	                <tbody>
	                    <tr>
	                        <td rowspan="3" width="360"><p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p></td>
	                        <td width="150">Order No. (REF) : '.$order->oid.'</td>
	                        <td width="150">GST-JR-3557</td>
	                    </tr>
	                    <tr>
	                        <td width="150">Mode : '.$order->payment_method_title.' </td>
	                        <td width="150">Amount : Rs.'.$order->total.'</td>
	                    </tr>
	                    <tr>
	                        <td colspan="2" align="center">Courier Partner : DIRECT_DELHIVERY</td>
	                    </tr>
	                </tbody>
	            </table>
				<table width="100%" cellpadding="5" class="child_table customer_info">
	                <tbody>
	                    <tr>
	                        <td width="660"><p><span class="c_name">'.strtoupper($order->first_name).' '.strtoupper($order->last_name).' <span>(M) '.$order->phone.'</span></span><br>Address : '.$order->address_1;
								if($order->address_2 != ''){
									$html2 .= '<br>Landmark : '.$order->address_2;
								}else{
									$html2 .= '<br>Landmark : N/A';
								}
								$html2 .= '<br>Pincode : '.$order->postcode.', '.$order->city.', '.$order->state.', '.$order->country.'</p>
							</td>
	                    </tr>
	                </tbody>
				</table>
				<table width="100%" cellpadding="5" class="child_table customer_awb">
	                <tbody>
	                    <tr>
	                        <td width="660" align="center"><span class="awb_text">AWB : '.$wbill.'</span><br/>' . $params . '</td>
	                    </tr>
	                </tbody>
				</table>
	            <table width="100%" cellpadding="5" class="child_table product_info">
	                <thead>
	                    <tr>
	                        <th width="40">S.No.</th>
	                        <th width="250">Product(s)</th>
	                        <th width="100">SL Price</th>
	                        <th width="80" align="center">QTY (Pcs)</th>
	                        <th width="90">Discount</th>
	                        <th width="100">Net Amount</th>
	                    </tr>
	                </thead>
				</table>
				<table width="100%" cellpadding="5" class="child_table product_info">
	                <tbody>';
	                	$order_items =DB::table("line_items")->where('order_id','=',$oid)->where('vid','=',$request->vid)->get();
						$j = 1;
						$itm = 0;
						$itm_sub = 0;
						$cnt = count($order_items);
						if($cnt >=1){
							$min_height = 516/$cnt;
						}else{
							$min_height = 516;
						}
						$height = $min_height.'px';
						foreach( $order_items as $product ) {
							$itm = $itm + $product->quantity;
							$itm_sub = $itm_sub + $product->subtotal;
							$html2 .= '<tr>
								<td width="40" height="'.$height.'">'.$j.'</td>
								<td width="250">'.$product->name.'</td>
								<td width="100">Rs.'.$product->subtotal.'</td>
								<td width="80" align="center">'.$product->quantity.'</td>
								<td width="90">Rs.0</td>
								<td width="100">Rs.'.$product->total.'</td>
								</tr>';
							$j++;
						}
				$html2 .= '</tbody></table><table width="100%" cellpadding="5" class="child_table product_info">
	                <tbody>
						<tr>
	                        <td width="40">T1</td>
	                        <td width="250">T1</td>
	                        <td width="100">Rs.'.$itm_sub.'</td>
	                        <td width="80" align="center">'.$itm.'</td>
	                        <td width="90">Rs.'.$order->discount_total.'</td>
	                        <td width="100">Rs.'.$order->total.'</td>
	                    </tr>
	                    <tr>
	                        <td rowspan="2" colspan="4" class="bottom_barcode">
	                            '.$order->oid.'<br>'.$params2;
	// $html2 .= '<tcpdf method="write1DBarcode" params="' . $params2 . '" />';
	                        $html2 .= '</td>
	                        <td width="90">Serv.Charges</td>
	                        <td width="100">Payable AMT.</td>
	                    </tr>
	                    <tr>
	                        <td width="90">Rs.'.$order->total_tax.'</td>
	                        <td width="100">Rs.'.$order->total.'</td>
	                    </tr>
	                </tbody>
	            </table>
				
				<table width="100%" cellpadding="5" class="child_table">
	                <tr>
	                    <td width="660" align="center"><div class="currency_text">'.strtoupper($order->total).'</div></td>
	                </tr>
				</table>';
	//             <table width="100%" cellpadding="5" class="child_table tax_value">
	//                 <tr>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">Taxable Value</span><br>Rs.'.$order->total.'</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">CGST</span><br>Rs.0</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">SGST</span><br>Rs.0</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">IGST</span><br>Rs.0</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">Tax Value</span><br>Rs.'.$order->total_tax.'</p></td>
	//                 </tr>
	//             </table>
				$html2 .= '<table width="100%" cellpadding="5" class="child_table">
	                <tr>
	                    <td width="660" align="center"><p><span class="tax_invoice_text">TAX INVOICE - Style by NansJ</span><br>This is computer generated tax invoice.</p></td>
	                </tr>
				</table>
	</div>';

	    	// add a page
	    	$pdf::AddPage();
			
			// output the HTML content
			$pdf::writeHTML($html2, true, false, true, false, '');
	    	
	    	$i++;
		}
    	$filename = "all_order_".time().".pdf";

		// reset pointer to the last page
		$pdf::lastPage();
		$pdf::Output(public_path($filename), 'F');
		$file_url =  url('/all_order_'.time().'.pdf');
		return response()->json(['error' => false, 'msg' => "PDF generated successfully.", "pdf_url" => $file_url, "ErrorCode" => "000"],200); //response()->download(public_path($filename));
  	}

  	function printOrderSlip(Request $request){
  		$d = new DNS1D();
  		// create new PDF document
    	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    	// set document information
    	$pdf::SetTitle('Slip');
    
    	// set default monospaced font
    	$pdf::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    	$pdf::SetPrintHeader(false);
		$pdf::SetPrintFooter(false);
    
    	// set auto page breaks
    	$pdf::SetAutoPageBreak(TRUE, 0);
    
    	// set image scale factor
    	$pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);
    	
    	$pdf::SetFont('helvetica', '', 10);

        // define barcode style
        $style = array(
            'position' => 'C',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '0',
            'vpadding' => '0',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
    
		$orders =DB::table("orders")
		  	->join('billings','orders.oid','=','billings.order_id')	
		  	->where('orders.vid',$request->vid)
		  	->where('orders.oid',$request->oid)
			->where('orders.status','packed')->get();

		$oid = $request->oid;
		$results2 =DB::table("waybill")	
		  	->where('vid',$request->vid)
		  	->where('order_id',$oid)->get()->toArray();
		if(!empty($results2)){
			$wbill = $results2[0]->waybill_no;

	  		$img_base64_encoded = 'data:image/png;base64,'. $d->getBarcodePNG($wbill, 'C128', 3,33,array(1,1,1));
	  		$params = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '">';

	  		$img_base64_encoded2 = 'data:image/png;base64,'. $d->getBarcodePNG($oid, 'C128', 3,33,array(1,1,1));
	  		$params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
		}else{
			$wbill = "";
			$params = "";
	  		$img_base64_encoded2 = 'data:image/png;base64,'. $d->getBarcodePNG($oid, 'C128', 3,33,array(1,1,1));
	  		$params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
		}
	    $i=1;
	    // $params = $d->getBarcodeHTML($wbill, 'C128');
  		// $params = '<img src="data:image/png;base64,' . $d->getBarcodePNG($wbill, 'C128', 3,33,array(1,1,1)) . '" alt="barcode"   />';
		// $params2 = $d->getBarcodeHTML($oid, 'C128');
  		// $params2 = '<img src="data:image/png;base64,' . $d->getBarcodePNG($oid, 'C128', 3,33,array(1,1,1)) . '" alt="barcode"   />';
		
		
		// echo $params2; die;
    	// var_dump($pdf::write1DBarcode($wbill, 'C128', '', '', '', 18, 1.0, $style, 'N')); die;
		foreach($orders as $order){
	// 	    $pdf::Cell(0, 0, 'CODABAR', 0, 1);
	//         $params = $pdf::write1DBarcode($order->id, 'CODABAR', '', '', '', 18, 0.4, $style, 'N');
			// $params = $pdf::serializeTCPDFtagParameters(array($wbill, 'C128', '', '', '', 18, 1.0, $style, 'N'));
			// $params2 = $pdf::serializeTCPDFtagParameters(array($oid, 'C128', '', '', '', 18, 1.0, $style, 'N'));
			// define some HTML content with style

	$html2 = '<style type="text/css">

	@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap");
	table, th, td, p, div, h1, h2, h3, h4, h5, h6 {font-family: "Poppins", sans-serif;}
	table, th, td {  border:1px solid black; border-collapse: collapse; margin: 0 auto;}
	div, table{
	    padding:0;
	    margin:0;   
	}
	h1, h2, h3, h4, h5, h6 {
	    margin: 0;padding:0;
	}
	.tax_invoice_text{font-size:16pt;font-weight:bold;}
	.parent_table p{margin:0;padding:0}
	.parent_table{border:none;}
	.company_info td{font-size: 8pt;}
	.product_info th{font-size: 9pt;font-weight:bold;}
	.product_info td{font-size: 9pt;}
	.currency_text{font-size: 13pt;font-weight:bold;}
	.tax_invoice_val{font-size: 11pt;font-weight:bold;}
	.tax_value p{font-size: 8pt;margin:0;padding:0;}
	.customer_info p, .customer_info td{font-size: 8pt;}
	.customer_info h2{width:100%;}
	td.bottom_barcode{font-size: 11pt;font-weight:bold;}
	.c_name{font-size: 12pt;font-weight:bold;margin-bottom:5px;}
	.cus_name{font-size: 13pt;font-weight:bold;}
	.awb_text{font-size: 13pt;font-weight:bold;}
	</style>
	<div class="parent_table">
	            <table width="100%" cellpadding="5" class="child_table company_info">
	                <tbody>
	                    <tr>
	                        <td rowspan="3" width="360"><p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p></td>
	                        <td width="150">Order No. (REF) : '.$order->oid.'</td>
	                        <td width="150">GST-JR-3557</td>
	                    </tr>
	                    <tr>
	                        <td width="150">Mode : '.$order->payment_method_title.' </td>
	                        <td width="150">Amount : Rs.'.$order->total.'</td>
	                    </tr>
	                    <tr>
	                        <td colspan="2" align="center">Courier Partner : DIRECT_DELHIVERY</td>
	                    </tr>
	                </tbody>
	            </table>
				<table width="100%" cellpadding="5" class="child_table customer_info">
	                <tbody>
	                    <tr>
	                        <td width="660"><p><span class="c_name">'.strtoupper($order->first_name).' '.strtoupper($order->last_name).' <span>(M) '.$order->phone.'</span></span><br>Address : '.$order->address_1;
								if($order->address_2 != ''){
									$html2 .= '<br>Landmark : '.$order->address_2;
								}else{
									$html2 .= '<br>Landmark : N/A';
								}
								$html2 .= '<br>Pincode : '.$order->postcode.', '.$order->city.', '.$order->state.', '.$order->country.'</p>
							</td>
	                    </tr>
	                </tbody>
				</table>
				<table width="100%" cellpadding="5" class="child_table customer_awb">
	                <tbody>
	                    <tr>
	                        <td width="660" align="center"><span class="awb_text">AWB : '.$wbill.'</span><br/>'.$params;
	                        //<tcpdf method="write1DBarcode" params="' . $params . '" />
							$html2 .= '</td>
	                    </tr>
	                </tbody>
				</table>
	            <table width="100%" cellpadding="5" class="child_table product_info">
	                <thead>
	                    <tr>
	                        <th width="40">S.No.</th>
	                        <th width="250">Product(s)</th>
	                        <th width="100">SL Price</th>
	                        <th width="80" align="center">QTY (Pcs)</th>
	                        <th width="90">Discount</th>
	                        <th width="100">Net Amount</th>
	                    </tr>
	                </thead>
				</table>
				<table width="100%" cellpadding="5" class="child_table product_info">
	                <tbody>';
	                	$order_items =DB::table("line_items")->where('order_id','=',$oid)->where('vid','=',$request->vid)->get();
						$j = 1;
						$itm = 0;
						$itm_sub = 0;
						$cnt = count($order_items);
						if($cnt >=1){
							$min_height = 516/$cnt;
						}else{
							$min_height = 516;
						}
						$height = $min_height.'px';
						foreach( $order_items as $product ) {
							$itm = $itm + $product->quantity;
							$itm_sub = $itm_sub + $product->subtotal;
							$html2 .= '<tr>
								<td width="40" height="'.$height.'">'.$j.'</td>
								<td width="250">'.$product->name.'</td>
								<td width="100">Rs.'.$product->subtotal.'</td>
								<td width="80" align="center">'.$product->quantity.'</td>
								<td width="90">Rs.0</td>
								<td width="100">Rs.'.$product->total.'</td>
								</tr>';
							$j++;
						}
				$html2 .= '</tbody></table><table width="100%" cellpadding="5" class="child_table product_info">
	                <tbody>
						<tr>
	                        <td width="40">T1</td>
	                        <td width="250">T1</td>
	                        <td width="100">Rs.'.$itm_sub.'</td>
	                        <td width="80" align="center">'.$itm.'</td>
	                        <td width="90">Rs.'.$order->discount_total.'</td>
	                        <td width="100">Rs.'.$order->total.'</td>
	                    </tr>
	                    <tr>
	                        <td rowspan="2" colspan="4" class="bottom_barcode">
	                            '.$oid.'<br/>'.$params2;
	// $html2 .= '<tcpdf method="write1DBarcode" params="' . $params2 . '" />';
	                        $html2 .= '</td>
	                        <td width="90">Serv.Charges</td>
	                        <td width="100">Payable AMT.</td>
	                    </tr>
	                    <tr>
	                        <td width="90">Rs.'.$order->total_tax.'</td>
	                        <td width="100">Rs.'.$order->total.'</td>
	                    </tr>
	                </tbody>
	            </table>
				
				<table width="100%" cellpadding="5" class="child_table">
	                <tr>
	                    <td width="660" align="center"><div class="currency_text">'.strtoupper($order->total).'</div></td>
	                </tr>
				</table>';
	//             <table width="100%" cellpadding="5" class="child_table tax_value">
	//                 <tr>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">Taxable Value</span><br>Rs.'.$order->total.'</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">CGST</span><br>Rs.0</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">SGST</span><br>Rs.0</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">IGST</span><br>Rs.0</p></td>
	//                     <td width="132" align="center"><p><span class="tax_invoice_val">Tax Value</span><br>Rs.'.$order->total_tax.'</p></td>
	//                 </tr>
	//             </table>
				$html2 .= '<table width="100%" cellpadding="5" class="child_table">
	                <tr>
	                    <td width="660" align="center"><p><span class="tax_invoice_text">TAX INVOICE - Style by NansJ</span><br>This is computer generated tax invoice.</p></td>
	                </tr>
				</table>
	</div>';
	    	// add a page
	    	$pdf::AddPage();
			
			// output the HTML content
			$pdf::writeHTML($html2, true, false, true, false, '');
			$i++;
		}
    	$filename = "order".$request->oid."_".time().".pdf";

		// reset pointer to the last page
		$pdf::lastPage();
		$pdf::Output(public_path($filename), 'F');
		$file_url =  url('/order'.$request->oid.'_'.time().'.pdf');
		return response()->json(['error' => false, 'msg' => "PDF generated successfully.", "pdf_url" => $file_url, "ErrorCode" => "000"],200); //response()->download(public_path($filename));
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

    public function city_Search(Request $request)
	{
	
		       	// $order=orders::whereBetween('date_created_gmt',$range)->get();                 
       	$order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
       	        
       	         ->where('orders.vid',$request->vid)
       	         ->where('billings.vid',$request->vid)
       	         ->select("orders.*","orders.status as orderstatus","billings.*",
		                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = ".intval($request->vid)." GROUP BY line_items.order_id) as quantity"))
       	          // ->Where('billings.city', 'like', '%' . $request->city . '%')
       	         ->Where('billings.city', $request->city)

		          	          ->get();
	
	          // ->select("orders.*","billings.*","line_items.*",

	          //           DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

	          //                       WHERE line_items.order_id = orders.oid

	          //                       GROUP BY line_items.order_id) as quantity"))
	          // ->get();

        return $order;
    }
    public function get_processing_data($vid, $status)
	    	{
	    		// echo $status; die;
	  		    $orders =DB::table("orders")
	  		       ->join('billings','orders.oid','=','billings.order_id')
			  		->where('orders.vid',$vid)
		       ->where('orders.status',$status)
		       ->select("orders.*","billings.*",
		        		DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." GROUP BY line_items.order_id) as quantity"),
		        		DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as name"),
		        		DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as sku"))
		       ->orderBy('oid','DESC')
		        ->get();
		        
				return $orders;
	  		}

    public function state_Search(Request $request)
	{
		       	// $order=orders::whereBetween('date_created_gmt',$range)->get();      
       	$order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
	         ->Where('state', 'like', '%' . $request->state . '%')->get();
	          // ->select("orders.*","billings.*","line_items.*",
	          //           DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
	          //                       WHERE line_items.order_id = orders.oid
	          //                       GROUP BY line_items.order_id) as quantity"))
	          // ->get();
        return $order;
    }
		    public function state_data(Request $request)
		    	{
			       	$order =DB::table('billings')
				       	     ->distinct()
				       	     ->select('billings.state')
				       	     ->where('billings.vid',$request->vid)
			       		 	 ->get();     
			        return $order;
			    }
			public function city_data(Request $request)
		    	{
			       	$order =DB::table('billings')
				       	     ->distinct()
				       	     ->select('billings.city')
				       	     ->where('billings.vid',$request->vid)
			       		 	 ->get();     
			        return $order;
			    }
				    public function status_data(Request $request)
			    	{
				       	$order = DB::table("orders")
					       	     ->distinct()
					       	     ->select('orders.status')
					       	     ->where('orders.vid',$request->vid)
				       		 	 ->get();     
				        return $order;
				    }
		    public function status_Search(Request $request)
				{
             	$order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
       	         		->select("orders.*","orders.status as orderstatus","billings.*",
		                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = ".intval($request->vid)." GROUP BY line_items.order_id) as quantity"))
       	         ->Where('orders.status',$request->status)
       	         ->where('orders.vid',$request->vid)
       	         ->where('billings.vid',$request->vid)
	              ->get();
	              return $order;
			     }
		  	function Refundchange_Status(Request $request)
		  		{
  					$imp = explode(',', $request->allSelected);
				  		for ($i=0; $i < count($imp); $i++) 
				  			{ 
					  		   DB::table('orders')
					          ->where('oid', $imp[$i])
					          ->where('vid', $request->vid)
					          ->update(['status' => $request->status]);

								$vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();
						        $curl = curl_init();
							    curl_setopt_array($curl, array(
							    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$imp[$i].'?status='.$request->status,
							    CURLOPT_RETURNTRANSFER => true,
							    CURLOPT_ENCODING => '',
							    CURLOPT_MAXREDIRS => 10,
							    CURLOPT_TIMEOUT => 0,
							    CURLOPT_FOLLOWLOCATION => true,
							    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							    CURLOPT_CUSTOMREQUEST => 'PUT',
							    CURLOPT_HTTPHEADER => array(
			       				 'Authorization: Basic '.$vendor[0]->token
			      			),
			    		));
							    $response = curl_exec($curl);
							    curl_close($curl);
							    $jsonResp = json_decode($response);
						    }
	        			return response()->json(['error' => false, 'msg' => "Order Status successfully updated.","ErrorCode" => "000"],200);
  				}

			function download_Sheet(Request $request){
				if ($request->selectall) {
					$listImp=explode(',',$request->selectall);

					$vid = intval($request->vid);
					$orderItems[] =DB::table("orders")

	  		       // ->join('billings','orders.oid','=','billings.order_id')
			  		->where('orders.vid',$vid)
			  		->whereIn('orders.oid', $listImp)
		        	
		       ->orderBy('oid','DESC')
		        ->get();

					// dd($listImp);
				//   for($i=0;$i<count($listImp); $i++)
				// {
		    // $orderItems[] =DB::table("orders.*",
		    // 			 // ->where('orders.oid',intval($listImp[$i]))
		    // 		     	DB::raw("(SELECT line_items.name as Name, SUM(line_items.quantity) as Quantity FROM line_items WHERE line_items.order_id = orders.oid)"))
	     //    	         // ->select("orders.oid as OrderID","orders.status as Status","line_items.quantity as Qty","line_items.parent_name as Parent","orders.date_created as Date")
	     //    	        // ->where('orders.vid',intval($request->vid)) 
	     //    	        ->whereIn('orders.oid', $listImp)
	     //                ->get();


	           // }

	           // var_dump($orderItems); die;
	                }else{
	                	$orderItems[] =DB::table("orders")
	                	->where('vid',intval($request->vid)) 
	                    ->get();

	                }
	 
	         return $orderItems;		

	  	}

	  	function changeProcessing_Status(Request $request){
	  		// var_dump($_REQUEST); die;
	  		// echo $request->loc; die;
	  		if($request->loc == "wp"){
	  			$listImp['0'] = $request->oid;
	  		}elseif($request->allSelected == "false"){
	  			$listImp['0'] = $request->oid;
	  		// var_dump($listImp); die;
	  		}else{
	  			$listImp=explode(',',$request->allSelected);
	  		}

	  	  		for($i=0;$i<count($listImp); $i++)
	  		          {
		  				DB::table('orders')
			          ->where('oid', intval($listImp[$i]))
			          ->where('vid', intval($request->vid))
			          ->update(['status' => $request->status_assign]);
					// print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
					// https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
				$vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();
					          $curl = curl_init();
				    curl_setopt_array($curl, array(
				    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$listImp[$i].'?status='.$request->status_assign,
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_ENCODING => '',
				    CURLOPT_MAXREDIRS => 10,
				    CURLOPT_TIMEOUT => 0,
				    CURLOPT_FOLLOWLOCATION => true,
				    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				    CURLOPT_CUSTOMREQUEST => 'PUT',
				    CURLOPT_HTTPHEADER => array(

				        'Authorization: Basic '.$vendor[0]->token
				      ),
				    ));

				    $response = curl_exec($curl);
				    curl_close($curl);
				    $jsonResp = json_decode($response);
					// var_dump($jsonResp);
	  		}
	  		return response()->json(['error' => false, 'msg' => "Order Status successfully updated.","ErrorCode" => "000"],200);
	  	}

	  	public function getProcessingOrder_Details(Request $request)
		{
			$vendor = $request->vid;
			if($vendor != null){
				$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
				   // ->join('waybill','orders.oid','=','waybill.order_id')
		          ->where('orders.vid','=',intval($vendor))
		          ->where('billings.vid','=',intval($vendor))
		          ->orderBy('oid','DESC')
		           // ->select("orders.*","waybill.waybill_no","orders.status as orderstatus","billings.*",
		          ->select("orders.*","orders.status as orderstatus","billings.*",
		                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = ".intval($vendor)." GROUP BY line_items.order_id) as quantity"))
		          	          ->get();
		       
		    }else{
		    	$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
		    	->orderBy('oid', 'DESC')
		          ->select("orders.*","orders.status as orderstatus","billings.*",
		                    DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
		                                WHERE line_items.order_id = orders.oid
		                                GROUP BY line_items.order_id) as quantity"))    
		          ->get();
		        }
	        return $orders;
		}

	function processing_download_Sheet(Request $request){
				if ($request->allSelected) 
				{
					$listImp=explode(',',$request->allSelected);
					$vid = intval($request->vid);
					$orders[] =DB::table("orders")
	  		       ->join('billings','orders.oid','=','billings.order_id')
	  		       ->select("orders.oid as OrderID","orders.date_created as OrderDate","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")    
		  		   ->where('orders.vid',$vid)
		           ->where('orders.status',"processing")
		       	   ->where('orders.vid',$vid)
			  	   ->whereIn('orders.oid', $listImp)
		           ->orderBy('oid','DESC')
		           ->get();
			    }
			    else
			        {
	                	$orders[] =DB::table("orders")->join('billings','orders.oid','=','billings.order_id')
	                	->select("orders.oid as OrderID","orders.date_created as OrderDate","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")
	                	->where('orders.vid',intval($request->vid)) 
	                	 ->where('orders.status',"processing")
						->where('billings.vid',intval($request->vid)) 
	                    	                    ->get();

	                }
	 
	         return $orders;		

	  	}

	function onhold_download_Sheet(Request $request){
				if ($request->allSelected) 
				{
					$listImp=explode(',',$request->allSelected);
					$vid = intval($request->vid);
					$orders[] =DB::table("orders")
	  		       ->join('billings','orders.oid','=','billings.order_id')
	  		       ->select("orders.oid as OrderID","orders.date_created as OrderDate","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")    
		  		   ->where('orders.vid',$vid)
		           ->where('orders.status',"on-hold")
		       	   ->where('orders.vid',$vid)
			  	   ->whereIn('orders.oid', $listImp)
		           ->orderBy('oid','DESC')
		           ->get();
			    }
			    else
			        {
	                	$orders[] =DB::table("orders")->join('billings','orders.oid','=','billings.order_id')
	                	->select("orders.oid as OrderID","orders.date_created as OrderDate","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")
	                	->where('orders.vid',intval($request->vid)) 
	                	 ->where('orders.status',"on-hold")
						->where('billings.vid',intval($request->vid)) 
	                    	                    ->get();

	                }
	 
	         return $orders;		

	  	}

	  	function confirm_download_Sheet(Request $request){
				if ($request->allSelected) 
				{
					$listImp=explode(',',$request->allSelected);
					$vid = intval($request->vid);
					$orders[] =DB::table("orders")
	  		       ->join('line_items','orders.oid','=','line_items.order_id')
	  		       ->select("orders.oid as OrderID","orders.date_created as OrderDate","line_items.product_id as Item ID","line_items.name as Item Name","line_items.sku as SKU","line_items.quantity as Qty")    
		  		   ->where('orders.vid',$vid)
		  		     ->where('line_items.vid',$vid)
		           ->where('orders.status',"confirmed")
			  	   ->whereIn('orders.oid', $listImp)
		           ->orderBy('oid','DESC')
		           ->get();
			    }
			    else
			        {
	                	$orders[] =DB::table("orders")->join('line_items','orders.oid','=','line_items.order_id')
	                	 ->select("orders.oid as OrderID","orders.date_created as OrderDate","line_items.product_id as Item ID","line_items.name as Item Name","line_items.sku as SKU","line_items.quantity as Qty") 
	                	->where('orders.vid',intval($request->vid)) 
	                	 ->where('orders.status',"confirmed")
						->where('line_items.vid',intval($request->vid)) 
	                    	                    ->get();

	                }
	 	         return $orders;		
	  	}		

	  	function pending_download_Sheet(Request $request){
				if ($request->allSelected) 
				{
					$listImp=explode(',',$request->allSelected);
					$vid = intval($request->vid);
					$orders[] =DB::table("orders")
	  		       ->join('billings','orders.oid','=','billings.order_id')
	  		       ->select("orders.oid as OrderID","orders.date_created as OrderDate","orders.payment_method as Payment Method","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")    
		  		   ->where('orders.vid',$vid)
		           ->where('orders.status',$request->status)
		       	   ->where('orders.vid',$vid)
			  	   ->whereIn('orders.oid', $listImp)
		           ->orderBy('oid','DESC')
		           ->get();
			    }
			    else
			        {
	                	$orders[] =DB::table("orders")->join('billings','orders.oid','=','billings.order_id')
	                	->select("orders.oid as OrderID","orders.date_created as OrderDate","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")
	                	->where('orders.vid',intval($request->vid)) 
	                	 ->where('orders.status',$request->status)
						->where('billings.vid',intval($request->vid)) 
	                    	                    ->get();

	                }
	 
	         return $orders;		

	  	}  		

	  	function delivery_download_Sheet(Request $request){
				if ($request->selectall) 
				{
					$listImp=explode(',',$request->selectall);
					$vid = intval($request->vid);
					$orders[] =DB::table("orders")
	  		       ->join('billings','orders.oid','=','billings.order_id')
	  		       ->select("orders.oid as OrderID","orders.date_created as OrderDate","orders.payment_method as Payment Method","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")    
		  		   ->where('orders.vid',$vid)
		           ->where('orders.status',"dispatched")
		       	   ->where('orders.vid',$vid)
			  	   ->whereIn('orders.oid', $listImp)
		           ->orderBy('oid','DESC')
		           ->get();
			    }
			    else
			        {
	                	$orders[] =DB::table("orders")->join('billings','orders.oid','=','billings.order_id')
	                	->select("orders.oid as OrderID","orders.date_created as OrderDate","billings.first_name as First Name","billings.last_name as Last Name","billings.city as City","billings.state as State","billings.email as Email","billings.email as Email")
	                	->where('orders.vid',intval($request->vid)) 
	                	 ->where('orders.status',"dispatched")
						->where('billings.vid',intval($request->vid)) 
	                    	                    ->get();

	                }
	 
	         return $orders;		

	  	}  	

	  	function changeStatusDispatch(Request $request){
	  		// echo $request->dispatch;
	  		// echo $request->vid; die;
	  		$order_items =DB::table("waybill")
					->where('waybill.vid',intval($request->vid))
                    ->orWhere('waybill.order_id',$request->dispatch)
                    ->orwhere('waybill.waybill_no',$request->dispatch)->get()->toArray();

            // foreach ($order_items as $order) {
                $this->changeOrderStatus(intval($request->vid),intval($order_items[0]->order_id),"dispatched"); 

	  		return response()->json(['error' => false, 'msg' => "Success, Your order number ". intval($order_items[0]->order_id) ." with AWB number is ". intval($order_items[0]->waybill_no), "ErrorCode" => "000"],200);
            // }
            // var_dump($order_items); die;
	  	}	
	  
}
