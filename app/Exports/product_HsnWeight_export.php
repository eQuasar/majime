<?php

namespace App\Exports;

use App\Models\Products;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Http\Resources\UnitDetailsResource5;

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
        $vendor_name=DB::table("vendors")->select('id','name')->get();
        $details = DB::table("line_items")
            ->join('products', 'line_items.product_id', '=', 'products.product_id')
            ->join('vendors', 'vendors.id', '=', 'products.vid')
            ->select("products.hsn_code as HSN","products.weight as Weight","products.name as product_name","products.product_id as product_id","vendors.id as vendor_id")->get();
        return $details;
    }
}
