<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetCoatLevel extends Model
{
    use HasFactory;
    public function pet()
    {
        return $this->hasOne(Pet::class,'coat_level');
    }
}
