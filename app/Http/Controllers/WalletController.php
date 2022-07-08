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
	public function walletDetail(Request $request)
  {
    // echo "string";
    //   dd($request->vid); die;
     $vendor = $request->vid;
   		$orders = DB::table("orders")
  //     ->join('billings', 'orders.oid', '=', 'billings.order_id')
		// ->join('products', 'orders.id', '=', 'products.id')
      ->where('orders.vid','=',intval($vendor))
  //   ->where('billings.vid','=',intval($vendor))
  //   ->where('products.vid','=',intval($vendor))
  //   ->where('line_items.vid','=',intval($vendor))
     ->select("orders.*","orders.oid as orderno",
      DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
            WHERE line_items.order_id = orders.oid 
            GROUP BY line_items.order_id) as Quantity"),
          (DB::raw('(total*(5/100)) AS m_cost')),(DB::raw('(total*(5/100)) AS v_cost')),
          (DB::raw('(total*(2/100)) AS gateway_cost')),(DB::raw('(total*(2/100)) AS s_cost')),
          (DB::raw('(total*(93/100)) AS net_amount')),(DB::raw('(Quantity*(0.250)) AS logistics_cost'))
          )
          ->get();
        return $orders;
 }
}
