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

class ProductController extends Controller
{
    public function insertAllOrders()
	{
		//dd($request);
		$jsonResponse=$this->getOrderWP();
		$this->InsertOrder($jsonResponse);
	}
	public function productDetail()
	{
		//return billings::all();
		$obj=line_items::join('billings','line_items.Order_id','=','billings.Order_id')
						->join('line_items_meta','line_items_meta.product_id','=', 'line_items.product_id')
						->get();
                         return $obj;
		//return $obj['data'];
		//return InsertBilling::all();

		/* $orders = Orders::join('line_items','line_items.Order_id','=', 'Orders.id')
		 ->join('line_items_meta','line_items.id','=', 'Orders.id')
		  ->where('Orders.id','=', 5393)
				->get();
		 echo $orders;
		*/
	}

		private function getOrderWP()
		{
			 $curl = curl_init();

		    curl_setopt_array($curl, array(

		    CURLOPT_URL => 'https://isdemo.in/fc/wp-json/wc/v3/orders',
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => '',
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => 'GET',
		    CURLOPT_HTTPHEADER => array(

		        'Authorization: Basic Y2tfOWE3MDUwOWVkODk2ODZiZTM0YTY5ZGY3OTI2ZWNhMjBmZmE0MDY0ZTpjc184OWJiODY2ZTY5NjIyMmE3ZjFmZmViMTNkMzhiNjFmYmFkZDFjZjRm'
		      ),
		    ));

		    $response = curl_exec($curl);

		    curl_close($curl);
		    $jsonResp = json_decode($response);
	return  $jsonResp;
		//echo $response;

 }
			public function InsertOrder($InOrder)
			{
				foreach($InOrder as $order)
				{
		          // dd($order);
					$Orders[]=[

						'order_id'=>$order->id,
						'vid'=>'5',
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
						 'customer_user_agent'=>$order->customer_user_agent,
						 'created_via'=>$order->created_via,
						 'customer_note'=>$order->customer_note,
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
					$this->InsertBilling($order->id,$order->billing);
					$this->InsertShipping($order->id,$order->shipping);
				    $this->OrderMetaData($order->id,$order->meta_data);
				    $this->insertLineItems($order->id,$order->line_items);
					$this->LineItem_Metadata($order->id,$order->line_items);
					$this->OrderTaxLines($order->id,$order->tax_lines);
					$this->OrderShipping_Lines($order->id,$order->shipping_lines);
					$this->OrderFee_Lines($order->id,$order->fee_lines);
					$this->OrderCoupan_Lines($order->id,$order->coupon_lines);
				    $this->Order_refunds($order->id,$order->refunds);
					$this->Order_links($order->id,$order->_links);
			    }
		           Orders::insert($Orders);
		          // header("Content-Type:JSON");
		          // echo json_encode($Orders); 
		           // echo "Data Sucessfully Inserted";  	
		    }


		    	 public function InsertBilling($orderID,$BillingData)
		    {
		    	 //foreach($InBilling as $order){
		    		// $BillingData=$orderBilling;
		    
		    		$billing[]=[
		    				'Order_id'=>$orderID,
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
		    	// } 

		    	 billings::insert($billing);
		        
		    }
		      public function InsertShipping($InsertorderID,$shippingData)
		    {
		    	//foreach($InShipping as $order){
		    		//$shippingData=$order->shipping;
		    		$shipping[]=[
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

		  
		    public function OrderMetaData($OrderID,$Order_MetaData)
		    {
				//$metaDataValue=$Order_MetaData->value;
						foreach($Order_MetaData as $metadata)
						{
						$meta_data[]=[
							'Order_id'=>$OrderID,
							'id'=>$metadata->id,  
							'key'=>$metadata->key,
							//'value'=>$metadata->value,          
						];
						meta_data::insert($meta_data); 
			    	    }
	    	}
			public function insertLineItems($IDLineItem,$LineItemData)
			{

					foreach($LineItemData as $LineItem)
					{
						$line_items[]=[
						 	'Order_id'=>$IDLineItem,
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
					  line_items::insert($line_items);
					  }
			}

			public function LineItem_Metadata($IDLineMetaData,$LineMetaData)
			{
				foreach($LineMetaData as $lineData)
					{
				$Line_MetaData=$lineData->meta_data;
					foreach($Line_MetaData as $Line_meta_data)
						{
							$Line_ItemID=$Line_meta_data->id;
					$line_items_meta[]=[	
					'_id'=>$IDLineMetaData,
					'Line_meta_id'=>$Line_meta_data->id,
					'key'=>$Line_meta_data->key,
					'value'=>$Line_meta_data->value,
					'display_key'=>$Line_meta_data->display_key,
					'display_value'=>$Line_meta_data->display_value,
				           ];
				line_items_meta::insert($line_items_meta);
					    }
	      			}     
	    	} 
		    	
		    public function OrderTaxLines($InTaxLines,$OrderTaxData)
			{
				$Order_Tax_Lines[]=[		
				'Order_id'=>$InTaxLines,
			         ];
				tax_lines::insert($Order_Tax_Lines);
			}

			public function OrderShipping_Lines($InShippingLines,$OrderShippingData)
			{
				$Order_Shipping_Lines[]=[		
				'Order_id'=>$InShippingLines,
					];
					 
		        shipping_lines::insert($Order_Shipping_Lines);
		    }

			public function OrderFee_Lines($InFeeLines,$OrderFeeData)
			{
				$Order_Fee_Lines[]=[		
				'Order_id'=>$InFeeLines,
					];
				Order_fee_lines::insert($Order_Fee_Lines);
			}

			public function OrderCoupan_Lines($InCoupanLines,$OrderCoupanData)
			{
				$Order_Coupan[]=[		
				'Order_id'=>$InCoupanLines,
					];
			    Order_Coupan_lines::insert($Order_Coupan);
			}

			public function Order_refunds($InOrderRefunds,$OrderRefundData)
			{
				$Order_refunds[]=[		
				'Order_id'=>$InOrderRefunds,
					];
			Order_Refunds::insert($Order_refunds);
			}

			public function Order_links($InOrderLinks,$OrderLinkData)
			{
				$selfLinkData=$OrderLinkData->self;
				foreach($selfLinkData as $linkData)
				{
							$Order_links[]=[		
									 	'Order_id'=>$InOrderLinks,
									 	'href'=>$linkData->href,
								];
				    }
			    Order_links::insert($Order_links);
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
