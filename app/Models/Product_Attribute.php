<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Attribute extends Model
{
    
   protected $table = 'products_attributes';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'id',

					'vid',
					'product_id',
					'attribute_id',
					'name',
					'slug',
					'type',
					'order_by',
					'has_archives',
				

	    
	];


}