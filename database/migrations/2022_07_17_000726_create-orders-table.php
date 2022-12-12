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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cust_id')->unsigned();
            $table->integer('vehicle_id')->unsigned();
            $table->integer('ins_adj_id')->unsigned()->nullable();
            $table->float('tax_rate',8,4)->default(0.0761);
            $table->float('labor_rate',8,2)->default(105.00);
            $table->float('towing',8,2)->default(0);
            $table->float('waste',8,2)->default(0);
            $table->integer('storage_days')->default(0);
            $table->float('storage_rate',8,2)->default(24.50);
            $table->float('storage_cost',8,2)->default(0);
            $table->float('parts_markup',8,2)->default(0.35);
            $table->float('discount',8,2)->default(0);
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('date_created')->default(NOW());
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('cust_id')->references('id')->on('customer_details');
            $table->foreign('vehicle_id')->references('id')->on('vehicle_details');
            $table->foreign('ins_adj_id')->references('id')->on('insurance_adjuster');
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
};
