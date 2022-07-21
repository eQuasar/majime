<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Models\Vendors;
use App\Models\Orders;
use App\Models\Billings;
use App\Models\shippings;
use App\Models\meta_data;
use App\Models\LineItems;
use App\Models\LineItemsMeta;
use App\Models\tax_lines;
use App\Models\shipping_lines;
use App\Models\Order_fee_lines;
use App\Models\Order_Coupan_lines;
use App\Models\Order_Refunds;
use App\Models\Order_links;
use App\Models\meta_data_value;
use App\Models\Products;
use App\Models\Category;
use App\Models\ProductTag;
use App\Models\UpdateStatus;
use App\Models\Product_Attribute;
use Illuminate\Support\Facades\DB;

class JsonController extends Controller
{	
	public function getAllLinks()
	{
		$data= Vendors::all();
		return $data;
	}

    public function getJson(Request $request)
	{
		//DD($request);
		$url = $request->url;
		$vid = $request->vid;
		$jsonResponse=$this->getOrderWP($url, $vid);
		$this->InsertOrder($jsonResponse, $vid, $url);
	}

    public function get_order_data(Request $request)
	{
		$url = $_REQUEST['api_url'];
		$vid = $_REQUEST['vid'];
		$jsonResponse=$this->addOrderWP($url, $vid);
		$this->InsertOrderID($jsonResponse, $vid, $url);
    }

      public function order_update(Request $request)
	{
		$oid = $request->oid;
		$status=$request->status;
		$jsonResponse=$this->OrderUpdateWP($url, $vid);
		$this->InsertOrderID($jsonResponse, $vid, $url);
    }




	private function addOrderWP($url, $vid)
	{
		$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		$curl = curl_init();

	    curl_setopt_array($curl, array(

	    CURLOPT_URL => $url,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(

	        'Authorization: Basic '.$vendor[0]->token
	      ),
	    ));

	    $response = curl_exec($curl);
	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}

 	public function InsertOrderID($order, $vid, $url)
	{

		$orderItems =DB::table("orders")->where('oid','=',$order->id)->where('vid','=',intval($vid))->get()->toArray();
		
		if(!empty($orderItems)){}else{
		
          	$Orders[]=[
				'oid'=>$order->id,
				'vid'=>intval($vid),
				'parent_id'=>$order->parent_id,
				'status'=>$order->status,
				'currency'=>$order->currency,
				 'version'=>$order->version,
				 'prices_include_tax'=>$order->prices_include_tax,
				 'date_created'=>$order->date_created,
				 'date_modified'=>$order->date_modified,
				 'discount_total'=>$order->discount_total,
				 'discount_tax'=>$order->discount_tax,
				 'shipping_total'=>$order->shipping_total,
				 'shipping_tax'=>$order->shipping_tax,
				 'cart_tax'=>$order->cart_tax,
				 'total'=>$order->total,
				 'total_tax'=>$order->total_tax,
				 'customer_id'=>$order->customer_id,
				 'order_key'=>$order->order_key,
				 'payment_method'=>$order->payment_method,
				 'payment_method_title'=>$order->payment_method_title,
				 'transaction_id'=>$order->transaction_id,
				 'customer_ip_address'=>$order->customer_ip_address,
				 // 'customer_user_agent'=>$order->customer_user_agent,
				 'customer_user_agent'=>'',
				 'created_via'=>$order->created_via,
				 'customer_note'=>'NA',
				 'date_completed'=>$order->date_completed,
				 'date_paid'=>$order->date_paid,
				 'cart_hash'=>$order->cart_hash,
				 'number'=>$order->number,
				 'date_created_gmt'=>$order->date_created_gmt,
				 'date_modified_gmt'=>$order->date_modified_gmt,
				 'date_completed_gmt'=>$order->date_completed_gmt,
				 'date_paid_gmt'=>$order->date_paid_gmt,
				 'currency_symbol'=>$order->currency_symbol,
			];
			$this->InsertBilling($order->id,$order->billing,$vid);
			$this->InsertShipping($order->id,$order->shipping,$vid);
		    //$this->OrderMetaData($order->id,$order->meta_data);
		    $this->insertLineItems($order->id,$order->line_items,$vid);
			//$this->LineItem_Metadata($order->id,$order->line_items);
			$this->OrderTaxLines($order->id,$order->tax_lines,$vid);
			$this->OrderShipping_Lines($order->id,$order->shipping_lines,$vid);
			$this->OrderFee_Lines($order->id,$order->fee_lines,$vid);
			$this->OrderCoupan_Lines($order->id,$order->coupon_lines,$vid);
		    $this->Order_refunds($order->id,$order->refunds,$vid);
			$this->Order_links($order->id,$order->_links,$vid);

			// $this->getWayBill($vid, $url);
	    
	    	Orders::insert($Orders); 	
       }
    }

    public function getWayBill($vid, $url){
    	// https://isdemo.in/fc/wp-json/waybill_import/waybill_import_data
    	$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		var_dump($vendor);
    	$curl = curl_init();

	    curl_setopt_array($curl, array(

	    CURLOPT_URL => $url.'/wp-json/waybill_import/waybill_import_data',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(

	        'Authorization: Basic '.$vendor[0]->token
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response, true);

	    // var_dump($jsonResp); die;

	    for ($i=0; $i < count($jsonResp); $i++)
		{
			
			DB::table('waybill')->insert(
			    ['vid' => intval($vid), 'order_id' => $jsonResp[$i]['order_id'], 'waybill_no' => $jsonResp[$i]['waybill_no'], 'return_waybill_no' => $jsonResp[$i]['return_waybill_no'], 'date_created' => $jsonResp[$i]['date_created']]
			);

	    }
    }

	private function getProductWP($url, $vid)
	{
		$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		$curl = curl_init();

	    curl_setopt_array($curl, array(

	    CURLOPT_URL => $url.'/wp-json/wc/v3/products?per_page=100',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(

	        'Authorization: Basic '.$vendor[0]->token
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}


	private function getProductCatWP($url, $vid)
	{
		$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		$curl = curl_init();

	    curl_setopt_array($curl, array(

	    CURLOPT_URL => $url.'/wp-json/wc/v3/products/categories?per_page=100',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(

	        'Authorization: Basic '.$vendor[0]->token
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;

 	}
	
	private function getOrderWP($url, $vid)
	{
		$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		$curl = curl_init();

	    curl_setopt_array($curl, array(

	    CURLOPT_URL => $url.'/wp-json/wc/v3/orders?per_page=100',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(

	        'Authorization: Basic '.$vendor[0]->token
	      ),
	    ));

	    $response = curl_exec($curl);
	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}

 	private function getProductTagWp($url, $vid)
	{
		$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		$curl = curl_init();

	    curl_setopt_array($curl, array(

	    CURLOPT_URL => $url.'/wp-json/wc/v3/products/tags?per_page=100',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(

	        'Authorization: Basic '.$vendor[0]->token
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}

 	private function getProductAttWp($url, $vid)
	{
		$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		$curl = curl_init();

	    curl_setopt_array($curl, array(

	    CURLOPT_URL => $url.'/wp-json/wc/v3/products/attributes?per_page=100',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(

	        'Authorization: Basic '.$vendor[0]->token
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}

 	public function cronOrderStatusUpdate($vid){


// /*		
		// Forward Orders
 		$orders=DB::table("orders")
 				// ->join('waybill', 'orders.oid','=','waybill.order_id')
				->whereIn('orders.status',['dispatched','intransit'])
				// ->where('orders.oid',intval('6907'))
 				// ->whereIn('orders.status',['dtointransit']) 
				 
 				->where('orders.vid',intval($vid))
				->orderBy('orders.oid','desc')
				->get();
		// print_r($orders); exit();
		echo "Forward Orders: ".$countOfOrders = count($orders);
		if($countOfOrders==0){          // If no record found
			$Result['0'] = "No update required.";
			echo "<pre>";print_r($Result);echo "</pre>";
		}else{
			$result = $this->getAWBStatus($orders,$vid,"fwd");
			echo "<pre>";print_r($result);echo "</pre>";
		}
// */

		// Return Orders
		$orders=DB::table("orders")
			->whereIn('orders.status',['dtobooked','dtointransit'])
			// ->where('orders.oid',intval('7107'))
			->where('orders.vid',intval($vid))
			->orderBy('orders.oid','desc')
			->get();
		echo "Reverse Orders: ".$countOfOrders = count($orders);
		if($countOfOrders==0){          // If no record found
			$Result['0'] = "No update required.";
			echo "<pre>";print_r($Result);echo "</pre>";
		}else{
			$result = $this->getAWBStatus($orders,$vid,"rev");
			echo "<pre>";print_r($result);echo "</pre>";
		}
		exit();

 	}

 	public function getAWBStatus($orders,$vid,$fwdOrReturn)
	{	
		if($vid == 1){
			$curlopt_url = "https://staging-express.delhivery.com/api/v1/packages/json/?";
			$del_url = "https://staging-express.delhivery.com/api/v1/packages/json/?";
			
		}else{
			$curlopt_url = "https://track.delhivery.com/api/v1/packages/json/?";
			$del_url = "https://track.delhivery.com/api/v1/packages/json/?";
		}

		$waybill_nos = "";
		foreach ($orders as $order) {
			$waybillnos=DB::table("waybill")
 				->where('order_id',$order->oid)
				->where('vid',$vid)
				->limit('1')
				->orderBy('id','desc')
				->get();
				// var_dump($waybillnos); die;
				if($fwdOrReturn == "fwd"){
					$waybillno = $waybillnos[0]->waybill_no;
				}else{
					$waybillno = $waybillnos[0]->return_waybill_no ;
				}
				

			
	    
			$url = $del_url."waybill=".$waybillno."&token=ed99803a18868406584c6d724f71ebccc80a89f9";

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $url, 
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET' 
			));
		
			$response = curl_exec($curl);
			$response = json_decode($response,true);

			if (isset($response['Error'])){
				$data['0'] = "Error: ".$response['Error'];
			}else{
				$data[$order->oid] = $this->processAWBStatus($response,$vid,$order->oid);
			}
		}
		return $data;
	    // $waybill_nos = substr($waybill_nos, 1);
	}
	public function processAWBStatus($response,$vid,$order_id)
	{
		// print_r($response);
		if (isset($response['ShipmentData'])){
			
			for($i=0; $i < sizeof($response['ShipmentData']) ; $i++){

				// waybill table get orderId of awb = $response['ShipmentData'][$i]['Shipment']['AWB'];
				
				$status = $response['ShipmentData'][$i]['Shipment']['Status']['Status'];


				if ($status == "Manifested" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
					$status = "dispatched";
				}else if ($status == "Pending" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "UD" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
					$status = "intransit";
				}else if ($status == "In Transit" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
					$status = "intransit";
				}else if ($status == "In Transit" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == TRUE && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType']!="RT"){
					$status = "dtointransit";
				}else if ($status == "In Transit" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == TRUE && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType']=="RT"){
					$status = "intransit";
				} else if ($status == "RTO" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "DL" ){
					$status = "rto-delivered";
				} else if ($status == "DTO" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "DL" ){
					$status = "dtodelivered";
				} else if ($status == "DTO" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] != "DL" ){
					$status = "dtointransit";
				} else if ($status == "Closed" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] != "CN" ){
					$status = "deliveredtocust";
				} else if ($status == "Delivered" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] != "DL" ){
					$status = "deliveredtocust";
				} else if ($status == "DTO" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] != "DL" ){
					$status = "dtodelivered";
				} else if ($status == "Canceled" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] != "CN" ){
					$status = "deliveredtocust";
				}
				//  else if ($status == "Delivered"){
				// 	$status = "deliveredtocust";
				// // }else if(){
				// // 	delivery_status_name     DL
				// // 	delivery_brief_status    delivered
				// }
				else{
					$status = "undefined";
				}
				// $order_id = $orderIds[$response['ShipmentData'][$i]['Shipment']['AWB']];
				
		// 		foreach ($orders as $order) {
					// $waybill_nos = $waybill_nos.",".$order->waybill_no;
			//   }
			//   $waybill_nos = substr($waybill_nos, 1);
				$statusDel[$i]['vid'] = addslashes( $vid );
				$statusDel[$i]['awb'] = addslashes( $response['ShipmentData'][$i]['Shipment']['AWB'] );
				$statusDel[$i]['oid'] = addslashes( $order_id );
				$statusDel[$i]['status'] = addslashes( $status );
				$statusDel[$i]['ReverseInTransit'] = addslashes( $response['ShipmentData'][$i]['Shipment']['ReverseInTransit']);
				$statusDel[$i]['delivery_status_name'] = addslashes( $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] );
				$statusDel[$i]['delivery_status'] = addslashes( $response['ShipmentData'][$i]['Shipment']['Status']['Status'] );
				$statusDel[$i]['delivery_status_code'] = addslashes( $response['ShipmentData'][$i]['Shipment']['Status']['StatusCode'] );
				$statusDel[$i]['delivery_order_sno'] = addslashes( $response['ShipmentData'][$i]['Shipment']['ReferenceNo'] );
				$statusDel[$i]['delivery_status_date_and_time'] = addslashes( $response['ShipmentData'][$i]['Shipment']['Status']['StatusDateTime'] );
				$statusDel[$i]['delivery_brief_status'] = strtolower( addslashes( $response['ShipmentData'][$i]['Shipment']['Status']['Status'] ) );
				$statusDel[$i]['delivery_instructions'] = addslashes( $response['ShipmentData'][$i]['Shipment']['Status']['Instructions'] );
				$statusDel[$i]['delivery_dispatch_count'] = (int) $response['ShipmentData'][$i]['Shipment']['DispatchCount'];
				$statusDel[$i]['delivery_invoice_amount'] =  $response['ShipmentData'][$i]['Shipment']['InvoiceAmount'];
				$statusDel[$i]['delivery_scans'] = addslashes( json_encode($delivery_scans = $response['ShipmentData'][$i]['Shipment']['Scans'] ) );
				$statusDel[$i]['delivery_destination_received_date'] = addslashes( str_replace( "T", " ", $response['ShipmentData'][$i]['Shipment']['DestRecieveDate'] ) );
				$statusDel[$i]['delivery_pickup_date'] = addslashes( str_replace( "T", " ", $response['ShipmentData'][$i]['Shipment']['PickUpDate'] ) );
				$statusDel[$i]['delivery_charged_weight_in_grams'] = (int) $response['ShipmentData'][$i]['Shipment']['ChargedWeight'] ;
			}
		}else{
			return "No Data Found";
		}


		// print_r($statusDel);
		// die();
    	$ResUpdateStatusDelivary = $this->updateStatusDelivary($statusDel,$vid);

    	
    	return  $ResUpdateStatusDelivary;

 	}

 	public function updateStatusDelivary($statusDel,$vid){
 		foreach($statusDel as  $status_update)
 		{
          	$data[]=[
				
				'vid'=>intval($vid),
				'orderid'=>$status_update['oid'],
				'awb'=>$status_update['awb'],
				'status'=>$status_update['status'],
				'delivery_status_name'=>$status_update['delivery_status_name'],
				'delivery_status_code'=>$status_update['delivery_status_code'],
				'delivery_order_sno'=>$status_update['delivery_order_sno'],
				 'delivery_status_date_and_time'=>$status_update['delivery_status_date_and_time'],
				 'delivery_brief_status'=>$status_update['delivery_brief_status'],
				 'delivery_status'=>$status_update['delivery_status'],
				 'delivery_instructions'=>$status_update['delivery_instructions'],
				 'delivery_dispatch_count'=>$status_update['delivery_dispatch_count'],
				 'delivery_invoice_amount'=>$status_update['delivery_invoice_amount'],
				 'delivery_scans'=>$status_update['delivery_scans'],
				//  'delivery_destination_received_date'=>$status_update['delivery_destination_received_date'],
				 'delivery_pickup_date'=>$status_update['delivery_pickup_date'],
				 'delivery_charged_weight_in_grams'=>$status_update['delivery_charged_weight_in_grams'],
				 			
			];
			$statusCheck=DB::table("update_statuses")
				->where([
							'update_statuses.awb' => $status_update['awb'],
							'update_statuses.delivery_status_code' => $status_update['delivery_status_code'],
							'update_statuses.delivery_brief_status' => $status_update['delivery_brief_status']
					])->get();

		
			if( sizeof($statusCheck) == 0){
				$Result[$status_update['awb']] = "AWB: ".$status_update['awb']." Updated Successfully.";
				UpdateStatus::insert($data);
				// var_dump($status_update); die;
				
				if($status_update['status'] != "undefined"){
					OrderController::changeOrderStatus(intval($vid),$status_update['oid'],$status_update['status']); 
				}
				

				// call order status update method that will further update vendor's wordpress order status as well.
				
			}else{
				$Result['0'] = "No update required.";
			}
	    }
		return $Result;
	}


 	// Table to add all data 



 		// order table status get check if new status and old status is different



 		// update the status in oredr table (Hold)




 		/*
 		DB::table('orders')
          ->where('oid', $request->oid)
          ->where('vid', $request->vid)
          ->update(['status' => $request->status_assign]);

			// print_r($woocommerce->put('orders/'.$request->oid, $data)); die;
			// https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
		$vendor =DB::table("vendors")->where('id','=',intval($request->vid))->get();
		
          $curl = curl_init();

		    curl_setopt_array($curl, array(

		    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.$request->oid.'?status='.$request->status_assign,
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
		  */
 

    public function getUpdateStatus(Request $request)
	{	

// https://dlv-api.delhivery.com/v3/track?wbn=1325110000232

		$response = OrderController::changeStatus($request);
		return  $response;

 	}

	public function InsertOrder($InOrder, $vid, $url){
		foreach($InOrder as $order)
		{
			// var_dump($order); die;
          	$Orders[]=[
				'oid'=>intval($order->id),
				'vid'=>intval($vid),
				'parent_id'=>$order->parent_id,
				'status'=>$order->status,
				'currency'=>$order->currency,
				 'version'=>$order->version,
				 'prices_include_tax'=>$order->prices_include_tax,
				 'date_created'=>$order->date_created,
				 'date_modified'=>$order->date_modified,
				 'discount_total'=>$order->discount_total,
				 'discount_tax'=>$order->discount_tax,
				 'shipping_total'=>$order->shipping_total,
				 'shipping_tax'=>$order->shipping_tax,
				 'cart_tax'=>$order->cart_tax,
				 'total'=>$order->total,
				 'total_tax'=>$order->total_tax,
				 'customer_id'=>$order->customer_id,
				 'order_key'=>$order->order_key,
				 'payment_method'=>$order->payment_method,
				 'payment_method_title'=>$order->payment_method_title,
				 'transaction_id'=>$order->transaction_id,
				 'customer_ip_address'=>$order->customer_ip_address,
				 // 'customer_user_agent'=>$order->customer_user_agent,
				 'customer_user_agent'=>'',
				 'created_via'=>$order->created_via,
				 // 'customer_note'=>$order->customer_note,
				 'customer_note'=>'NA',
				 'date_completed'=>$order->date_completed,
				 'date_paid'=>$order->date_paid,
				 'cart_hash'=>$order->cart_hash,
				 'number'=>$order->number,
				 'date_created_gmt'=>$order->date_created_gmt,
				 'date_modified_gmt'=>$order->date_modified_gmt,
				 'date_completed_gmt'=>$order->date_completed_gmt,
				 'date_paid_gmt'=>$order->date_paid_gmt,
				 'currency_symbol'=>$order->currency_symbol,
			];
			$this->InsertBilling($order->id,$order->billing,$vid);
			$this->InsertShipping($order->id,$order->shipping,$vid);
		    //$this->OrderMetaData($order->id,$order->meta_data);
		    $this->insertLineItems($order->id,$order->line_items,$vid);
			//$this->LineItem_Metadata($order->id,$order->line_items);
			$this->OrderTaxLines($order->id,$order->tax_lines,$vid);
			$this->OrderShipping_Lines($order->id,$order->shipping_lines,$vid);
			$this->OrderFee_Lines($order->id,$order->fee_lines,$vid);
			$this->OrderCoupan_Lines($order->id,$order->coupon_lines,$vid);
		    $this->Order_refunds($order->id,$order->refunds,$vid);
			$this->Order_links($order->id,$order->_links,$vid);

	    }
       	Orders::insert($Orders); 	

       	$this->getWayBill($vid, $url);
			
		$jsonResponse=$this->getProductWP($url, intval($vid));
		$this->InsertProduct($jsonResponse, $vid);
		// dd($jsonResponse);

		$jsonResponsecat=$this->getProductCatWP($url, intval($vid));
		$this->InsertProductCat($jsonResponsecat, $vid);
		// dd($jsonResponsecat);

		$jsonResponseTag=$this->getProductTagWp($url, intval($vid));
		$this->InsertProductTag($jsonResponseTag, $vid);
		//dd($jsonResponseTag);

		$jsonResponseAtt=$this->getProductAttWp($url, intval($vid));
		$this->InsertProductAtt($jsonResponseAtt, $vid);
		// dd($jsonResponseAtt);
		
    }

	public function InsertProduct($ProductData,$vid)
		{	
			// dd($ProductData);
			foreach($ProductData as $InProduct)
		 {
		 	$cat = '';
		 	for ($i=0; $i < count($InProduct->categories); $i++) { 
		 		// if($i == count($InProduct->categories)-1){
		 			// $cat .= $InProduct->categories[$i]->name;
		 		// }else{
		 			$cat .= $InProduct->categories[$i]->name.",";
		 		// }
		 		
		 	}

			$product[]=[
				'vid'=>intval($vid),
				'product_id'=>$InProduct->id,
				'name'=>$InProduct->name,
				'slug'=>$InProduct->slug,
				'permalink'=>$InProduct->permalink,
//     'type'=>'',
//     'status'=>'',
// 'featured'=>'',
// 'catalog_visibility'=>'',
// 'description'=>'',
// 'short_description'=>'',
// 'sku'=>'',
// 'price'=>'',
// 'regular_price'=>'',
// 'sale_price'=>'',
// 'on_sale'=>'',
				'type'=>$InProduct->type,
				'status'=>$InProduct->status,
				'featured'=>$InProduct->featured,
				'catalog_visibility'=>$InProduct->catalog_visibility,
				'description'=>$InProduct->description,
				'short_description'=>$InProduct->short_description,
				'sku'=>$InProduct->sku,
				'price'=>$InProduct->price,
				'regular_price'=>$InProduct->regular_price,
				'sale_price'=>$InProduct->sale_price,
				'on_sale'=>$InProduct->on_sale,

				// 'purchasable'=>'',
				//'total_sales'=>'',
				//'virtual'=>'',
				//'downloadable'=>'',
			
				//'download_limit'=>'',
				//'download_expiry'=>'',
				//'external_url'=>'',
				//'button_text'=>'',
				//'tax_status'=>'',
				//'tax_class'=>'',
				//'manage_stock'=>'',
				//'stock_quantity'=>'',
				//'backorders'=>'',
				//'backorders_allowed'=>'',
				'backordered'=>'',
				'low_stock_amount'=>'',
				'sold_individually'=>'',
				'weight'=>'',
				'shipping_required'=>'',
				//'shipping_taxable'=>'',
				//'shipping_class'=>'',
			//	'shipping_class_id'=>'',
			//	'reviews_allowed'=>'',
				//'average_rating'=>'',
				//'rating_count'=>'',
				//'upsell_ids'=>'',
				'cross_sell_ids'=>'',
				//'parent_id'=>'',
		
				//'purchase_note'=>'',

				'purchasable'=>$InProduct->purchasable,
				'total_sales'=>$InProduct->total_sales,
				 'virtual'=>$InProduct->virtual,
				 'downloadable'=>$InProduct->downloadable,
				 'download_limit'=>$InProduct->download_limit,
				'download_expiry'=>$InProduct->download_expiry,
				 'external_url'=>$InProduct->external_url,
				 'button_text'=>$InProduct->button_text,
				'tax_status'=>$InProduct->tax_status,
				 'tax_class'=>$InProduct->tax_class,
				'manage_stock'=>$InProduct->manage_stock,
				 'stock_quantity'=>$InProduct->stock_quantity,
				'backorders'=>$InProduct->backorders,
				'backorders_allowed'=>$InProduct->backorders_allowed,
				//
				 //'low_stock_amount'=>$InProduct->low_stock_amount,
				 //'sold_individually'=>$InProduct->sold_individually,
				 //'weight'=>$InProduct->weight,
				// 'shipping_required'=>$InProduct->shipping_required,
				 'shipping_taxable'=>$InProduct->shipping_taxable,
				'shipping_class'=>$InProduct->shipping_class,
				'shipping_class_id'=>$InProduct->shipping_class_id,
				 'reviews_allowed'=>$InProduct->reviews_allowed,
			 	'average_rating'=>$InProduct->average_rating,
				// 'rating_count'=>$InProduct->rating_count,
			//	'upsell_ids'=>$InProduct->upsell_ids,
				//'cross_sell_ids'=>$InProduct->cross_sell_ids, 
				'parent_id'=>$InProduct->purchase_note,
			 	'purchase_note'=>$InProduct->purchase_note,
				'categories'=> $cat,
				];
			}

    	 Products::insert($product);
    }


    public function InsertProductCat($ProductCat,$vid)
		{	
			//dd($ProductCat);
			foreach($ProductCat as $InProductCat)
		 {
			$productCat[]=[

					'vid'=>intval($vid),
					'category_id'=>$InProductCat->id,
					'name'=>$InProductCat->name,
					'slug'=>$InProductCat->slug,
		
				];
			}

    	  Category::insert($productCat);
    }

    public function InsertProductTag($ProductTag,$vid)
		{	

			foreach($ProductTag as $InProductTag)
		 {
			$productTags[]=[

					'vid'=>intval($vid),
					'tag_id'=>$InProductTag->id,
					'name'=>$InProductTag->name,
					'slug'=>$InProductTag->slug,
					'description'=>$InProductTag->description,
					'count'=>$InProductTag->count,
				];
			}

    	  ProductTag::insert($productTags);
    }

        public function InsertProductAtt($ProductAtt,$vid)
		{	
			
			foreach($ProductAtt as $InProductAtt)
		 {
			$productAtts[]=[

					'vid'=>intval($vid),
				
					'attribute_id'=>$InProductAtt->id,
					'name'=>$InProductAtt->id,
					'slug'=>$InProductAtt->id,
					'type'=>$InProductAtt->id,
					'order_by'=>$InProductAtt->id,
					'has_archives'=>$InProductAtt->id,
				
				];
			}

    	  Product_Attribute::insert($productAtts);
    }


	public function InsertBilling($orderID,$BillingData,$vid)
    {
		$billing[]=[
				'vid'=>intval($vid),
				'order_id'=>$orderID,
				'first_name'=>$BillingData->first_name,
				'last_name'=>$BillingData->last_name,
				'company'=>$BillingData->company,
				'address_1'=>$BillingData->address_1,
				'address_2'=>$BillingData->address_2,
				'city'=>$BillingData->city,
				'state'=>$BillingData->state,
				'postcode'=>$BillingData->postcode,
				'country'=>$BillingData->country,
				'email'=>$BillingData->email,
				'phone'=>$BillingData->phone,
				];

    	 Billings::insert($billing);
    }
		      
	public function InsertShipping($InsertorderID,$shippingData,$vid)
    {
		$shipping[]=[
				'vid'=>intval($vid),
				'Order_id'=>$InsertorderID,
				'first_name'=>$shippingData->first_name,
				'last_name'=>$shippingData->last_name,
				'company'=>$shippingData->company,
				'address_1'=>$shippingData->address_1,
				'address_2'=>$shippingData->address_2,
				'city'=>$shippingData->city,
				'state'=>$shippingData->state,
				'postcode'=>$shippingData->postcode,
				'country'=>$shippingData->country,
				'phone'=>$shippingData->phone,

				    ];
		shippings::insert($shipping);
	}
			
	public function insertLineItems($IDLineItem,$LineItemData,$vid)
	{
		foreach($LineItemData as $LineItem)
		{
			$LineItems[]=[
				'vid'=>intval($vid),
			 	'order_id'=>$IDLineItem,
			 	'line_item_id'=>$LineItem->id,
			 	'name'=>$LineItem->name,
			 	'product_id'=>$LineItem->product_id,
			 	'variation_id'=>$LineItem->variation_id,
			 	'quantity'=>$LineItem->quantity,
			 	'tax_class'=>$LineItem->tax_class,
			 	'subtotal'=>$LineItem->subtotal,
			 	'subtotal_tax'=>$LineItem->subtotal_tax,
			 	'total'=>$LineItem->total,
			 	'total_tax'=>$LineItem->total_tax,
			 	'sku'=>$LineItem->sku,
			 	'price'=>$LineItem->price,
			 	'parent_name'=>$LineItem->parent_name,
			];
	  
	  	LineItems::insert($LineItems);
	  	}
	}

	/*public function OrderMetaData($OrderID,$Order_MetaData)
    {
		foreach($Order_MetaData as $metadata)
		{
			$meta_data[]=[
				'Order_id'=>$OrderID,
				'id'=>$metadata->id,  
				'key'=>$metadata->key,
				//'value'=>$metadata->value,          
			]; 
	    }
		// meta_data::insert($meta_data);
	}

	public function LineItem_Metadata($IDLineMetaData,$LineMetaData)
	{
		foreach($LineMetaData as $lineData)
		{
		   	$Product_ID=$lineData->product_id;
		    $LineMetaData=$lineData->meta_data;
		  	foreach($LineMetaData  as $Line_Meta_Data)
	  		{
				$line_items_meta[]=[
				'id'=>	$Line_Meta_Data->id,
				'product_id'=>$Product_ID,
				'line_item_id'=>$lineData->id,
				'key'=>$Line_Meta_Data->key,
				'value'=>$Line_Meta_Data->value,
				'display_key'=>$Line_Meta_Data->display_key,
				'display_value'=>$Line_Meta_Data->display_value,
			           ];
	    	}
			// LineItemsMeta::insert($line_items_meta);
		}   
	} */
		    	
    public function OrderTaxLines($InTaxLines,$OrderTaxData,$vid)
	{
		$Order_Tax_Lines[]=[	
				'vid'=>intval($vid),	
		'Order_id'=>$InTaxLines,
	         ];
		tax_lines::insert($Order_Tax_Lines);
	}

	public function OrderShipping_Lines($InShippingLines,$OrderShippingData,$vid)
	{
		$Order_Shipping_Lines[]=[
				'vid'=>intval($vid),		
		'Order_id'=>$InShippingLines,
			];
			 
        shipping_lines::insert($Order_Shipping_Lines);
    }

	public function OrderFee_Lines($InFeeLines,$OrderFeeData,$vid)
	{
		$Order_Fee_Lines[]=[
				'vid'=>intval($vid),		
		'Order_id'=>$InFeeLines,
			];
		Order_fee_lines::insert($Order_Fee_Lines);
	}

	public function OrderCoupan_Lines($InCoupanLines,$OrderCoupanData,$vid)
	{
		$Order_Coupan[]=[	
				'vid'=>intval($vid),	
		'Order_id'=>$InCoupanLines,
			];
	    Order_Coupan_lines::insert($Order_Coupan);
	}

	public function Order_refunds($InOrderRefunds,$OrderRefundData,$vid)
	{
		$Order_refunds[]=[	
				'vid'=>intval($vid),	
		'Order_id'=>$InOrderRefunds,
			];
		Order_Refunds::insert($Order_refunds);
	}

	public function Order_links($InOrderLinks,$OrderLinkData,$vid)
	{
		$selfLinkData=$OrderLinkData->self;
		foreach($selfLinkData as $linkData)
		{
			$Order_links[]=[
				'vid'=>intval($vid),		
			 	'Order_id'=>$InOrderLinks,
			 	'href'=>$linkData->href,
			];
	    }
	    Order_links::insert($Order_links);
    }
}
