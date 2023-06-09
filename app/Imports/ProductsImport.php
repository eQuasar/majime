<?php

namespace App\Imports;

use App\Models\Products;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ProductsImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    
    {
        $product = new Products([
            "vid"=>$row['vid'],
            "product_id"=>$row['product_id'],
            "cost"=>$row['cost'],
            "hsn_code"=>$row['hsn_code'],
            "weight"=>$row['weight'],
            "categories"=>$row['categories'],
            "price"=>$row['price'],
            "name"=>$row['name'],
        ]);
        // die();
        // $data = Products::where('product_id', $product_id)->first();
        $row_data = DB::table("products")->where('vid','=',intval($row['vid']))->where('product_id','=',intval($row['product_id']))->get();
        // dd($row_data);
        // die();
        if($row_data->isempty())
        {
            Products::insert(['vid'=>$row['vid'],'product_id' =>$row['product_id'], 'name'=>$row['name'],'weight'=>$row['weight']??'-','price'=>$row['price'], 'hsn_code'=>$row['hsn_code']??'-','categories'=>$row['categories']??'-']);
        }
        else
        {
           DB::table('products')->where('product_id','=',$row['product_id'])->where('products.vid','=',intval($row['vid']))->update(['name'=>$row['name'],'weight'=>$row['weight'],'price'=>$row['price'], 'hsn_code'=>$row['hsn_code'],'categories'=>$row['categories']]);
        //    return response()->json([ 'msg' => "Update Successfully"]);
        }
       
 
    }    
    
}

