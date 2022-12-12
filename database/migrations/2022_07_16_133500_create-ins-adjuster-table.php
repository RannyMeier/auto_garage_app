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
        Schema::create('insurance_adjuster', function (Blueprint $table) {
            $table->increments('id');
            $table->string('adjuster_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('car')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insurance_adjuster');
    }
};
