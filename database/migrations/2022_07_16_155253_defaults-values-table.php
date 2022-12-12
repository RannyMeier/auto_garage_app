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
        Schema::create('default_values', function (Blueprint $table) {
            $table->increments('id');
            $table->float('labor_rate',8,2);
            $table->float('storage_rate',8,2);
            $table->float('parts_markup',8,2);
            $table->float('interest_rate',8,2);
            $table->float('sales_tax_rate',8,4);
            $table->float('paint_material_rate',8,2);
            $table->string('owner_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::table('default_values')->insert(
            [
                ['labor_rate'=>105.00,'storage_rate'=>24.50,'parts_markup'=>0.35,'interest_rate'=>0.18,'sales_tax_rate'=>0.0761,'paint_material_rate'=>26.00,'owner_name'=>'Ron Fischer','company_name'=>'West County Auto Body Repair','address'=>'1650 North Lindbergh','city'=>'St. Louis','state'=>'MO','zip'=>'63132','phone1'=>'(314)993-4466','phone2'=>'(314)993-6005'],
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
        Schema::dropIfExists('default_values');
    }
};
