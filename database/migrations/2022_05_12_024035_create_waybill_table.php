<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWayBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waybill', function (Blueprint $table) {
            $table->id();
           $table->integer('order_id');
           $table->integer('vid');
           $table->string('waybill_no')->nullable();
           $table->string('return_waybill_no')->nullable();
           $table->integer('date_created');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waybill');
    }
}
