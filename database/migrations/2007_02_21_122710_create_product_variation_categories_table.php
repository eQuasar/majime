<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variation_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('vid');
            $table->integer('product_id');
            $table->integer('variation_id');
            $table->string('categories_id');
            $table->integer('name');
            $table->integer('slug');    
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
        Schema::dropIfExists('product_variation_categories');
    }
}
