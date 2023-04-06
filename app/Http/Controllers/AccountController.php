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
    
	//api fetch  account detail from billings and line_items table table
	public function Account_detail()
	{
	
		//join two table billing and line_items with order_id
		 $obj=orders::join('billings','orders.oid','=','billings.order_id')		                 
		->join('line_items','line_items.order_id','=', 'billings.order_id')
		->get();
        return $obj;	
	}

		

}
