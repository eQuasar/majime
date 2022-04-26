<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class meta_data extends Model
{
   protected $table = 'meta_data';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	//'id',
	'Order_id',
	'id',
	'key', 
	'value',

	];


}