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
        Schema::create('vehicle_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cust_id')->unsigned();
            $table->string('license')->nullable();
            $table->string('state')->nullable();
            $table->integer('make_model_id')->unsigned();
            $table->string('year')->nullable();
            $table->string('paint_color')->nullable();
            $table->string('paint_number')->nullable();
            $table->string('vehicle_num')->nullable();
            $table->string('manufacture_date')->nullable();
            $table->integer('miles')->nullable();  
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('cust_id')->references('id')->on('customer_details');
            $table->foreign('make_model_id')->references('id')->on('make_model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_details');
    }
};
