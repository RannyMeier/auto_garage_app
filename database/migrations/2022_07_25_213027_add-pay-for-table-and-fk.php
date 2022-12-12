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
        Schema::create('vendor_pay_for', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pay_for');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('vendor_pay_details', function (Blueprint $table) {
            $table->integer('pay_for_id')->unsigned()->nullable()->after('payment_type');

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
        Schema::table('vendor_pay_details', function (Blueprint $table) {
            $table->dropColumn('pay_for_id');
        });
        Schema::dropIfExists('vendor_pay_for');
    }
};
