<!-- <?php
// namespace App\Http\Controllers;
// use Illuminate\Http\Request;
// use App\Models\Orders;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;

// class DashboardController extends Controller {

// public function Order_detail (Request $request) {

// $order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
//                                         WHERE line_items.order_id = orders.oid GROUP BY line_items.order_id) as quantity"))->get();

 // return $order;

}




} 