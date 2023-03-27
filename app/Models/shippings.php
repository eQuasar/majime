<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shippings extends Model
{
    
   protected $table = 'shippings';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'Order_id',
	'first_name',
	'last_name', 
	'company',
	'address_1',
    'address_2',
    'city',
    'state',
    'postcode',
    'country',
    'phone',
    
	];


}