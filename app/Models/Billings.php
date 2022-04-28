<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billings extends Model
{
    use HasFactory;
    public function orders($id)
    {
        return $this->belongsTo(Orders::class, 'id');
    }
}