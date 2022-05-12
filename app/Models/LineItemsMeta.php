<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineItemsMeta extends Model
{
    use HasFactory;
     public function line_items($id)
    {
        return $this->hasOne(line_items::class, 'product_id');
    }
}