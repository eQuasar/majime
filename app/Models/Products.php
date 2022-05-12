<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    
   protected $table = 'products';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'order_id',
	'vid',
	'product_id', 
	'name',
	'slug',
    'permalink',
    'type',
    'status',
	'featured',
	'catalog_visibility',
	'description',
	'short_description',
	'sku',
	'price',
	'regular_price',
	'sale_price',
	'on_sale',
	'purchasable',
	'total_sales',
	'virtual',
	'downloadable',
	'downloads',
	'download_limit',
	'download_expiry',
	'external_url',
	'button_text',
	'tax_status',
	'tax_class',
	'manage_stock',
	'stock_quantity',
	'backorders',
	'backorders_allowed',
	'backordered',
	'low_stock_amount',
	'sold_individually',
	'weight',
	'shipping_required',
	'shipping_taxable',
	'shipping_class',
	'shipping_class_id',
	'reviews_allowed',
	'average_rating',
	'rating_count',
	'upsell_ids',
	'cross_sell_ids',
	'parent_id',
	'purchase_note',
	'categories',
	    
	];


}