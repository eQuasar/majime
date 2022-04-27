<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('line_item_id');
            $table->string('key');
            $table->string('value');
            $table->string('display_key');
            $table->string('display_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_categories');
    }
}
