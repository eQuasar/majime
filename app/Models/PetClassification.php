<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetClassification extends Model
{
    use HasFactory;
    public function breed()
    {
        return $this->belongsTo(Breed::class,'breed_id');
    }
    public function ages()
    {
        return $this->belongsTo(ClassificationMonth::class,'age');
    }
    public function classes()
    {
        return $this->belongsTo(PetClass::class,'pet_class');
    }
}
