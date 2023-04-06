<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\JsonController;
use App\Models\Orders;
use App\Models\billings;
use App\Models\Products;
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
use App\Models\walletprocessed;
use App\Models\order_reldate;
use App\Http\Resources\OrderResource;
use App\Http\Resources\BillingResource;
use App\Http\Resources\LineItemsResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use Carbon\Carbon;
use PDF;
use \Milon\Barcode\DNS1D;
class OrderController extends Controller {
    //featch data orderdetails with orders,billing,product table
    public function OrderDetail(Request $request) {
      $vid = $request->vid;
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->join('products', 'orders.id', '=', 'products.id')->where('orders.vid', '=', $request->vid)->select("orders.*", "billings.*", "products.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                        WHERE line_items.order_id = orders.oid
                        GROUP BY line_items.order_id) as quantity"))->get();
        return $orders;
    }
    //order_search with table orders and billings  (orders.oid == billing.order_id)
    public function Order_Search(Request $request) {
        $vid = $request->vid;
        //if condition date from equal data to
        if($request->date_from == $request->date_to){
            $order = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->whereDate('orders.date_created', '=', $request->date_from)->where('orders.status','=',$request->status)->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                                        WHERE line_items.order_id = orders.oid
                                        GROUP BY line_items.order_id) as quantity"))->orderBy('orders.date_created','DESC')->get();
        }else{
            $range = [$request->date_from.' 00:00:00', $request->date_to.' 23:59:59'];
            // $order=orders::whereBetween('date_created_gmt',$range)->get();
            $order = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->whereBetween('orders.date_created', $range)->where('orders.status','=',$request->status)->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                                            WHERE line_items.order_id = orders.oid
                                            GROUP BY line_items.order_id) as quantity"))->orderBy('orders.date_created','DESC')->get();
        }
        return $order;
    }
    //wallet_Search walletprocesseds table search with oid base
    public function wallet_Search(Request $request) {
        $date = date('Y-m-d H:i:s');
        $from = $request->date_from." 00:00:00";
        $range = [$from, $date];
        $vendor=$request->vid;
            $order= DB::table("walletprocesseds")->where('walletprocesseds.vid',$vendor)
            ->whereBetween('created_at',$range)->select("walletprocesseds.*","walletprocesseds.oid as orderno")->orderBy('id','DESC')->get();
            if($order->isEmpty())
            {       
                $order= DB::table("walletprocesseds")->where('walletprocesseds.vid',$vendor)->orderBy('id','DESC')->get();
                $Clos = $order->first();
                $Closing_balance=$Clos->current_wallet_bal;
            }
            else 
            {
                $Clos = $order->first();
                $Closing_balance=$Clos->current_wallet_bal;
                $open=$order[0]->current_wallet_bal;
            }
            //get data from table walletprocesseds according to vid and
            $OBalorder=DB::table("walletprocesseds")->where('walletprocesseds.vid',$vendor)
                        //this is from table walletprocesseds greaterthan data from
                        ->where('walletprocesseds.created_at', '<', $request->date_from." 00:00:00")
                        ->orderBy('id','DESC')->get();
                //if condtion check is empty yes /no
            if($OBalorder->isEmpty()){
                $opening_balance=0;   
            }else{
                //method return the first record found from the database first()
                $OpnBal = $OBalorder->first();
                $opening_balance=$OpnBal->current_wallet_bal;
            }
        return response()->json([ 'order'=> $order,'closing_bal'=> $Closing_balance,'opening_bal'=> $opening_balance], 200);
      
    }
    // filter search orders.status,orders.oid,billings.city,billings.state
     public function filter_Search (Request $request) {
        $vendor = $request->vid;
        $search = $request->filterit;
       $orders = DB::table("orders")->join('billings', function($join) use ($vendor)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vendor));
        })->where('orders.vid','=', $vendor)->where('billings.vid','=',$vendor)
              ->where('orders.status','like','%'.$search.'%')->where('orders.vid','=',$vendor)
              ->orWhere('orders.oid','like','%'.$search.'%')->where('orders.vid','=',$vendor)
              ->orWhere('billings.city','like','%'.$search.'%')->where('billings.vid','=',$vendor)
             ->orWhere('billings.state','like','%'.$search.'%')->where('billings.vid','=',$vendor)
            //  ->orWhere('line_items.quantity','like','%'.$search.'%')->where('line_items.vid','=',$vendor)
             ->orWhere('orders.date_created','like','%'.$search.'%')->where('orders.vid','=',$vendor)
             ->orWhere('orders.total','like','%'.$search.'%')->where('orders.vid','=',$vendor)
          ->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND  line_items.vid = " . intval($vendor) . " GROUP BY line_items.order_id) as quantity"))->get();
        
         return $orders;
           
    }
    //fetch order profile  orders.oid(table)with billings.order_id base pa
    public function order_Profile($oid) {
      $vid = $_REQUEST['vid'];
        $order = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.oid', '=', $oid)->where('orders.vid', '=', intval($_REQUEST['vid']))->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($_REQUEST['vid']) . " GROUP BY line_items.order_id) as quantity"), DB::raw("(SELECT SUM(line_items.total) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($_REQUEST['vid']) . " GROUP BY line_items.order_id) as total_main"))->get();
        return $order;
    }
    //fetch order items table line_items.order_id = oid(match)and next line_items.vid(match)next pluck product_id use for toArray
    public function order_items($oid) {
        $orderItems = DB::table("line_items")       
        ->where('line_items.order_id', '=', $oid)
        ->where('line_items.vid', '=', intval($_REQUEST['vid']))
        ->pluck('product_id')->toArray();//table line_item to fetch product_id
        //get detail products table according to product_id (match)$orderItems(variable)
        $get_detail=DB::table("products")->whereIn('product_id',$orderItems)->where('vid', '=', intval($_REQUEST['vid']))->get();
        
        return $get_detail;
    }
    //getOrderDetails   vendors table according to vendors id=vid
    public function getOrderDetails(Request $request) {
        $vendor = $request->vid;
        $vendor2 = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $vendor2[0]->url.'/wp-json/wc/v3/orders',
          // CURLOPT_URL => $vendor2[0]->url.'/wp-json/wc/v3/orders',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . $vendor2[0]->token
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $jsonResp = json_decode($response);
        foreach ($jsonResp as $jp) {
            $curl_data[] = $jp->id;
            
        }

        // $orders = DB::table("orders")->where('vid', '=', intval($_REQUEST['vid']))->where('status', '=', "processing")->get();
        $orders = DB::table("orders")->where('vid', '=', intval($request->vid))->get();
        // var_dump($orders);
        if (count($orders) >= 1) {
            foreach ($orders as $order) {
                $order_data[] = $order->oid;
            }
        }else{
            $order_data = array();
        }

        // var_dump($curl_data);
        // var_dump($order_data); die;
        $result_arr=array_diff($curl_data,$order_data);
        $result = array_values($result_arr); 
        // var_dump($result); die;
        if (count($result) >= 1) {
            for ($i=0; $i < count($result); $i++) { 
                // echo "<a href='#' target='_blank'>Click here (ORDER ID - ".$result[$i].")</a><br><br>";
                // echo "<a href='https://cl.majime.in/api/v1/get_order_data?api_url=".$vendor[0]->url."/wp-json/wc/v3/orders/".intval($result[$i])."&vid=".intval($_REQUEST['vid'])."' target='_blank'>Click here (ORDER ID - ".$result[$i].")</a><br><br>";
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://cl.majime.in/api/v1/get_order_data?api_url=".$vendor2[0]->url."/wp-json/wc/v3/orders/".intval($result[$i])."&vid=".intval($request->vid),
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic Og=='
                  ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;
            }
        }
        $date = \Carbon\Carbon::today()->subDays(7);
        if ($vendor != null) {
        //     $orders = DB::table("orders")->join('billings', function($join) use ($vendor)
        // {
        //     $join->on('orders.oid', '=', 'billings.order_id')
        //          ->where('billings.vid', '=', intval($vendor));
        // })->where('orders.vid', '=', intval($vendor))->orderBy('oid', 'DESC')
        //     ->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = " . intval($vendor) . " GROUP BY line_items.order_id) as quantity"))->get();
            // echo "abc";die();
            $orders = DB::table("orders")->join('billings', function($join) use ($vendor)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vendor));
        })
        ->where('orders.vid', '=', intval($vendor))->where('date_created','>=',$date)->orderBy('oid', 'DESC')->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                                        WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vendor) . "
                                        GROUP BY line_items.order_id) as quantity"))->get();
        } else {
            $orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')->where('orders.vid', '=', intval($vendor))->where('billings.vid', '=', intval($vendor))->where('date_created','>=',$date)->orderBy('oid', 'DESC')->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                                        WHERE line_items.order_id = orders.oid
                                        GROUP BY line_items.order_id) as quantity"))->get();
                                                        
        }
        return $orders;
    }
    //packed order use table orders and billings
    public function getPackdetail($vid) {
        $orderItems = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->join('waybill', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'waybill.order_id')
                 ->where('waybill.vid', '=', intval($vid));
        })->where('orders.vid', $vid)->where('orders.status', "packed")->select("orders.*", "billings.*","waybill.waybill_no", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " GROUP BY line_items.order_id) as quantity"), DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as name"), DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as sku"))
        // ->select("line_items.sku as SKU","line_items.name as Name","line_items.quantity as Qty")
        // DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
        //                      WHERE line_items.order_id = orders.oid
        //                      GROUP BY line_items.order_id) as quantity")
        ->orderBy('oid', 'DESC')->get();
        return $orderItems;
    }
    //fetch packed details refund according to orders table and billings
    public function get_packdetail_Refund($vid) {
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', $vid)->where('orders.status', "dtodelivered")->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " GROUP BY line_items.order_id) as quantity"), DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as name"), DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as sku"))->orderBy('oid', 'DESC')->get();
        return $orders;
    }
    //fetch order stataus (api send)$vid,$status(variavle) according to orders table and billings
    public function getOrderOnStatus($vid, $status) {
        // echo "string"; die;
        $orderItems = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', $vid)
        // ->where('billings.vid',$vid)
        ->where('orders.status', $status)->get();
        return $orderItems;
    }
     //fetch order stataus (api send)$vid, $statrto,$statdto,$statcomp,$clos(variavle) according to orders table and billings
    public function getComplete_OrdersStatus($vid, $statrto,$statdto,$statcomp,$clos) 
    {
        // echo "string"; die;
        $int_check = 0;
        $orderItems = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->join('waybill', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'waybill.order_id')
                 ->where('waybill.vid', '=', intval($vid));
        })->where('orders.vid', $vid)->where('orders.wallet_processed', $int_check)
        ->whereIn('orders.status',[$statrto,$statdto,$statcomp,$clos])
        ->orderBy('orders.oid','DESC')
        ->get();
        
        // ->where('billings.vid',$vid)
        // ->where('orders.status',$status)->where('orders.status',$state)->get();
        return $orderItems;
    }
    //return order order_id fetch to data table way_data city,name,pin ,country,phone,add,token,order_perfix
    public function return_order() {
        $oid = $_REQUEST['order_id'];
        $vid = $_REQUEST['vid'];
        $my_data = DB::table("way_data")->where('vid', $_REQUEST['vid'])->get();
        $city = $my_data[0]->city;
        $name = $my_data[0]->name;
        $pin = $my_data[0]->pin;
        $country = $my_data[0]->country;
        $phone = $my_data[0]->phone;
        $add = $my_data[0]->add;
        $token = $my_data[0]->token;
        $order_prefix = $my_data[0]->order_prefix;
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', '=', $vid)->where('orders.oid', '=', $oid)->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                                        WHERE line_items.order_id = orders.oid
                                        GROUP BY line_items.order_id) as quantity"))->get();

        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_URL => 'https://track.delhivery.com/api/cmu/create.json', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => 'format=json&data={
                          "shipments": [
                            {
                              "add": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|' ), ' ', $orders[0]->address_1) . ', ' . str_replace( array( '\'', '"', '-', ';', '<', '>', '&', '|' ), ' ', $orders[0]->address_2) . '",
                              "phone": ' . $orders[0]->phone . ',
                              "payment_mode": "Pickup",
                              "name": "' . $orders[0]->first_name . '",
                              "pin": ' . $orders[0]->postcode . ',
                              "order": '.$order_prefix.$oid . '",
                              "return_state": "' . $city . '",
                                "return_city": "' . $city . '",
                                "return_phone": "' . $phone . '",
                                "return_add": "' . $add . '",
                                "return_pin": "' . $pin . '",
                                "extra_parameters": { 
                                  "return_reason": "Return Order"
                                },
                                "return_name": "' . $name . '"
                            }
                          ],
                          "pickup_location": 
                            {
                              "city": "' . $city . '",
                              "name": "' . $name . '",
                              "pin": "' . $pin . '",
                              "country": "' . $country . '",
                              "phone": "' . $phone . '",
                              "add": "' . $add . '"
                            }
                        }', CURLOPT_HTTPHEADER => array('Authorization: Token ' . $token, 'Content-Type: application/json', 'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'),));
        $response = curl_exec($curl);
        $new_val = json_decode($response, true);
        $wbill = $new_val["packages"][0]["waybill"];
        $order_items = DB::table("waybill")->where('waybill.vid', $vid)->where('waybill.order_id', $oid)->get()->toArray();
        // var_dump($order_items); die;
        if (count($order_items) >= 1) {
            $curl = curl_init();
            $vendor = DB::table("vendors")->where('id', '=', intval($vid))->get();
            curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=dtobooked', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
            $response = curl_exec($curl);
            curl_close($curl);
            $jsonResp = json_decode($response);
            DB::table('waybill')->where('order_id', $_REQUEST['order_id'])->where('vid', $_REQUEST['vid'])->update(['return_waybill_no' => $wbill]);
            DB::table('orders')->where('oid', $_REQUEST['order_id'])->where('vid', $_REQUEST['vid'])->update(['status' => "dtobooked"]);
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
            return response()->json(['error' => false, 'abn_no' => $wbill, "ErrorCode" => "000"], 200);
        }
    }
    //assign number according to packed order
    public function assignAWB(Request $request) {
        $main = explode(',', $request->allSelected);
        $vid = $request->vid;
        // echo "strong"; die;
        for ($i = 0;$i < count($main);$i++) {
            $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.oid', intval($main[$i]))->where('orders.vid', $request->vid)->where('orders.status', 'confirmed')->get();
            $my_data = DB::table("way_data")->where('vid', $request->vid)->get();
            $city = $my_data[0]->city;
            $name = $my_data[0]->name;
            $pin = $my_data[0]->pin;
            $country = $my_data[0]->country;
            $phone = $my_data[0]->phone;
            $add = $my_data[0]->add;
            $token = $my_data[0]->token;
            $order_prefix = $my_data[0]->order_prefix;
            $weight = '450';
            foreach ($orders as $order) {
                // var_dump($order);die();
                $order_id = $order->oid;
                $product_name = "";
                $order_items = DB::table("line_items")->where('line_items.vid', $request->vid)->where('line_items.order_id', $order_id)->get();
                if ($request->vid == 1) {
                    $curlopt_url = "https://staging-express.delhivery.com/api/cmu/create.json";
                    $del_url = "https://staging-express.delhivery.com/c/api/pin-codes/json/";
                } else {
                    $curlopt_url = "https://track.delhivery.com/api/cmu/create.json";
                    $del_url = "https://track.delhivery.com/c/api/pin-codes/json/";
                }
                // var_dump($order->postcode);
                foreach ($order_items as $product) {
                    $product_name = $product_name . " | " . $product->name . " - " . $product->quantity;
                }
                $curl2 = curl_init();
                curl_setopt_array($curl2, array(CURLOPT_URL => $del_url . '?filter_codes=' . $order->postcode, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_HTTPHEADER => array('Authorization: Token ' . $token, 'Content-Type: application/json'),));
                $response2 = curl_exec($curl2);
                curl_close($curl2);
                $new_val2 = json_decode($response2, true);
                // var_dump($new_val2); die;
                if (count($new_val2["delivery_codes"]) > 0) {
                    $curl = curl_init();
                    if ($order->payment_method == "cod") {
                        curl_setopt_array($curl, array(CURLOPT_URL => $curlopt_url, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => 'format=json&data={
                          "shipments": [
                            {
                              "add": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|', ',' ), ' ', $order->address_1) . ', ' . str_replace( array( '\'', '"', '-', ';', '<', '>', '&', '|', ',' ), ' ', $order->address_2) . '",
                              "phone": ' . $order->phone . ',
                              "payment_mode": "COD",
                              "name": "' . $order->first_name . ' ' . $order->last_name . '",
                              "pin": ' . $order->postcode . ',
                              "cod_amount":' . $order->total . ',
                              "order": "' . $order_prefix . $order->oid . '",
                              "shipping_mode" : "Surface",
                              "products_desc": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|' ), ' ', $product_name) . '",
                              "weight": "' . $weight . '"
                            }
                          ],
                          "pickup_location": 
                            {
                              "city": "' . $city . '",
                              "name": "' . $name . '",
                              "pin": "' . $pin . '",
                              "country": "' . $country . '",
                              "phone": "' . $phone . '",
                              "add": "' . $add . '"
                            }
                        }', CURLOPT_HTTPHEADER => array('Authorization: Token ' . $token, 'Content-Type: application/json', 'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'),));
                    } else {
                        curl_setopt_array($curl, array(CURLOPT_URL => $curlopt_url, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => 'format=json&data={
                          "shipments": [
                            {
                              "add": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|' ), ' ', $order->address_1) . ', ' . str_replace( array( '\'', '"', '-', ';', '<', '>', '&', '|' ), ' ', $order->address_2) . '",
                              "phone": ' . $order->phone . ',
                              "payment_mode": "Prepaid",
                              "name": "' . $order->first_name . ' ' . $order->last_name . '",
                              "pin": ' . $order->postcode . ',
                              "cod_amount":' . $order->total . ',
                              "order": "' . $order_prefix . $order->oid . '",
                              "shipping_mode" : "Surface",
                              "products_desc": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|' ), ' ', $product_name) . '"
                            }
                          ],
                          "pickup_location": 
                            {
                              "city": "' . $city . '",
                              "name": "' . $name . '",
                              "pin": "' . $pin . '",
                              "country": "' . $country . '",
                              "phone": "' . $phone . '",
                              "add": "' . $add . '"
                            }
                        }', CURLOPT_HTTPHEADER => array('Authorization: Token ' . $token, 'Content-Type: application/json', 'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'),));
                    }
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $new_val = json_decode($response, true);
                    // if (isset($new_val["packages"])) {
                        if (!empty($new_val["packages"])) {
                            if($new_val["packages"][0]['status'] != "Fail"){
                            //     $curl = curl_init();
                            //     $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                            //     curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=on-hold', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                            //     $response = curl_exec($curl);
                            //     curl_close($curl);
                            //     $jsonResp = json_decode($response);
                            //     DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "on-hold"]);
                            //     // return response()->json(['error' => true, 'msg' => $new_val['rmk'], "ErrorCode" => - 2], 200);
                            //     $msg = "Delivery is not available on this pincode.";
                            // }else{
                                $wbill = $new_val["packages"][0]["waybill"];
                                $o_id = $order_id;
                                $order_items = DB::table("waybill")->where('waybill.vid', $request->vid)->where('waybill.order_id', $order_id)->get()->toArray();
                                if (empty($order_items)) {
                                    DB::table('waybill')->insert(['vid' => $request->vid, 'order_id' => $order_id, 'waybill_no' => $wbill, 'date_created' => date("Y-m-d h:i:s") ]);
                                    DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "packed"]);
                                    $curl = curl_init();
                                    $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                                    curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=packed', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $jsonResp = json_decode($response);
                                    $curl2 = curl_init();
                                    curl_setopt_array($curl2, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/waybill/waybill_data?order_id=' . $order_id . '&waybill_no=' . $new_val["packages"][0]["waybill"], CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET',));
                                    $response2 = curl_exec($curl2);
                                    curl_close($curl2);
                                    $jsonResp2 = json_decode($response2);
                                    $error = false;
                                    $msg = "WayBill successfully added.";
                                    // return response()->json(['error' => false, 'msg' => "WayBill successfully added.", "ErrorCode" => "000"],200);
                                    
                                } else {
                                    $curl = curl_init();
                                    $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                                    curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=packed', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $jsonResp = json_decode($response);
                                    DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "packed"]);
                                    $error = false;
                                    $msg = "Already Assign AWB No.";
                                    // return response()->json(['error' => true, 'msg' => "Already Assign AWB No.","ErrorCode" => -2],200);
                                }
                            }
                        } else {
                            $error = true;
                            $curl = curl_init();
                            $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                            curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=on-hold', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                            $response = curl_exec($curl);
                            curl_close($curl);
                            $jsonResp = json_decode($response);
                            DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "on-hold"]);
                            $msg = $new_val['rmk'];
                            // return response()->json(['error' => true, 'msg' => $new_val['rmk'],"ErrorCode" => -2],200);
                        }
                    // } else {
                    //     $error = true;
                    //     $curl = curl_init();
                    //     $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                    //     curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=on-hold', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                    //     $response = curl_exec($curl);
                    //     curl_close($curl);
                    //     $jsonResp = json_decode($response);
                    //     DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "on-hold"]);
                    //     $msg = $new_val['detail'];
                    //     // return response()->json(['error' => true, 'msg' => $new_val['detail'],"ErrorCode" => -2],200);
                    // }
                } else {
                    DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "on-hold"]);
                    $curl = curl_init();
                    $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                    curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=on-hold', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $jsonResp = json_decode($response);
                    $error = false;
                    $msg = "No WayBill generate so status set to on-hold.";
                    // return response()->json(['error' => false, 'msg' => "No WayBill generate so status set to on-hold.","ErrorCode" => "000"],200);
                    
                }
            }
        }
        if ($error == false) {
            return response()->json(['error' => false, 'msg' => $msg, "ErrorCode" => "000"], 200);
        } else {
            return response()->json(['error' => true, 'msg' => $msg, "ErrorCode" => - 2], 200);
        }
    }
    //assign aws number packed order
    function assignAWBOrder(Request $request) {
        // dd($request); die;
        $vid = $request->vid;
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', intval($request->vid))->where('orders.oid', intval($request->oid))->get();
        // var_dump($orders); die;
        // echo "string"; die;
        $my_data = DB::table("way_data")->where('vid', intval($request->vid))->get();
        // var_dump($my_data); die;
        $city = $my_data[0]->city;
        $name = $my_data[0]->name;
        $pin = $my_data[0]->pin;
        $country = $my_data[0]->country;
        $phone = $my_data[0]->phone;
        $add = $my_data[0]->add;
        $weight = '450';
        $token = $my_data[0]->token;
        $order_prefix = $my_data[0]->order_prefix;
        if ($request->vid == 1) {
            $curlopt_url = "https://staging-express.delhivery.com/api/cmu/create.json";
            $del_url = "https://staging-express.delhivery.com/c/api/pin-codes/json/";
        } else {
            $curlopt_url = "https://track.delhivery.com/api/cmu/create.json";
            $del_url = "https://track.delhivery.com/c/api/pin-codes/json/";
        }
        // $curlopt_url = "https://track.delhivery.com/api/cmu/create.json";
        foreach ($orders as $order) {
            // var_dump($order);die();
            $order_id = $order->oid;
            $product_name = "";
            $order_items = DB::table("line_items")->where('line_items.vid', intval($request->vid))->where('line_items.order_id', intval($order_id))->get();
            foreach ($order_items as $product) {
                $product_name = $product_name . " | " . $product->name . " - " . $product->quantity;
            }
            // echo $order->postcode; die;
            $curl2 = curl_init();
            curl_setopt_array($curl2, array(CURLOPT_URL => $del_url . '?filter_codes=' . $order->postcode, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_HTTPHEADER => array('Authorization: Token ' . $token, 'Content-Type: application/json'),));
            $response2 = curl_exec($curl2);
            curl_close($curl2);
            $new_val2 = json_decode($response2, true);

            // echo $addr = str_replace( array( '\'', '"', '&', ';', '-', '<', '>', '_' ), ' ', $order->address_1);
            // echo $add; Bhanusree Lade, 501 Block 10, Myhomeavatar, Hyderabad 500089, Telangana
            // echo str_replace( array( '\'', '"', ';', '-', '<', '>' ), ' ', $order->address_1) . ', ' . str_replace( array( '\'', '"', '-', ';', '<', '>' ), ' ', $order->address_2); die;
            // 36 & 37 Suyog Residency 2 , B wing , Flat no 104, Sector 8 , Sanpada, Navi Mumbai
            // var_dump($new_val2); die;
            if ($new_val2 != NULL) {
                $curl = curl_init();
                if ($order->payment_method == "cod") {
                    $payment_mode = "COD";
                } else {
                    $payment_mode = "Prepaid";
                }
                $postfields = 'format=json&data={
                      "shipments": [
                        {
                          "add": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|', ',', '/' ), ' ', $order->address_1) . ', ' . str_replace( array( '\'', '"', '-', ';', '<', '>', '&', '|', ',', '/' ), ' ', $order->address_2) . '",
                          "phone": ' . $order->phone . ',
                          "payment_mode": "' . $payment_mode . '",
                          "name": "' . htmlentities($order->first_name) . ' ' . htmlentities($order->last_name) . '",
                          "pin": ' . $order->postcode . ',
                          "cod_amount":' . $order->total . ',
                          "order": "' . $order_prefix . $order->oid . '",
                          "shipping_mode" : "Surface",
                          "products_desc": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|' ), ' ', $product_name) . '",
                          "weight": "' . $weight . '"
                        }
                      ],
                      "pickup_location": 
                        {
                          "city": "' . $city . '",
                          "name": "' . $name . '",
                          "pin": "' . $pin . '",
                          "country": "' . $country . '",
                          "phone": "' . $phone . '",
                          "add": "' . $add . '"
                        }
                    }';
                    // echo $postfields; die;
                // echo $postfields; die;
                curl_setopt_array($curl, array(CURLOPT_URL => $curlopt_url, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => $postfields, CURLOPT_HTTPHEADER => array('Authorization: Token ' . $token, 'Content-Type: application/json', 'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'),));
                $response = curl_exec($curl);
                curl_close($curl);
                $new_val = json_decode($response, true);
                // var_dump($new_val["packages"][0]['status']); die;
                // if(isset($new_val["packages"])){
                if (!empty($new_val["packages"])) {
                    // if($new_val["packages"][0]['status'] == "Fail"){
                    //     $curl = curl_init();
                    //     $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                    //     curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=on-hold', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                    //     $response = curl_exec($curl);
                    //     curl_close($curl);
                    //     $jsonResp = json_decode($response);
                    //     DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "on-hold"]);
                    //     return response()->json(['error' => true, 'msg' => $new_val['rmk'], "ErrorCode" => - 2], 200);
                    // }else{
                        $wbill = $new_val["packages"][0]["waybill"];
                        $o_id = $order_id;
                        $order_items = DB::table("waybill")->where('waybill.vid', $request->vid)->where('waybill.order_id', $order_id)->get()->toArray();
                        if (empty($order_items)) {
                            if($new_val["packages"][0]['status'] == "Fail"){
                                return response()->json(['error' => false, 'msg' => $new_val["packages"][0]['remarks'][0], "ErrorCode" => "000"], 200);
                            }else{
                                DB::table('waybill')->insert(['vid' => $request->vid, 'order_id' => $order_id, 'waybill_no' => $wbill, 'date_created' => date("Y-m-d h:i:s") ]);
                                DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "packed"]);
                                $curl = curl_init();
                                $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                                curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=packed', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                                $response = curl_exec($curl);
                                curl_close($curl);
                                $jsonResp = json_decode($response);
                                $curl2 = curl_init();
                                curl_setopt_array($curl2, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/waybill/waybill_data?order_id=' . $order_id . '&waybill_no=' . $new_val["packages"][0]["waybill"], CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET',));
                                $response2 = curl_exec($curl2);
                                curl_close($curl2);
                                $jsonResp2 = json_decode($response2);
                                return response()->json(['error' => false, 'msg' => "WayBill successfully added.", "ErrorCode" => "000"], 200);
                            }
                        } else {
                            if($new_val["packages"][0]['status'] == "Fail"){
                                return response()->json(['error' => false, 'msg' => $new_val["packages"][0]['remarks'][0], "ErrorCode" => "000"], 200);
                            }else{
                                $curl = curl_init();
                                $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                                curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=packed', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                                $response = curl_exec($curl);
                                curl_close($curl);
                                $jsonResp = json_decode($response);
                                DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "packed"]);
                                return response()->json(['error' => false, 'msg' => "Already Assign AWB No.", "ErrorCode" => - 2], 200);
                            }
                        }
                    // }
                } else {
                    $curl = curl_init();
                    $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                    curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=on-hold', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $jsonResp = json_decode($response);
                    DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "on-hold"]);
                    return response()->json(['error' => true, 'msg' => $new_val['rmk'], "ErrorCode" => - 2], 200);
                }
                // }else{
                //  return response()->json(['error' => true, 'msg' => $new_val['detail'],"ErrorCode" => -2],200);
                // }
                
            } else {
                DB::table('orders')->where('oid', $order_id)->where('vid', $request->vid)->update(['status' => "on-hold"]);
                $curl = curl_init();
                $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $order_id . '?status=on-hold', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                $response = curl_exec($curl);
                curl_close($curl);
                $jsonResp = json_decode($response);
                return response()->json(['error' => false, 'msg' => "No WayBill generate so status set to on-hold.", "ErrorCode" => "000"], 200);
            }
        }
    }
    // RETURN 
    function return_awb(Request $request) {
        $oid = $request->oid;
        $vid = $request->vid;
        $my_data = DB::table("way_data")->where('vid', intval($request->vid))->get();
        $city = $my_data[0]->city;
        $name = $my_data[0]->name;
        $pin = $my_data[0]->pin;
        $country = $my_data[0]->country;
        $phone = $my_data[0]->phone;
        $add = $my_data[0]->add;
        $token = $my_data[0]->token;
        $order_prefix = $my_data[0]->order_prefix;
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', '=', intval($vid))->where('orders.oid', '=', intval($oid))->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                                        WHERE line_items.order_id = orders.oid
                                        GROUP BY line_items.order_id) as quantity"))->get();
        if ($request->vid == 1) {
            $curlopt_url = "https://staging-express.delhivery.com/api/cmu/create.json";
            $del_url = "https://staging-express.delhivery.com/c/api/pin-codes/json/";
        } else {
            $curlopt_url = "https://track.delhivery.com/api/cmu/create.json";
            $del_url = "https://track.delhivery.com/c/api/pin-codes/json/";
        }
        // var_dump($orders); die;
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_URL => $curlopt_url, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => 'format=json&data={
                          "shipments": [
                            {
                              "add": "' . str_replace( array( '\'', '"', ';', '-', '<', '>', '&', '|' ), ' ', $orders[0]->address_1) . ', ' . str_replace( array( '\'', '"', '-', ';', '<', '>', '&', '|' ), ' ', $orders[0]->address_2) . '",
                              "phone": ' . $orders[0]->phone . ',
                              "payment_mode": "Pickup",
                              "name": "' . $orders[0]->first_name . '",
                              "pin": ' . $orders[0]->postcode . ',
                              "order": "' . $order_prefix . $orders[0]->oid . '",
                              "return_state": "' . $city . '",
                                "return_city": "' . $city . '",
                                "return_phone": "' . $phone . '",
                                "return_add": "' . $add . '",
                                "return_pin": "' . $pin . '",
                                "extra_parameters": { 
                                  "return_reason": "Return Order"
                                },
                                "return_name": "' . $name . '"
                            }
                          ],
                          "pickup_location": 
                            {
                              "city": "' . $city . '",
                              "name": "' . $name . '",
                              "pin": "' . $pin . '",
                              "country": "' . $country . '",
                              "phone": "' . $phone . '",
                              "add": "' . $add . '"
                            }
                        }', CURLOPT_HTTPHEADER => array('Authorization: Token ' . $token, 'Content-Type: application/json', 'Cookie: sessionid=ze4ncds5tobeyynmbb1u0l6ccbpsmggx; sessionid=3q84k2vbcp2r6mq1hpssniobesxvcf12'),));
        $response = curl_exec($curl);
        $new_val = json_decode($response, true);
        // var_dump($new_val); die;
        $wbill = $new_val["packages"][0]["waybill"];
        $order_items = DB::table("waybill")->where('waybill.vid', $vid)->where('waybill.order_id', $oid)->get()->toArray();
        // var_dump($order_items); die;
        if (count($order_items) >= 1) {
            DB::table('waybill')->where('order_id', intval($request->oid))->where('vid', intval($request->vid))->update(['return_waybill_no' => $wbill]);
            DB::table('orders')->where('oid', intval($request->oid))->where('vid', intval($request->vid))->update(['status' => "dtobooked"]);
            $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
            curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $oid . '?status=dtobooked', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                $response = curl_exec($curl);
                curl_close($curl);
                $jsonResp = json_decode($response);
            //           $curl = curl_init();
            //           $vendor =DB::table("vendors")->where('id','=',intval($vid))->get();
            //           var_dump($vendor[0]->url); die;
            //    curl_setopt_array($curl, array(
            //    CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders/'.intval($request->oid).'?status=dtobooked',
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
            return response()->json(['error' => false, 'msg' => 'Successfully Done', 'abn_no' => $wbill, "ErrorCode" => "000"], 200);
        }
    }
    //this api use all status cancelled,processing,confirmed,
    function changeStatus(Request $request) {
        // if ($request->status_assign == 'cancelled') {
        //     // echo $request->status; die;
        //     if ($request->status == 'processing' || $request->status == 'on-hold') {
        //         DB::table('orders')->where('oid', intval($request->selectall))->where('vid', intval($request->vid))->update(['status' => $request->status_assign]);
        //         // print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
        //         // https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
        //         $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
        //         $curl = curl_init();
        //         curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . intval($request->selectall) . '?status=' . $request->status_assign, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
        //         $response = curl_exec($curl);
        //         curl_close($curl);
        //         $jsonResp = json_decode($response);
        //         // var_dump($jsonResp);
        //         return response()->json(['error' => false, 'msg' => "Order Status successfully updated.", "ErrorCode" => "000"], 200);
        //     } else {
        //         return response()->json(['error' => false, 'msg' => "Please Note :  Order Can Not Be Cancel.", "ErrorCode" => "000"], 200);
        //     }
        // } else {

            $listImp = explode(',', $request->selectall);
            for ($i=0; $i < count($listImp); $i++) { 
                DB::table('orders')->where('oid', intval($listImp[$i]))->where('vid', intval($request->vid))->update(['status' => $request->status_assign]);
                $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                $curl = curl_init();
                curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . intval($listImp[$i]) . '?status=' . $request->status_assign, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                $response = curl_exec($curl);
                curl_close($curl);
                $jsonResp = json_decode($response);
            }


            // print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
            // https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
            
            // var_dump($jsonResp);
            return response()->json(['error' => false, 'msg' => "Order Status Successfully Updated.", "ErrorCode" => "000"], 200);
    //    } 
    }
    //all status api use changeOrderStatus
    public function changeOrderStatus($vid, $oid, $status) {
        // var_dump($vid);
        // var_dump($oid);
        // var_dump($status); die;
        DB::table('orders')->where('oid', intval($oid))->where('vid', intval($vid))->update(['status' => $status]);
        // print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
        // https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
        $vendor = DB::table("vendors")->where('id', '=', intval($vid))->get();
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . intval($oid) . '?status=' . $status, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
        $response = curl_exec($curl);
        curl_close($curl);
        $jsonResp = json_decode($response);
        return response()->json(['error' => false, 'msg' => "Order Status Successfully Updated.", "ErrorCode" => "000"], 200);
    }
    function printSlip(Request $request) {
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
        $style = array('position' => 'C', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => '0', 'vpadding' => '0', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
        'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4);
        $vid = $request->vid;
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', $request->vid)->whereIn('orders.oid', $listImp)->where('orders.status', 'packed')->get();

        if($request->vid == 1){
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }elseif($request->vid == 2){
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }elseif($request->vid == 3){
            $address_store = '<p><span class="c_name">HK Texfab Pvt Ltd<br>GST NO : 03AAFCH1375M1ZJ</span><br>E-207 IV-A Focal Point, Focal Point, Focal Point,<br>141010 - Ludhiana, Punjab, India</p>';
            $industry_name = "HK Texfab Pvt Ltd";
        }elseif($request->vid == 4){
            $address_store = '<p><span class="c_name">INDRA HOSIERY MILLS<br>GST NO : 03AAAFI3516P1ZG</span><br>Plot 31,32,33 & 34,35,36 Behind Nagesh Building<br>Sharman Enclave near Jalandhar Bypass <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "INDRA HOSIERY MILLS";
        }elseif($request->vid == 8){
            $address_store = '<p><span class="c_name">ShopMetalm<br>GST NO : 03AAAFI3516P1ZG</span><br>Plot 31,32,33 & 34,35,36 Behind Nagesh Building<br>Sharman Enclave near Jalandhar Bypass <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "ShopMetalm";
        }elseif($request->vid == 9){
            $address_store = '<p><span class="c_name">Krelon Cosmetics (Opc) Pvt Ltd<br>GST NO : 03AAJCK4238L1ZE</span><br>374-A,Model Town Extension <br>Ludhiana, Punjab, India</p>';
            $industry_name = "Krelon Cosmetics (Opc) Pvt Ltd";
        }elseif($request->vid == 10){
            $address_store = '<p><span class="c_name">Yall<br>GST NO : 03ABXFA0812G1ZP</span><br>Shop #19-20, Lower Ground Floor (LGF), Society Plaza, Near Old Lakkar Bridge, Ludhiana 141008</p>';
            $industry_name = "Yall";
        }elseif($request->vid == 11){
            $address_store = '<p><span class="c_name">Oh Ex<br>GST NO : 03EIIPP4249C1ZE</span><br>Deep Vihar, E-10/7530/9, Village Famra Bahadur ke Road<br>Deep Vihar, The Knit Lounge, <br>141004 - Ludhiana, Punjab, India</p>';
            $industry_name = "Oh Ex";
        }elseif($request->vid == 12){
            $address_store = '<p><span class="c_name">Donna Flair<br>GST NO : </span><br>Deep Vihar, E-10/7530/9, Village Famra Bahadur ke Road, <br>Deep Vihar, The Knit Lounge, <br>141004 - Ludhiana, Punjab, India</p>';
            $industry_name = "Donna Flair";
        }elseif($request->vid == 13){
            $address_store = '<p><span class="c_name">MAD<br>GST NO : 03BGXPR0302L1Z8</span><br>B-32-E12/875 Thapar Nagar Opp GMT Public School Near Jalandhar Bypass, <br>Jalandhar Bypass, GMT Public School, <br>141008 - Ludhiana, Punjab, India</p>';
            $industry_name = "MAD";
        }elseif($request->vid == 14){
            $address_store = '<p><span class="c_name">Menage<br>GST NO : 03AKFPS6566A1ZA</span><br>H.No. 3732 Sector 32-A, <br>Chandigarh Road<br>141010 - Ludhiana, Punjab, India</p>';
            $industry_name = "Menage";
        }elseif($request->vid == 15){
            $address_store = '<p><span class="c_name">Siko Customs<br>GST NO : 03ABQFM7484H1ZS</span><br>B/501 Aditya Aryan, Chogle Nagar, Near Shani Mandir,  <br>Borivali East, Chaogle Nagar, Shani Mandir<br>400066 - Mumbai, Maharashtra, India</p>';
            $industry_name = "Siko Customs";
        }elseif($request->vid == 16){
            $address_store = '<p><span class="c_name">Algos Clothing<br>GST NO : 03ABQFM7484H1ZS</span><br>160, Dugri Phase 2, Dugri, Dugri, <br>141002 - Ludhiana, Punjab, India</p>';
            $industry_name = "Algos Clothing";
        }elseif($request->vid == 17){
            $address_store = '<p><span class="c_name">DripArt<br>GST NO : 03ABQFM7484H1ZS</span><br>160, Dugri Phase 2,<br> Dugri, Dugri,<br>141002 - Ludhiana, Punjab, India</p>';
            $industry_name = "DripArt";
        }elseif($request->vid == 18){
            $address_store = '<p><span class="c_name">Raysa fabrics pvt ltd<br>GST NO : 03AALCR9865Q1ZC</span><br>Shop no-127,129,131,<br/>Bhadaur House,AC Market <br>141008 - Ludhiana, Punjab, India</p>';
            $industry_name = "Raysa fabrics pvt ltd";
        }elseif($request->vid == 19){
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }else{
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }


        // dd($orders); die;
        $i = 1;
        foreach ($orders as $order) {
            $oid = $order->oid;
            $results2 = DB::table("waybill")->where('vid', $request->vid)->where('order_id', $oid)->get()->toArray();
            if (count($results2) >= 1) {
                $wbill = $results2[0]->waybill_no;
                $img_base64_encoded = 'data:image/png;base64,' . $d->getBarcodePNG($wbill, 'C128', 3, 33, array(1, 1, 1));
                $params = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '">';
                $img_base64_encoded2 = 'data:image/png;base64,' . $d->getBarcodePNG($order->oid, 'C128', 3, 33, array(1, 1, 1));
                $params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
            } else {
                $wbill = "";
                $params = "";
                $img_base64_encoded2 = 'data:image/png;base64,' . $d->getBarcodePNG($order->oid, 'C128', 3, 33, array(1, 1, 1));
                $params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
            }
            //      $pdf::Cell(0, 0, 'CODABAR', 0, 1);
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
                            <td rowspan="3" width="360">'.$address_store.'</td>
                            <td width="150">Order No. (REF) : ' . $order->oid . '</td>
                            <td width="150">GST-JR-3557</td>
                        </tr>
                        <tr>
                            <td width="150">Mode : ' . $order->payment_method_title . ' </td>
                            <td width="150">Amount : Rs.' . $order->total . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">Courier Partner : DIRECT_DELHIVERY</td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" cellpadding="5" class="child_table customer_info">
                    <tbody>
                        <tr>
                            <td width="660"><p><span class="c_name">' . strtoupper($order->first_name) . ' ' . strtoupper($order->last_name) . ' <span>(M) ' . $order->phone . '</span></span><br>Address : ' . $order->address_1;
            if ($order->address_2 != '') {
                $html2.= '<br>Landmark : ' . $order->address_2;
            } else {
                $html2.= '<br>Landmark : N/A';
            }
            $html2.= '<br>Pincode : ' . $order->postcode . ', ' . $order->city . ', ' . $order->state . ', ' . $order->country . '</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" cellpadding="5" class="child_table customer_awb">
                    <tbody>
                        <tr>
                            <td width="660" align="center"><span class="awb_text">AWB : ' . $wbill . '</span><br/>' . $params . '</td>
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
            $order_items = DB::table("line_items")->where('order_id', '=', $oid)->where('vid', '=', $request->vid)->get();
            $j = 1;
            $itm = 0;
            $itm_sub = 0;
            $cnt = count($order_items);
            if ($cnt >= 1) {
                $min_height = 516 / $cnt;
            } else {
                $min_height = 516;
            }
            $height = $min_height . 'px';
            foreach ($order_items as $product) {
                $itm = $itm + $product->quantity;
                $itm_sub = $itm_sub + $product->subtotal;
                $html2.= '<tr>
                                <td width="40" height="' . $height . '">' . $j . '</td>
                                <td width="250">' . $product->name . '<br>SKU: ' . $product->sku . '</td>
                                <td width="100">Rs.' . $product->subtotal . '</td>
                                <td width="80" align="center">' . $product->quantity . '</td>
                                <td width="90">Rs.0</td>
                                <td width="100">Rs.' . $product->total . '</td>
                                </tr>';
                $j++;
            }
            $html2.= '</tbody></table><table width="100%" cellpadding="5" class="child_table product_info">
                    <tbody>
                        <tr>
                            <td width="40">T1</td>
                            <td width="250">T1</td>
                            <td width="100">Rs.' . $itm_sub . '</td>
                            <td width="80" align="center">' . $itm . '</td>
                            <td width="90">Rs.' . $order->discount_total . '</td>
                            <td width="100">Rs.' . $order->total . '</td>
                        </tr>
                        <tr>
                            <td rowspan="2" colspan="4" class="bottom_barcode">
                                ' . $order->oid . '<br>' . $params2;
            // $html2 .= '<tcpdf method="write1DBarcode" params="' . $params2 . '" />';
            $html2.= '</td>
                            <td width="90">Serv.Charges</td>
                            <td width="100">Payable AMT.</td>
                        </tr>
                        <tr>
                            <td width="90">Rs.' . $order->total_tax . '</td>
                            <td width="100">Rs.' . $order->total . '</td>
                        </tr>
                    </tbody>
                </table>
                
                <table width="100%" cellpadding="5" class="child_table">
                    <tr>
                        <td width="660" align="center"><div class="currency_text">' . strtoupper($order->total) . '</div></td>
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
            $html2.= '<table width="100%" cellpadding="5" class="child_table">
                    <tr>
                        <td width="660" align="center"><p><span class="tax_invoice_text">TAX INVOICE - '.$industry_name.'</span><br>This is computer generated tax invoice.</p></td>
                    </tr>
                </table>
    </div>';
            // add a page
            $pdf::AddPage();
            // output the HTML content
            $pdf::writeHTML($html2, true, false, true, false, '');
            $i++;
        }
        $filename = "all_order_" . time() . ".pdf";
        // reset pointer to the last page
        $pdf::lastPage();
        $pdf::Output(public_path($filename), 'F');
        $file_url = url('/all_order_' . time() . '.pdf');
        return response()->json(['error' => false, 'msg' => "PDF Generated Successfully.", "pdf_url" => $file_url, "ErrorCode" => "000"], 200); //response()->download(public_path($filename));
        
    }
    //api use printOrderSlip according to order slip
    function printOrderSlip(Request $request) {
        
    //   var_dump($request->ods); die;
        $dis_status=$request->ods;
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
        $style = array('position' => 'C', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => '0', 'vpadding' => '0', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
        'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4);
        $vid = $request->vid;
        if($dis_status == 'dispacthed')
        {
            
            $orders = DB::table("orders")->join('billings', function($join) use ($vid)
            {
                $join->on('orders.oid', '=', 'billings.order_id')
                     ->where('billings.vid', '=', intval($vid));
            })->where('orders.vid', $request->vid)->where('orders.oid', $request->oid)->where('orders.status', 'dispatched')->get();
            
        }
        else{
        $orders = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', $request->vid)->where('orders.oid', $request->oid)->where('orders.status', 'packed')->get();
    }
    
        if($request->vid == 1){
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }elseif($request->vid == 2){
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }elseif($request->vid == 3){
            $address_store = '<p><span class="c_name">HK Texfab Pvt Ltd<br>GST NO : 03AAFCH1375M1ZJ</span><br>E-207 IV-A Focal Point, Focal Point, Focal Point,<br>141010 - Ludhiana, Punjab, India</p>';
            $industry_name = "HK Texfab Pvt Ltd";
        }elseif($request->vid == 4){
            $address_store = '<p><span class="c_name">INDRA HOSIERY MILLS<br>GST NO : 03AAAFI3516P1ZG</span><br>Plot 31,32,33 & 34,35,36 Behind Nagesh Building<br>Sharman Enclave near Jalandhar Bypass <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "INDRA HOSIERY MILLS";
        }elseif($request->vid == 8){
            $address_store = '<p><span class="c_name">ShopMetalm<br>GST NO : 03AAAFI3516P1ZG</span><br>Plot 31,32,33 & 34,35,36 Behind Nagesh Building<br>Sharman Enclave near Jalandhar Bypass <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "ShopMetalm";
        }elseif($request->vid == 9){
            $address_store = '<p><span class="c_name">Krelon Cosmetics (Opc) Pvt Ltd<br>GST NO : 03AAJCK4238L1ZE</span><br>374-A,Model Town Extension <br>Ludhiana, Punjab, India</p>';
            $industry_name = "Krelon Cosmetics (Opc) Pvt Ltd";
        }elseif($request->vid == 10){
            $address_store = '<p><span class="c_name">Yall<br>GST NO : 03ABXFA0812G1ZP</span><br>Shop #19-20, Lower Ground Floor (LGF), Society Plaza, Near Old Lakkar Bridge, Ludhiana 141008</p>';
            $industry_name = "Yall";
        }elseif($request->vid == 11){
            $address_store = '<p><span class="c_name">Oh Ex<br>GST NO : 03EIIPP4249C1ZE</span><br>Deep Vihar, E-10/7530/9, Village Famra Bahadur ke Road<br>Deep Vihar, The Knit Lounge, <br>141004 - Ludhiana, Punjab, India</p>';
            $industry_name = "Oh Ex";
        }elseif($request->vid == 12){
            $address_store = '<p><span class="c_name">Donna Flair<br>GST NO : </span><br>Deep Vihar, E-10/7530/9, Village Famra Bahadur ke Road, <br>Deep Vihar, The Knit Lounge, <br>141004 - Ludhiana, Punjab, India</p>';
            $industry_name = "Donna Flair";
        }elseif($request->vid == 13){
            $address_store = '<p><span class="c_name">MAD<br>GST NO : 03BGXPR0302L1Z8</span><br>B-32-E12/875 Thapar Nagar Opp GMT Public School Near Jalandhar Bypass, <br>Jalandhar Bypass, GMT Public School, <br>141008 - Ludhiana, Punjab, India</p>';
            $industry_name = "MAD";
        }elseif($request->vid == 14){
            $address_store = '<p><span class="c_name">Menage<br>GST NO : 03AKFPS6566A1ZA</span><br>H.No. 3732 Sector 32-A, <br>Chandigarh Road<br>141010 - Ludhiana, Punjab, India</p>';
            $industry_name = "Menage";
        }elseif($request->vid == 15){
            $address_store = '<p><span class="c_name">Siko Customs<br>GST NO : 03ABQFM7484H1ZS</span><br>B/501 Aditya Aryan, Chogle Nagar, Near Shani Mandir,  <br>Borivali East, Chaogle Nagar, Shani Mandir<br>400066 - Mumbai, Maharashtra, India</p>';
            $industry_name = "Siko Customs";
        }elseif($request->vid == 16){
            $address_store = '<p><span class="c_name">Algos Clothing<br>GST NO : 03ABQFM7484H1ZS</span><br>160, Dugri Phase 2, Dugri, Dugri, <br>141002 - Ludhiana, Punjab, India</p>';
            $industry_name = "Algos Clothing";
        }elseif($request->vid == 17){
            $address_store = '<p><span class="c_name">DripArt<br>GST NO : 03ABQFM7484H1ZS</span><br>160, Dugri Phase 2,<br> Dugri, Dugri,<br>141002 - Ludhiana, Punjab, India</p>';
            $industry_name = "DripArt";
        }elseif($request->vid == 18){
            $address_store = '<p><span class="c_name">Raysa fabrics pvt ltd<br>GST NO : 03AALCR9865Q1ZC</span><br>Shop no-127,129,131,<br/>Bhadaur House,AC Market <br>141008 - Ludhiana, Punjab, India</p>';
            $industry_name = "Raysa fabrics pvt ltd";
        }elseif($request->vid == 19){
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }else{
            $address_store = '<p><span class="c_name">Style By NansJ<br>GST NO : 03AEMFS1193J1ZT</span><br>41/12 Village Bajra<br>Rahon Road <br>141007 - Ludhiana, Punjab, India</p>';
            $industry_name = "Style by NansJ";
        }

        $oid = $request->oid;
        $results2 = DB::table("waybill")->where('vid', $request->vid)->where('order_id', $oid)->get()->toArray();
        if (count($results2) >= 1) {
            $wbill = $results2[0]->waybill_no;
            $img_base64_encoded = 'data:image/png;base64,' . $d->getBarcodePNG($wbill, 'C128', 3, 33, array(1, 1, 1));
            $params = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '">';
            $img_base64_encoded2 = 'data:image/png;base64,' . $d->getBarcodePNG($oid, 'C128', 3, 33, array(1, 1, 1));
            $params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
        } else {
            $wbill = "";
            $params = "";
            $img_base64_encoded2 = 'data:image/png;base64,' . $d->getBarcodePNG($oid, 'C128', 3, 33, array(1, 1, 1));
            $params2 = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded2) . '">';
        }
        $i = 1;
        // $params = $d->getBarcodeHTML($wbill, 'C128');
        // $params = '<img src="data:image/png;base64,' . $d->getBarcodePNG($wbill, 'C128', 3,33,array(1,1,1)) . '" alt="barcode"   />';
        // $params2 = $d->getBarcodeHTML($oid, 'C128');
        // $params2 = '<img src="data:image/png;base64,' . $d->getBarcodePNG($oid, 'C128', 3,33,array(1,1,1)) . '" alt="barcode"   />';
        // echo $params2; die;
        // var_dump($pdf::write1DBarcode($wbill, 'C128', '', '', '', 18, 1.0, $style, 'N')); die;
        foreach ($orders as $order) {
            //      $pdf::Cell(0, 0, 'CODABAR', 0, 1);
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
                            <td rowspan="3" width="360">'.$address_store.'</td>
                            <td width="150">Order No. (REF) : ' . $order->oid . '</td>
                            <td width="150">GST-JR-3557</td>
                        </tr>
                        <tr>
                            <td width="150">Mode : ' . $order->payment_method_title . ' </td>
                            <td width="150">Amount : Rs.' . $order->total . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">Courier Partner : DIRECT_DELHIVERY</td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" cellpadding="5" class="child_table customer_info">
                    <tbody>
                        <tr>
                            <td width="660"><p><span class="c_name">' . strtoupper($order->first_name) . ' ' . strtoupper($order->last_name) . ' <span>(M) ' . $order->phone . '</span></span><br>Address : ' . $order->address_1;
            if ($order->address_2 != '') {
                $html2.= '<br>Landmark : ' . $order->address_2;
            } else {
                $html2.= '<br>Landmark : N/A';
            }
            $html2.= '<br>Pincode : ' . $order->postcode . ', ' . $order->city . ', ' . $order->state . ', ' . $order->country . '</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" cellpadding="5" class="child_table customer_awb">
                    <tbody>
                        <tr>
                            <td width="660" align="center"><span class="awb_text">AWB : ' . $wbill . '</span><br/>' . $params;
            //<tcpdf method="write1DBarcode" params="' . $params . '" />
            $html2.= '</td>
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
            $order_items = DB::table("line_items")->where('order_id', '=', $oid)->where('vid', '=', $request->vid)->get();
            $j = 1;
            $itm = 0;
            $itm_sub = 0;
            $cnt = count($order_items);
            if ($cnt >= 1) {
                $min_height = 516 / $cnt;
            } else {
                $min_height = 516;
            }
            $height = $min_height . 'px';
            foreach ($order_items as $product) {
                $itm = $itm + $product->quantity;
                $itm_sub = $itm_sub + $product->subtotal;
                $html2.= '<tr>
                                <td width="40" height="' . $height . '">' . $j . '</td>
                                <td width="250">' . $product->name . '<br>SKU: ' . $product->sku . '</td>
                                <td width="100">Rs.' . $product->subtotal . '</td>
                                <td width="80" align="center">' . $product->quantity . '</td>
                                <td width="90">Rs.0</td>
                                <td width="100">Rs.' . $product->total . '</td>
                                </tr>';
                $j++;
            }
            $html2.= '</tbody></table><table width="100%" cellpadding="5" class="child_table product_info">
                    <tbody>
                        <tr>
                            <td width="40">T1</td>
                            <td width="250">T1</td>
                            <td width="100">Rs.' . $itm_sub . '</td>
                            <td width="80" align="center">' . $itm . '</td>
                            <td width="90">Rs.' . $order->discount_total . '</td>
                            <td width="100">Rs.' . $order->total . '</td>
                        </tr>
                        <tr>
                            <td rowspan="2" colspan="4" class="bottom_barcode">
                                ' . $oid . '<br/>' . $params2;
            // $html2 .= '<tcpdf method="write1DBarcode" params="' . $params2 . '" />';
            $html2.= '</td>
                            <td width="90">Serv.Charges</td>
                            <td width="100">Payable AMT.</td>
                        </tr>
                        <tr>
                            <td width="90">Rs.' . $order->total_tax . '</td>
                            <td width="100">Rs.' . $order->total . '</td>
                        </tr>
                    </tbody>
                </table>
                
                <table width="100%" cellpadding="5" class="child_table">
                    <tr>
                        <td width="660" align="center"><div class="currency_text">' . strtoupper($order->total) . '</div></td>
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
            $html2.= '<table width="100%" cellpadding="5" class="child_table">
                    <tr>
                        <td width="660" align="center"><p><span class="tax_invoice_text">TAX INVOICE - '.$industry_name.'</span><br>This is computer generated tax invoice.</p></td>
                    </tr>
                </table>
    </div>';
            // add a page
            $pdf::AddPage();
            // output the HTML content
            $pdf::writeHTML($html2, true, false, true, false, '');
            $i++;
        }
        $filename = "order" . $request->oid . "_" . time() . ".pdf";
        // reset pointer to the last page
        $pdf::lastPage();
        $pdf::Output(public_path($filename), 'F');
        $file_url = url('/order' . $request->oid . '_' . time() . '.pdf');
        return response()->json(['error' => false, 'msg' => "PDF Generated Successfully.", "pdf_url" => $file_url, "ErrorCode" => "000"], 200); //response()->download(public_path($filename));
        
    }
    //use delete data oders table
    public function delete($id) {
        $Orders = Orders::find($id);
        if (empty($orders)) {
            return;
        }
        $Orders->delete();
    }
    //api use city serach use table accroding to orders tavle and billings
    public function city_Search(Request $request) {
        // $order=orders::whereBetween('date_created_gmt',$range)->get();
        $vid = $request->vid;
        $order = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->where('orders.vid', $request->vid)->where('billings.vid', $request->vid)->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = " . intval($request->vid) . " GROUP BY line_items.order_id) as quantity"))
        // ->Where('billings.city', 'like', '%' . $request->city . '%')
        ->Where('billings.city', $request->city)->get();
        // ->select("orders.*","billings.*","line_items.*",
        //           DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
        //                       WHERE line_items.order_id = orders.oid
        //                       GROUP BY line_items.order_id) as quantity"))
        // ->get();
        return $order;
    }
    // api fetch data get proccesing data according to orders table billings 
    public function get_processing_data($vid, $status) {
        // echo $status; die;
        if ($status == 'processing' || $status == 'on-hold'){
            $orders = DB::table("orders")->join('billings', function($join) use ($vid)
            {
                $join->on('orders.oid', '=', 'billings.order_id')
                     ->where('billings.vid', '=', intval($vid));
            })->where('orders.vid', $vid)->where('billings.vid', $vid)->where('orders.status', $status)->select("orders.*", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " GROUP BY line_items.order_id) as quantity"), DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as name"), DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as sku"))->orderBy('oid', 'DESC')->get();
        }else{
            $orders = DB::table("orders")->join('billings', function($join) use ($vid)
            {
                $join->on('orders.oid', '=', 'billings.order_id')
                     ->where('billings.vid', '=', intval($vid));
            })->leftjoin('waybill', function($join) use ($vid){
                $join->on('orders.oid', '=', 'waybill.order_id')
                     ->where('waybill.vid', '=', intval($vid));
            })->where('orders.vid', $vid)->where('billings.vid', $vid)->where('orders.status', $status)->select("orders.*", "billings.*","waybill.waybill_no", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " GROUP BY line_items.order_id) as quantity"), DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as name"), DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = " . intval($vid) . " limit 1) as sku"))->orderBy('oid', 'DESC')->get();
        }
        
        
        return $orders;
    }
    // api state serch according to orders table billings 
    public function state_Search(Request $request) {
        // $order=orders::whereBetween('date_created_gmt',$range)->get();
        $order = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')->Where('state', 'like', '%' . $request->state . '%')->get();
        // ->select("orders.*","billings.*","line_items.*",
        //           DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
        //                       WHERE line_items.order_id = orders.oid
        //                       GROUP BY line_items.order_id) as quantity"))
        // ->get();
        return $order;
    }
     // api state data according to orders table billings 
    public function state_data(Request $request) {
        $order = DB::table('billings')->distinct()->select('billings.state')->where('billings.vid', $request->vid)->get();
        return $order;
    }
    // api city data fetch according  table billings 
    public function city_data(Request $request) {
        $order = DB::table('billings')->distinct()->select('billings.city')->where('billings.vid', $request->vid)->get();
        return $order;
    }    
    // api state data fetch according  table orders 
    public function status_data(Request $request) {
        $order = DB::table("orders")->distinct()->select('orders.status')->where('orders.vid', $request->vid)->get();
        return $order;
    }
       // api state search fetch according  table billings 
    public function status_Search(Request $request) {
      $vid = $request->vid;
        $order = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = " . intval($request->vid) . " GROUP BY line_items.order_id) as quantity"))->Where('orders.status', $request->status)->where('orders.vid', $request->vid)->where('billings.vid', $request->vid)->get();
        return $order;
    }
       // api zone search fetch according  table zonedetails 
    public function zone_Search(Request $request) {
        $data=DB::table('zonedetails')->distinct()->select('zonedetails.zoneno')->get();
        // $order = DB::table("orders")->distinct()->select('orders.status')->where('orders.vid', $request->vid)->get();
        return $data;
    }
    //api refund change status  
    function Refundchange_Status(Request $request) {
        $imp = explode(',', $request->allSelected);
        for ($i = 0;$i < count($imp);$i++) {
            DB::table('orders')->where('oid', $imp[$i])->where('vid', $request->vid)->update(['status' => $request->status]);
            $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
            $curl = curl_init();
            curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $imp[$i] . '?status=' . $request->status, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
            $response = curl_exec($curl);
            curl_close($curl);
            $jsonResp = json_decode($response);
        }
        return response()->json(['error' => false, 'msg' => "Order Status Successfully Updated.", "ErrorCode" => "000"], 200);
    }
    //api according to table orders download Sheet
    function download_Sheet(Request $request) {
        if ($request->selectall) {
            $listImp = explode(',', $request->selectall);
            $vid = intval($request->vid);
            $orderItems[] = DB::table("orders")
            // ->join('billings','orders.oid','=','billings.order_id')
            ->where('orders.vid', $vid)->whereIn('orders.oid', $listImp)->orderBy('oid', 'DESC')->get();
           
            
        } else {
            $orderItems[] = DB::table("orders")->where('vid', intval($request->vid))->get();
        }
        return $orderItems;
    }
    //api all use according to status
    function changeProcessing_Status(Request $request) {
        // var_dump($_REQUEST); die;
        // echo $request->loc; die;
       
        if ($request->loc == "wp") {
            $listImp['0'] = $request->oid;
        } elseif ($request->allSelected == "false") {
            $listImp['0'] = $request->oid;
            
            // var_dump($listImp); die;
            
        } else {
            $listImp = explode(',', $request->allSelected);
        }
        for ($i = 0;$i < count($listImp);$i++) 
        {
         
            DB::table('orders')->where('oid', intval($listImp[$i]))->where('vid', intval($request->vid))->update(['status' => $request->status_assign]);
            // print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
            // https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
            $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
            $curl = curl_init();
            curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $listImp[$i] . '?status=' . $request->status_assign, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
            $response = curl_exec($curl);
            curl_close($curl);
            $jsonResp = json_decode($response);
            // var_dump($jsonResp);
            if($request->status_assign=='confirmed')
            {
                $date = date('Y-m-d');
                $confirm_order_data[]=[     
                'vid'=>$request->vid,
                'oid'=>$listImp[$i],
                'order_confirmdate'=>$date,
                ];   
               
            }elseif($request->status_assign=='dto-refunded')
            {
                $date = date('Y-m-d');
                $confirm_order_data[]=[     
                'vid'=>$request->vid,
                'oid'=>$listImp[$i],
                'dto_refunddate'=>$date,
                ];   
                
            }elseif($request->status_assign=='closed')
            {
                $date = date('Y-m-d');
                $confirm_order_data[]=[     
                'vid'=>$request->vid,
                'oid'=>$listImp[$i],
                'order_closedate'=>$date,
                ];   
            }
            elseif($request->status_assign=='cancelled')
            {
                $date = date('Y-m-d');
                $confirm_order_data[]=[     
                'vid'=>$request->vid,
                'oid'=>$listImp[$i],
                'order_canceldate'=>$date,
                ];   

                $orders = DB::table("orders")->where('vid', '=', intval($request->vid))->where('oid', '=', intval($listImp[$i]))->get()->toArray();
                if($orders[0]['payment_method'] == "cod"){
                    JsonController::smsSend($request->vid,$listImp[$i],"cancel-cod");
                }else{
                    JsonController::smsSend($request->vid,$listImp[$i],"cancel-prepaid");
                }
            }
            elseif($request->status_assign=='dispatched')
            {
                $date = date('Y-m-d');
                $confirm_order_data[]=[     
                'vid'=>$request->vid,
                'oid'=>$listImp[$i],
                'order_canceldate'=>$date,
                ];   
            }  
        }
        // else
        // {
        //     return response()->json(['error' => false, 'msg' => "Please Enter HSN Code and Weight.", "ErrorCode" => "000"], 200);
        // }
            // hsn code and weight with processing confirmed(if )
        order_reldate::insert($confirm_order_data); 
        return response()->json(['error' => false, 'msg' => "Order Status Successfully Updated.", "ErrorCode" => "000"], 200);
            // }
            // else{
            //     return response()->json(['error' => false, 'msg' => "Please Enter HSN Code and Weight", "ErrorCode" => "000"], 200);
            //    }
            

       
    }
    //// hsn code and weight with processing confirmed(if start)
    public function changeProcessing_Status_confirmed(Request $request){
        // function changeProcessing_Status(Request $request) {
            //echo "asdsad";die();
            // var_dump($_REQUEST); die;
            // echo $request->loc; die;
           
            if ($request->loc == "wp") {
                $listImp['0'] = $request->oid;
            } elseif ($request->allSelected == "false") {
                $listImp['0'] = $request->oid;
                
                // var_dump($listImp); die;
                
            } else {
                $listImp = explode(',', $request->allSelected);
            }

            for ($i = 0;$i < count($listImp);$i++) 
            {
                // dd($listImp);die();
              // hsn code and weight with processing confirmed(start code)
                $data=DB::table('line_items')->join('products','products.product_id','=','line_items.product_id')
                ->where('line_items.order_id','=', $listImp[$i])
                ->where('line_items.vid', '=', intval($request->vid))
                ->where('products.vid', '=', intval($request->vid))->get();
                // dd(count($data));die();
                // $hsn = $data[0]->hsn_code;
                // $weight=$data[0]->weight;
                // $product_id = $data[0]->product_id;
                // dd($product_id);die();
                // dd($data);die();
                    // hsn code and weight with processing confirmed(if start)
                    //check product id data 
                    if(count($data)=='0'){
                        return response()->json(['error' => false, 'msg' => "Product Not Found", "ErrorCode" => "000"], 200);
                        // echo "0";die();
                        //     $hsn = $data[0]->hsn_code;
                        //     $weight=$data[0]->weight;
                        //     $product_id = $data[0]->product_id;
                        //     // dd(count($data)==1);
                            // die();
                    } 
                    
                    if(count($data)=='1'){
                        // echo "0";die();
                            $hsn = $data[0]->hsn_code;
                            $weight=$data[0]->weight;
                            $product_id = $data[0]->product_id;
                        //     // dd(count($data)==1);
                            // die();
                    }

                    if(count($data)<1){
                   
                        for ($i = 0;$i < count($data);$i++){
                         $hsn = $data[$i]->hsn_code;
                        $weight=$data[$i]->weight;
                        $product_id = $data[$i]->product_id;

                        }
                        //  dd(count($data)<1);
                        //     die();
                    }
                  
                    // dd(count($data)< 1);die();
                
                if(!empty($hsn && $weight)){
                
                    // dd($datas);die();
                    DB::table('orders')->where('oid', intval($listImp[$i]))->where('vid', intval($request->vid))->update(['status' => $request->status_assign]);
                    // print_r($woocommerce->put('orders/'.$imp[$i], $data)); die;
                    // https://isdemo.in/fc/wp-json/wc/v3/orders/5393?status=completed
                    $vendor = DB::table("vendors")->where('id', '=', intval($request->vid))->get();
                    $curl = curl_init();
                    curl_setopt_array($curl, array(CURLOPT_URL => $vendor[0]->url . '/wp-json/wc/v3/orders/' . $listImp[$i] . '?status=' . $request->status_assign, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $vendor[0]->token),));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $jsonResp = json_decode($response);
                    // var_dump($jsonResp);
                    if($request->status_assign=='confirmed')
                    {
                        $date = date('Y-m-d');
                        $confirm_order_data[]=[     
                        'vid'=>$request->vid,
                        'oid'=>$listImp[$i],
                        'order_confirmdate'=>$date,
                        ];   
                    
                    }elseif($request->status_assign=='dto-refunded')
                    {
                        $date = date('Y-m-d');
                        $confirm_order_data[]=[     
                        'vid'=>$request->vid,
                        'oid'=>$listImp[$i],
                        'dto_refunddate'=>$date,
                        ];   
                        
                    }elseif($request->status_assign=='closed')
                    {
                        $date = date('Y-m-d');
                        $confirm_order_data[]=[     
                        'vid'=>$request->vid,
                        'oid'=>$listImp[$i],
                        'order_closedate'=>$date,
                        ];   
                    }
                    elseif($request->status_assign=='cancelled')
                    {
                        $date = date('Y-m-d');
                        $confirm_order_data[]=[     
                        'vid'=>$request->vid,
                        'oid'=>$listImp[$i],
                        'order_canceldate'=>$date,
                        ];   
        
                        $orders = DB::table("orders")->where('vid', '=', intval($request->vid))->where('oid', '=', intval($listImp[$i]))->get()->toArray();
                        if($orders[0]['payment_method'] == "cod"){
                            JsonController::smsSend($request->vid,$listImp[$i],"cancel-cod");
                        }else{
                            JsonController::smsSend($request->vid,$listImp[$i],"cancel-prepaid");
                        }
                    }
                    elseif($request->status_assign=='dispatched')
                    {
                        $date = date('Y-m-d');
                        $confirm_order_data[]=[     
                        'vid'=>$request->vid,
                        'oid'=>$listImp[$i],
                        'order_canceldate'=>$date,
                        ];   
                    }  
                }
                else
                {
                    return response()->json(['error' => false, 'msg' => "Please Enter HSN Code and Weight.",'product_id' => $product_id, "ErrorCode" => "000"], 200);
                }
                    // hsn code and weight with processing confirmed(if )
                    order_reldate::insert($confirm_order_data); 
                    return response()->json(['error' => false, 'msg' => "Order Status Successfully Updated.", "ErrorCode" => "000"], 200);
                        // }
                    // else{
                    //     return response()->json(['error' => false, 'msg' => "Please Enter HSN Code and Weight", "ErrorCode" => "000"], 200);
                //    }
            }
    
           
        
        }
    
        public function getProcessingOrder_Details(Request $request) {
        $vendor = $request->vid;
        if ($vendor != null) {
            $orders = DB::table("orders")->join('billings', function($join) use ($vendor)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vendor));
        })->where('orders.vid', '=', intval($vendor))->where('billings.vid', '=', intval($vendor))->orderBy('oid', 'DESC')
            // ->select("orders.*","waybill.waybill_no","orders.status as orderstatus","billings.*",
            ->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND     line_items.vid = " . intval($vendor) . " GROUP BY line_items.order_id) as quantity"))->get();
        } else {
            $orders = DB::table("orders")->join('billings', 'orders.oid', '=', 'billings.order_id')->orderBy('oid', 'DESC')->select("orders.*", "orders.status as orderstatus", "billings.*", DB::raw("(SELECT SUM(line_items.quantity) FROM line_items
                                        WHERE line_items.order_id = orders.oid
                                        GROUP BY line_items.order_id) as quantity"))->get();
        }
        return $orders;
    }
    // api  proceesing download sheet according to table orders and billings
    function processing_download_Sheet(Request $request) {
        if ($request->allSelected) {
            $listImp = explode(',', $request->allSelected);
            $vid = intval($request->vid);
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', $vid)->where('orders.status', "processing")->where('orders.vid', $vid)->whereIn('orders.oid', $listImp)->orderBy('oid', 'DESC')->get();
        } else {
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', intval($request->vid))->where('orders.status', "processing")->where('billings.vid', intval($request->vid))->get();
        }
        return $orders;
    }
    //api on hold download sheet according to table orders and billings
    function onhold_download_Sheet(Request $request) {
        if ($request->allSelected) {
            $listImp = explode(',', $request->allSelected);
            $vid = intval($request->vid);
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', $vid)->where('orders.status', "on-hold")->where('orders.vid', $vid)->whereIn('orders.oid', $listImp)->orderBy('oid', 'DESC')->get();
        } else {
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', intval($request->vid))->where('orders.status', "on-hold")->where('billings.vid', intval($request->vid))->get();
        }
        return $orders;
    }
    //api confirm download sheet  according to table orders and line_items 
    function confirm_download_Sheet(Request $request) {
        if ($request->allSelected) {
            $listImp = explode(',', $request->allSelected);
            $vid = intval($request->vid);
            $orders[] = DB::table("orders")->join('line_items', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'line_items.order_id')
                 ->where('line_items.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "line_items.product_id as Item ID", "line_items.name as Item Name", "line_items.sku as SKU", "line_items.quantity as Qty")->where('orders.vid', $vid)->where('line_items.vid', $vid)->where('orders.status', "confirmed")->whereIn('orders.oid', $listImp)->orderBy('oid', 'DESC')->get();
        } else {
            $orders[] = DB::table("orders")->join('line_items', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'line_items.order_id')
                 ->where('line_items.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "line_items.product_id as Item ID", "line_items.name as Item Name", "line_items.sku as SKU", "line_items.quantity as Qty")->where('orders.vid', intval($request->vid))->where('orders.status', "confirmed")->where('line_items.vid', intval($request->vid))->get();
        }
        return $orders;
    }
      //api pending download sheet  according to table orders and billings 
    function pending_download_Sheet(Request $request) {
        if ($request->allSelected) {
            $listImp = explode(',', $request->allSelected);
            $vid = intval($request->vid);
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "orders.payment_method as Payment Method", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', $vid)->where('orders.status', $request->status)->where('orders.vid', $vid)->whereIn('orders.oid', $listImp)->orderBy('oid', 'DESC')->get();
        } else {
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', intval($request->vid))->where('orders.status', $request->status)->where('billings.vid', intval($request->vid))->get();
        }
        return $orders;
    }
     //api delivery download sheet  according to table orders and billings 
    function delivery_download_Sheet(Request $request) {
        if ($request->selectall) {
            $listImp = explode(',', $request->selectall);
            $vid = intval($request->vid);
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "orders.payment_method as Payment Method", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', $vid)->where('orders.status', "dispatched")->where('orders.vid', $vid)->whereIn('orders.oid', $listImp)->orderBy('oid', 'DESC')->get();
        } else {
            $orders[] = DB::table("orders")->join('billings', function($join) use ($vid)
        {
            $join->on('orders.oid', '=', 'billings.order_id')
                 ->where('billings.vid', '=', intval($vid));
        })->select("orders.oid as OrderID", "orders.date_created as OrderDate", "billings.first_name as First Name", "billings.last_name as Last Name", "billings.city as City", "billings.state as State", "billings.email as Email", "billings.email as Email")->where('orders.vid', intval($request->vid))->where('orders.status', "dispatched")->where('billings.vid', intval($request->vid))->get();
        }
        return $orders;
    }
    // api change status dispatch table waybill
    function changeStatusDispatch(Request $request) {
        // echo $request->dispatch;
        // echo $request->vid; die;
        $order_items1 = DB::table("waybill")->where('waybill.vid', intval($request->vid))->where('waybill.order_id', intval($request->dispatch))->limit(1)->get()->toArray();
        $order_items2 = DB::table("waybill")->where('waybill.vid', intval($request->vid))->where('waybill.waybill_no', intval($request->dispatch))->limit(1)->get()->toArray();
        $order_items = array_merge($order_items1,$order_items2);
        // echo $orders[0]->status; die;
        // foreach ($order_items as $order) {
        if(count($order_items) >= 1){
        // var_dump($order_items); die;
          $orders = DB::table("orders")->where('orders.oid', intval($order_items[0]->order_id))->get()->toArray();
          if($orders[0]->status == "packed"){
              $this->changeOrderStatus(intval($request->vid), intval($order_items[0]->order_id), "dispatched");
              $date = date('Y-m-d');
              $confirm_order_data[]=[     
              'vid'=>$request->vid,
              'oid'=>$request->dispatch,
              'order_dispatchdate'=>$date,
              ];   
              order_reldate::insert($confirm_order_data);        
              return response()->json(['error' => false, 'msg' => "Success, Your order number " . intval($order_items[0]->order_id) . " with AWB number is " . intval($order_items[0]->waybill_no), "ErrorCode" => "000"], 200);
          }else{
              return response()->json(['error' => true, 'msg' => "Order is not Packed.", "ErrorCode" => "000"], 200);
          }
        }else{
            return response()->json(['error' => true, 'msg' => "Order ID not found.", "ErrorCode" => "000"], 200);
        }
        // }
        // var_dump($order_items); die;
        
    } 
    //api use assign wallet according table orders update table wallet_processed
    public function assign_wallet(Request $request) {
         $main = explode(',', $request->allSelected);
        //  var_dump($main);
         $wallet=$request->walletvalue;
         for ($i = 0;$i < count($main);$i++) 
          {
           DB::table('orders')->where('orders.oid', intval($main[$i]))->where('vid', intval($request->vid))->update(['wallet_processed' => $wallet]);
           
         }
         return response()->json(['error' => true, 'msg' => " Wallet Data Processed", "ErrorCode" => "000"], 200);
        
       
    }

    // get my orders - http://majime.nmf.im/api/v1/my_orders?vid=1
    public function my_orders()
    {
        if(isset($_REQUEST['per_page']) != '' && isset($_REQUEST['offset']) != ''){
            $data = '?per_page='.$_REQUEST['per_page'].'&offset='.$_REQUEST['offset'];
        }elseif(isset($_REQUEST['per_page']) != ''){
            $data = '?per_page='.$_REQUEST['per_page'];
        }else{
            $data = '';
        }
        // echo $data; die;
        $vendor = DB::table("vendors")->where('id', '=', intval($_REQUEST['vid']))->get();
        $curl = curl_init();

        curl_setopt_array($curl, array(
          // CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders',
          CURLOPT_URL => $vendor[0]->url.'/wp-json/wc/v3/orders'.$data,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . $vendor[0]->token
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $jsonResp = json_decode($response);
        foreach ($jsonResp as $jp) {
            $curl_data[] = $jp->id;
        }

        // $orders = DB::table("orders")->where('vid', '=', intval($_REQUEST['vid']))->where('status', '=', "processing")->get();
        $orders = DB::table("orders")->where('vid', '=', intval($_REQUEST['vid']))->get();
        // var_dump($orders);
        if (count($orders) >= 1) {
            foreach ($orders as $order) {
                $order_data[] = $order->oid;
            }
        }else{
            $order_data = array();
        }

        // var_dump($curl_data);
        // var_dump($order_data); die;
        $result_arr=array_diff($curl_data,$order_data);
        $result = array_values($result_arr); 
        // var_dump($result); die;
        if (count($result) >= 1) {
            for ($i=0; $i < count($result); $i++) { 
                // echo "<a href='#' target='_blank'>Click here (ORDER ID - ".$result[$i].")</a><br><br>";
                // echo "<a href='https://cl.majime.in/api/v1/get_order_data?api_url=".$vendor[0]->url."/wp-json/wc/v3/orders/".intval($result[$i])."&vid=".intval($_REQUEST['vid'])."' target='_blank'>Click here (ORDER ID - ".$result[$i].")</a><br><br>";
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://cl.majime.in/api/v1/get_order_data?api_url=".$vendor[0]->url."/wp-json/wc/v3/orders/".intval($result[$i])."&vid=".intval($_REQUEST['vid']),
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic Og=='
                  ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;
            }
        }else{
            echo "No record found.";
        }
    }
    //api complete order according to vendr  use table orders
    public function Complete_orders($vendr)
    {        $pw=0;
        //if single value passed 
        $vendor=$vendr;
            $orders=DB::table("orders")->where('orders.vid','=',$vendor)->where('orders.status','completed')->get();
        //using for loop for more than one order 
        for($y=0;$y<count($orders);$y++) 
        {

            $order_table=DB::table("orders")->where('orders.oid','=',($orders[$y]->oid))->where('orders.vid','=',$vendor)->get();
            //assign order status 
            if($order_table[0]->status== 'completed' || $order_table[0]->status== 'closed')
            {
                $mystatus= 'Delivered';
            }
         
            $data=DB::table("billings")->where('billings.order_id','=',intval($orders[$y]->oid))->where('billings.vid','=',$order_table[0]->vid)->get();
            $origin_pin=141001;
            //get API through Zone 
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://track.delhivery.com/api/kinko/v1/invoice/charges/.json?md=S&cgm=250&o_pin=141001&d_pin=' .$data[0]->postcode. '&ss=' .$mystatus,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token ed99803a18868406584c6d724f71ebccc80a89f9'
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $jsonResp = json_decode($response);
            $zone=$jsonResp[0]->zone;
            $zone=substr($zone, 0, 1);
            $o_id=$orders[$y]->oid;
            //Get Rate List according to Zone no 
            $zone_rate=DB::table("zoneratecards")->where('zoneratecards.zoneno','like',$zone)->where('zoneratecards.vid','=',$order_table[0]->vid)->get();
            //Get Line Items through order id 
            $line_items=DB::table("line_items")->where('line_items.order_id','=',intval($orders[$y]->oid))->where('line_items.vid','=',$order_table[0]->vid)->get();
            //Get quantity from line items
            $line_items_qty=DB::table("line_items")->select(DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = $o_id AND line_items.vid = " . intval($order_table[0]->vid) . " GROUP BY line_items.order_id) as quantity"))->get(); 
            //Get Product Weight through product id 
            $product_weight=DB::table("products")->where('products.product_id','=',$line_items[0]->product_id)->get();  
            //Get rate card according to the Vendor 
            $vendor_rate=DB::table("vendor_ratecards")->where('vendor_ratecards.vid','=',$order_table[0]->vid)->get();
            //SMS Charges according to the vendor 
            $sms_cost=$vendor_rate[0]->sms_charges;
            //Majime Cost according to the vendor
            $mjm_cost=$vendor_rate[0]->majime_charges; 
            //if weight is above 500g charges accordingly 
            $above_value=$vendor_rate[0]->after500gm;
            $oc_balance=DB::table("opening_closing_tables")->where('opening_closing_tables.vid','=',$order_table[0]->vid)->orderBy('id','DESC')->limit(1)->get();
            // Value total of all line items clubbed together to make order total
            $orderTotalAmt = DB::table("line_items")->select(DB::raw("(SELECT SUM(line_items.total) FROM line_items WHERE line_items.order_id = ".intval($o_id)." AND line_items.vid = " . intval($order_table[0]->vid) . " GROUP BY line_items.order_id) as total_main"))->first();
            $amtPaid=$order_table[0]->total;
            $sale_Amount = $orderTotalAmt->total_main;
            $walletUsedAmt = $sale_Amount - $amtPaid;
            //calculate majime cost 
            $majime_cost=(($order_table[0]->total)*($mjm_cost/100));
            if($order_table[0]->payment_method == 'cod')
            {
                $payment_gateway=0;
            }else{
                $payment_gateway=(($order_table[0]->total)*2.36/100);
            }
             
            if(count($oc_balance) >= 1){
                $opening_balance=$oc_balance[0]->closing_bal;
            }else{
                $opening_balance=0;
            }
            if($mystatus== 'Delivered')
            {
                $zone_price=$zone_rate[0]->fwd;
            }
            $zone_price = $zone_price*1.18;
            // if order status is dto-refunded or completed
            if($order_table[0]->status != 'rto-delivered')
            {   
                    //caluclate logistic cost if payment method id COD
                if($order_table[0]->payment_method == 'cod')
                {   
                    $Vcodper = $vendor_rate[0]->codper;
                    $getval = $sale_Amount*($Vcodper/100);
                    $cod_charges = $vendor_rate[0]->cod;
                    if($getval > $vendor_rate[0]->cod){
                        $cod_charges = $getval;
                    }
                    $pw=$product_weight[0]->weight;
                    if($pw==0)
                    {   
                        $pw=490;
                    }
                    $qty=$line_items_qty[0]->quantity;
                    $total_weight=($pw)*($qty);
                    if($total_weight>500)
                    {
                        $cod_cost=$zone_price;
                        $percent_price=(int)($total_weight/500);
                        $pp_amount=($cod_cost*($above_value/100))*$percent_price;
                        $logistic_cost=$cod_cost+$pp_amount+$cod_charges;
                    }
                    else{
                        $cod_cost=$zone_price;
                        $logistic_cost=$cod_cost+$cod_charges;
                    }                        
                        $net_amount=$sale_Amount-($walletUsedAmt+$logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                        $closing_bal=$opening_balance+$net_amount;
                }
                //caluclate logistic cost if payment method is prepaid
                else{
                    $cod_charges = 0;
                    if($pw==0)
                    {   
                        $pw=490;
                    }
                    $qty=$line_items_qty[0]->quantity;
                    $total_weight=($pw)*($qty);
                    if($total_weight>500)
                    {
                        $cod_cost=$zone_price;
                        $percent_price=(int)($total_weight/500);
                        $pp_amount=($cod_cost*85/100)*$percent_price;
                        $logistic_cost=$cod_cost+$pp_amount+$cod_charges;
                    }
                    else{
                        $cod_cost=$zone_price;
                        $logistic_cost=$cod_cost+$cod_charges;
                    }
                    $net_amount=$sale_Amount-($walletUsedAmt+$logistic_cost+$majime_cost+$sms_cost+$payment_gateway);
                    $closing_bal=$opening_balance+$net_amount;
                }
            }
         
            $wallet=1;
            //insert values into Wallet Processed table into database
            $Wallet_order_data[]=[     
                'date_created'=>$order_table[0]->date_created,
                'transaction_id'=>"N/A",
                'oid'=>$order_table[0]->oid,
                'vid'=>$order_table[0]->vid,
                'payment_mode'=>$order_table[0]->payment_method,
                'status'=>$order_table[0]->status,
                'sale_amount'=>$sale_Amount,
                'Wallet_used'=>$walletUsedAmt,
                'logistic_cost'=>$logistic_cost,
                'payment_gateway_charges'=>$payment_gateway,
                'sms_cost'=>$sms_cost,
                'majime_charges'=>$majime_cost,
                'net_amount'=>$net_amount,
                'current_wallet_bal'=>$closing_bal,
                'order_count'=> $line_items[0]->quantity,
                'zone_amt'=> $zone_price
            ];   
            // insert values into opening closing table into database  
            DB::table('opening_closing_tables')->insert(
                ['vid' => $vendor, 'opening_bal' => $opening_balance, 'closing_bal' => $closing_bal, 'created_at' => date('Y-m-d h:m:s'), 'updated_at' => date('Y-m-d h:m:s')]
            );
            // update order table with wallet processed 
            DB::table('orders')->where('orders.oid',$o_id)->where('vid', intval($vendor))->update(['wallet_processed' => $wallet]);
            // unset($orders);
        }   
        walletprocessed::insert($Wallet_order_data); 

        return response()->json(['error' => false, 'msg' => "Wallet Processed Successfully", "ErrorCode" => "000"], 200);
     }

     public function wallet_Sheet_download(Request $request)
     {
        $date = date('Y-m-d H:i:s');
        $from = $request->date_from." 00:00:00";
        $range = [$from, $date];
        if($from!=0)
        {
         $order= DB::table("walletprocesseds")->where('walletprocesseds.vid', intval($request->vid))->whereBetween('walletprocesseds.created_at',$range)
        ->select("walletprocesseds.oid as OrderID","walletprocesseds.transaction_id as TXNID","walletprocesseds.created_at as TXN Date", "walletprocesseds.payment_mode as Payment Mode", "walletprocesseds.status  as Status", "walletprocesseds.sale_amount as Sale Amount", "walletprocesseds.Wallet_used as Wallet Used", "walletprocesseds.logistic_cost as Logistic Cost", "walletprocesseds.payment_gateway_charges as Pymt Gateway Chrges","walletprocesseds.sms_cost as SMS Cost","walletprocesseds.majime_charges as Majime Charges","walletprocesseds.zone_amt as Zone Amount","walletprocesseds.net_amount  as Net Amount","walletprocesseds.current_wallet_bal  as Wallet Balance","walletprocesseds.current_wallet_bal  as Wallet Balance")
        ->orderBy('id','DESC')     
        ->get();
    }
        else
        {
        $order= DB::table("walletprocesseds")->where('walletprocesseds.vid', intval($request->vid))
        ->select("walletprocesseds.oid as OrderID","walletprocesseds.transaction_id as TXNID","walletprocesseds.created_at as TXN Date", "walletprocesseds.payment_mode as Payment Mode", "walletprocesseds.status  as Status", "walletprocesseds.sale_amount as Sale Amount", "walletprocesseds.Wallet_used as Wallet Used", "walletprocesseds.logistic_cost as Logistic Cost", "walletprocesseds.payment_gateway_charges as Pymt Gateway Chrges","walletprocesseds.sms_cost as SMS Cost","walletprocesseds.majime_charges as Majime Charges","walletprocesseds.zone_amt as Zone Amount","walletprocesseds.net_amount  as Net Amount","walletprocesseds.current_wallet_bal  as Wallet Balance","walletprocesseds.current_wallet_bal  as Wallet Balance")
        ->get();
     
        }
        return $order;

     }
  

}
