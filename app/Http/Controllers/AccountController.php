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
		 $obj=Orders::join('billings','Orders.id','=','billings.Order_id')		                 
						 ->join('line_items','line_items.Order_id','=', 'billings.Order_id')
						->get();
                        return $obj;	
	}

		

}
