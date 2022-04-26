<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class line_items extends Model
{
     protected $table = 'line_items';
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
	'name', 
	'product_id',
	'variation_id',
	'quantity',
	'tax_class',
	'subtotal',
	'subtotal_tax',
	'total',
	'total_tax',
	'sku',
	'price',
	'parent_name',

	];


}