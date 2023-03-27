<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
           $table->integer('vid');
           $table->integer('product_id');
           $table->string('slug');
           $table->string('permalink');
           $table->string('type');
           $table->string('status');
           $table->integer('featured');
           $table->string('catalog_visibility');
           $table->string('catalog_visibility');
           $table->string('description');
           $table->string('short_description');
           $table->string('sku');
           $table->integer('price');
           $table->integer('regular_price');
           $table->integer('sale_price');
           $table->string('on_sale');
           $table->string('purchasable');
           $table->integer('total_sales');
           $table->string('virtual');
           $table->string('downloadable');
           $table->string('downloads');
           $table->integer('download_limit');
           $table->integer('download_expiry');
           $table->string('external_url');
           $table->string('button_text');
            $table->string('tax_status');
            $table->string(' tax_class ');
            $table->string('manage_stock');
            $table->string('stock_quantity')->nullable();
            $table->string('backorders');
            $table->string('backorders_allowed');
            $table->string('backordered');
            $table->string('low_stock_amount')->nullable();
            $table->string('sold_individually');
            $table->integer('weight');
            $table->string('shipping_required');
            $table->string('shipping_taxable');
            $table->string('shipping_class');
            $table->integer('shipping_class_id');
            $table->string('reviews_allowed');
            $table->integer('average_rating');
            $table->integer('rating_count')->nullable();
            $table->integer('upsell_ids')->nullable();
            $table->integer('cross_sell_ids');
            $table->string('parent_id')->nullable();
            $table->string('purchase_note')->nullable();
            $table->string('categories')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
