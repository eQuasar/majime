<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{
   public function dashboard_detail($vid)
   {
      $clos='closed';
      $del='deliveredtocust';
      $date = \Carbon\Carbon::today()->subDays(7);
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->where('date_created', '>=', $date)->get();
    $total_order_count=count($orders);
    $total_saleAmount=$orders->sum('total');
    $wallet=DB::table("walletprocesseds")->where('walletprocesseds.vid','=',$vid)->where('created_at', '>=', $date)->get();
    $wallet_order_count=count($wallet);
    $wallet_saleAmount=$wallet->sum('sale_amount');
     $deliver_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$del])->where('date_created', '>=', $date)->get();
    $deliver_order_count=count($deliver_orders);
    $deliver_saleAmount=$deliver_orders->sum('total');
    $cancelled_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','cancelled')->where('date_created', '>=', $date)->get();
    $cancel_order_count=count($cancelled_orders);
    $cancel_saleAmount=$cancelled_orders->sum('total');
    $fail_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','failed')->where('date_created', '>=', $date)->get();
    $fail_order_count=count($fail_orders);
    $fail_saleAmount=$fail_orders->sum('total');
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('date_created', '>=', $date)->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');
    $gross_orders=$total_order_count-$cancel_order_count-$fail_order_count;
    $gross_Sale=$total_saleAmount- $cancel_saleAmount- $hold_saleAmount;
    $net_count= ceil(($gross_orders)*33/100);
    $net_sale=($gross_Sale)*33/100;
    $dashboardData['totalcount'] = $total_order_count;
    $dashboardData['totalSaleAmount'] = $total_saleAmount;
    $dashboardData['walletcount'] = $wallet_order_count;
    $dashboardData['walletsale'] = $wallet_saleAmount;
    $dashboardData['delivercustcount'] = $deliver_order_count;
    $dashboardData['deliversale'] = $deliver_saleAmount;
    $dashboardData['intransitcount'] = $deliver_order_count;
    $dashboardData['intransitSaleAmount'] = $deliver_saleAmount;
    $dashboardData['grosscount'] = $gross_orders;
    $dashboardData['grossSaleAmount'] = $gross_Sale;
    $dashboardData['netcount'] = $net_count;
    $dashboardData['netsale'] = $net_sale;
    return  $dashboardData;

   }

   public function dashboard_search(Request $request)
   {
    $vid=$request->vid;
    $range = [$request->date_from, $request->date_to];
   $clos='closed';
      $del='deliveredtocust';
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereBetween('orders.date_created',$range)->get();
    $total_order_count=count($orders);
    $total_saleAmount=$orders->sum('total');
    $wallet=DB::table("walletprocesseds")->where('walletprocesseds.vid','=',$vid)->whereBetween('walletprocesseds.created_at',$range)->get();
    $wallet_order_count=count($wallet);
    $wallet_saleAmount=$wallet->sum('sale_amount');
     $deliver_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$del])->whereBetween('orders.date_created_gmt',$range)->get();
    $deliver_order_count=count($deliver_orders);
    $deliver_saleAmount=$deliver_orders->sum('total');
    $cancelled_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','cancelled')->whereBetween('orders.date_created_gmt',$range)->get();
    $cancel_order_count=count($cancelled_orders);
    $cancel_saleAmount=$cancelled_orders->sum('total');
    $fail_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','failed')->whereBetween('orders.date_created_gmt',$range)->get();
    $fail_order_count=count($fail_orders);
    $fail_saleAmount=$fail_orders->sum('total');
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->whereBetween('orders.date_created_gmt',$range)->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');
    $gross_orders=$total_order_count-$cancel_order_count-$fail_order_count;
    $gross_Sale=$total_saleAmount- $cancel_saleAmount- $hold_saleAmount;
    $net_count= ceil(($gross_orders)*33/100);
    $net_sale=($gross_Sale)*33/100;
    $dashboardData['totalcount'] = $total_order_count;
    $dashboardData['totalSaleAmount'] = $total_saleAmount;
    $dashboardData['walletcount'] = $wallet_order_count;
    $dashboardData['walletsale'] = $wallet_saleAmount;
    $dashboardData['delivercustcount'] = $deliver_order_count;
    $dashboardData['deliversale'] = $deliver_saleAmount;
    $dashboardData['intransitcount'] = $deliver_order_count;
    $dashboardData['intransitSaleAmount'] = $deliver_saleAmount;
    $dashboardData['grosscount'] = $gross_orders;
    $dashboardData['grossSaleAmount'] = $gross_Sale;
    $dashboardData['netcount'] = $net_count;
    $dashboardData['netsale'] = $net_sale;
    return  $dashboardData;
   }

   public function chart_data($vid)
   {
    $date = \Carbon\Carbon::today()->subDays(7);
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->where('date_created', '>=', $date)->get();
    $process_order_count=count($processing_orders);
    $confirmed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->where('date_created', '>=', $date)->get();
    $confirm_order_count=count($confirmed_orders);
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->where('date_created', '>=', $date)->get();
    $packed_order_count=count($packed_orders);
    $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dispatched')->where('date_created', '>=', $date)->get();
    $dispatched_order_count=count($dispatched_orders);
    $intransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->where('date_created', '>=', $date)->get();
    $intransit_order_count=count($intransit_orders);
    $deliveredtocust_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','deliveredtocust')->where('date_created', '>=', $date)->get();
    $deliveredtocust_order_count=count($deliveredtocust_orders);
    $completed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','completed')->where('date_created', '>=', $date)->get();
    $completed_order_count=count($completed_orders);
    $closed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','closed')->where('date_created', '>=', $date)->get();
    $closed_order_count=count($closed_orders);
    $dtobooked_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtobooked')->where('date_created', '>=', $date)->get();
    $dtobooked_order_count=count($dtobooked_orders);
    $onhold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('date_created', '>=', $date)->get();
    $onhold_order_count=count($onhold_orders);
    $cancelled_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','cancelled')->where('date_created', '>=', $date)->get();
    $cancelled_order_count=count($cancelled_orders);
    $rtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('date_created', '>=', $date)->get();
    $rtodelivered_order_count=count($rtodelivered_orders);
    $dtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-delivered')->where('date_created', '>=', $date)->get();
    $dtodelivered_order_count=count($dtodelivered_orders);

    $chartData['values'][0] = $process_order_count;
    $chartData['values'][1] = $confirm_order_count;
    $chartData['values'][2] = $packed_order_count;
    $chartData['values'][3] = $dispatched_order_count;
    $chartData['values'][4] = $intransit_order_count;
    $chartData['values'][5] = $deliveredtocust_order_count;
    $chartData['values'][6] = $completed_order_count;
    $chartData['values'][7] = $closed_order_count;
    $chartData['values'][8] = $dtobooked_order_count;
    $chartData['values'][9] = $onhold_order_count;
    $chartData['values'][10] = $cancelled_order_count;
    $chartData['values'][11] = $rtodelivered_order_count;
    $chartData['values'][12] = $dtodelivered_order_count;

    return  $chartData;
   }
     public function piechart_data($vid)
   {
        $dtobook='dtobooked';
        $intrans='intransit';
        $dtointrans='dtointransit';
        $Comple='completed';
        $rto_del='rto-delivered';
        $dto_ref='dto-refunded';
        $clos='closed';
        $process='processing';
        $confirm='confirmed';
        $pack='packed';
        $hold='on-hold';
        $dis='dispatched';
        $del='deliveredtocust';
        $dto_del='dtodelivered';
        $cancel='cancelled';
        $fail='failed';
      $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$rto_del,$dto_ref])->get();
      $total_Processed=count($orders);
      $cancelled=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$cancel,$fail])->get();
      $total_cancelled=count($cancelled);
      $intransit=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$intrans])->get();
      $total_transit=count($intransit);
      $others=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dtointrans,$Comple,$process,$confirm,$pack,$hold,$dis,$del,$dto_del])->whereDate('date_created', Carbon::now()->subDays(7))->get();
      
      $total_others=count($others);
      $piedata['pie'][0]= $total_Processed;
      $piedata['pie'][1]= $total_cancelled;
      $piedata['pie'][2]= $total_transit;
      $piedata['pie'][3]= $total_others;
      return $piedata;

   }
    public function secondpiechart_data($vid)
   {
        $dtobook='dtobooked';
        $intrans='intransit';
        $dtointrans='dtointransit';
        $Comple='completed';
        $rto_del='rto-delivered';
        $dto_ref='dto-refunded';
        $clos='closed';
        $process='processing';
        $confirm='confirmed';
        $pack='packed';
        $hold='on-hold';
        $dis='dispatched';
        $del='deliveredtocust';
        $dto_del='dtodelivered';
        $cancel='cancelled';
        $fail='failed';
      $date = \Carbon\Carbon::today()->subDays(7);
      $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$rto_del,$dto_ref])->get();
      $processed_saleAmount=$orders->sum('total');
      $del=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$del])->get();
      $del_saleAmount=$del->sum('total');
      $intransit=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$intrans])->get();
      $intransit_saleAmount=$intransit->sum('total');
      $saledata['amount'][0]= $processed_saleAmount;
      $saledata['amount'][1]= $del_saleAmount;
      $saledata['amount'][2]= $intransit_saleAmount;
      return $saledata;

   }
}
