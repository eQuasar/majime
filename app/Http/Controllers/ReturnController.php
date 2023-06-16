<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Orders;
use App\Models\billings;
use App\Models\LineItems;
use App\Models\tax_lines;
use App\Models\shipping_lines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use PDF;
use \Milon\Barcode\DNS1D;

class ReturnController extends Controller
{
	//get data of Dto Intansit,Dto Delivered,Dto Booked
		 public function get_Status($vid, $status)
	    	{
				
	    		// echo $status; die;
	  		    $orders =DB::table("orders")
	  		       ->join('billings','orders.oid','=','billings.order_id')
			  		->where('orders.vid',$vid)
		       ->where('orders.status',$status)
			   ->where('billings.vid',$vid)
		       ->select("orders.*","billings.*",
		        		DB::raw("(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." GROUP BY line_items.order_id) as quantity"),
		        		DB::raw("(SELECT parent_name FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as name"),
		        		DB::raw("(SELECT sku FROM line_items WHERE line_items.order_id = orders.oid AND line_items.vid = ".intval($vid)." limit 1) as sku"))
		       ->orderBy('oid','DESC')
		        ->get();
		        
				return $orders;
	  		}
	}

