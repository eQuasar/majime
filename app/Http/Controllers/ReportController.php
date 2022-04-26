<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Orders;
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

class ReportController extends Controller
{
   
	public function Report_detail()
	{
		 // $obj=Orders::where('id','5393')->first();
		 //$obj=Orders::whereId('5393')->first();
           //             return $obj;
		return orders::all();
	}

    public function ReportProfile_detail()
  
    {

          //$obj=Orders::whereId('5393')->first(); 
           $obj=Orders::where('id','5393')->first();
		// $obj=Orders::whereId('5393')->first();
        return $obj; 
 
        //
    
        // dd($client);
     //  return $order;
    }
           
		
	}










