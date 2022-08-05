<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReldatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_reldates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vid');
            $table->integer('oid');
            $table->date('order_confirmdate');
            $table->date('order_dispatchdate');
            $table->date('order_canceldate');
            $table->date('order_deldate');
            $table->date('dto_refunddate');
            $table->date('rto_deldate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_reldates');
    }
}
