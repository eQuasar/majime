<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    public function cost()
    {
        return $this->hasMany(ServiceCost::class,'service_id');
    }
    public function charge()
    {
        return $this->hasMany(ClassificationCharge::class,'service_id');
    }
    public function appointment()
    {
        return $this->hasOne(AppointmentService::class,'service_id');
    }
}
