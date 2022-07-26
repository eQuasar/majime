<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorRatecardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_ratecards', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->integer('vid');
            $table->integer('cod');
            $table->integer('codper');
            $table->integer('after500gm');                       
            $table->integer('sms_charges');    
            $table->integer('majime_charges');                       
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
        Schema::dropIfExists('vendor_ratecards');
    }
}
