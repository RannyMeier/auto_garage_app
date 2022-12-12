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
        Schema::create('make_model', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_name')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::table('make_model')->insert(
            [
                ['model_name'=>'ABC'],
                ['model_name'=>'DEF'],
                ['model_name'=>'GHI'],
                ['model_name'=>'JKL'],
                ['model_name'=>'MNO'],
                ['model_name'=>'PQR'],
                ['model_name'=>'STU'],
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
        Schema::dropIfExists('make_model');
    }
};
