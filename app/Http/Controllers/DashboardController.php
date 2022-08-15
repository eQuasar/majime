<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Orders;
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
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->where('date_created', '>=', $date)->get();
    $processing_order_count=count($processing_orders);
    $processing_saleAmount=$processing_orders->sum('total');
    $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->where('date_created', '>=', $date)->get();
    $confirm_order_count=count($confirm_orders);
    $confirm_saleAmount=$confirm_orders->sum('total');
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('date_created', '>=', $date)->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->where('date_created', '>=', $date)->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');

    $dashboardData['processingcount'] = $processing_order_count;
    $dashboardData['processingsale'] = $processing_saleAmount;
    $dashboardData['confirmcount'] = $confirm_order_count;
    $dashboardData['confirmSaleAmount'] = $confirm_saleAmount;
    $dashboardData['holdcount'] = $hold_order_count;
    $dashboardData['onholdSaleAmount'] = $hold_saleAmount;
    $dashboardData['packedcount'] = $packed_order_count;
    $dashboardData['packedSaleAmount'] = $packed_saleAmount;
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
    $rtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('date_created', '>=', $date)->get();
    $rtodelivered_order_count=count($rtodelivered_orders);
    $dtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-delivered')->where('date_created', '>=', $date)->get();
    $dtodelivered_order_count=count($dtodelivered_orders);
    $dtorefunded_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-refunded')->where('date_created', '>=', $date)->get();
    $dtorefunded_order_count=count($dtorefunded_orders);
    $picked_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','pickedup')->where('date_created', '>=', $date)->get();
    $picked_order_count=count($picked_orders);
    $dtoIntras_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtointransit')->where('date_created', '>=', $date)->get();
    $dtoIntra_order_count=count($dtoIntras_orders);
    $dtowarehouse_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtodel2warehouse')->where('date_created', '>=', $date)->get();
    $dtowarehouse_order_count=count($dtowarehouse_orders);

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
    $chartData['values'][10] = $rtodelivered_order_count;
    $chartData['values'][11] = $dtodelivered_order_count;
    $chartData['values'][12] = $dtorefunded_order_count;
    $chartData['values'][13] = $picked_order_count;
    $chartData['values'][14] = $dtoIntra_order_count;
    $chartData['values'][15] = $dtowarehouse_order_count;

    return  $chartData;
   }
     public function piechart_data($vid)
   {
        $dtobook='dtobooked';
        $intrans='intransit';
        $Comple='completed';
        $rto_del='rto-delivered';
        $dto_ref='dto-refunded';
        $dto_del='dtodelivered';
        $dtointrans='dtointransit';
        $clos='closed';
        $process='processing';
        $confirm='confirmed';
        $pack='packed';
        $hold='on-hold';
        $dis='dispatched';
        $del='deliveredtocust';
        $cancel='cancelled';
        $fail='failed';
      $date = \Carbon\Carbon::today()->subDays(7);
      $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$del])->where('date_created', '>=', $date)->get();
      $total_Processed=count($orders);
      $rto=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('date_created', '>=', $date)->get();
      $total_rto=count($rto);
      $dto=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dto_ref,$dto_del, $dtointrans])->where('date_created', '>=', $date)->get();
      $total_dto=count($dto);
      $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$intrans,$dis])->where('date_created', '>=', $date)->get();
      $dispatched_order_count=count($dispatched_orders);
      $piedata['pie'][0]= $total_Processed;
      $piedata['pie'][1]= $total_rto;
      $piedata['pie'][2]= $total_dto;
      $piedata['pie'][3]= $dispatched_order_count;
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
     public function getsales_data($vid)
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
    $pick='pickedup';
    $warehouse='dtodel2warehouse';
    $date = \Carbon\Carbon::today()->subDays(7);
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->where('date_created', '>=', $date)->get();
    $process_order_count=count($processing_orders);
    $processed_saleAmount=$processing_orders->sum('total');
    $confirmed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->where('date_created', '>=', $date)->get();
    $confirm_order_count=count($confirmed_orders);
    $confirm_saleAmount=$confirmed_orders->sum('total');
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->where('date_created', '>=', $date)->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');
    $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dispatched')->where('date_created', '>=', $date)->get();
    $dispatched_order_count=count($dispatched_orders);
    $dispatch_saleAmount=$dispatched_orders->sum('total');
    $intransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->where('date_created', '>=', $date)->get();
    $intransit_order_count=count($intransit_orders);
    $intransit_saleAmount=$intransit_orders->sum('total');
    $deliveredtocust_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','deliveredtocust')->where('date_created', '>=', $date)->get();
    $deliveredtocust_order_count=count($deliveredtocust_orders);
    $deliver_saleAmount=$deliveredtocust_orders->sum('total');
    $completed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','completed')->where('date_created', '>=', $date)->get();
    $completed_order_count=count($completed_orders);
    $complete_saleAmount=$completed_orders->sum('total');
    $closed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','closed')->where('date_created', '>=', $date)->get();
    $closed_order_count=count($closed_orders);
    $closed_saleAmount=$closed_orders->sum('total');
    $dtobooked_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtobooked')->where('date_created', '>=', $date)->get();
    $dtobooked_order_count=count($dtobooked_orders);
    $dtobooked_saleAmount=$dtobooked_orders->sum('total');
    $onhold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('date_created', '>=', $date)->get();
    $onhold_order_count=count($onhold_orders);
    $onhold_saleAmount=$onhold_orders->sum('total');
    $rtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('date_created', '>=', $date)->get();
    $rtodelivered_order_count=count($rtodelivered_orders);
    $rtodelivered_saleAmount=$rtodelivered_orders->sum('total');
    $dtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-delivered')->where('date_created', '>=', $date)->get();
    $dtodelivered_order_count=count($dtodelivered_orders);
    $dtodelivered_saleAmount=$dtodelivered_orders->sum('total');
    $dtorefunded_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-refunded')->where('date_created', '>=', $date)->get();
    $dtorefunded_order_count=count($dtorefunded_orders);
    $dtorefunded_saleAmount=$dtorefunded_orders->sum('total');
    $picked_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','pickedup')->where('date_created', '>=', $date)->get();
    $picked_order_count=count($picked_orders);
    $picked_saleAmount=$picked_orders->sum('total');
    $dtoIntras_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtointransit')->where('date_created', '>=', $date)->get();
    $dtoIntra_order_count=count($dtoIntras_orders);
    $dtoIntrans_saleAmount=$dtoIntras_orders->sum('total');
    $dtowarehouse_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtodel2warehouse')->where('date_created', '>=', $date)->get();
    $dtowarehouse_order_count=count($dtowarehouse_orders);
    $dtowarehouse_saleAmount=$dtowarehouse_orders->sum('total');

    
    $sales[0]['count'] = $process_order_count;
    $sales[1]['count'] = $confirm_order_count;
    $sales[2]['count'] = $packed_order_count;
    $sales[3]['count'] = $dispatched_order_count;
    $sales[4]['count'] = $intransit_order_count;
    $sales[5]['count'] = $deliveredtocust_order_count;
    $sales[6]['count'] = $completed_order_count;
    $sales[7]['count'] = $closed_order_count;
    $sales[8]['count'] = $dtobooked_order_count;
    $sales[9]['count'] = $onhold_order_count;
    $sales[10]['count'] = $rtodelivered_order_count;
    $sales[11]['count'] = $dtodelivered_order_count;
    $sales[12]['count'] = $dtorefunded_order_count;
    $sales[13]['count'] = $picked_order_count;
    $sales[14]['count'] = $dtoIntra_order_count;
    $sales[15]['count'] = $dtowarehouse_order_count;

    $sales[0]['sale'] = $processed_saleAmount;
    $sales[1]['sale'] = $confirm_saleAmount;
    $sales[2]['sale'] = $packed_saleAmount;
    $sales[3]['sale'] = $dispatch_saleAmount;
    $sales[4]['sale'] = $intransit_saleAmount;
    $sales[5]['sale'] = $deliver_saleAmount;
    $sales[6]['sale'] = $complete_saleAmount;
    $sales[7]['sale'] = $closed_saleAmount;
    $sales[8]['sale'] = $dtobooked_saleAmount;
    $sales[9]['sale'] = $onhold_saleAmount;
    $sales[10]['sale'] = $rtodelivered_saleAmount;
    $sales[11]['sale'] = $dtodelivered_saleAmount;
    $sales[12]['sale'] = $dtorefunded_saleAmount;
    $sales[13]['sale'] = $picked_saleAmount;
    $sales[14]['sale'] = $dtoIntrans_saleAmount;
    $sales[15]['sale'] = $dtowarehouse_saleAmount;

    $sales[0]['status'] = $process;
    $sales[1]['status'] = $confirm;
    $sales[2]['status'] = $pack;
    $sales[3]['status'] = $dis;
    $sales[4]['status'] = $intrans;
    $sales[5]['status'] = $del;
    $sales[6]['status'] = $Comple;
    $sales[7]['status'] =  $clos;
    $sales[8]['status'] =  $dtobook;
    $sales[9]['status'] =  $hold;
    $sales[10]['status'] =  $rto_del;
    $sales[11]['status'] = $dto_del;
    $sales[12]['status'] =  $dto_ref;
    $sales[13]['status'] = $pick;
    $sales[14]['status'] =  $dtointrans;
    $sales[15]['status'] = $warehouse;


    // return $salesdata ;die();
    // $salesdata['sales_data'][2][2][2]=$sales;
    // $salesdata['sales_data'][3][2][3]=$sales;
    // $salesdata['sales_data'][4][4][4]=$sales;
    // $salesdata['sales_data'][5][5][5]=$sales;
    // $salesdata['sales_data'][6][6][6]=$sales;
    // $salesdata['sales_data'][7][7][7]=$sales;
    // $salesdata['sales_data'][8][8][8]=$sales;
    // $salesdata['sales_data'][9][9][9]=$sales;
    // $salesdata['sales_data'][10][10][10]=$sales;
    // $salesdata['sales_data'][11][11][11]=$sales;
    // $salesdata['sales_data'][12][12][12]=$sales;
    // $salesdata['sales_data'][13][13][13]=$sales;

     return $sales ;
    
    


    //  return $sales;

   }

}
