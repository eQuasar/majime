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

class WalletController extends Controller
{
	public function Wallet_detail()
	{
		// $id =  
		 $obj=Orders::join('billings','orders.oid','=','billings.order_id')		                 
						 ->join('line_items','line_items.order_id','=', 'billings.order_id')->get(['orders.total','line_items.name','orders.date_created']);
						// ->join('line_items_meta','line_items_meta.Line_item_id','=', 'line_items.line_item_id')
                        return $obj;
      		
	}

}
