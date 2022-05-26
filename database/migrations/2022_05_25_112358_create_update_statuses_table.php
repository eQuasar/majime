<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('update_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('orderid');
            $table->integer('vid');
            $table->string('awb');
            $table->string('delivery_status_name');
            $table->string('delivery_status_code');
            $table->string('delivery_order_sno');
            $table->dateTime('delivery_status_date_and_time');
            $table->string('delivery_brief_status');
            $table->string('delivery_instructions');
            $table->integer('delivery_dispatch_count');
            $table->integer('delivery_invoice_amount');
            $table->string('delivery_scans');
            $table->dateTime('delivery_destination_received_date');
            $table->dateTime('delivery_pickup_date');
            $table->integer('delivery_charged_weight_in_grams');
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
        Schema::dropIfExists('update_statuses');
    }
}