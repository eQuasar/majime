<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;
    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }
    public function category()
    {
        return $this->belongsTo(PetCategory::class,'pet_cat_id');
    }
    public function breed()
    {
        return $this->belongsTo(Breed::class,'breed_id');
    }
    public function coat()
    {
        return $this->belongsTo(PetCoatLevel::class,'coat_level');
    }
    public function aggresive_level()
    {
        return $this->belongsTo(PetAggresiveLevel::class,'aggresive');
    }
    public function appointment()
    {
        return $this->hasMany(Appointment::class,'pet_id');
    }
}
