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

	public function data_scraping()
	{
		// echo "string"; die;
		$url = "http://api.scraperapi.com?api_key=e2947471986f6185ce437527fef00886&url=https://www.idp.com/india/universities/university-of-toronto/iid-ca-00764/";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		var_dump($response);
	}

}
