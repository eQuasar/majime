<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    use HasFactory;
    public function category()
    {
        return $this->belongsTo(PetCategory::class,'pet_cat_id');
    }
    public function pet()
    {
        return $this->belongsTo(Pet::class,'breed_id');
    }
    public function classification()
    {
        return $this->hasMany(PetClassification::class,'breed_id');
    }
}
