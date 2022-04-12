<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMode extends Model
{
    use HasFactory;
    public function cost()
    {
        return $this->hasMany(ServiceCost::class,'service_mode');
    }
    public function appointment()
    {
        return $this->hasMany(Appointment::class,'vehicle_id');
    }
    public function vehicle()
    {
        return $this->hasMany(Vehicle::class,'vehicle_type');
    }
}
