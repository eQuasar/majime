<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    public function billing()
    {
        return $this->belongsTo(Billings::class);
    }
    public function line_items()
    {
        return $this->hasMany(LineItems::class);
    }
}
