<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function pet()
    {
        return $this->hasMany(Pet::class,'client_id');
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
    public function area()
    {
        return $this->belongsTo(Area::class,'area_id');
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class,'zone_id');
    }
    public function appointment()
    {
        return $this->hasMany(Appointment::class,'client_id');
    }
}
