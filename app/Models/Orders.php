<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    use HasFactory;
    public function billings($id)
    {
        return $this->belongsTo(Billings::class, 'id');
    }
    public function line_items()
    {
        return $this->hasMany(LineItems::class);
    }
}
