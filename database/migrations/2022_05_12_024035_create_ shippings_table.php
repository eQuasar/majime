<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateshippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->integer('vid');
            $table->integer('vid');
            $table->integer('Order_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address_1');
            $table->string('address_2');
            $table->string('city');
            $table->string('state');
            $table->string('postcode');
            $table->string('country');
            $table->integer('phone');
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shippings');
    }
}
