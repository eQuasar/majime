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
        return new Products([
            "vid"=>$row['vid'],
            "product_id"=>$row['product_id'],
            "name"=>$row['product_name'],
            "weight"=>$row['weight'],
            "hsn_code"=>$row['hsn'],
        ]);
       
    }
}

