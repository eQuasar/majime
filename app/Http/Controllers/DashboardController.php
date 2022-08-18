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
      $process='processing';
      $con='confirmed';
      $pack='packed';
      $hold='on-hold';


      $date = \Carbon\Carbon::today()->subDays(7);
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->where('date_created', '>=', $date)->get();
    $processing_order_count=count($processing_orders);
    $processing_saleAmount=$processing_orders->sum('total');

    $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->where('date_created', '>=', $date)->get();
    $confirm_order_count=count($confirm_orders);
    $confirm_saleAmount=$confirm_orders->sum('total');

    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->where('date_created', '>=', $date)->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');

    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('date_created', '>=', $date)->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');

    $pendencychart['deldata']=$processing_order_count;
    $pendencychart['deldata']=$confirm_order_count;
    $pendencychart['deldata']=$packed_order_count;
    $pendencychart['deldata']=$hold_order_count;

    $pendencytable[0]['status']=$process;
    $pendencytable[1]['status']=$con;
    $pendencytable[2]['status']=$pack;
    $pendencytable[3]['status']=$hold;
    $pendencytable[0]['count']=$processing_order_count;
    $pendencytable[1]['count']=$confirm_order_count;
    $pendencytable[2]['count']=$packed_order_count;
    $pendencytable[3]['count']=$hold_order_count;
    $pendencytable[0]['amount']=$processing_saleAmount;
    $pendencytable[1]['amount']=$confirm_saleAmount;
    $pendencytable[2]['amount']=$packed_saleAmount;
    $pendencytable[3]['amount']=$hold_saleAmount;

    return[
      $pendencychart,
      $pendencytable,

    ];

   }

   public function dashboard_search(Request $request)
   {
    $vid=$request->vid;
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
    $deld='Delivered';
    $fail='failed';
    $pick='pickedup';
    $rt='RTO';
    $dt='DTO';
    $onhold='on-hold';

    $range = [$request->date_from, $request->date_to];
    $date = \Carbon\Carbon::today()->subDays(7);
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->whereBetween('orders.date_created_gmt',$range)->get();
    $process_order_count=count($processing_orders);
    $processed_saleAmount=$processing_orders->sum('total');
    $confirmed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->whereBetween('orders.date_created_gmt',$range)->get();
    $confirm_order_count=count($confirmed_orders);
    $confirm_saleAmount=$confirmed_orders->sum('total');
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->whereBetween('orders.date_created_gmt',$range)->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');
    $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dispatched')->whereBetween('orders.date_created_gmt',$range)->get();
    $dispatched_order_count=count($dispatched_orders);
    $dispatch_saleAmount=$dispatched_orders->sum('total');
    $intransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->whereBetween('orders.date_created_gmt',$range)->get();
    $intransit_order_count=count($intransit_orders);
    $intransit_saleAmount=$intransit_orders->sum('total');
    $deliveredtocust_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','deliveredtocust')->whereBetween('orders.date_created_gmt',$range)->get();
    $deliveredtocust_order_count=count($deliveredtocust_orders);
    $deliver_saleAmount=$deliveredtocust_orders->sum('total');
    $completed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','completed')->whereBetween('orders.date_created_gmt',$range)->get();
    $completed_order_count=count($completed_orders);
    $complete_saleAmount=$completed_orders->sum('total');
    $closed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','closed')->whereBetween('orders.date_created_gmt',$range)->get();
    $closed_order_count=count($closed_orders);
    $closed_saleAmount=$closed_orders->sum('total');
    $dtobooked_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtobooked')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtobooked_order_count=count($dtobooked_orders);
    $dtobooked_saleAmount=$dtobooked_orders->sum('total');
    $onhold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->whereBetween('orders.date_created_gmt',$range)->get();
    $onhold_order_count=count($onhold_orders);
    $onhold_saleAmount=$onhold_orders->sum('total');
    $rtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->whereBetween('orders.date_created_gmt',$range)->get();
    $rtodelivered_order_count=count($rtodelivered_orders);
    $rtodelivered_saleAmount=$rtodelivered_orders->sum('total');
    $dtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-delivered')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtodelivered_order_count=count($dtodelivered_orders);
    $dtodelivered_saleAmount=$dtodelivered_orders->sum('total');
    $dtorefunded_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dto-refunded')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtorefunded_order_count=count($dtorefunded_orders);
    $dtorefunded_saleAmount=$dtorefunded_orders->sum('total');
    $picked_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','pickedup')->whereBetween('orders.date_created_gmt',$range)->get();
    $picked_order_count=count($picked_orders);
    $picked_saleAmount=$picked_orders->sum('total');
    $dtoIntras_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtointransit')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtoIntra_order_count=count($dtoIntras_orders);
    $dtoIntrans_saleAmount=$dtoIntras_orders->sum('total');
    $dtowarehouse_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','dtodel2warehouse')->whereBetween('orders.date_created_gmt',$range)->get();
    $dtowarehouse_order_count=count($dtowarehouse_orders);
    $dtowarehouse_saleAmount=$dtowarehouse_orders->sum('total');
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$del])->whereBetween('orders.date_created_gmt',$range)->get();
    $total_Processed=count($orders);
    $total_amount=$orders->sum('total');
    $rto=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->whereBetween('orders.date_created_gmt',$range)->get();
    $total_rtoo=count($rto);
    $rto_amount=$rto->sum('total');
    $dto=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dto_ref,$dto_del, $dtointrans,$dtobook])->whereBetween('orders.date_created_gmt',$range)->get();
    $total_dtoo=count($dto);
    $dto_amount=$dto->sum('total');
    $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$intrans,$dis])->whereBetween('orders.date_created_gmt',$range)->get();
    $dispatched_order_count=count($dispatched_orders);
    $dispatched_amount=$dispatched_orders->sum('total');
    $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$del])->whereBetween('orders.date_created_gmt',$range)->get();
    $total_Processedd=$orders->sum('total');
    $total_count_process=count($orders);
    $rto=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->whereBetween('orders.date_created_gmt',$range)->get();
    $total_rto=$rto->sum('total');
    $dto=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dto_ref,$dto_del, $dtointrans,$dtobook])->whereBetween('orders.date_created_gmt',$range)->get();
    $total_dto=$dto->sum('total');
    $confirmed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->whereBetween('orders.date_created_gmt',$range)->get();
    $confirmed_amount=$confirmed_orders->sum('total');
    $intransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->whereBetween('orders.date_created_gmt',$range)->get();
    $intransit_amount=$intransit_orders->sum('total');
    $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$intrans,$dis])->whereBetween('orders.date_created_gmt',$range)->get();
    $dispatched_amount=$dispatched_orders->sum('total');
    $packed=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->whereBetween('orders.date_created_gmt',$range)->get();
    $packed_amount=$packed->sum('total');
    $processing=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->whereBetween('orders.date_created_gmt',$range)->get();
    $processing_amount=$processing->sum('total');
    $complete=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','completed')->whereBetween('orders.date_created_gmt',$range)->get();
    $complete_amount=$complete->sum('total');
    $hold=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->whereBetween('orders.date_created_gmt',$range)->get();
    $hold_amount=$hold->sum('total');
    $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->whereBetween('orders.date_created_gmt',$range)->get();
    $processing_order_count=count($processing_orders);
    $processing_saleAmount=$processing_orders->sum('total');
    $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->whereBetween('orders.date_created_gmt',$range)->get();
    $confirm_order_count=count($confirm_orders);
    $confirm_saleAmount=$confirm_orders->sum('total');
    $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->whereBetween('orders.date_created_gmt',$range)->get();
    $packed_order_count=count($packed_orders);
    $packed_saleAmount=$packed_orders->sum('total');
    $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->whereBetween('orders.date_created_gmt',$range)->get();
    $hold_order_count=count($hold_orders);
    $hold_saleAmount=$hold_orders->sum('total');
    $totalp=$processing_order_count+$confirm_order_count+$packed_order_count+$hold_order_count;
    $total=$total_count_process+$total_rtoo+$total_dtoo+ $dispatched_order_count;
    $process_percentage=round((($total_count_process/$total)*100),2);
    $rto_percentage=round((($total_rtoo/$total)*100),2);
    $dto_percentage=round((($total_dtoo/$total)*100),2);
    $dispatch_percentage=round((($dispatched_order_count/$total)*100),2);


  $i = 0;
  $chartData['catgories'][$i] = 'No Result Found';
  $chartData['values'][$i] = '0';

  $date = \Carbon\Carbon::today()->subDays(7);
  $chartOrders=DB::table("orders")
                            ->select(
                              DB::raw("(COUNT(id)) as count"),
                              DB::raw("(DATE_FORMAT(date_created_gmt, '%Y-%m-%d')) as date")
                              )
                            ->where('orders.vid','=',$vid)
                            ->whereNotIn('orders.status', ['cancelled','failed'])
                            ->whereBetween('orders.date_created_gmt',$range)
                            ->orderBy('date_created_gmt','ASC')
                            ->groupBy(DB::raw("DATE_FORMAT(date_created_gmt, '%Y-%m-%d')"))
                            ->get();
  $i = 0;
  foreach($chartOrders as $chartOrder){
    // print_r($porders);
    $chartData['catgories'][$i] = $chartOrder->date;
    $chartData['values'][$i] = $chartOrder->count;
    $i = $i +1;
  }

  $sale='Sales';
  $retr='Returns';
  $can='Cancellations';
  $date = \Carbon\Carbon::today()->subDays(7);
  $sales_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereBetween('orders.date_created_gmt',$range)->whereIn("orders.status",[$dtobook,$intrans,$dtointrans,$Comple,$dto_ref,$clos,$process,$confirm,$pack,$onhold,$dis,$del,$dto_del,$pick,$warehouse])->get();
  $sales_order_count=count($sales_orders);
  $salesorder_saleAmount=$sales_orders->sum('total');
  $salesrtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->whereBetween('orders.date_created_gmt',$range)->get();
  $salesrtodelivered_order_count=count($salesrtodelivered_orders);
  $salesrtodelivered_saleAmount=$salesrtodelivered_orders->sum('total');
  $salescancel_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$cancel,$fail])->whereBetween('orders.date_created_gmt',$range)->get();
  $salescancel_order_count=count($salescancel_orders);
  $salesrtodelivered_saleAmount=$salescancel_orders->sum('total');
  $sales[0]['count'] = $sales_order_count;
  $sales[1]['count'] = $salesrtodelivered_order_count;
  $sales[2]['count'] = $salescancel_order_count;
  $sales[0]['sale'] = $salesorder_saleAmount;
  $sales[1]['sale'] = $salesrtodelivered_saleAmount;
  $sales[2]['sale'] = $salesrtodelivered_saleAmount;
  $sales[0]['status'] = $sale;
  $sales[1]['status'] = $retr;
  $sales[2]['status'] = $can;

    $pendencychart['deldata'][0]=$processing_saleAmount;
    $pendencychart['deldata'][1]=$confirm_saleAmount;
    $pendencychart['deldata'][2]=$packed_saleAmount;
    $pendencychart['deldata'][3]=$hold_saleAmount;

    $pendencytable[0]['status']=$process;
    $pendencytable[1]['status']=$confirm;
    $pendencytable[2]['status']=$pack;
    $pendencytable[3]['status']=$onhold;
    $pendencytable[0]['count']=$processing_order_count;
    $pendencytable[1]['count']=$confirm_order_count;
    $pendencytable[2]['count']=$packed_order_count;
    $pendencytable[3]['count']=$hold_order_count;
    $pendencytable[0]['amount']=$processing_saleAmount;
    $pendencytable[1]['amount']=$confirm_saleAmount;
    $pendencytable[2]['amount']=$packed_saleAmount;
    $pendencytable[3]['amount']=$hold_saleAmount;

    
    $piedata['pie'][0]= $total_Processedd;
    $piedata['pie'][1]= $total_rto;
    $piedata['pie'][2]= $total_dto;
    $piedata['pie'][3]= $dispatched_amount;
    $logisticsdata[0]['count']= $total_Processed;
    $logisticsdata[1]['count']= $total_rto;
    $logisticsdata[2]['count']= $total_dto;
    $logisticsdata[3]['count']= $dispatched_order_count;
    $logisticsdata[0]['status']= $deld;
    $logisticsdata[1]['status']= $rt;
    $logisticsdata[2]['status']= $dt;
    $logisticsdata[3]['status']= $intrans;
    $logisticsdata[0]['amount']= $total_amount;
    $logisticsdata[1]['amount']= $rto_amount;
    $logisticsdata[2]['amount']=$dto_amount;
    $logisticsdata[3]['amount']=$dispatched_amount;
    $logisticsdata[0]['percentage']= $process_percentage;
    $logisticsdata[1]['percentage']= $rto_percentage;
    $logisticsdata[2]['percentage']=$dto_percentage;
    $logisticsdata[3]['percentage']=$dispatch_percentage;
    $pieData['piedata'][0]= $total_Processed;
    $pieData['piedata'][1]= $total_rto;
    $pieData['piedata'][2]= $total_dto;
    $pieData['piedata'][3]= $dispatched_amount;
    $pieData['piedata'][4]= $packed_amount;
    $pieData['piedata'][5]= $processing_amount;
    $pieData['piedata'][6]= $confirmed_amount;
    $pieData['piedata'][7]= $intransit_amount;
    $pieData['piedata'][8]= $complete_amount;
    $pieData['piedata'][9]= $hold_amount;
  
    return
    [
      $piedata,
      $logisticsdata,
      $pendencychart,
      $pendencytable,
      $pieData,
      $sales ,
      $chartData,

    ] ;

   }

   public function chart_data($vid)
   {

    $date = \Carbon\Carbon::today()->subDays(7);
    $processing_orders=DB::table("orders")
                              ->select(
                                DB::raw('SUM(orders.total) As total'),
                                DB::raw("(DATE_FORMAT(date_created_gmt, '%Y-%m-%d')) as date")
                                )
                              ->where('orders.vid','=',$vid)
                              ->whereNotIn('orders.status', ['cancelled','failed'])
                              ->where('date_created', '>=', $date)
                              ->orderBy('date_created_gmt','ASC')
                              ->groupBy(DB::raw("DATE_FORMAT(date_created_gmt, '%Y-%m-%d')"))
                              ->get();
    $i = 0;
    foreach($processing_orders as $porders){
      // print_r($porders);
      $chartData['catgories'][$i] = $porders->date;
      $chartData['values'][$i] = $porders->total;
      $i = $i +1;
    }
    
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
        $deld='Delivered';
        $rt='RTO';
        $dt='DTO';
      $date = \Carbon\Carbon::today()->subDays(7);
      $orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$clos,$del])->where('date_created', '>=', $date)->get();
      $total_Processedd=count($orders);
      $total_amount=$orders->sum('total');
      $rto=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('date_created', '>=', $date)->get();
      $total_rto=count($rto);
      $rto_amount=$rto->sum('total');
      $dto=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dto_ref,$dto_del, $dtointrans,$dtobook])->where('date_created', '>=', $date)->get();
      $total_dto=count($dto);
      $dto_amount=$dto->sum('total');
      $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$intrans,$dis])->where('date_created', '>=', $date)->get();
      $dispatched_order_count=count($dispatched_orders);
      $dispatched_amount=$dispatched_orders->sum('total');
      $total=$total_Processedd+$total_rto+$total_dto+ $dispatched_order_count;
      $process_percentage=round((($total_Processedd/$total)*100),2);
      $rto_percentage=round((($total_rto/$total)*100),2);
      $dto_percentage=round((($total_dto/$total)*100),2);
      $dispatch_percentage=round((($dispatched_order_count/$total)*100),2);

      $piedata['pie'][0]= $total_Processedd;
      $piedata['pie'][1]= $total_rto;
      $piedata['pie'][2]= $total_dto;
      $piedata['pie'][3]= $dispatched_order_count;
      $logisticsdata[0]['count']= $total_Processedd;
      $logisticsdata[1]['count']= $total_rto;
      $logisticsdata[2]['count']= $total_dto;
      $logisticsdata[3]['count']= $dispatched_order_count;
      $logisticsdata[0]['status']= $deld;
      $logisticsdata[1]['status']= $rt;
      $logisticsdata[2]['status']= $dt;
      $logisticsdata[3]['status']= $intrans;
      $logisticsdata[0]['amount']= $total_amount;
      $logisticsdata[1]['amount']= $rto_amount;
      $logisticsdata[2]['amount']=$dto_amount;
      $logisticsdata[3]['amount']=$dispatched_amount;
      $logisticsdata[0]['percentage']= $process_percentage;
      $logisticsdata[1]['percentage']= $rto_percentage;
      $logisticsdata[2]['percentage']=$dto_percentage;
      $logisticsdata[3]['percentage']=$dispatch_percentage;

      return
      [
        $piedata,
        $logisticsdata,

      ] ;

   }
    public function secondpiechart_data($vid)
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
            $total_Processed=$orders->sum('total');
            $rto=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('date_created', '>=', $date)->get();
            $total_rto=$rto->sum('total');
            $dto=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dto_ref,$dto_del, $dtointrans,$dtobook])->where('date_created', '>=', $date)->get();
            $total_dto=$dto->sum('total');
            $confirmed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->where('date_created', '>=', $date)->get();
            $confirmed_amount=$confirmed_orders->sum('total');
            $intransit_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','intransit')->where('date_created', '>=', $date)->get();
            $intransit_amount=$intransit_orders->sum('total');
            $dispatched_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$intrans,$dis])->where('date_created', '>=', $date)->get();
            $dispatched_amount=$dispatched_orders->sum('total');
            $packed=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->where('date_created', '>=', $date)->get();
            $packed_amount=$packed->sum('total');
            $processing=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->where('date_created', '>=', $date)->get();
            $processing_amount=$processing->sum('total');
            $complete=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','completed')->where('date_created', '>=', $date)->get();
            $complete_amount=$complete->sum('total');
            $hold=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('date_created', '>=', $date)->get();
            $hold_amount=$hold->sum('total');

            $pieData['piedata'][0]= $total_Processed;
            $pieData['piedata'][1]= $total_rto;
            $pieData['piedata'][2]= $total_dto;
            $pieData['piedata'][3]= $dispatched_amount;
            $pieData['piedata'][4]= $packed_amount;
            $pieData['piedata'][5]= $processing_amount;
            $pieData['piedata'][6]= $confirmed_amount;
            $pieData['piedata'][7]= $intransit_amount;
            $pieData['piedata'][8]= $complete_amount;
            $pieData['piedata'][9]= $hold_amount;
            return $pieData;

   }
     public function getsales_data($vid)
   {
    $dtobook='dtobooked';
    $intrans='intransit';
    $dtointrans='dtointransit';
    $Comple='completed';
    $dto_ref='dto-refunded';
    $clos='closed';
    $process='processing';
    $confirm='confirmed';
    $pack='packed';
    $hold='on-hold';
    $dis='dispatched';
    $del='deliveredtocust';
    $dto_del='dtodelivered';
    $pick='pickedup';
    $warehouse='dtodel2warehouse';
    $rto_del='rto-delivered';
    $cancel='cancelled';
    $fail='failed';
    $sale='Sales';
    $retr='Returns';
    $can='Cancellations';
    $date = \Carbon\Carbon::today()->subDays(7);
    $sales_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$dtobook,$intrans,$dtointrans,$Comple,$dto_ref,$clos,$process,$confirm,$pack,$hold,$dis,$del,$dto_del,$pick,$warehouse])->get();
    $sales_order_count=count($sales_orders);
    $salesorder_saleAmount=$sales_orders->sum('total');
    $salesrtodelivered_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','rto-delivered')->where('date_created', '>=', $date)->get();
    $salesrtodelivered_order_count=count($salesrtodelivered_orders);
    $salesrtodelivered_saleAmount=$salesrtodelivered_orders->sum('total');
    $salescancel_orders=DB::table("orders")->where('orders.vid','=',$vid)->whereIn("orders.status",[$cancel,$fail])->where('date_created', '>=', $date)->get();
    $salescancel_order_count=count($salescancel_orders);
    $salesrtodelivered_saleAmount=$salescancel_orders->sum('total');
    $sales[0]['count'] = $sales_order_count;
    $sales[1]['count'] = $salesrtodelivered_order_count;
    $sales[2]['count'] = $salescancel_order_count;
    $sales[0]['sale'] = $salesorder_saleAmount;
    $sales[1]['sale'] = $salesrtodelivered_saleAmount;
    $sales[2]['sale'] = $salesrtodelivered_saleAmount;
    $sales[0]['status'] = $sale;
    $sales[1]['status'] = $retr;
    $sales[2]['status'] = $can;
     return $sales ;

   }

   public function delpiechart_data($vid)
   {
        $clos='closed';
        $del='deliveredtocust';
        $process='processing';
        $con='confirmed';
        $pack='packed';
        $hold='on-hold';
        $date = \Carbon\Carbon::today()->subDays(7);
        $processing_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','processing')->where('date_created', '>=', $date)->get();
        $processing_order_count=count($processing_orders);
        $processing_saleAmount=$processing_orders->sum('total');
        $confirm_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','confirmed')->where('date_created', '>=', $date)->get();
        $confirm_order_count=count($confirm_orders);
        $confirm_saleAmount=$confirm_orders->sum('total');
        $packed_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','packed')->where('date_created', '>=', $date)->get();
        $packed_order_count=count($packed_orders);
        $packed_saleAmount=$packed_orders->sum('total');
        $hold_orders=DB::table("orders")->where('orders.vid','=',$vid)->where('orders.status','=','on-hold')->where('date_created', '>=', $date)->get();
        $hold_order_count=count($hold_orders);
        $hold_saleAmount=$hold_orders->sum('total');
        $totalp=$processing_order_count+$confirm_order_count+$packed_order_count+$hold_order_count;

        // $process_percentage=round((($processing_order_count/$totalp)*100),2);
        // $confirm_percentage=round((($confirm_order_count/$totalp)*100),2);
        // $packed_percentage=round((($packed_order_count/$totalp)*100),2);
        // $hold_percentage=round((($hold_order_count/$totalp)*100),2);

        $pendencychart['deldata'][0]=$processing_order_count;
        $pendencychart['deldata'][1]=$confirm_order_count;
        $pendencychart['deldata'][2]=$packed_order_count;
        $pendencychart['deldata'][3]=$hold_order_count;

        $pendencytable[0]['status']=$process;
        $pendencytable[1]['status']=$con;
        $pendencytable[2]['status']=$pack;
        $pendencytable[3]['status']=$hold;
        $pendencytable[0]['count']=$processing_order_count;
        $pendencytable[1]['count']=$confirm_order_count;
        $pendencytable[2]['count']=$packed_order_count;
        $pendencytable[3]['count']=$hold_order_count;
        $pendencytable[0]['amount']=$processing_saleAmount;
        $pendencytable[1]['amount']=$confirm_saleAmount;
        $pendencytable[2]['amount']=$packed_saleAmount;
        $pendencytable[3]['amount']=$hold_saleAmount;
        // $pendencytable[0]['percentage']=$process_percentage;
        // $pendencytable[1]['percentage']=$confirm_percentage;
        // $pendencytable[2]['percentage']=$packed_percentage;
        // $pendencytable[3]['percentage']=$hold_percentage;

        return[
          $pendencychart,
          $pendencytable,

        ];

   }

}