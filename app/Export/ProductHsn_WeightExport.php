<?php

namespace App\Exports;

use App\Models\Products;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class product_HsnWeight_export implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */ 
    public function headings():array{
        return[
            'HSN',
            'Weight',
            'Product_name',
            'Product_id',
            'vendor_name',
        ];
    } 
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $unitdetails = DB::table("line_items")
            ->join('products', 'line_items.product_id', '=', 'products.product_id')
            // ->where('vid','=','products.vid')
            // ->where('vid','=','line_items.vid')
            ->select("products.hsn_code as HSN","products.weight as Weight","products.name as product_name","products.product_id as product_id")->get();
        return $unitdetails;
    }
}
