<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduct_AttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_attributes', function (Blueprint $table) {
            $table->id();
            $table->integer('vid');
            $table->integer('attribute_id');
            $table->integer('name');
            $table->integer('slug');
            $table->integer('type');
            $table->integer('order_by');
            $table->integer('has_archives');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_attributes');
    }
}
