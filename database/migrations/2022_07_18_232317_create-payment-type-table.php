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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::table('payment_types')->insert(
            [
                ['name'=>'Cash'],
                ['name'=>'Check'],
                ['name'=>'Master'],
                ['name'=>'Visa'],
                ['name'=>'American Express'],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_types');
    }
};
