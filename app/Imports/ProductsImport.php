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
            "price"=>$row['price'],
            "name"=>$row['name'],
            "hsn_code"=>$row['hsn_code'],
            "weight"=>$row['weight'],
            "categories"=>$row['categories'],
        ]);
        // dd($row['product_id'],);
        // die();
        // $data = Products::where('product_id', $product_id)->first();
        $row_data = DB::table("products")->where('vid','=',intval($row['vid']))->where('product_id','=',intval($row['product_id']))->get();
        // $hsn_data = DB::table("hsn_details")->pluck('hsn_code')->toArray();
        // if (in_array($row['hsn_code'], $hsn_data))
        // {
        //     echo "yes";die();
        if($row_data->isempty())
        {
            Products::insert(['vid'=>$row['vid'],'product_id' =>$row['product_id'], 'name'=>$row['name'],'weight'=>$row['weight']??'-', 'hsn_code'=>$row['hsn_code']??'-','categories'=>$row['categories']??'-']);
        }
        else
        {
           DB::table('products')->where('product_id','=',$row['product_id'])->update(['name'=>$row['name'],'weight'=>$row['weight'],'price'=>$row['price'], 'hsn_code'=>$row['hsn_code'],'categories'=>$row['categories']]);
        //    return response()->json([ 'msg' => "Update Successfully"]);
        }
        
    // }
    // else
    // {
    //         return response()->json([ 'msg' => "Please Enter Valid HSN Code"]);
    // }
       
 
    }    
    
}

