<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('oid');
            $table->integer('vid');
            $table->integer('parent_id');
            $table->string('status');
            $table->string('currency');
            $table->string('version');
            $table->integer('prices_include_tax');
            $table->datetime('date_created');
            $table->datetime('date_modified');
            $table->integer('discount_total');
            $table->integer('discount_tax');
            $table->integer('shipping_total');
            $table->integer('shipping_tax');
            $table->integer('cart_tax');
            $table->integer('total');
            $table->integer('total_tax');
            $table->integer('customer_id');
            $table->string('order_key');
            $table->string('payment_method');
            $table->string('payment_method_title');
            $table->string('transaction_id');
            $table->string('customer_ip_address');
            $table->string('customer_user_agent');
            $table->string('created_via');
            $table->string('customer_note');
            $table->date('date_completed')->nullable();
            $table->date('date_paid')->nullable();
            $table->string('cart_hash')->nullable();
            $table->string('number');
            $table->date('date_created_gmt')->nullable();
            $table->date('date_modified_gmt')->nullable();
            $table->date('date_completed_gmt')->nullable();
            $table->date('date_paid_gmt')->nullable();
            $table->string('currency_symbol');
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
        Schema::dropIfExists('orders');
    }
}
