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
        Schema::table('adjuster', function (Blueprint $table) {
            $table->integer('ins_id')->unsigned()->nullable()->change();
        });
        
        Schema::table('employee_pay_details', function (Blueprint $table) {
            $table->integer('invoice_id')->unsigned()->nullable()->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('order_id')->unsigned()->nullable()->change();
            $table->integer('employee_id')->unsigned()->nullable()->change();
            $table->integer('service_desc_id')->unsigned()->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('cust_id')->unsigned()->nullable()->change();
            $table->integer('vehicle_id')->unsigned()->nullable()->change();
        });

        Schema::table('payment_details', function (Blueprint $table) {
            $table->integer('order_id')->unsigned()->nullable()->change();
        });

        Schema::table('vehicle_details', function (Blueprint $table) {
            $table->integer('cust_id')->unsigned()->nullable()->change();
        });

        Schema::table('vendor_pay_details', function (Blueprint $table) {
            $table->integer('vendor_id')->unsigned()->nullable()->change();
            $table->integer('invoice_id')->unsigned()->nullable()->change();
        });

        Schema::table('vendor_pay_details', function (Blueprint $table) {
            $table->integer('vendor_id')->unsigned()->nullable()->change();
            $table->integer('pay_for_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
