<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    
   protected $table = 'products_tag';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'id',
	'vid',
	'tag_id', 
	'name',
	'slug',
	'description',
	'count',
	    
	];


}