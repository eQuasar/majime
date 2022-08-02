<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
   public function dashboard_detail($vid)
   {
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->get();
    $total_order_count=count($orders);
    $total_saleAmount=$orders->sum('total');
    $cancelled_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','cancelled')->get();
    $cancel_order_count=count($cancelled_orders);
    $cancel_saleAmount=$cancelled_orders->sum('total');
    $fail_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','failed')->get();
    $fail_order_count=count($fail_orders);
    $fail_saleAmount=$fail_orders->sum('total');
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->get();
    $processing_order_count=count($processing_orders);
    $processing_saleAmount=$processing_orders->sum('total');
    $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->get();
    $confirm_order_count=count($confirm_orders);
    $confirm_saleAmount=$confirm_orders->sum('total');
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');
    $dispatch_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dispatched')->get();
    $dispatch_order_count=count($dispatch_orders);
    $dispatch_saleAmount=$dispatch_orders->sum('total');
    $transit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->get();
    $transit_order_count=count($transit_orders);
    $transit_saleAmount=$transit_orders->sum('total');
    $deliver_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','deliveredtocust')->get();
    $deliver_order_count=count($deliver_orders);
    $deliver_saleAmount=$deliver_orders->sum('total');
    $rto_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->get();
    $rto_order_count=count($rto_orders);
    $rto_saleAmount=$rto_orders->sum('total');
    $dtobook_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtobooked')->get();
    $dtobook_order_count=count($dtobook_orders);
    $dtobook_saleAmount=$dtobook_orders->sum('total');
    $dtointransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtointransit')->get();
    $dtointransit_order_count=count($dtointransit_orders);
    $dtointransit_saleAmount=$dtointransit_orders->sum('total');
    $dtodel_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtodelivered')->get();
    $dtodel_order_count=count($dtodel_orders);
    $dtodel_saleAmount=$dtodel_orders->sum('total');
    $dtoref_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-refunded')->get();
    $dtoref_order_count=count($dtoref_orders);
    $dtoref_saleAmount=$dtoref_orders->sum('total');

    $gross_orders=$total_order_count-$cancel_order_count-$fail_order_count;
    $gross_Sale=$total_saleAmount- $cancel_saleAmount- $hold_saleAmount;
    $net_count= ceil(($gross_orders)*33/100);
    $net_sale=($gross_Sale)*33/100;

    $dashboardData['totalcount'] = $total_order_count;
    $dashboardData['totalSaleAmount'] = $total_saleAmount;
    $dashboardData['canceltotalcount'] = $cancel_order_count;
    $dashboardData['canceltotalSaleAmount'] = $cancel_saleAmount;
    $dashboardData['failtotalcount'] = $fail_order_count;
    $dashboardData['failtotalSaleAmount'] = $fail_saleAmount;
    $dashboardData['holdtotalcount'] = $hold_order_count;
    $dashboardData['holdtotalSaleAmount'] = $hold_saleAmount;
    $dashboardData['processingtotalcount'] = $processing_order_count;
    $dashboardData['processingtotalSaleAmount'] = $processing_saleAmount;
    $dashboardData['confirmtotalcount'] = $confirm_order_count;
    $dashboardData['confirmtotalSaleAmount'] = $confirm_saleAmount;
    $dashboardData['packedtotalcount'] = $packed_order_count;
    $dashboardData['packedtotalSaleAmount'] = $packed_saleAmount;
    $dashboardData['dispatchtotalcount'] = $dispatch_order_count;
    $dashboardData['dispatchtotalSaleAmount'] = $dispatch_saleAmount;
    $dashboardData['transittotalcount'] = $transit_order_count;
    $dashboardData['transittotalSaleAmount'] = $transit_saleAmount;
    $dashboardData['deltotalcount'] = $deliver_order_count;
    $dashboardData['deltotalSaleAmount'] = $deliver_saleAmount;
    $dashboardData['rtototalcount'] = $rto_order_count;
    $dashboardData['rtototalSaleAmount'] = $rto_saleAmount;
    $dashboardData['dtobktotalcount'] = $rto_order_count;
    $dashboardData['dtobktotalSaleAmount'] = $rto_saleAmount;
    $dashboardData['dtointtotalcount'] = $dtointransit_order_count;
    $dashboardData['dtointtotalSaleAmount'] = $dtointransit_saleAmount;
    $dashboardData['dtodeltotalcount'] = $dtodel_order_count;
    $dashboardData['dtodeltotalSaleAmount'] = $dtodel_saleAmount;
    $dashboardData['dtoreftotalcount'] = $dtoref_order_count;
    $dashboardData['dtoreftotalSaleAmount'] = $dtoref_saleAmount;
    $dashboardData['grosscount'] = $gross_orders;
    $dashboardData['grossSaleAmount'] = $gross_Sale;
    $dashboardData['netcount'] = $net_count;
    $dashboardData['netsale'] = $net_sale;

    return  $dashboardData;

   }

   public function dashboard_search(Request $request)
   {
    $range = [$request->date_from, $request->date_to];
    $vid=$request->vid;
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.date_created_gmt',$range)->get();
    $total_order_count=count($orders);
    $total_saleAmount=$orders->sum('total');
    $cancelled_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','cancelled')->whereBetween('orders.date_created_gmt',$range)->get();
    $cancel_order_count=count($cancelled_orders);
    $cancel_saleAmount=$cancelled_orders->sum('total');
    $fail_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','failed')->where('orders.date_created_gmt',$range)->get();
    $fail_order_count=count($fail_orders);
    $fail_saleAmount=$fail_orders->sum('total');
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('orders.date_created_gmt',$range)->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->where('orders.date_created_gmt',$range)->get();
    $processing_order_count=count($processing_orders);
    $processing_saleAmount=$processing_orders->sum('total');
    $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->where('orders.date_created_gmt',$range)->get();
    $confirm_order_count=count($confirm_orders);
    $confirm_saleAmount=$confirm_orders->sum('total');
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->where('orders.date_created_gmt',$range)->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');
    $dispatch_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dispatched')->where('orders.date_created_gmt',$range)->get();
    $dispatch_order_count=count($dispatch_orders);
    $dispatch_saleAmount=$dispatch_orders->sum('total');
    $transit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->where('orders.date_created_gmt',$range)->get();
    $transit_order_count=count($transit_orders);
    $transit_saleAmount=$transit_orders->sum('total');
    $deliver_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','deliveredtocust')->where('orders.date_created_gmt',$range)->get();
    $deliver_order_count=count($deliver_orders);
    $deliver_saleAmount=$deliver_orders->sum('total');
    $rto_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('orders.date_created_gmt',$range)->get();
    $rto_order_count=count($rto_orders);
    $rto_saleAmount=$rto_orders->sum('total');
    $dtobook_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtobooked')->where('orders.date_created_gmt',$range)->get();
    $dtobook_order_count=count($dtobook_orders);
    $dtobook_saleAmount=$dtobook_orders->sum('total');
    $dtointransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtointransit')->where('orders.date_created_gmt',$range)->get();
    $dtointransit_order_count=count($dtointransit_orders);
    $dtointransit_saleAmount=$dtointransit_orders->sum('total');
    $dtodel_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtodelivered')->where('orders.date_created_gmt',$range)->get();
    $dtodel_order_count=count($dtodel_orders);
    $dtodel_saleAmount=$dtodel_orders->sum('total');
    $dtoref_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-refunded')->where('orders.date_created_gmt',$range)    ->get();
    $dtoref_order_count=count($dtoref_orders);
    $dtoref_saleAmount=$dtoref_orders->sum('total');

    $gross_orders=$total_order_count-$cancel_order_count-$fail_order_count;
    $gross_Sale=$total_saleAmount- $cancel_saleAmount- $hold_saleAmount;
    $net_count= ceil(($gross_orders)*33/100);
    $net_sale=($gross_Sale)*33/100;

    $dashboardData['totalcount'] = $total_order_count;
    $dashboardData['totalSaleAmount'] = $total_saleAmount;
    $dashboardData['canceltotalcount'] = $cancel_order_count;
    $dashboardData['canceltotalSaleAmount'] = $cancel_saleAmount;
    $dashboardData['failtotalcount'] = $fail_order_count;
    $dashboardData['failtotalSaleAmount'] = $fail_saleAmount;
    $dashboardData['holdtotalcount'] = $hold_order_count;
    $dashboardData['holdtotalSaleAmount'] = $hold_saleAmount;
    $dashboardData['processingtotalcount'] = $processing_order_count;
    $dashboardData['processingtotalSaleAmount'] = $processing_saleAmount;
    $dashboardData['confirmtotalcount'] = $confirm_order_count;
    $dashboardData['confirmtotalSaleAmount'] = $confirm_saleAmount;
    $dashboardData['packedtotalcount'] = $packed_order_count;
    $dashboardData['packedtotalSaleAmount'] = $packed_saleAmount;
    $dashboardData['dispatchtotalcount'] = $dispatch_order_count;
    $dashboardData['dispatchtotalSaleAmount'] = $dispatch_saleAmount;
    $dashboardData['transittotalcount'] = $transit_order_count;
    $dashboardData['transittotalSaleAmount'] = $transit_saleAmount;
    $dashboardData['deltotalcount'] = $deliver_order_count;
    $dashboardData['deltotalSaleAmount'] = $deliver_saleAmount;
    $dashboardData['rtototalcount'] = $rto_order_count;
    $dashboardData['rtototalSaleAmount'] = $rto_saleAmount;
    $dashboardData['dtobktotalcount'] = $rto_order_count;
    $dashboardData['dtobktotalSaleAmount'] = $rto_saleAmount;
    $dashboardData['dtointtotalcount'] = $dtointransit_order_count;
    $dashboardData['dtointtotalSaleAmount'] = $dtointransit_saleAmount;
    $dashboardData['dtodeltotalcount'] = $dtodel_order_count;
    $dashboardData['dtodeltotalSaleAmount'] = $dtodel_saleAmount;
    $dashboardData['dtoreftotalcount'] = $dtoref_order_count;
    $dashboardData['dtoreftotalSaleAmount'] = $dtoref_saleAmount;
    $dashboardData['grosscount'] = $gross_orders;
    $dashboardData['grossSaleAmount'] = $gross_Sale;
    $dashboardData['netcount'] = $net_count;
    $dashboardData['netsale'] = $net_sale;

    return  $dashboardData;
   }
}
