<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('appointment_id');
            $table->integer('user_id');
            $table->string('start_process_date');
            $table->string('process_start_time');
            $table->string('end_process_date');
            $table->string('process_end_time');
            $table->string('startprocess_image_files');
            $table->string('endprocess_image_files');
            $table->string('payment_method');
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
        Schema::dropIfExists('process_appointments');
    }
}
