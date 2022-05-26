<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWayDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('way_data', function (Blueprint $table) {
            $table->id();
           $table->integer('user_id');
           $table->integer('vid');
           $table->string('city');
           $table->string('name');
           $table->integer('pin');
           $table->string('country');
           $table->integer('phone');
           $table->string('add');
           $table->string('Token');
           $table->string('order_prefix');
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
        Schema::dropIfExists('way_data');
    }
}
