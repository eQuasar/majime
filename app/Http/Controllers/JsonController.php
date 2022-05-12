<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
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
use App\Models\Product_Attribute;

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
		$jsonResponse=$this->getOrderWP($url);
		$this->InsertOrder($jsonResponse, $vid, $url);
	}

	private function getProductWP($url)
	{
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

	        'Authorization: Basic Y2tfOWE3MDUwOWVkODk2ODZiZTM0YTY5ZGY3OTI2ZWNhMjBmZmE0MDY0ZTpjc184OWJiODY2ZTY5NjIyMmE3ZjFmZmViMTNkMzhiNjFmYmFkZDFjZjRm'
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}


	private function getProductCatWP($url)
	{
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

	        'Authorization: Basic Y2tfOWE3MDUwOWVkODk2ODZiZTM0YTY5ZGY3OTI2ZWNhMjBmZmE0MDY0ZTpjc184OWJiODY2ZTY5NjIyMmE3ZjFmZmViMTNkMzhiNjFmYmFkZDFjZjRm'
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;

 	}
	
	private function getOrderWP($url)
	{
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

	        'Authorization: Basic Y2tfOWE3MDUwOWVkODk2ODZiZTM0YTY5ZGY3OTI2ZWNhMjBmZmE0MDY0ZTpjc184OWJiODY2ZTY5NjIyMmE3ZjFmZmViMTNkMzhiNjFmYmFkZDFjZjRm'
	      ),
	    ));

	    $response = curl_exec($curl);
	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}

 	private function getProductTagWp($url)
	{
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

	        'Authorization: Basic Y2tfOWE3MDUwOWVkODk2ODZiZTM0YTY5ZGY3OTI2ZWNhMjBmZmE0MDY0ZTpjc184OWJiODY2ZTY5NjIyMmE3ZjFmZmViMTNkMzhiNjFmYmFkZDFjZjRm'
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}

 	private function getProductAttWp($url)
	{
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

	        'Authorization: Basic Y2tfOWE3MDUwOWVkODk2ODZiZTM0YTY5ZGY3OTI2ZWNhMjBmZmE0MDY0ZTpjc184OWJiODY2ZTY5NjIyMmE3ZjFmZmViMTNkMzhiNjFmYmFkZDFjZjRm'
	      ),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $jsonResp = json_decode($response);
		return  $jsonResp;
 	}

 	





	public function InsertOrder($InOrder, $vid, $url)
	{
		// var_dump($InOrder); die;

		foreach($InOrder as $order)
		{
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

		
			$jsonResponse=$this->getProductWP($url);
			$this->InsertProduct($jsonResponse, $vid);
			// dd($jsonResponse);

			$jsonResponsecat=$this->getProductCatWP($url);
			$this->InsertProductCat($jsonResponsecat, $vid);
			// dd($jsonResponsecat);

			$jsonResponseTag=$this->getProductTagWp($url);
			$this->InsertProductTag($jsonResponseTag, $vid);
			//dd($jsonResponseTag);

			$jsonResponseAtt=$this->getProductAttWp($url);
			$this->InsertProductAtt($jsonResponseAtt,$jsonResponse,$vid,);
			// dd($jsonResponseAtt);
			

	    }
       	Orders::insert($Orders); 	
    }

	public function InsertProduct($ProductData,$vid)
		{	
			// dd($ProductData);
			foreach($ProductData as $InProduct)
		 {
		 	$cat = '';
		 	for ($i=0; $i < count($InProduct->categories); $i++) { 
		 		$cat .= $InProduct->categories[$i]->name.", ";
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
	  	}

	  	LineItems::insert($LineItems);
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
