<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }
    public function pet()
    {
        return $this->belongsTo(Pet::class,'pet_id');
    }
    public function breed()
    {
        return $this->belongsTo(Breed::class,'breed_id');
    }
    public function vehicle()
    {
        return $this->belongsTo(ServiceMode::class,'vehicle_id');
    }
    public function time()
    {
        return $this->belongsTo(TimeSlot::class,'time_id');
    }
    public function assign_vehicle()
    {
        return $this->belongsTo(Vehicle::class,'vehicle_assign');
    }
    public function items()
    {
        return $this->hasMany(AppointmentService::class,'appointment_id');
    }
}
