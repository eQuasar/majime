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
use App\Models\product_variation;
use App\Models\LineItemsMeta;
use App\Models\product_variation_categories;
use App\Models\tax_lines;
use App\Models\shipping_lines;
use App\Models\Order_fee_lines;
use App\Models\Order_Coupan_lines;
use App\Models\Order_Refunds;
use App\Models\Order_links;
use App\Models\suborder_detail;
use App\Models\product_variation_attributes;
use App\Models\product_variation_images;
use App\Models\meta_data_value;
use App\Models\Products;
use App\Models\Category;
use App\Models\ProductTag;
use App\Models\UpdateStatus;
use App\Models\Product_Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
		// dd($jsonResponse);die();
		$this->InsertOrder($jsonResponse, $vid, $url);
	}

	public function productgetJson(Request $request)
	{
		if(isset($request->url) && $request->url != '')
		{
			$url = $request->url;
		}
		else
		{
			$vid = $request->vid;
			$vendor = DB::table("vendors")->where('id','=',intval($vid))->get()->toArray();
			$url = $vendor[0]->url;
		}
		$vid = $request->vid;
		$jsonResponse=$this->getOrderWP($url, $vid);
		if(empty($jsonResponse))
        {
            return response()->json(['error' => false, 'msg' =>"New Prpoduct Nor", "ErrorCode" => "000"], 200); 
        }
		else{
			$this->readdProduct($jsonResponse, $vid, $url);
		}
		// dd($jsonResponse);die();
		// OrderController::pending_order($vid);
		
		
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
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		$orderItems =DB::table("orders")->where('oid','=',$order->id)->where('vid','=',intval($vid))->get()->toArray();
		if(count($orderItems)>0){
			echo "Already In The System"; die;
		}else{
			if ($order->status == 'pending' || $order->status == 'Pending') {}else{
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
				// print_r($Orders);
				// die();
				$this->InsertBilling($order->id,$order->billing,$vid);
				$this->InsertShipping($order->id,$order->shipping,$vid);
			    //$this->OrderMetaData($order->id,$order->meta_data);
			    $this->insertLineItems($order->id,$order->line_items,$vid);
				// $this->LineItem_Metadata($order->id,$order->line_items);
				$this->OrderTaxLines($order->id,$order->tax_lines,$vid);
				$this->OrderShipping_Lines($order->id,$order->shipping_lines,$vid);
				$this->OrderFee_Lines($order->id,$order->fee_lines,$vid);
				$this->OrderCoupan_Lines($order->id,$order->coupon_lines,$vid);
			    $this->Order_refunds($order->id,$order->refunds,$vid);
				$this->Order_links($order->id,$order->_links,$vid);

				if ($order->status == 'processing' || $order->status == 'Processing') {
					$this->smsSend($vid,$order->id,"placed");
				}
				// echo "Insert";

		    	Orders::insert($Orders); 	
		    }
       }
    }

    public function getWayBill($vid, $url){
    	// https://isdemo.in/fc/wp-json/waybill_import/waybill_import_data
    	$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		// var_dump($vendor);
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
	public function getorder_detail(Request $request)
	{
		$url=$request->url;
		$vid=$request->vid;
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
	 private function getProductVariation($productvariation,$url, $vid)
	 {
		$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
		 $products =DB::table("products")->where('vid','=',intval($vid))->get();
		 $product_id = array();
		 foreach ($products as $item) { array_push($product_id,$item->product_id); }
		 for($i=3;$i<count($product_id);$i++)
		 {
			echo $product_id[$i];
		 $curl = curl_init();
		 curl_setopt_array($curl, array(
		   CURLOPT_URL => $url.'/wp-json/wc/v3/products/'.$product_id[$i],
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
		
		// }
		 $response = curl_exec($curl);
		 curl_close($curl);
		 $jsonResp = json_decode($response);
		 $this->ProductVariation($jsonResp->variations, $productvariation,$url, $vid); 

		 }
		 
		 exit();
		
		//  return  $jsonResp;
	  }

	public function smsSend($vid,$order_id,$smsTemplate)
	{
		$vendor=DB::table("vendors")
				->where('id',intval($vid))
				->get();
		$billing=DB::table("billings")
				->where('order_id',intval($order_id))
				->where('vid',intval($vid))
				->get();
		$phone = "91".$billing[0]->phone;
		// $phone = "91"."8284840500";
		$logInfo = "SMS - VID - ".$vid."  "."OID: ".$order_id."  "."SMSTemplate: ".$smsTemplate."  "."Phone: ".$phone." Date: ".date("d-m-y");
		Log::info([$logInfo]);

		switch ($smsTemplate) {
			case "placed":
				$msgBody = "{\n  \"flow_id\": \"637f4f117c38cf4cf62622e2\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "cancel-cod":
				$msgBody = "{\n  \"flow_id\": \"6385ee531281a12b9812b8e2\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "cancel-prepaid":
				$msgBody = "{\n  \"flow_id\": \"6385ef0b55758f69cc0a5962\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "dispatched":
				$msgBody = "{\n  \"flow_id\": \"6385ef8befba0b1ab8266d68\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "outfordelivery":
				$msgBody = "{\n  \"flow_id\": \"6385f03f0eb40a3057563d13\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "deliveredtocust":
				$msgBody = "{\n  \"flow_id\": \"6385f0b16a49dc4e46765015\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "dtobooked":
				$msgBody = "{\n  \"flow_id\": \"6385f103269ef8502c3a0f34\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "dtodelivered":
				$msgBody = "{\n  \"flow_id\": \"6385f18d18a81617bc73f865\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
			case "dtorefunded":
				$msgBody = "{\n  \"flow_id\": \"6385f2032242b93f2d223c55\",\n  \"sender\": \"MAJIME\",\n  \"short_url\": \"0\",\n  \"mobiles\": \"".$phone
					."\",\n  \"orderno\": \"".$order_id."\",\n  \"website\": \"".$vendor[0]->name
					."\",\n  \"weblink\":\"".$vendor[0]->url."\"\n}";
				break;
		}
		
		if ($msgBody!=null){
			echo $msgBody;
			$curl = curl_init();

			curl_setopt_array($curl, [
			CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $msgBody,
			CURLOPT_HTTPHEADER => [
				"authkey: 245817AwfugX7zT6246d820P1",
				"content-type: application/json"
			],
			]);
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
			
			curl_close($curl);
			
			if ($err) {
				echo "cURL Error #:" . $err;
				Log::info(["SMS - cURL Error #:" . $err]);
			} else {
				echo $response;
				Log::info(["SMS - Response #:" . $response]);
			}
		}
	}

	public function smsTrigger()
	{
		$this->smsSend("3","10879","placed");
	}

 	public function cronOrderStatusUpdate($vid){

		// Forward Orders
 		$orders=DB::table("orders")
 				->whereIn('orders.status',['dispatched','packed','intransit'])
				->where('orders.vid',intval($vid))
				->orderBy('orders.oid','desc')
				->get();
		
		echo "Forward Orders: ".$countOfOrders = count($orders);
		if($countOfOrders==0){          // If no record found
			$Result['0'] = "No update required.";
			echo "<pre>";print_r($Result);echo "</pre>";
		}else{
			$result = $this->getAWBStatus($orders,$vid,"fwd");
			echo "<pre>";print_r($result);echo "</pre>";
		}

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
		$way_data=DB::table("way_data")
		->where('vid',intval($vid))
		->get();
	
			
		// }else{
			$curlopt_url = "https://track.delhivery.com/api/v1/packages/json/?";
			$del_url = "https://track.delhivery.com/api/v1/packages/json/?";
		// }
		$waybill_nos = "";
		foreach ($orders as $order) {
			$waybillnos=DB::table("waybill")
 				->where('order_id',$order->oid)
				->where('vid',$vid)
				->limit('1')
				->orderBy('id','desc')
				->get();
				// var_dump($waybillnos); die;
				if (count($waybillnos)>=1){
					
				
					if($fwdOrReturn == "fwd"){
						$waybillno = $waybillnos[0]->waybill_no;
					}else{
						$waybillno = $waybillnos[0]->return_waybill_no ;
					}
					echo "\n<br/>WAY: ".$waybillno." Order: ".$order->oid;
				$url = $del_url."waybill=".$waybillno."&token=".$way_data[0]->token;
				// $url = $del_url."waybill=".$waybillno."&token=ed99803a18868406584c6d724f71ebccc80a89f9";
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
					if($fwdOrReturn == "fwd"){
						$data[$order->oid] = $this->processAWBStatusFwd($response,$vid,$order->oid);
					}else{
						$data[$order->oid] = $this->processAWBStatusRev($response,$vid,$order->oid);
					}
				}
			}
		}
		return $data;
	}
	public function processAWBStatusFwd($response,$vid,$order_id)
	{
		if (isset($response['ShipmentData'])){		
			for($i=0; $i < sizeof($response['ShipmentData']) ; $i++){
				// waybill table get orderId of awb = $response['ShipmentData'][$i]['Shipment']['AWB'];
				$status = $response['ShipmentData'][$i]['Shipment']['Status']['Status'];
				// if ($status == "Manifested" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
				// 	$status = "dispatched";
				// }else 
				if ($status == "In Transit" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "UD" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
					$status = "intransit";
					// $this->smsSend($vid,$order_id,"dispatched");
				}else if ($status == "In Transit" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "RT" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
					$status = "intransit";
					// $this->smsSend($vid,$order_id,"dispatched");
				}else if ($status == "Dispatched" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "UD" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
					$status = "intransit";
					$this->smsSend($vid,$order_id,"dispatched");
				}else if ($status == "Pending" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "UD" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == FALSE ){
					$status = "intransit";
					// $this->smsSend($vid,$order_id,"dispatched");
				} else if ($status == "RTO" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "DL" ){
					$status = "rto-delivered";
				} else if ($status == "Closed" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "CN" ){
					$status = "deliveredtocust";
					// $this->smsSend($vid,$order_id,"deliveredtocust");
				} else if ($status == "Canceled" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "CN" ){
					$status = "deliveredtocust";
					// $this->smsSend($vid,$order_id,"deliveredtocust");
				} else if ($status == "Delivered" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "DL" ){
					$status = "deliveredtocust";
					$this->smsSend($vid,$order_id,"deliveredtocust");
				} 
				else{
					$status = "undefined";
				}
				
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
    	$ResUpdateStatusDelivary = $this->updateStatusDelivary($statusDel,$vid,$order_id);
    	return  $ResUpdateStatusDelivary;
 	}
	public function processAWBStatusRev($response,$vid,$order_id)
	{
		 if (isset($response['ShipmentData'])){
			for($i=0; $i < sizeof($response['ShipmentData']) ; $i++){
				// waybill table get orderId of awb = $response['ShipmentData'][$i]['Shipment']['AWB'];
				$status = $response['ShipmentData'][$i]['Shipment']['Status']['Status'];
				if ($status == "In Transit" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == TRUE && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType']=="RT"){
					$status = "dtointransit";
				} else if ($status == "In Transit" && $response['ShipmentData'][$i]['Shipment']['ReverseInTransit'] == TRUE && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType']=="PU"){
					$status = "dtointransit";
				} else if ($status == "DTO" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "DL" ){
					$status = "dtodelivered";
					$this->smsSend($vid,$order_id,"dtodelivered");
				} else if ($status == "DTO" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] != "DL" ){
					$status = "dtointransit";
				} else if ($status == "Closed" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "CN" ){
					$status = "deliveredtocust";
					$this->smsSend($vid,$order_id,"deliveredtocust");
				} else if ($status == "Canceled" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "CN" ){
					$status = "deliveredtocust";
					$this->smsSend($vid,$order_id,"deliveredtocust");
				} else if ($status == "Delivered" && $response['ShipmentData'][$i]['Shipment']['Status']['StatusType'] == "DL" ){
					$status = "deliveredtocust";
					$this->smsSend($vid,$order_id,"deliveredtocust");
				} 
				else{
					$status = "undefined";
				}
				
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
		$ResUpdateStatusDelivary = $this->updateStatusDelivary($statusDel,$vid,$order_id);
		return  $ResUpdateStatusDelivary;
	}

 	public function updateStatusDelivary($statusDel,$vid,$order_id){

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
							'update_statuses.status' => $status_update['status'],
							'update_statuses.delivery_status_code' => $status_update['delivery_status_code'],
							'update_statuses.delivery_brief_status' => $status_update['delivery_brief_status']
					])->get();

			if( sizeof($statusCheck) == 0){
				$Result[$status_update['awb']] = "AWB: ".$status_update['awb']." Updated Successfully.";
				UpdateStatus::insert($data);
				switch ($status_update['status']){
					case 'deliveredtocust':
						DB::table('order_reldates')->where('oid', intval($order_id))->where('vid', intval($vid))->update(['order_deldate' => date('Y-m-d')]);
						break;
					case 'rto-delivered':
						DB::table('order_reldates')->where('oid', intval($order_id))->where('vid', intval($vid))->update(['rto_deldate' => date('Y-m-d')]);
						break;
					default:
						break;
				}
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

    public function getUpdateStatus(Request $request)
	{	

// https://dlv-api.delhivery.com/v3/track?wbn=1325110000232

		$response = OrderController::changeStatus($request);
		return  $response;

 	}

	public function InsertOrder($InOrder, $vid, $url){
		DB::table('products')->where('vid', intval($vid))->delete();
		DB::table('orders')->where('vid', intval($vid))->delete();
		DB::table('billings')->where('vid', intval($vid))->delete();
		DB::table('shippings')->where('vid', intval($vid))->delete();
		DB::table('line_items')->where('vid', intval($vid))->delete();
		DB::table('Order_Coupan_lines')->where('vid', intval($vid))->delete();
		DB::table('Order_fee_lines')->where('vid', intval($vid))->delete();
		DB::table('Order_links')->where('vid', intval($vid))->delete();
		DB::table('Order_Refunds')->where('vid', intval($vid))->delete();
		DB::table('products_attributes')->where('vid', intval($vid))->delete();
		DB::table('order_reldates')->where('vid', intval($vid))->delete();
		DB::table('tax_lines')->where('vid', intval($vid))->delete();
		DB::table('categories')->where('vid', intval($vid))->delete();
		DB::table('products_tag')->where('vid', intval($vid))->delete();
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
		    // $this->OrderMetaData($order->id,$order->meta_data);
		    $this->insertLineItems($order->id,$order->line_items,$vid);
			//$this->LineItem_Metadata($order->id,$order->line_items);
			$this->OrderTaxLines($order->id,$order->tax_lines,$vid);
			$this->OrderShipping_Lines($order->id,$order->shipping_lines,$vid);
			$this->OrderFee_Lines($order->id,$order->fee_lines,$vid);
			$this->OrderCoupan_Lines($order->id,$order->coupon_lines,$vid);
		    $this->Order_refunds($order->id,$order->refunds,$vid);
			$this->Order_links($order->id,$order->_links,$vid);

	    }
       	
	    if(!empty($Orders)){
	   
	       	Orders::insert($Orders); 	
			// echo "Product Insert Successfully";
	       	// $this->getWayBill($vid, $url);
       }
       	
			
		$jsonResponse=$this->getProductWP($url, intval($vid));
		// var_dump($jsonResponse); die;
		$this->InsertProduct($jsonResponse, $vid);
		
	 	// $jsonResponseVariation=$this->getProductVariation($jsonResponse,$url, intval($vid));

		
		// dd($jsonResponse);

		$jsonResponsecat=$this->getProductCatWP($url, intval($vid));
		$this->InsertProductCat($jsonResponsecat, $vid);
		// var_dump($jsonResponsecat);
		// dd($jsonResponsecat);

		$jsonResponseTag=$this->getProductTagWp($url, intval($vid));
		$this->InsertProductTag($jsonResponseTag, $vid);
		// dd($jsonResponseTag);

		$jsonResponseAtt=$this->getProductAttWp($url, intval($vid));
		$this->InsertProductAtt($jsonResponseAtt, $vid);
		
		
    }

	public function InsertProduct($ProductData,$vid)
		{	
			$pro_id=$ProductData[0]->id;
			if(products::where('product_id', '=', $pro_id)->where('vid','=',intval($vid))->exists()) {
				return response()->json([ 'msg' => "Product already Exist"]);	
				
			 }
			 else
			 {
				foreach($ProductData as $InProduct)
		         {
		 	$cat = '';
		 	for ($i=0; $i < count($InProduct->categories); $i++) { 
		 		if($i == count($InProduct->categories)-1){
		 			$cat .= $InProduct->categories[$i]->name;
		 		}else{
		 			$cat .= $InProduct->categories[$i]->name.",";
		 		}
		 	}
			 // if (Products::where('product_id', $InProduct->id)->where('vid',$vid)->exists())
			 // {
				//  return response()->json([ 'msg' => "Product already Exist"]);	
			 // }
			 // else
			 // {
			$product[]=[
				'vid'=>intval($vid),
				'product_id'=>$InProduct->id,
				'name'=>$InProduct->name,
				'slug'=>$InProduct->slug,
				'permalink'=>$InProduct->permalink,
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
				// 'backorders'=>$InProduct->backorders,
				// 'backorders_allowed'=>$InProduct->backorders_allowed,
				 'low_stock_amount'=>$InProduct->low_stock_amount,
				 'sold_individually'=>$InProduct->sold_individually,
				 'weight'=>$InProduct->weight,
				'shipping_required'=>$InProduct->shipping_required,
				 'shipping_taxable'=>$InProduct->shipping_taxable,
				'shipping_class'=>$InProduct->shipping_class,
				'shipping_class_id'=>$InProduct->shipping_class_id,
				 'reviews_allowed'=>$InProduct->reviews_allowed,
			 	'average_rating'=>$InProduct->average_rating,
				'rating_count'=>$InProduct->rating_count,
			 	'purchase_note'=>$InProduct->purchase_note,
				'categories'=> $cat,
				];
			}
		// }
    	 Products::insert($product);
		 return response()->json([ 'msg' => "Product Insert Successfully"]);	
			 }
			
		// return response()->json(['error' => false, 'msg' =>"Product Insert Successfully", "ErrorCode" => "000"], 200);
		
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
			// var_dump($productCat); die;
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
	public function ProductVariation($variations,$ProductVariation,$url,$vid)
	{

		if(count($variations)!='0'){
			$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
			$products =DB::table("products")->where('vid','=',intval($vid))->pluck('product_id')->toArray();
			// $product_id = array();
			// foreach ($products as $item) { array_push($product_id,$item->product_id); }
			for($i=0;$i<count($products);$i++)
			{
				for($j=0;$j<count($variations);$j++)
				{
					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_URL => $url.'/wp-json/wc/v3/products/'.$products[$i].'/variations/'.$variations[$j],
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
					$this->InsertProductVariation($response,$vid,$products[$i]);
					// $this->InsertProductVariation_categories($response,$vid,$product_id[$i]);
					// $this->InsertProductVariation_attributes($response,$vid,$product_id[$i]);
					// $this->InsertProductVariation_images($response,$vid,$product_id[$i]);
					// $this->InsertProductVariation_tags($response,$vid,$product_id[$i]);
					// $this->InsertProductVariation_images($response,$vid,$product_id[$i]);
					// $this->InsertProductVariation_links($response,$vid,$product_id[$i]);
					// $this->InsertProductVariation_dimensions($response,$vid,$product_id[$i]);
				}
			}	
		}

	}
	public function InsertProductVariation($ProductVariation,$vid,$proId)
		{
			$Variation_data = json_decode($ProductVariation);
			if (product_variation::where('variation_id', $Variation_data->id)->where('vid',$vid)->exists())
			{
				// return response()->json([ 'msg' => "Product Variation already Exist"]);	
			}
			else
			{
			// print_r($Variation_data);die();
			$product_variation[]=[
				'vid'=>intval($vid),
				'product_id'=>$proId,
				'variation_id'=>$Variation_data->id,
				'sku'=>$Variation_data->sku,
				'price'=>$Variation_data->price,
				'regular_price'=>$Variation_data->regular_price,
				'stock_quantity'=>$Variation_data->stock_quantity,
				'stock_status'=>$Variation_data->stock_status,
				'description'=>$Variation_data->description,
				'permalink'=>$Variation_data->permalink,
				'sale_price'=>$Variation_data->sale_price,
				// 'date_on_sale_from'=>$Variation_data->date_on_sale_from,
				// 'date_on_sale_from_gmt'=>$Variation_data->date_on_sale_from_gmt,
				// 'date_on_sale_to'=>$Variation_data->date_on_sale_to,
				// 'date_on_sale_to_gmt'=>$Variation_data->date_on_sale_to_gmt,
				'on_sale'=>$Variation_data->on_sale,
				'status'=>$Variation_data->status,
				'purchasable'=>$Variation_data->purchasable,
				'virtual'=>$Variation_data->virtual,
				'downloadable'=>$Variation_data->downloadable,
				'download_limit'=>$Variation_data->download_limit,
				'download_expiry'=>$Variation_data->download_expiry,
				'tax_status'=>$Variation_data->tax_status,
				'tax_class'=>$Variation_data->tax_class,
				'manage_stock'=>$Variation_data->manage_stock,
				// 'backorders'=>$Variation_data->backorders,
				// 'backorders_allowed'=>$Variation_data->backorders_allowed,
				// 'backordered'=>$Variation_data->backordered,
				'low_stock_amount'=>$Variation_data->low_stock_amount,
				'weight'=>$Variation_data->weight,
				'shipping_class'=>$Variation_data->shipping_class,
				'shipping_class_id'=>$Variation_data->shipping_class_id,
				// 'image'=>$Variation_data->image,
				'menu_order'=>$Variation_data->menu_order,
			];
		// }
			product_variation::insert($product_variation);
		}
	}
public function InsertProductVariation_attributes($product_attributes,$vid,$proId)
{
	$attributes_data = json_decode($product_attributes);
	$pro_attri=$attributes_data->attributes;
	foreach($pro_attri as $attributes)
	{
		$productAtts[]=[
			'vid'=>intval($vid),
			'product_id'=>$proId,
			'variation_id'=>$attributes_data->id,
			'attribute_id'=>$attributes->id,
			'color'=>$attributes->name,
			'size'=>$attributes->option,
		];
	}
	product_variation_attributes::insert($productAtts);
}
	public function InsertProductVariation_categories($ProductVariation,$vid,$proId)
	{
		$categories_data = json_decode($ProductVariation);	
		foreach($categories_data as $categories)
		{
			// dd($categories);die();
			$productAtts[]=[
				'vid'=>intval($vid),
				'product_id'=>$proId,
				'variation_id'=>$categories_data->id,
				'categories_id'=>$categories_data->id,
				'name'=>$categories_data->name,
				'slug'=>$categories_data->slug,
			];
			
		}
		product_variation_categories::insert($productAtts);
	}
	public function InsertProductVariation_images($ProductVariation,$vid,$proId)
	{
		$images_data = json_decode($ProductVariation);
		$pro_images=$images_data->image;
		if($pro_images==null)
		{
			return response()->json([ 'msg' => "Images Not Found"]);	
			
		}
		else {
			// foreach($images_data as $image)
			// {
				// dd($categories);die();
				$productImages[]=[
					'vid'=>intval($vid),
					'product_id'=>$proId,
					'variation_id'=>$images_data->id,
					'image_id'=>$pro_images->id,
					'date_created'=>$pro_images->date_created,
					'date_created_gmt'=>$pro_images->date_created_gmt,
					'date_modified'=>$pro_images->date_modified,
					'date_modified_gmt'=>$pro_images->date_modified_gmt,
					'src'=>$pro_images->src,
					'name'=>$pro_images->name,
					'alt'=>$pro_images->alt,

				];
			
		// }
		product_variation_images::insert($productImages);
	     }
		
	
	}
	// public function InsertProductVariation_tags($ProductVariation,$vid,$proId)
	// {
	// 	$tags_data = json_decode($ProductVariation);
	// }
	// public function InsertProductVariation_images($ProductVariation,$vid,$proId)
	// {
	// 	$images_data = json_decode($ProductVariation);
	// }
	// public function InsertProductVariation_links($ProductVariation,$vid,$proId)
	// {
	// 	$links_data = json_decode($ProductVariation);
	// }
	// public function InsertProductVariation_dimensions($ProductVariation,$vid,$proId)
	// {
	// 	$dimensions_data = json_decode($ProductVariation);
	// }


		

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
		if(!empty($LineItemData)){
			foreach($LineItemData as $LineItem)
			{
				$LineItems2[]=[
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
		  	}
			LineItems::insert($LineItems2);
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
	public function suborder_detail(Request $request)
	{
		$vid=$request->vid;
		$url=$request->url;
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
		$this->InsertSubOrder($jsonResp, $vid, $url);
	}
	public function InsertSubOrder($OrderData,$vid,$url)
	{
		
		foreach($OrderData as $order)
		{
			
			$current_year=date("Y");
			$order_id=intval($order->id);
			$vid=intval($vid);
			$lineItem_data=$order->line_items;
			$j="-";
			$order_id_concat=$order_id.$j;
			$vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
			//insert sub order detail of order
			// foreach($lineItem_data as $line_data)
			// {	
			// 	$line_quantity=$line_data->quantity;
			// 	for($i=0;$i<$line_quantity;$i++)
			// 	{
			// 		DB::table('suborder_details')->insert(['order_id'=>$order_id,'vid'=>$vid,'suborder_id'=>$order_id_concat.$i,'line_item_id'=>$line_data->id]);
			// 	}
			// }
			// insert invoice related info
			foreach($lineItem_data as $line_data)
			{	
				$line_quantity=$line_data->quantity;
				for($i=0;$i<$line_quantity;$i++)
				{
					$way_data =DB::table("way_data")->where('vid','=',$vid)->get();
					if(($way_data[0]->gateway)==0)
					{
						$vendor_name='MAJ';
						$vendor_invoice_no=$vendor_name.$j.$current_year.$order_id.$j.$i;
						$customer_invoice_no="-";
					}
					else
					{
						$customer_invoice_no=$vendor[0]->name.$j.$current_year.$order_id.$j.$i;
						$vendor_invoice_no="-";
					}
					
					
					DB::table('invoice_infos')->insert(['invoice_no'=>$vendor_invoice_no,'customer_invoice_no'=>$customer_invoice_no,'order_id'=>$order_id,'vid'=>$vid,'suborder_id'=>$order_id_concat.$i,'line_item_id'=>$line_data->id,'total'=>$line_data->total]);
				}
			}
		}
	}
	public function suborder_data(Request $request)
	{
		$vid=$request->vid;
		$data =DB::table("suborder_details")->join('line_items','suborder_details.line_item_id','=','line_items.line_item_id')
		->where('suborder_details.vid','=',intval($vid))
		->where('line_items.vid','=',intval($vid))
		->select('suborder_details.suborder_id','suborder_details.line_item_id','suborder_details.order_id','line_items.name','line_items.sku','line_items.quantity','line_items.price','line_items.subtotal')
		->get();
		return response()->json(['data'=>$data ,'msg' => "", "ErrorCode" => "000"], 200);
	}


	public function readdProduct($InOrder, $vid, $url){
		// echo $url; die;
		// DB::table('products')->where('vid', intval($vid))->delete();
		// echo "string"; die;
		foreach($InOrder as $order)
		{
			// if(){

			// }else{
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
				// $this->InsertBilling($order->id,$order->billing,$vid);
				// $this->InsertShipping($order->id,$order->shipping,$vid);
			//    $this->OrderMetaData($order->id,$order->meta_data);
				// $this->insertLineItems($order->id,$order->line_items,$vid);
				//$this->LineItem_Metadata($order->id,$order->line_items);
				// $this->OrderTaxLines($order->id,$order->tax_lines,$vid);
				// $this->OrderShipping_Lines($order->id,$order->shipping_lines,$vid);
				// $this->OrderFee_Lines($order->id,$order->fee_lines,$vid);
				// $this->OrderCoupan_Lines($order->id,$order->coupon_lines,$vid);
			//    $this->Order_refunds($order->id,$order->refunds,$vid);
				// $this->Order_links($order->id,$order->_links,$vid);

			}
			
			// if(!empty($Orders)){
			//    	Orders::insert($Orders); 	

			//    	// $this->getWayBill($vid, $url);
		//   	}
				
			$jsonResponse=$this->getProductWP($url, intval($vid));
			// var_dump($jsonResponse); die;
			$this->InsertProduct($jsonResponse, $vid);
			
			// $jsonResponseVariation=$this->getProductVariation($jsonResponse,$url, intval($vid));

			
			// dd($jsonResponse);

			// $jsonResponsecat=$this->getProductCatWP($url, intval($vid));
			// $this->InsertProductCat($jsonResponsecat, $vid);
			// // var_dump($jsonResponsecat);
			// // dd($jsonResponsecat);

			// $jsonResponseTag=$this->getProductTagWp($url, intval($vid));
			// $this->InsertProductTag($jsonResponseTag, $vid);
			// // dd($jsonResponseTag);

			// $jsonResponseAtt=$this->getProductAttWp($url, intval($vid));
			// $this->InsertProductAtt($jsonResponseAtt, $vid);
		// }
		
		
    }
	
}
