<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuborderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suborder_details', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('vid');
            $table->string('suborder_id');
            $table->integer('line_item_id');
            $table->string('sale_return_order_status');
            $table->string('invoice_no');
            $table->string('customer_invoice_no');
            $table->string('total');
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
        Schema::dropIfExists('suborder_details');
    }
}
