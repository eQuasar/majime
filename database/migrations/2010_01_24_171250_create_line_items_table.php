<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->integer('vid');
            $table->integer('order_id');
            $table->integer('line_item_id');
            $table->string('name');
            $table->integer('product_id');
            $table->integer('variation_id')->nullable();
            $table->integer('quantity');
            $table->string('tax_class')->nullable();
            $table->integer('subtotal');
            $table->integer('subtotal_tax');
            $table->integer('total');
            $table->integer('total_tax');
            $table->string('sku')->nullable();
            $table->integer('price');
            $table->string('parent_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_items');
    }
}
