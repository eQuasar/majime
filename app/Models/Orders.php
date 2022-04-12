<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
   protected $table = 'Orders';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'order_id',
	'vid',
    'parent_id',
	'status', 
	'currency',
	'version',
    'prices_include_tax',
    'date_created',
    'date_modified',
    'discount_total',
    'discount_tax',
    'shipping_total',
    'shipping_tax',
    'cart_tax',
    'total',
    'total_tax',
    'customer_id',
    'order_key',
    'payment_method', 
    'payment_method_title',
    'transaction_id',
    'customer_ip_address',
    'customer_user_agent',
    'created_via',
    'customer_note',
    'date_completed',
    'date_paid',
    'cart_hash',
    'number',
    'date_created_gmt',
    'date_modified_gmt',
    'date_completed_gmt',
    'date_paid_gmt',
    'currency_symbol',

	];


}
