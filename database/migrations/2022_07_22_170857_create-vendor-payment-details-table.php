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
        Schema::create('vendor_pay_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->integer('invoice_id')->unsigned();
            $table->integer('payment_type')->unsigned();
            $table->float('total_bill',8,2)->default(0);
            $table->float('amount_paid',8,2)->default(0);
            $table->float('balance',8,2)->default(0);
            $table->string('payment_date')->nullable();
            $table->string('card_check_number')->nullable();
            $table->string('memo')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('invoice_id')->references('id')->on('invoices');
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
        Schema::dropIfExists('vendor_pay_details');
    }
};
