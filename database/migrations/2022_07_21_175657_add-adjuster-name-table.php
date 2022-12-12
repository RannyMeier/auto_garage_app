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
        Schema::create('adjuster', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ins_id')->unsigned();
            $table->string('adjuster_name')->unique();
            $table->string('phone');
            $table->string('fax');
            $table->string('car');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('ins_id')->references('id')->on('insurance_adjuster');
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
