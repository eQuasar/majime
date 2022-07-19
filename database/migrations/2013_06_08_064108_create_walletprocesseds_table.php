<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletprocessedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walletprocesseds', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->dateTime('date_created');
            $table->integer('oid');
            $table->integer('vid');
            $table->string('payment_mode');
            $table->string('status');
            $table->integer('sale_amount');
            $table->integer('Wallet_used');
            $table->integer('logistic_cost');
            $table->integer('payment_gateway_charges');
            $table->integer('sms_cost');
            $table->integer('majime_charges');
            $table->integer('net_amount');
            $table->integer('current_wallet_bal');
            $table->integer('order_count');
            
                








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
        Schema::dropIfExists('walletprocesseds');
    }
}
