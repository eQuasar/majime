<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hsn_detail extends Model
{
    protected $table = 'hsn_details';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'id',
	'hsn_code',
	'slab_1', 
	'slab_2',
	'sale_amount',
    'description',

    
	];
}
