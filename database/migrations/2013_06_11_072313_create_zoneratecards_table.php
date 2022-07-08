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
           $table->bigInteger('id')->primary();
            $table->integer('zoneno');
            $table->integer('vid');
            $table->integer('fwd');
            $table->integer('dto');
            $table->integer('rto');
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
