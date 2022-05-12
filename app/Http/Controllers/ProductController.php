<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Billings;
use App\Models\LineItems;
use App\Models\line_items_metas;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Support\Facades\DB;




class ProductController extends Controller
{
 
	// public function productDetail()
	// {
	// 	//return billings::all();
	// $obj=Products::join('categories','products.id','=','categories.id')->get(['products.product_id','products.name','products.price','categories.name']);
	//     //->join('billings','billings.order_id','=','orders.oid')-
	  
 //        return $obj;
	// }

	public function productDetail(Request $request)
	{
		// dd($request->vid);die;
		$vendor=$request->vid;
		$Order=DB::table("line_items")->where('line_items.vid','=',intVal($vendor))
		->get();


	return $Order;







		//$orders = DB::table('line_items')
        //->select("line_items.*")
        // ->get();
        //  ->groupBy('variation_id')

                    // DB::raw("(SELECT products.categories FROM products

                    //             WHERE products.product_id = line_items.product_id

                    //             GROUP BY products.id) as cat_name"))
//
         // ->get();
		// $obj=Products::all();
         
       // return $collection;
	}
}



