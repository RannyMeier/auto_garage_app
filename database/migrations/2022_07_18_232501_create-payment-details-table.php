<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->float('total_bill',8,2)->nullable();
            $table->float('amount_paid',8,2)->nullable();
            $table->float('balance',8,2)->nullable();
            $table->integer('payment_type')->unsigned();
            $table->string('credit_card_number')->nullable();
            $table->string('payment_date')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('payment_type')->references('id')->on('payment_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
};
