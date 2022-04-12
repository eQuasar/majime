<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassificationCharge extends Model
{
    use HasFactory;
    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
    public function petclass()
    {
        return $this->belongsTo(PetClass::class,'class_id');
    }
}
