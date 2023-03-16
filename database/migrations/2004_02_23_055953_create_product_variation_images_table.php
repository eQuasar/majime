<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variation_images', function (Blueprint $table) {
            $table->id();
            $table->integer('vid');
            $table->integer('product_id');
            $table->integer('variation_id');
            $table->integer('image_id');
            $table->dateTime('date_created');
            $table->dateTime('date_created_gmt');  
            $table->dateTime('date_modified');  
            $table->dateTime('date_modified_gmt');  
            $table->string('src');  
            $table->string('name');  
            $table->string('alt');  

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
        Schema::dropIfExists('product_variation_images');
    }
}
