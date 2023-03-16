<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variation_attributes', function (Blueprint $table) {

            $table->id();
            $table->integer('vid');
            $table->integer('product_id');
            $table->integer('variation_id');
            $table->integer('attribute_id');
            $table->string('color');
            $table->string('size');    
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
        Schema::dropIfExists('product_variation_attributes');
    }
}
