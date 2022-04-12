<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetClass extends Model
{
    use HasFactory;
    public function classification()
    {
        return $this->hasMany(PetClassification::class,'pet_class');
    }
    public function charge()
    {
        return $this->hasMany(ClassificationCharge::class,'class_id');
    }
}
