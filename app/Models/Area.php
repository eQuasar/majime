<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
    public function zone_area()
    {
        return $this->hasMany(ZoneArea::class,'zone_id');
    }
}
