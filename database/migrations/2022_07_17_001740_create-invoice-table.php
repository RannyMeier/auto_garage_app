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
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->integer('employee_id')->unsigned();
            $table->integer('service_desc_id')->unsigned();
            $table->boolean('is_sublet')->default(0);
            $table->string('sublet_amount')->default(0);
            $table->integer('parts_num')->default(0);
            $table->float('quantity',8,2)->default(0);
            $table->float('our_cost',8,2)->default(0);
            $table->float('cust_cost',8,2)->default(0);
            $table->float('est_cost',8,2)->default(0);
            $table->boolean('is_paint_material')->default(0);
            $table->float('total_paint_material_cost',8,2)->default(0);
            $table->float('labor_hours',8,2)->default(0);
            $table->float('total_labor_cost',8,2)->default(0);
            $table->float('estimated_labor',8,2)->default(0);
            $table->float('sub_total',8,2)->default(0);
            $table->float('sub_total_with_tax',8,2)->default(0);
            $table->timestamp('date_created')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('service_desc_id')->references('id')->on('service_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
