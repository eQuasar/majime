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
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereBetween('orders.date_created_gmt',$range)->get();
    $total_order_count=count($orders);
    $total_saleAmount=$orders->sum('total');
    $cancelled_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','cancelled')->whereBetween('orders.date_created_gmt',$range)->get();
    $cancel_order_count=count($cancelled_orders);
    $cancel_saleAmount=$cancelled_orders->sum('total');
    $fail_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','failed')->whereBetween('orders.date_created_gmt',$range)->get();
    $fail_order_count=count($fail_orders);
    $fail_saleAmount=$fail_orders->sum('total');
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->whereBetween('orders.date_created_gmt',$range)->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->whereBetween('orders.date_created_gmt',$range)->get();
    $processing_order_count=count($processing_orders);
    $processing_saleAmount=$processing_orders->sum('total');
    $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->whereBetween('orders.date_created_gmt',$range)->get();
    $confirm_order_count=count($confirm_orders);
    $confirm_saleAmount=$confirm_orders->sum('total');
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->whereBetween('orders.date_created_gmt',$range)->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');
    $dispatch_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dispatched')->whereBetween('orders.date_created_gmt',$range)->get();
    $dispatch_order_count=count($dispatch_orders);
    $dispatch_saleAmount=$dispatch_orders->sum('total');
    $transit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->whereBetween('orders.date_created_gmt',$range)->get();
    $transit_order_count=count($transit_orders);
    $transit_saleAmount=$transit_orders->sum('total');
    $deliver_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','deliveredtocust')->whereBetween('orders.date_created_gmt',$range)->get();
    $deliver_order_count=count($deliver_orders);
    $deliver_saleAmount=$deliver_orders->sum('total');
    $rto_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->whereBetween('orders.date_created_gmt',$range)->get();
    $rto_order_count=count($rto_orders);
    $rto_saleAmount=$rto_orders->sum('total');
    $dtobook_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtobooked')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtobook_order_count=count($dtobook_orders);
    $dtobook_saleAmount=$dtobook_orders->sum('total');
    $dtointransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtointransit')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtointransit_order_count=count($dtointransit_orders);
    $dtointransit_saleAmount=$dtointransit_orders->sum('total');
    $dtodel_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtodelivered')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtodel_order_count=count($dtodel_orders);
    $dtodel_saleAmount=$dtodel_orders->sum('total');
    $dtoref_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-refunded')->whereBetween('orders.date_created_gmt',$range)    ->get();
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

   public function chart_data($vid)
   {
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->get();
    $total_order_count=count($orders);
    $cancelled_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','cancelled')->get();
    $cancel_order_count=count($cancelled_orders);
    $fail_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','failed')->get();
    $fail_order_count=count($fail_orders);
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->get();
    $hold_order_count=count($hold_orders);
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->get();
    $processing_order_count=count($processing_orders);
    $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->get();
    $confirm_order_count=count($confirm_orders);
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->get();
    $packed_order_count=count($packed_orders);
    $dispatch_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dispatched')->get();
    $dispatch_order_count=count($dispatch_orders);
    $transit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->get();
    $transit_order_count=count($transit_orders);
    $deliver_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','deliveredtocust')->get();
    $deliver_order_count=count($deliver_orders);
    $rto_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->whereDate('date_created', Carbon::now()->subDays(27))->get();
    $rto_order_count=count($rto_orders);
    $dtobook_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtobooked')->whereDate('date_created', Carbon::now()->subDays(27))->get();
    $dtobook_order_count=count($dtobook_orders);
    $dtointransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtointransit')->whereDate('date_created', Carbon::now()->subDays(27))->get();
    $dtointransit_order_count=count($dtointransit_orders);
    $dtodel_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtodelivered')->whereDate('date_created', Carbon::now()->subDays(27))->get();
    $dtodel_order_count=count($dtodel_orders);
    $dtoref_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-refunded')->whereDate('date_created', Carbon::now()->subDays(27))->get();
    $dtoref_order_count=count($dtoref_orders);

    $chartData['values'][0] = $total_order_count;
    $chartData['values'][1] = $cancel_order_count;
    $chartData['values'][2] = $fail_order_count;
    $chartData['values'][3] = $hold_order_count;
    $chartData['values'][4] = $processing_order_count;
    $chartData['values'][5] = $confirm_order_count;
    $chartData['values'][6] = $packed_order_count;
    $chartData['values'][7] = $dispatch_order_count;
    $chartData['values'][8] = $transit_order_count;
    $chartData['values'][9]= $deliver_order_count;
    $chartData['values'][10] = $rto_order_count;
   //  $chartData['values'][11] = $rto_order_count;
   //  $chartData['values'][12] = $dtointransit_order_count;
   //  $chartData['values'][13] = $dtodel_order_count;
   //  $chartData['values'][14] = $dtoref_order_count;
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
