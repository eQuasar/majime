<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\billings;
use App\Models\line_items;
use App\Models\line_items_meta;


class ProductController extends Controller
{
 
	public function productDetail()
	{
		//return billings::all();
		$obj=line_items::join('billings','line_items.Order_id','=','billings.Order_id')
						->join('line_items_meta','line_items_meta.product_id','=', 'line_items.product_id')
						->get();
                         return $obj;
		
}
