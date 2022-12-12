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
        Schema::table('insurance_adjuster', function (Blueprint $table) {
            $table->dropColumn('adjuster_name');
            $table->dropColumn('phone');
            $table->dropColumn('car');
            $table->string('address')->after('fax');
            $table->string('city')->after('address');
            $table->string('state')->after('city');
            $table->string('zip')->after('state');
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
