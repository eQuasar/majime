<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
    
    public function groomer()
    {
        return $this->belongsTo(OtherUser::class,'groomer_id');
    }
    public function type()
    {
        return $this->belongsTo(ServiceMode::class,'vehicle_type');
    }
    public function zone()
    {
        return $this->hasMany(VehicleZone::class,'vehicle_id');
    }
    public function appointment()
    {
        return $this->hasMany(Appointment::class,'vehicle_assign');
    }
}
