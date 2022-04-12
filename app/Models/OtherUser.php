<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherUser extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class,'state_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class,'groomer_id');
    }
    
}
