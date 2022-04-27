<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineItems extends Model
{
    use HasFactory;
    public function line_item_meta()
    {
        return $this->belongsTo(LineItemsMeta::class,'product_id');
    }
}