<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// 1994_03_17_095511_create_billing_processeds_table.php
class CreateBillingProcessedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_processeds', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_name');
            $table->string('invoicing_type');
            $table->string('customer_invice_no');
            $table->string('sub_order_id');
            $table->string('textable_amount');
            $table->integer('igst');
            $table->integer('sgst');
            $table->integer('cgst');
            $table->string('invoice_amount');
            $table->string('hsn_code');
            $table->string('text_percentage');
            $table->date('dispatch_date');
            $table->string('order_from');
            $table->string('order');
            $table->date('delivered_date');
            $table->date('dto_booked_date');
            $table->date('dto_delivered_to_warhouse_date');
            $table->integer('sale_return_status');
            $table->date('sale_return_date');
            $table->date('refund_date');
            $table->date('wallet_procesed_date');
            $table->integer('waybill_no');
            $table->integer('waybpill_no');
            $table->integer('parent_order_number');
            $table->integer('order_status');
            $table->date('order_date');
            $table->string('customer_note');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->string('city');
            $table->integer('status_code');
            $table->integer('post_code');
            $table->integer('country_code');
            $table->string('email');
            $table->integer('phone');
            $table->string('pay_method_title');
            $table->integer('order_subtotal_amount');
            $table->integer('cart_discount_amount');
            $table->integer('coupon_discount');
            $table->integer('order_amount');
            $table->string('wallet_used');
            $table->string('collectable_amount');
            $table->string('orderrefund_amount');
            $table->integer('product_id');
            $table->string('product_name');
            $table->string('sku');
            $table->integer('product_qty');
            $table->string('item_cost');
            $table->integer('coupon_code');
            $table->string('product_weight');
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
        Schema::dropIfExists('billing_processeds');
    }
}
