<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountController;
use App\Models\Orders;
use App\Models\billings;

use App\Models\meta_data_value;
use App\Http\Resources\OrdersResource;

class AccountController extends Controller
{
    
	public function Account_detail()
	{
		// $id =  
		 $obj=orders::join('billings','orders.oid','=','billings.order_id')		                 
		->join('line_items','line_items.order_id','=', 'billings.order_id')
		->get();
        return $obj;	
	}

		

}
