<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassificationMonth extends Model
{
    use HasFactory;
    public function classification()
    {
        return $this->hasMany(PetClassification::class,'age');
    }
}
