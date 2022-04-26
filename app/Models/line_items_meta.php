<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class line_items_meta extends Model
{
   protected $table = 'line_items_meta';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'id',
	'product_id',
	'Line_item_id',
	'key', 
	'value',
	'display_key',
	'display_value',
	






	];


}