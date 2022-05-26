<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\orders;
use App\Models\anku;
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

class WalletController extends Controller
{
	public function Wallet_detail()
  {
    // if($orders->status == $orders->status)
    // {
    //     // skip if status wasn't changed. I use this skip so that we don't have to check all of the following. 
    // }

		// $orders = DB::table("orders")->join('line_items', 'orders.oid', '=', 'line_items.order_id')
		// ->join('products', 'orders.id', '=', 'products.id')
  //         ->select("orders.*","line_items.*","products.*",
  //         DB::raw("(total*(5/100)) as m_cost")

		$orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')
		->join('products', 'orders.id', '=', 'products.id')
    ->select("orders.*","orders.oid as orderno","products.*",
      DB::raw("(SELECT SUM(line_items.quantity) FROM line_items

                                WHERE line_items.order_id = orders.oid

                                GROUP BY line_items.order_id) as quantity"),
          (DB::raw('(total*(5/100)) AS m_cost')),(DB::raw('(total*(5/100)) AS v_cost')),
          (DB::raw('(total*(2/100)) AS gateway_cost')),(DB::raw('(total*(2/100)) AS s_cost')),
          (DB::raw('(total*(93/100)) AS net_amount')),(DB::raw('(total*(93/100)) AS a_cost'))
          )

          ->get();
        return $orders;


 }
}
