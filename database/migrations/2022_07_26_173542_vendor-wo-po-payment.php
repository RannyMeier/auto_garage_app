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
        Schema::create('vendor_pay_wo_po', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->integer('pay_for_id')->unsigned();
            $table->float('amount_paid',8,2)->default(0);
            $table->string('payment_date');
            $table->string('check_number');
            $table->string('memo')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('pay_for_id')->references('id')->on('vendor_pay_for');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIFExists('vendor_pay_wo_po');
    }
};
