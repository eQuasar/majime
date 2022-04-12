<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
    public function areas_tab()
    {
        return $this->hasMany(ZoneArea::class,'zone_id');
    }
    public function vehicle_tab()
    {
        return $this->hasMany(VehicleArea::class,'zone_id');
    }
    
}
