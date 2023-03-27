<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneratecardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoneratecards', function (Blueprint $table) {
           $table->bigIncrements('id');
            $table->string('zoneno');
            $table->integer('vid');
            $table->integer('fwd');
            $table->integer('rto');
            $table->integer('dto');
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
        Schema::dropIfExists('zoneratecards');
    }
}
