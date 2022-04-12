<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetCategory extends Model
{
    use HasFactory;
    public function breed()
    {
        return $this->hasOne(Breed::class,'pet_cat_id');
    }
    public function pet()
    {
        return $this->hasOne(Pet::class,'pet_cat_id');
    }
}
